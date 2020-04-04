<?php

namespace admin\controllers;

use classes\Functions as fs;

class LoginController extends AdminController
{
    public function content(array $args = [])
    {
    	$this->titile = "Zaloguj się";
        $data = [];
        if (DB_CONN && !empty($_POST)) {
            $data['alert']   = "danger";
            $data['invalid'] = [];

            foreach ($_POST as $name => $field) {
                $data['invalid'][$name] = empty($field);
            }

            if (!empty($_POST['submitLogin'])) {
                //Login
                if (!empty($_POST['email']) && !empty($_POST['password'])) {
                    if ($query = fs::$mysqli->query("SELECT * FROM `users` WHERE `email` = '" . $_POST['email'] . "';")) {
                        if (!empty($result = $query->fetch_assoc())) {
                            if (md5($_POST['password'] . $result['salt']) === $result['password']) {
                                $_SESSION['usersID']  = $result['id'];
                                $_SESSION['userName'] = $result['name'];
                                if (!empty($_POST['remember'])) {
                                    fs::setACookie('usersID', $result['id'], 3600 * 24 * 30);
                                }
                                header("Location: /admin/settings");
                                exit(0);
                            } else {
                                $data['message']             = "Niepoprawne hasło";
                                $data['invalid']['password'] = true;
                            }
                        } else {
                            $data['message']          = "Niepoprawny adres email";
                            $data['invalid']['email'] = true;
                        }
                    }
                } else {
                    $data['message'] = "Proszę uzupełnić wszystkie pola";
                }
            }
        }
        return $this->render(array_merge($args, $data));
    }
}