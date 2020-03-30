<?php

namespace controllers;

use Exception;
use classes\User;
use classes\Functions as fs;

class SettingsController extends PageController
{
    public static function ajax_save($get)
    {
        $invalid = [];
        foreach ($get as $name => $value) {
            $invalid[$name] = empty($value);
        }
        if (isset($invalid['flat'])) {
            unset($invalid['flat']);
        }

        $data = [
            'success' => true,
            'alert'   => "",
            'message' => "",
            'invalid' => $invalid,
        ];

        if (empty($get['city']) || empty($get['street']) || empty($get['building'])) {
            $data = [
                'success' => false,
                'alert'   => "warning",
                'message' => "Proszę uzupełnić wymagane pola",
                'invalid' => $invalid,
            ];
        }

        if ($data['success']) {
            if (empty($get['pinName'])) {
                $pinName = NULL;
            } else {
                $pinName = filter_var($get['pinName'], FILTER_SANITIZE_STRING);
            }
            $city     = filter_var($get['city'], FILTER_SANITIZE_STRING);
            $street   = filter_var($get['street'], FILTER_SANITIZE_STRING);
            $building = filter_var($get['building'], FILTER_SANITIZE_NUMBER_INT);
            if (empty($get['flat'])) {
                $flat = "NULL";
            } else {
                $flat = filter_var($get['flat'], FILTER_SANITIZE_NUMBER_INT);
            }
            $location = filter_var($get['location'], FILTER_SANITIZE_STRING);

            try {
                $user            = new User;
                $data['success'] = $user->updateInfo();
                $data['success'] &= $user->updateAddress($city, $street, $building, $flat, $location, $pinName);
            } catch (Exception $e) {
                fs::log("Error: " . $e->getMessage());
                $data = [
                    'success' => false,
                    'alert'   => "danger",
                    'message' => "Wystąpił nieznany błąd. Proszę spróbować ponownie.",
                    'invalid' => $invalid,
                ];
            }

            if ($data['success']) {
                $user->setPrivilege($priv);
            }
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
        $this->title = "Ustawienia";

        $user = new User;

        $data = [
            'user' => $user,
        ];

        return parent::content(array_merge($args, $data));
    }
}