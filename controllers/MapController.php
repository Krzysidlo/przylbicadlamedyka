<?php

namespace controllers;

use DateTime;
use Exception;
use classes\Pin;
use classes\User;
use classes\Frozen;
use classes\Hosmag;
use classes\Request;
use classes\Activity;
use classes\Functions as fs;

class MapController extends PageController
{
    public static function ajax_getInfo($get = []): array
    {
        $data = [
            'success' => true,
            'requests' => [],
        ];
        try {
            $requests = Request::getAll();
            foreach ($requests as $request) {
                $userAddress = $request->producer->getAddress();

                $frozen = false;
                $sql    = "SELECT 1 FROM `frozen` WHERE `requests_id` = '{$request->id}' AND `deleted` = 0;";

                if ($query = fs::$mysqli->query($sql)) {
                    $frozen = $query->fetch_row()[0] ?? false;
                }

                $flat    = !empty($userAddress->flat) ? "/$userAddress->flat" : "";
                $address = "{$userAddress->city}, {$userAddress->street} {$userAddress->building}";
                $address .= $flat;

                $data['requests'][$request->producer->id][$request->id] = [
                    'user_id'  => $request->producer->id,
                    'name'     => $request->producer->name,
                    'tel'      => $request->producer->tel,
                    'address'  => $address,
                    'latLng'   => $request->latLng,
                    'bascinet' => intval($request->bascinet),
                    'material' => intval($request->material),
                    'comments' => ($request->comments !== NULL ? (string)$request->comments . ", " : ""),
                    'frozen'   => (bool)$frozen,
                ];
            }

            if (!empty($data['requests'])) {
                foreach ($data['requests'] as &$dataRequest) {
                    foreach ($dataRequest as &$requests) {
                        if ($requests['comments'] !== "") {
                            $requests['comments'] = substr($requests['comments'], 0, -2);
                        }
                    }
                }
            }

            $pins = Pin::getAll();
            foreach ($pins as $pin) {
                $data['pins'][$pin->id] = [
                    'id'          => $pin->id,
                    'name'        => $pin->name,
                    'description' => $pin->description,
                    'latLng'      => $pin->latLng,
                    'bascinet'    => $pin->bascinet,
                    'material'    => $pin->material,
                    'type'        => $pin->type,
                ];
            }
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            return [
                'success' => false,
                'alert'   => "danger",
                'message' => "Wystąpił błąd podczas pobierania danych z bazy",
            ];
        }

        try {
            $data['bascinet'] = Frozen::count(USER_ID, "bascinet");
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            return [
                'success' => false,
                'alert'   => "danger",
                'message' => "Wystąpił błąd podczas pobierania danych z bazy",
            ];
        }

        return $data;
    }

    public static function ajax_newRequest($get = []): array
    {
        try {
            $latLng   = $get['latLng'] ?? NULL;
            $bascinet = $get['bascinet'] ?? NULL;
            $material = $get['material'] ?? NULL;
            $comments = $get['comments'] ?? NULL;

            $data = Request::create(USER_ID, $latLng, $bascinet, $material, $comments);
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            return [
                'success' => false,
                'alert'   => "danger",
                'message' => "Wystąpił błąd przy zapisie do bazy danych proszę spróbować ponownie",
            ];
        }

        return $data;
    }

    public static function ajax_freezeRequest($get = []): array
    {
        $usersID = filter_var($get['usersID'], FILTER_SANITIZE_STRING);
        $action  = filter_var($get['action'], FILTER_SANITIZE_STRING);
        $date    = filter_var($get['date'], FILTER_SANITIZE_STRING);
        $time    = filter_var($get['time'], FILTER_SANITIZE_STRING);

        try {
            $date = new DateTime($date . " " . $time);
            $now = new DateTime;
        } catch (Exception $e) {
            fs::log($e->getMessage());
            return [
                'success' => false,
                'alert'   => "danger",
                'message' => "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie.",
            ];
        }

        if ($date < $now) {
            return [
                'success' => false,
                'alert'   => "warning",
                'message' => "Data dostarczenia / odbioru musi być w przyszłości",
            ];
        }

        $requestsArr = Request::getIdsByUserID($usersID);

        return Frozen::create(USER_ID, $requestsArr, $date, $action, $usersID);;
    }

    public static function ajax_hosMag($get = []): array
    {
        if (empty($get['type']) || empty($get['quantity']) || empty($get['pinsID'])) {
            return [
                'success' => false,
                'alert'   => "danger",
                'message' => "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie.",
            ];
        }

        try {
            $pinsID    = filter_var($get['pinsID'], FILTER_SANITIZE_NUMBER_INT);
            $quantity = filter_var($get['quantity'], FILTER_SANITIZE_NUMBER_INT);
            $data     = Hosmag::create($pinsID, $quantity);

            $name = "";
            try {
                $pin = new Pin($pinsID);
                $name = $pin->name;
            } catch (Exception $e) {
                fs::log("Error: " . $e->getMessage());
            }

            $type = filter_var($get['type'], FILTER_SANITIZE_STRING);
            if ($data['success']) {
                switch ($type) {
                    case "hospital":
                        $endQuantity = Frozen::count(USER_ID, "bascinet");
                        if ($endQuantity > 0 || $data['success'] = Hosmag::deliverBascinet(USER_ID)) {
                            $date            = new DateTime;
                            $message         = "Dostarczono <span class='quantity'>{$quantity}</span> przyłbic do <span class='name'>{$name}</span>";
                            $data['success'] &= Activity::create(USER_ID, $date, $message, "action", NULL, NULL, $quantity)['success'];
                        }
                        break;
                    case 'magazine':
                        $date            = new DateTime;
                        $message         = "Zadeklarowano odbiór <span class='quantity'>{$quantity}</span> sztuk materiału z <span class='name'>{$name}</span>";
                        $data['success'] &= Activity::create(USER_ID, $date, $message, "action", NULL, NULL, NULL, $data['id'])['success'];
                        break;
                }
            }

            if (!$data['success']) {
                $data['alert']   = "danger";
                $data['message'] = "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie.";
            }

            return $data;

        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            return [
                'success' => true,
                'alert'   => "danger",
                'message' => "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie.",
            ];
        }
    }

    public static function ajax_collectHosMag($get = []): array
    {
        if (empty($get['hosMagID'])) {
            return [
                'success' => false,
                'alert'   => "danger",
                'message' => "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie.",
            ];
        }

        $hosMagID = filter_var($get['hosMagID'], FILTER_SANITIZE_NUMBER_INT);
        try {
            $hosMag = new Hosmag($hosMagID);
            if ($hosMag->collect()) {
                $date = new DateTime();
                $message = "Odebrano <span class='quantity'>{$hosMag->quantity}</span> sztuk materiału z <span class='name'>{$hosMag->pin->name}</span>";
                Activity::create(USER_ID, $date, $message);
                return [
                    'success' => true,
                    'alert'   => "success",
                    'message' => "Poprawnie potwierdzono odbiór",
                ];
            }
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            return [
                'success' => false,
                'alert'   => "danger",
                'message' => "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie.",
            ];
        }

        return [
            'success' => false,
            'alert'   => "danger",
            'message' => "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie.",
        ];
    }

    public static function ajax_delete($get = []): array
    {
        $data = [
            'success' => true,
            'alert'   => false,
            'message' => "",
        ];

        $error = [
            'success' => false,
            'alert'   => "danger",
            'message' => "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie.",
        ];

        if (!empty($get['id']) && !empty($get['type'])) {
            try {
                $element = NULL;
                switch ($get['type']) {
                    case "request":
                        $element = new Request($get['id']);
                        break;
                    case "frozen":
                        $element = new Frozen($get['id']);
                        break;
                    case "hosMag":
                        $element = new Hosmag($get['id']);
                        break;
                    default:
                        $data = $error;
                        break;
                }
                if ($data['success'] && !$element->delete()) {
                    $data = $error;
                }
            } catch (Exception $e) {
                fs::log("Error: " . $e->getMessage());
                $data = $error;
            }
        } else {
            $data = $error;
        }

        return $data;
    }

    public static function ajax_both($get = []): array
    {
        $material = self::ajax_material($get);
        if (!$material['success']) {
            return $material;
        }

        $bascinet = self::ajax_bascinet($get);
        if (!$bascinet['success']) {
            return $bascinet;
        }

        return [
            'success' => true,
            'alert'   => "success",
            'message' => "Poprawnie zarejestrowano dostarczenie i odbiór",
        ];
    }

    public static function ajax_material($get = []): array
    {
        $frozenIDs = filter_var($get['frozenID'], FILTER_SANITIZE_STRING);
        try {
            $frozen   = new Frozen($frozenIDs);
            $quantity = $frozen->material;
            $driver   = $frozen->driver;
            $producer = $frozen->producer;
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            return [
                'success' => false,
                'alert'   => "danger",
                'message' => "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie.",
            ];
        }

        if (!$frozen->deliver()) {
            return [
                'success' => false,
                'alert'   => "danger",
                'message' => "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie.",
            ];
        }

        $requestsIDs = explode(",", $frozen->requests);
        foreach ($requestsIDs as $requestID) {
            try {
                $request = new Request($requestID);
                $request->deliver();
            } catch (Exception $e) {
                continue;
            }
        }

        $date    = new DateTime;
        $message = "Dostarczono <span class='quantity'>{$quantity}</span> sztuk materiału do <span class='name'>{$producer->name}</span> (tel. <a href='tel:{$producer->tel}'>{$producer->tel}</a>)";
        $success = Activity::create($driver->id, $date, $message)['success'];
        $message = "Odebrano <span class='quantity'>{$quantity}</span> sztuk materiału od <span class='name'>{$driver->name}</span> (tel. <a href='tel:{$driver->tel}'>{$driver->tel}</a>)";
        $success &= Activity::create($producer->id, $date, $message)['success'];
        if ($success) {
            return [
                'success' => true,
                'alert'   => "success",
                'message' => "Poprawnie zarejestrowano dostarczenie maetriału",
            ];
        } else {
            return [
                'success' => false,
                'alert'   => "danger",
                'message' => "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie.",
            ];
        }
    }

    public static function ajax_bascinet($get = []): array
    {
        $quantity   = 0;
        $producer   = NULL;
        $driver     = new User;
        $requestIDs = filter_var($get['requestsID'], FILTER_SANITIZE_STRING);
        $requestIDs = explode(",", $requestIDs);
        try {
            foreach ($requestIDs as $requestID) {
                $request = new Request($requestID);

                if (empty($request->bascinet)) {
                    continue;
                }

                $quantity += $request->bascinet;
                if (empty($producer)) {
                    $producer = $request->producer;
                }

                if (!$request->deliver()) {
                    return [
                        'success' => false,
                        'alert'   => "danger",
                        'message' => "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie.",
                    ];
                }
            }
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            return [
                'success' => false,
                'alert'   => "danger",
                'message' => "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie.",
            ];
        }

        $date    = new DateTime;
        $message = "Odebrano <span class='quantity'>{$quantity}</span> przyłbic od <span class='name'>{$producer->name}</span> (tel. <a href='tel:{$producer->tel}'>{$producer->tel}</a>)";
        $success = Activity::create($driver->id, $date, $message)['success'];
        $message = "Przekazano <span class='quantity'>{$quantity}</span> przyłbic kierowcy <span class='name'>{$driver->name}</span> (tel. <a href='tel:{$driver->tel}'>{$driver->tel}</a>)";
        $success &= Activity::create($producer->id, $date, $message)['success'];
        if ($success) {
            return [
                'success' => true,
                'alert'   => "success",
                'message' => "Poprawnie zarejestrowano dostarczenie maetriału",
            ];
        } else {
            return [
                'success' => false,
                'alert'   => "danger",
                'message' => "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie.",
            ];
        }
    }

    public function content(array $args = [])
    {
        if (USER_PRV === User::USER_NO_CONFIRM) {
            self::redirect("/");
            exit(0);
        }
        $data = [];
        return parent::content(array_merge($args, $data));
    }
}