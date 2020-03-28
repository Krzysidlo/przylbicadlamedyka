<?php

namespace controllers;

use Exception;
use classes\Request;
use classes\Functions as fs;

class MapController extends PageController
{
    public static function ajax_savePoint($get)
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

        if (!empty($get['ajax'])) {
            echo json_encode($data);
        } else {
            self::redirect("/");
        }

        exit(0);
    }

    public static function ajax_getInfo($get)
    {
        $data = [
            'success' => true,
        ];
        try {
            $requests = Request::getAll();
            foreach ($requests as $request) {
                $data['requests'][$request->id] = [
                    'name'      => $request->user->name,
                    'tel'       => $request->user->tel,
                    'latLng'    => $request->latLng,
                    'bascinet'  => $request->bascinet,
                    'material'  => $request->material,
                    'comments'  => $request->comments,
                    'frozen'    => $request->frozen,
                    'delivered' => $request->delivered,
                ];
            }
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            $data = [
                'success' => false,
                'message' => "Wystąpił błąd podczas pobierania danych z bazy",
            ];
        }

        if (!empty($get['ajax'])) {
            echo json_encode($data);
        } else {
            self::redirect("/");
        }

        exit(0);
    }

    public function content(array $args = [])
    {
        $data = [];
        return parent::content(array_merge($args, $data));
    }
}