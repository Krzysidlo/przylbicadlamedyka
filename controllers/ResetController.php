<?php

namespace controllers;

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
        }

        $data['user'] = $user;
        if (!empty($this->get('resetPassword'))) {
            $break           = false;
            $data['message'] = "You have successfuly changed your password. <a href='/'>Click here to go back to log in</a>.";
            $data['alert']   = "success";
            if (!$break && empty($this->get('newPassword'))) {
                $data['message']                = "Password field cannot be empty";
                $data['invalid']['newPassword'] = true;
                $break                          = true;
            }
            if (!$break && empty($this->get('r-newPassword'))) {
                $data['message']                  = "Repeat password field cannot be empty";
                $data['invalid']['r-newPassword'] = true;
                $break                            = true;
            }
            if (!$break && $this->get('newPassword') !== $this->get('r-newPassword')) {
                $data['message']                = "Repeated password is not the same";
                $data['invalid']['newPassword'] = $data['invalid']['r-newPassword'] = true;
                $break                          = true;
            }
            if (!$break && strlen($this->get('newPassword')) < 8) {
                $data['message']                = "Minimum password length is 8";
                $data['invalid']['newPassword'] = $data['invalid']['r-newPassword'] = true;
                $break                          = true;
            }

            if (!$break) {
                if ($user->updatePassword($this->get('newPassword'))) {
                    $user->setOption('reset-password', NULL);
                } else {
                    $data['message'] = "There was an unexpected error";
                    $break           = true;
                }
            }

            if ($break) {
                $data['alert'] = "danger";
            }
        }

        return parent::content(array_merge($args, $data));
    }
}