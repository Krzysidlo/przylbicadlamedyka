<?php

namespace admin\controllers;

use Exception;
use classes\Pin;
use classes\User;
use classes\Functions as fs;
use controllers\PageController;

class IndexController extends PageController
{
    public static function ajax_savePin($get = []): array
    {
        $id = filter_var($get['id'], FILTER_SANITIZE_NUMBER_INT);
        try {
            $pin = new Pin($id);
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            return [
                'success' => false,
                'alert'   => "danger",
                'message' => "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie."
            ];
        }

        $name = filter_var($get['name'], FILTER_SANITIZE_STRING);
        $description = filter_var($get['description'], FILTER_SANITIZE_STRING);
        $material = filter_var($get['material'], FILTER_SANITIZE_STRING);
        $bascinet = filter_var($get['bascinet'], FILTER_SANITIZE_STRING);

        if (is_numeric($material)) {
            $material = intval($material);
        } else {
            $material = NULL;
        }

        if (is_numeric($bascinet)) {
            $bascinet = intval($bascinet);
        } else {
            $bascinet = NULL;
        }
        if ($pin->update($name, $description, $material, $bascinet)) {
            return [
                'success' => true,
                'alert'   => "success",
                'message' => "Poprawnie zapisano dane",
            ];
        }

        return [
            'success' => false,
            'alert'   => "danger",
            'message' => "Błąd przy zapisie do bazy danych",
        ];
    }

    private static function compare(Pin $ob1, Pin $ob2)
    {
        return ($ob1->type < $ob2->type);
    }

    public function content(array $args = [])
    {
        if (USER_PRV < User::USER_ADMIN) {
            self::redirect("/error");
        }

        $pins = [];
        try {
            $pins = Pin::getAll();
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            self::redirect("/error");
        }

        $data = [
            'magazines' => [],
            'hospitals' => [],
        ];

        foreach ($pins as $pin) {
            if ($pin->type === "magazine") {
                $data['magazines'][] = $pin;
                continue;
            }
            if ($pin->type === "hospital") {
                $data['hospitals'][] = $pin;
                continue;
            }
        }

        return parent::content(array_merge($args, $data));
    }
}
