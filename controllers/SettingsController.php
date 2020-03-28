<?php

namespace controllers;

use classes\User;

class SettingsController extends PageController
{
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