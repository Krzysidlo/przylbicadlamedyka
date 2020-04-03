<?php

namespace controllers;

use DateTime;
use Exception;
use classes\Pin;
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
        ];
        try {
            $requests = Request::getAll();
            foreach ($requests as $request) {
                $userAddress = $request->user->getAddress();

                $frozen = false;
                $sql    = "SELECT 1 FROM `frozen` WHERE `requests_id` = '{$request->id}' AND `deleted` = 0;";
                if ($query = fs::$mysqli->query($sql)) {
                    $frozen = $query->fetch_row()[0] ?? false;
                }
                $flat                                               = !empty($userAddress->flat) ? "/$userAddress->flat" : "";
                $address                                            = "{$userAddress->city}, {$userAddress->street} {$userAddress->building}";
                $address                                            .= $flat;
                $data['requests'][$request->user->id][$request->id] = [
                    'user_id'  => $request->user->id,
                    'name'     => $request->user->name,
                    'tel'      => $request->user->tel,
                    'address'  => $address,
                    'latLng'   => $request->latLng,
                    'bascinet' => intval($request->bascinet),
                    'material' => intval($request->material),
                    'comments' => ($request->comments !== NULL ? (string)$request->comments . ", " : ""),
                    'frozen'   => (bool)$frozen,
                ];
            }

            foreach ($data['requests'] as &$dataRequest) {
                foreach ($dataRequest as &$requests) {
                    if ($requests['comments'] !== "") {
                        $requests['comments'] = substr($requests['comments'], 0, -2);
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
        $usersID = filter_var($get['userId'], FILTER_SANITIZE_STRING);
        $action  = filter_var($get['action'], FILTER_SANITIZE_STRING);
        $date    = filter_var($get['date'], FILTER_SANITIZE_STRING);
        $time    = filter_var($get['time'], FILTER_SANITIZE_STRING);

        try {
            $date = new DateTime($date . " " . $time);
        } catch (Exception $e) {
            fs::log($e->getMessage());
            return [
                'success' => false,
                'alert'   => "danger",
                'message' => "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie.",
            ];
        }

        $requestsArr = Request::getIdsByUserID($usersID);

        return Frozen::create(USER_ID, $requestsArr, $date, $action, $usersID);;
    }

    public static function ajax_hosMag($get = []): array
    {
        if (empty($get['type']) || empty($get['quantity']) || empty($get['pinID'])) {
            return [
                'success' => false,
                'alert'   => "danger",
                'message' => "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie.",
            ];
        }

        try {
            $pinID    = filter_var($get['pinID'], FILTER_SANITIZE_NUMBER_INT);
            $quantity = filter_var($get['quantity'], FILTER_SANITIZE_NUMBER_INT);
            $data     = Hosmag::create($pinID, $quantity);

            $success = $data['success'];

            $name = "";
            $sql  = "SELECT `name` FROM `pins` WHERE `id` = {$pinID};";
            if ($query = fs::$mysqli->query($sql)) {
                $name = $query->fetch_row()[0] ?? "";
            }

            $type = filter_var($get['type'], FILTER_SANITIZE_STRING);
            if ($success && $type == "hospital") {
                if ($success = Hosmag::deliverBascinet(USER_ID)) {
                    $date            = new DateTime;
                    $message         = "Dostarczono <span class='quantity'>{$quantity}</span> przyłbic do <span class='name'>{$name}</span>";
                    $data['success'] &= Activity::create(USER_ID, $date, $message)['success'];
                }
            }

            if ($success && $type == "magazine") {
//                if ($success = Hosmag::deliverBascinet(USER_ID)) {
//                    $date = new DateTime;
//                    $message = "<span></span>";
//                    $data['success'] &= Activity::create(USER_ID, $date, $message)['success'];
//                }
            }

            return $data;

        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            return [
                'success' => true,
                'alert'   => false,
                'message' => "",
            ];
        }
    }

    public static function ajax_deliverMaterial($get = []): array
    {
        $frozenID  = filter_var($get['frozenID'], FILTER_SANITIZE_STRING);
        $frozenArr = explode(",", $frozenID);
        try {
            $frozen = new Frozen($frozenArr);
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

        $date = new DateTime;
        $message = "Potwierdzono odbiór <span class='quantity'></span>";
        if (Activity::create(USER_ID, $date, $message)['success']) {
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

    public function content(array $args = [])
    {
        $data = [];
        return parent::content(array_merge($args, $data));
    }
}