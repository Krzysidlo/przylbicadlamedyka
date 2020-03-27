<?php

namespace controllers;

use classes\User;
use classes\Functions as fs;
use classes\exceptions\UserNotFoundException;

class ConfirmController extends PageController
{
    public function content(array $args = [])
    {
        $hash = filter_var($this->get('hash'), FILTER_SANITIZE_STRING);
        try {
            $user = User::getByHash($hash);
        } catch (UserNotFoundException $e) {
            fs::log("Error: " . $e->getMessage());
            self::redirect("/");
            exit(0);
        }

        if ($user->setPrivilege(2)) {
            $user->setOption('confirm-email', NULL);
            $user->sendNotification("Your e-mail address has been successfully confirmed", NULL);
        }
        $_SESSION['usersID']  = $user->id;
        $_SESSION['userName'] = $user->name;
        self::redirect("/");
        exit(0);
    }
}