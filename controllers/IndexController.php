<?php

namespace controllers;

use classes\User;

class IndexController extends PageController
{
    public function content(array $args = [])
    {
        switch (USER_PRV) {
            case User::USER_DRIVER:
                break;
            case User::USER_PRODUCENT:
                break;
            case User::USER_ADMIN:
                break;
            default:
                self::redirect("/error");
        }
        return parent::content(array_merge($args, $data));
    }
}
