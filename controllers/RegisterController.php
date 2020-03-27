<?php

namespace controllers;

use Exception;
use classes\User;
use Google_Client;
use Facebook\Facebook;
use classes\Functions as fs;

class RegisterController extends PageController
{

    public function content(array $args = [])
    {
        if (LOGGED_IN) {
            header("Location: /");
        }

        try {
            //Google login
            $gClient = new Google_Client();
            $gClient->setClientId("758734978512-7787flc7237g5v8grplaqvprv887cd2v.apps.googleusercontent.com");
            $gClient->setClientSecret("uOQW_RHZSa7k1W6mIGGOBJWD");
            $gClient->setApplicationName(PAGE_NAME);
            $gClient->setRedirectUri(ROOT_URL . "/google");
            $gClient->addScope("https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email");
            $googleLoginURL = $gClient->createAuthUrl();
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            $googleLoginURL = false;
        }

        try {
            //Facebook login
            $fClient          = new Facebook(['app_id' => '211253152858745', 'app_secret' => '23c08914919c6a67c30190d9bc7a7633', 'default_graph_version' => self::FB_GRAPH_VERSION,]);
            $helper           = $fClient->getRedirectLoginHelper();
            $permissions      = ['email'];
            $facebookLoginURL = $helper->getLoginUrl(ROOT_URL . "/facebook", $permissions);
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            $facebookLoginURL = false;
        }

        $data = [
            'googleLoginURL'   => $googleLoginURL,
            'facebookLoginURL' => $facebookLoginURL,
            'easyLogIn'        => fs::getACookie('easyLogIn') ?? false,
        ];

        return $this->render(array_merge($args, $data));
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
                        if ($user->pswdExists) {
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
                                $data['message']              = fs::t("Incorrect password");
                                $data['alert']                = "warning";
                                $data['invalid']['lpassword'] = true;
                            }
                        } else {
                            $type            = $user->getAdditionalInfo()['type'];
                            $data['message'] = fs::t("Your account was created using") . " " . ucfirst($type) . ". " . fs::t("Please use the same method to log in") . ".";
                            $data['alert']   = "warning";
                        }
                    } catch (Exception $e) {
                        $data['message']           = fs::t("Incorrect e-mail address");
                        $data['alert']             = "warning";
                        $data['invalid']['lemail'] = true;
                    }
                } else {
                    $data['message']           = fs::t("Incorrect e-mail address");
                    $data['alert']             = "warning";
                    $data['invalid']['lemail'] = true;
                }
            } else {
                $data['message'] = fs::t("Please fill in all necessary fields");
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
                    $data['message']          = fs::t("This e-mail address already exists") . ". " . fs::t("Please use different") . ".";
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
                                    $data['message'] = fs::t("There was an error while creating new user");
                                }
                            } else {
                                $data['message']          = fs::t("E-mail address seems to be incorrect");
                                $data['alert']            = "warning";
                                $data['invalid']['email'] = true;
                            }
                        } else {
                            $data['message']             = fs::t("Minimum password length is 8");
                            $data['alert']               = "warning";
                            $data['invalid']['password'] = $data['invalid']['r-password'] = true;
                        }
                    } else {
                        $data['message']             = fs::t("Confirmed password is not the same");
                        $data['alert']               = "warning";
                        $data['invalid']['password'] = $data['invalid']['r-password'] = true;
                    }
                }
            } else {
                $data['message'] = fs::t("Please fill in all necessary fields");
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

        if (!$break && $user->pswdExists && empty($get['cpassword'])) {
            $data['message'] = fs::t("Please provide current password");
            $data['alert']   = "warning";
            $data['field']   = "cpassword";
            $break           = true;
        }

        if (!$break && $user->pswdExists && md5($get['cpassword'] . $user->salt) !== $user->password) {
            $data['message'] = fs::t("Incorrect password");
            $data['alert']   = "warning";
            $data['field']   = "cpassword";
            $break           = true;
        }

        if (!$break && (empty($get['password']) || empty($get['rpassword']))) {
            $data['message'] = fs::t("Please fill in all fields");
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
            $data['message'] = fs::t("Confirmed password is not the same");
            $data['alert']   = "warning";
            $data['field']   = "password,rpassword";
            $break           = true;
        }

        if (!$break) {
            $success  = $user->updatePassword($get['password']);
            $message  = ($success ? (fs::t("You have successfully") . " " . ($user->pswdExists ? fs::t("updated") : fs::t("created")) . " " . fs::t("your password")) : (fs::t("There was an error") . ". " . fs::t("Please refresh the page and try again") . "."));
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