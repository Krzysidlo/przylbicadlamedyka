<?php

namespace controllers;

use classes\User;

class SettingsController extends PageController
{
    public static function ajax_save($get)
    {
        $data = [
            'success' => false,
            'alert'   => "danger",
            'message' => 'Wystąpił błąd przy zapisie ustawień',
        ];

        $user = new User;

        $password = filter_var($get['password'], FILTER_SANITIZE_STRING);

        if ($user->password === md5($password . $user->salt)) {
            $nPassword = $rnPassword = NULL;
            if (!empty($get['npassword'])) {
                if (empty($get['rnpassword']) || $get['npassword'] !== $get['rnpassword']) {
                    $data = [
                        'success' => false,
                        'alert'   => "warning",
                        'message' => 'Powtórzone hasło nie jest takie samo',
                    ];
                } else {
                    $nPassword = filter_var($get['npassword'], FILTER_SANITIZE_STRING);
                }
            }
            $user->updateInfo($get['firstname'], $get['lastname'], $get['address'], $get['tel'], $nPassword);

        } else {
            $data = [
                'success' => false,
                'alert'   => "warning",
                'message' => 'Niepoprawne hasło',
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
        if (USER_PRV <= User::USER_NO_CONFIRM) {
            self::redirect("/error");
        }

        $user = new User;

        $data = [
            'user' => $user,
        ];

        return parent::content(array_merge($args, $data));
    }
}