<?php

namespace controllers;

use DateTime;
use Exception;
use classes\Frozen;
use classes\Request;
use classes\Hospital;
use classes\Functions as fs;

class MapController extends PageController
{
    public static function ajax_savePoint($get = []): array
    {
        $data = [
            'success' => true,
            'message' => "Poprawnie zapisano punkt",
        ];

        if (empty($get['lat']) || empty($get['lng']) || empty($get['user_id'])) {
            $data = [
                'success' => false,
                'message' => 'Brakuje jednej z wartości',
            ];
        }

        if ($data['success']) {

        }

        return $data;
    }

    public static function ajax_getInfo($get = []): array
    {
        $data = [
            'success' => true,
        ];
        try {
            $requests  = Request::getAll();
            foreach ($requests as $request) {
                $address = $request->user->getAddress();

                $frozen = false;
                $sql = "SELECT 1 FROM `frozen` WHERE `requests_id` = '{$request->id}' AND `deleted` = 0;";
                if ($query = fs::$mysqli->query($sql)) {
                    $frozen = $query->fetch_row()[0] ?? false;
                }
                $data['requests'][$request->user->id][$request->id] = [
                    'user_id'  => $request->user->id,
                    'name'     => $request->user->name,
                    'tel'      => $request->user->tel,
                    'address'  => "{$address->city}, {$address->street} {$address->building}/{$address->flat}",
                    'latLng'   => $request->latLng,
                    'bascinet' => intval($request->bascinet),
                    'material' => intval($request->material),
                    'comments' => ($request->comments !== NULL ? (string)$request->comments . ", " : ""),
                    'frozen'   => (bool)$frozen,
                ];
            }

            foreach ($data['requests'] as &$dataRequest) {
                foreach ($dataRequest as &$requests)
                if ($requests['comments'] !== "") {
                    $requests['comments'] = substr($requests['comments'], 0, -2);
                }
            }

            $hospitals = Hospital::getAll();
            foreach ($hospitals as $hospital) {
                $data['hospitals'][$hospital->id] = [
                    'id'     => $hospital->id,
                    'name'   => $hospital->name,
                    'latLng' => $hospital->latLng,
                ];
            }
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            $data = [
                'success' => false,
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
            $data = [
                'success' => false,
                'alert'   => "danger",
                'message' => "Wystąpił błąd przy zapisie do bazy danych proszę spróbować ponownie",
            ];
        }

        return $data;
    }

    public static function ajax_freezeRequest($get = []): array
    {
        $data = [
            'success' => true,
            'alert'   => false,
            'message' => "",
        ];

        $usersID = filter_var($get['userId'], FILTER_SANITIZE_STRING);
        $action  = filter_var($get['action'], FILTER_SANITIZE_STRING);
        $date    = filter_var($get['date'], FILTER_SANITIZE_STRING);
        $time    = filter_var($get['time'], FILTER_SANITIZE_STRING);

        try {
            $date    = new DateTime($date . " " . $time);
        } catch (Exception $e) {
            fs::log($e->getMessage());
            $data = [
                'success' => false,
                'alert'   => "danger",
                'message' => "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie.",
            ];
        }

        if ($data['success']) {
            $requestsArr = Request::getIdsByUserID($usersID);
            $data = Frozen::create(USER_ID, $requestsArr, $date, $action, $usersID);
        }

        return $data;
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