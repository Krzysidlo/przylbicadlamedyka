<?php

namespace controllers;

use Exception;
use classes\User;
use classes\Functions as fs;

class ConfirmController extends PageController
{
    public function content(array $args = [])
    {
        $hash = filter_var($this->get('hash'), FILTER_SANITIZE_STRING);
        try {
            $user = User::getByHash($hash);
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            self::redirect("/");
            exit(0);
        }

        $user->setOption('confirm-email', NULL);
        $user->sendNotification("Your e-mail address has been successfully confirmed", NULL);

        $_SESSION['usersID']  = $user->id;
        $_SESSION['userName'] = $user->name;
        self::redirect("/");
        exit(0);
    }
}