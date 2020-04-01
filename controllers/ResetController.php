<?php

namespace controllers;

use Exception;
use classes\User;
use classes\Functions as fs;
use classes\exceptions\UserNotFoundException;

class ResetController extends PageController
{

    public function content(array $args = [])
    {
        $hash = $this->get('hash');

        try {
            $user = User::getByHash($hash);
        } catch (UserNotFoundException $e) {
            fs::log("Error: " . $e->getMessage());
            self::redirect("/");
            exit(0);
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            self::redirect("/");
            exit(0);
        }

        $data['user'] = $user;

        return parent::content(array_merge($args, $data));
    }
}