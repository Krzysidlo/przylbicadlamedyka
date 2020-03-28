<?php

namespace controllers;

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