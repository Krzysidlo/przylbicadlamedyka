<?php

namespace controllers;

use classes\User;

class IndexController extends PageController
{
    public function content(array $args = [])
    {
        $data = [];
        $user = new User;
        switch (USER_PRV) {
            case User::USER_NO_ACCESS:
                self::redirect("/error");
                break;
            case User::USER_NO_CONFIRM:
            case User::USER_DRIVER:
            case User::USER_PRODUCER:
                if ($user->noAddress()) {
                    $this->view = "address";
                }
                break;
        }
        return parent::content(array_merge($args, $data));
    }
}
