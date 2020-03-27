<?php

namespace controllers;

use Exception;
use classes\User;
use classes\Group;
use classes\Functions as fs;

class SettingsController extends PageController
{
    public function content(array $args = [])
    {
        if (USER_PRV < 2) {
            self::redirect("/error");
        }

        $user = new User;
        $avatarUrl = $user->getOption('avatar');
        if (!filter_var($avatarUrl, FILTER_VALIDATE_URL)) {
            $avatarUrl = USR_URL . "/" . $avatarUrl . "?" . rand(1000, 9999);
        }

        try {
            $groups = Group::getAll();
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            self::redirect("/error");
            exit(0);
        }

        $data = [
            'avatar'     => "<img id='profile' src='{$avatarUrl}' class='img-responsive img-circle img-settings' alt='avatar'>",
            'user'       => $user,
            'groups'     => $groups,
        ];

        return parent::content(array_merge($args, $data));
    }
}