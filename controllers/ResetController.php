<?php

namespace controllers;

use classes\Functions as fs;

class ResetController extends PageController
{

    public function content(array $args = [])
    {
        $hash = $this->get('hash');

        if (empty($hash)) {
            header("Location: /");
            die();
        }

        $user = fs::findUserByHash($hash);
        if (empty($user)) {
            header("Location: /");
            die();
        }

        $args['user'] = $user;
        if (!empty($this->get('resetPassword'))) {
            $break           = false;
            $args['message'] = fs::t("You have successfuly changed your password") . ". <a href='/'>" . fs::t("Click here to go back to log in") . "</a>.";
            $args['alert']   = "success";
            if (!$break && empty($this->get('newPassword'))) {
                $args['message']                = fs::t("Password field cannot be empty");
                $args['invalid']['newPassword'] = true;
                $break                          = true;
            }
            if (!$break && empty($this->get('r-newPassword'))) {
                $args['message']                  = fs::t("Repeat password field cannot be empty");
                $args['invalid']['r-newPassword'] = true;
                $break                            = true;
            }
            if (!$break && $this->get('newPassword') !== $this->get('r-newPassword')) {
                $args['message']                = fs::t("Repeated password is not the same");
                $args['invalid']['newPassword'] = $args['invalid']['r-newPassword'] = true;
                $break                          = true;
            }
            if (!$break && strlen($this->get('newPassword']) < 8) {
                $args['message']                = fs::t("Minimum password length is 8");
                $args['invalid']['newPassword'] = $args['invalid']['r-newPassword'] = true;
                $break                          = true;
            }

            if (!$break) {
                $salt     = (string)rand(1111111111, 9999999999);
                $password = md5($this->get('newPassword') . $salt);
                if (fs::updatePassword($user['id'], $password, $salt)) {
                    unset($this->get('newPassword'), $this->get('r-newPassword'));
                    fs::setOption('reset-password', NULL, $user['id']);
                } else {
                    $args['message'] = fs::t("There was an unexpected error");
                    $break           = true;
                }
            }

            if ($break) {
                $args['alert'] = "danger";
            }
        }

        return parent::content($args);
    }
}