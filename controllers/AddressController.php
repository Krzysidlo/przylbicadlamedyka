<?php

namespace controllers;

use classes\User;

class AddressController extends PageController
{
    public function content(array $args = [])
    {
        $user = new User;
        if (!$user->noAddress()) {
            self::redirect("/");
            exit(0);
        }
        return parent::content($args);
    }
}