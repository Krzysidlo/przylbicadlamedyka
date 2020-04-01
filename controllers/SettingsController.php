<?php

namespace controllers;

use Exception;
use classes\User;
use classes\Functions as fs;

class SettingsController extends PageController
{
    public static function ajax_save($get = []): array
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
            $firstName = filter_var($get['firstName'], FILTER_SANITIZE_STRING);
            $lastName  = filter_var($get['lastName'], FILTER_SANITIZE_STRING);
            $tel       = filter_var($get['tel'], FILTER_SANITIZE_STRING);
            try {
                $user            = new User;
                $data            = RegisterController::ajax_address($get);
                $data['success'] &= $user->updateInfo($firstName, $lastName, $tel);
            } catch (Exception $e) {
                fs::log("Error: " . $e->getMessage());
                $data = [
                    'success' => false,
                    'alert'   => "danger",
                    'message' => "Wystąpił nieznany błąd. Proszę spróbować ponownie.",
                    'invalid' => $invalid,
                ];
            }
        }
        if ($data['success']) {
            $data['alert']   = "success";
            $data['message'] = "Poprawnie zapisano dane";
        }

        return $data;
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