<?php

namespace controllers;

use Exception;
use classes\User;
use classes\Request;
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

    public function content(array $args = [])
    {
        $data = [];
        return parent::content(array_merge($args, $data));
    }
}