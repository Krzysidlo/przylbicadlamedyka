<?php

namespace admin\controllers;

use classes\User;
use classes\Functions as fs;

class DefaultController extends AdminController
{
    public function content(array $args = [])
    {
        $this->title = "Uprawnienia";

        try {
            $users = User::getAll();
        } catch (Exception $e) {
            echo $e->getMessage();
            exit(0);
        }

        $data['users'] = $users;
        return parent::content(array_merge($args, $data));
    }
}