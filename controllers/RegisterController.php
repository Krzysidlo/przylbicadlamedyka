<?php

namespace controllers;

use Exception;
use classes\User;
use classes\Functions as fs;

class RegisterController extends PageController
{

    public function content(array $args = [])
    {
        if (LOGGED_IN) {
            self::redirect("/");
            exit(0);
        }

        return $this->render($args);
    }

    public static function ajax_login($get): void
    {
        $data = [];
        if (DB_CONN && !empty($get)) {
            $data = [
                'success' => false,
                'alert'   => false,
                'invalid' => [],
            ];

            foreach ($get as $name => $field) {
                $data['invalid'][$name] = empty($field);
            }

            if (!empty($get['lemail']) && !empty($get['lpassword'])) {
                $email = filter_var($get['lemail'], FILTER_SANITIZE_EMAIL);
                if ($email) {
                    try {
                        $user = new User($email);
                        if (md5($get['lpassword'] . $user->salt) === $user->password) {
                            $_SESSION['usersID']  = $user->id;
                            $_SESSION['userName'] = $user->name;
                            if (!empty($get['lremember'])) {
                                fs::setACookie('usersID', $user->id, 3600 * 24 * 30);
                            }
                            if (!empty($get['leasy'])) {
                                fs::setACookie('easyLogIn', $user->email, 3600 * 24 * 365);
                            } else {
                                fs::setACookie('easyLogIn', NULL, -1);
                            }
                            $data['success'] = true;
                        } else {
                            $data['message']              = "Incorrect password";
                            $data['alert']                = "warning";
                            $data['invalid']['lpassword'] = true;
                        }
                    } catch (Exception $e) {
                        $data['message']           = "Incorrect e-mail address";
                        $data['alert']             = "warning";
                        $data['invalid']['lemail'] = true;
                    }
                } else {
                    $data['message']           = "Incorrect e-mail address";
                    $data['alert']             = "warning";
                    $data['invalid']['lemail'] = true;
                }
            } else {
                $data['message'] = "Please fill in all necessary fields";
                $data['alert']   = "warning";
            }
        }

        if (!empty($get['ajax'])) {
            echo json_encode($data);
        } else {
            self::redirect("/");
        }

        exit(0);
    }

    public static function ajax_register($get): void
    {
        $data = [];
        if (DB_CONN && !empty($get)) {
            $data = [
                'success' => false,
                'alert'   => false,
                'invalid' => [],
            ];

            foreach ($get as $name => $field) {
                $data['invalid'][$name] = empty($field);
            }

            if (!empty(fs::getACookie('easyLogIn'))) {
                fs::setACookie('easyLogIn', NULL, -1);
            }
            if (!empty($get['name']) && !empty($get['password']) && !empty($get['r-password']) && !empty($get['email'])) {
                if (fs::$mysqli->query("SELECT 1 FROM `users` WHERE `email` = '{$get['email']}';")->num_rows) {
                    $data['message']          = "This e-mail address already exists" . ". " . "Please use different" . ".";
                    $data['alert']            = "warning";
                    $data['invalid']['email'] = true;
                } else {
                    if ($get['password'] === $get['r-password']) {
                        if (strlen($get['password']) >= 8) {
                            $password = filter_var($get['password'], FILTER_SANITIZE_STRING);
                            $email    = filter_var($get['email'], FILTER_SANITIZE_EMAIL);
                            $name     = filter_var($get['name'], FILTER_SANITIZE_STRING);
                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                try {
                                    $user                 = User::newUser($email, $name, $password);
                                    $_SESSION['usersID']  = $user->id;
                                    $_SESSION['userName'] = $user->name;
                                    $data['success']      = true;
                                } catch (Exception $e) {
                                    fs::log("Error: " . $e->getMessage());
                                    $data['alert']   = "danger";
                                    $data['message'] = "There was an error while creating new user";
                                }
                            } else {
                                $data['message']          = "E-mail address seems to be incorrect";
                                $data['alert']            = "warning";
                                $data['invalid']['email'] = true;
                            }
                        } else {
                            $data['message']             = "Minimum password length is 8";
                            $data['alert']               = "warning";
                            $data['invalid']['password'] = $data['invalid']['r-password'] = true;
                        }
                    } else {
                        $data['message']             = "Confirmed password is not the same";
                        $data['alert']               = "warning";
                        $data['invalid']['password'] = $data['invalid']['r-password'] = true;
                    }
                }
            } else {
                $data['message'] = "Please fill in all necessary fields";
                $data['alert']   = "warning";
            }
        }

        if (!empty($get['ajax'])) {
            echo json_encode($data);
        } else {
            self::redirect("/");
        }

        exit(0);
    }

    public static function ajax_chgpswd($get): void
    {
        $data = [];

        $break = false;

        try {
            $user = new User;
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            $break = true;
        }

        if (!$break && empty($get['changePassword'])) {
            $break = true;
        }

        if (!$break && empty($get['cpassword'])) {
            $data['message'] = "Please provide current password";
            $data['alert']   = "warning";
            $data['field']   = "cpassword";
            $break           = true;
        }

        if (!$break && md5($get['cpassword'] . $user->salt) !== $user->password) {
            $data['message'] = "Incorrect password";
            $data['alert']   = "warning";
            $data['field']   = "cpassword";
            $break           = true;
        }

        if (!$break && (empty($get['password']) || empty($get['rpassword']))) {
            $data['message'] = "Please fill in all fields";
            $data['alert']   = "warning";
            $field           = "";
            if (empty($_POST['password'])) {
                $field = "password";
                if (empty($_POST['rpassword'])) {
                    $field = "rpassword,password";
                }
            } else {
                if (empty($_POST['rpassword'])) {
                    $field = "rpassword";
                }
            }
            $data['field'] = $field;
            $break         = true;
        }

        if (!$break && $get['password'] !== $get['rpassword']) {
            $data['message'] = "Confirmed password is not the same";
            $data['alert']   = "warning";
            $data['field']   = "password,rpassword";
            $break           = true;
        }

        if (!$break) {
            $success  = $user->updatePassword($get['password']);
            $message  = ($success ? ("You have successfully updated your password") : ("There was an error. Please refresh the page and try again."));
            $alert    = ($success ? 'success' : 'warning');
            $data     = [
                'success' => $success,
                'message' => $message,
                'alert'   => $alert,
            ];

        }

        if (!empty($get['ajax'])) {
            echo json_encode($data);
        } else {
            self::redirect("/");
        }

        exit(0);
    }
}