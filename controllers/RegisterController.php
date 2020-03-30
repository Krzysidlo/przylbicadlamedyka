<?php

namespace controllers;

use Exception;
use classes\User;
use classes\Functions as fs;

class RegisterController extends PageController
{

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
                            $data['message']              = "Niepoprawne hasło";
                            $data['alert']                = "warning";
                            $data['invalid']['lpassword'] = true;
                        }
                    } catch (Exception $e) {
                        $data['message']           = "Podany adres e-mail nie został znaleziony";
                        $data['alert']             = "warning";
                        $data['invalid']['lemail'] = true;
                    }
                } else {
                    $data['message']           = "Niepoprawny adres e-mail";
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
            if (!empty($get['firstname']) && !empty($get['lastname']) && !empty($get['email']) && !empty($get['tel']) && !empty($get['password']) && !empty($get['r-password'])) {
                $email = filter_var($get['email'], FILTER_SANITIZE_EMAIL);
                try {
                    new User($email);
                    $data['message']          = "Konto o podanym adresie e-mail już istnieje";
                    $data['alert']            = "warning";
                    $data['invalid']['email'] = true;
                } catch (Exception $e) {
                    if ($get['password'] === $get['r-password']) {
                        if (strlen($get['password']) >= 8) {
                            $password  = filter_var($get['password'], FILTER_SANITIZE_STRING);
                            $firstName = filter_var($get['firstname'], FILTER_SANITIZE_STRING);
                            $lastName  = filter_var($get['lastname'], FILTER_SANITIZE_STRING);
                            $tel       = filter_var($get['tel'], FILTER_SANITIZE_STRING);
                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                try {
                                    $user                 = User::newUser($email, $firstName, $lastName, $tel, $password);
                                    $_SESSION['usersID']  = $user->id;
                                    $_SESSION['userName'] = $user->name;
                                    $data['success']      = true;
                                } catch (Exception $e) {
                                    fs::log("Error: " . $e->getMessage());
                                    $data['alert']   = "danger";
                                    $data['message'] = "Wystąpił błąd podczas tworzenia użytkownika";
                                }
                            } else {
                                $data['message']          = "Wygląda na to, że adres e-mail jest niepoprawny";
                                $data['alert']            = "warning";
                                $data['invalid']['email'] = true;
                            }
                        } else {
                            $data['message']             = "Hasło musi się składać z minimum 8 znaków";
                            $data['alert']               = "warning";
                            $data['invalid']['password'] = $data['invalid']['r-password'] = true;
                        }
                    } else {
                        $data['message']             = "Powótrzone hasło nie jest takie samo";
                        $data['alert']               = "warning";
                        $data['invalid']['password'] = $data['invalid']['r-password'] = true;
                    }
                }
            } else {
                $data['message'] = "Proszę wypełnić wszystkie pola w formularzu";
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
            $success = $user->updatePassword($get['password']);
            $message = ($success ? ("You have successfully updated your password") : ("There was an error. Please refresh the page and try again."));
            $alert   = ($success ? 'success' : 'warning');
            $data    = [
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

    public static function ajax_address($get): void
    {
        $invalid = [];
        foreach ($get as $name => $value) {
            $invalid[$name] = empty($value);
        }
        if (isset($invalid['flat'])) {
            unset($invalid['flat']);
        }

        $data = [
            'success' => true,
            'alert'   => "",
            'message' => "",
            'invalid' => $invalid,
        ];

        $priv = intval($get['type']);

        if ($priv === User::USER_PRODUCER && empty($get['pinName'])) {
            $data = [
                'success' => false,
                'alert'   => "warning",
                'message' => "Pole 'Nazwa' nie może być puste",
                'invalid' => $invalid,
            ];
        } else {
            if (empty($get['city']) || empty($get['street']) || empty($get['building'])) {
                $data = [
                    'success' => false,
                    'alert'   => "warning",
                    'message' => "Proszę uzupełnić wymagane pola",
                    'invalid' => $invalid,
                ];
            }
        }

        if ($data['success']) {
            if (empty($get['pinName'])) {
                $pinName = NULL;
            } else {
                $pinName = filter_var($get['pinName'], FILTER_SANITIZE_STRING);
            }
            $city     = filter_var($get['city'], FILTER_SANITIZE_STRING);
            $street   = filter_var($get['street'], FILTER_SANITIZE_STRING);
            $building = filter_var($get['building'], FILTER_SANITIZE_NUMBER_INT);
            if (empty($get['flat'])) {
                $flat = "NULL";
            } else {
                $flat = filter_var($get['flat'], FILTER_SANITIZE_NUMBER_INT);
            }
            $location = filter_var($get['location'], FILTER_SANITIZE_STRING);

            try {
                $user            = new User;
                $data['success'] = $user->updateAddress($city, $street, $building, $flat, $location, $pinName);
            } catch (Exception $e) {
                fs::log("Error: " . $e->getMessage());
                $data = [
                    'success' => false,
                    'alert'   => "danger",
                    'message' => "Wystąpił nieznany błąd. Proszę spróbować ponownie.",
                    'invalid' => $invalid,
                ];
            }

            if ($data['success']) {
                $user->setPrivilege($priv);
            }
        }

        if (!empty($get['ajax'])) {
            echo json_encode($data);
        } else {
            self::redirect("/");
        }

        exit(0);
    }

    public function content(array $args = [])
    {
        if (LOGGED_IN) {
            self::redirect("/");
            exit(0);
        }

        $this->title = "Zarejestruj się";

        return $this->render($args);
    }
}