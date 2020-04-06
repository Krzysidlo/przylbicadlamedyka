<?php

namespace controllers;

use Exception;
use classes\User;
use classes\Request;
use classes\Functions as fs;

class RegisterController extends PageController
{

    public static function ajax_login($get = []): array
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
                $data['message'] = "Proszę uzupełnić wszystkie pola";
                $data['alert']   = "warning";
            }
        }

        return $data;
    }

    public static function ajax_register($get = []): array
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

            if (!empty($get['email'])) {
                $email = filter_var($get['email'], FILTER_SANITIZE_EMAIL);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $csv = FILES_DIR . "/maile.csv";
                    if (file_exists($csv)) {
                        $file          = fopen($csv, "r");
                        $allowedEmails = [];
                        while ($line = fgets($file)) {
                            [$name, $mail] = str_getcsv($line, ";");
                            $allowedEmails[] = $mail;
                        }
                        if (!in_array($email, $allowedEmails)) {
                            return [
                                'success' => false,
                                'alert'   => "warning",
                                'message' => "Podany adres e-mail nie znajduje się na liście dzwolonych użytkowników. Proszę o kontakt z organizatorem w kwesti możliwości dołączenia.",
                            ];
                        }
                    }
                }
            }

            if (!empty(fs::getACookie('easyLogIn'))) {
                fs::setACookie('easyLogIn', NULL, -1);
            }
            if (!empty($get['firstname']) && !empty($get['lastname']) && !empty($get['email']) &&
                !empty($get['tel']) && !empty($get['password']) && !empty($get['r-password']) &&
                !empty($get['no-quarantine']) && !empty($get['regulations']) && !empty($get['rodo'])) {
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
                                    $data['alert']     = "danger";
                                    $data['message']   = "Wystąpił błąd podczas tworzenia użytkownika";
                                    $data['exception'] = $e->getMessage();
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

        return $data;
    }

    public static function ajax_forgot($get = []): array
    {
        $data = ['success' => true];

        if (empty($get['femail'])) {
            $data = [
                'success' => false,
                'alert'   => "warning",
                'message' => "Proszę podać adres e-mail",
            ];
        }

        if ($data['success']) {
            try {
                $user = new User($get['femail']);
                $hash = md5($user->lastName . time());
                $user->setOption('reset-password', $hash);

                $subject = PAGE_NAME . " - " . "Reset hasła";

                $text = "Aby zresetować hasło na stronie " . PAGE_NAME . " proszę kliknąć w poniższy link";
                $link = ROOT_URL . "/reset/" . $hash;
                // Message
                $message = <<< HTML
                <html lang="pl">
                <head>
                    <title>{$subject}</title>
                </head>
                <body>
                    <p>{$text}</p>
                    <a href="{$link}">{$link}</a>
                </body>
                </html>
HTML;

                $replyTo = EMAIL;
                // To send HTML mail, the Content-type header must be set
                $headers[] = 'MIME-Version: 1.0';
                $headers[] = 'Content-type: text/html; charset=UTF-8';
                // Additional headers
                $headers[] = "To: {$user->email}";
                $headers[] = "From: " . PAGE_NAME . "<no-reply@przylbicadlamedyka.pl>";
                $headers[] = "Reply-To: {$replyTo}";

                $data['success'] = mail($user->email, $subject, $message, implode("\r\n", $headers));

                if ($data['success']) {
                    $data = [
                        'success' => true,
                        'alert'   => "success",
                        'message' => "Na podany adres e-mail został wysłany link do zresetowania hasła",
                    ];
                } else {
                    $data = [
                        'success' => false,
                        'alert'   => "warning",
                        'message' => "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie.",
                    ];
                }
            } catch (Exception $e) {
                $data = [
                    'success' => false,
                    'alert'   => "warning",
                    'message' => "Podany adres e-mail nie został znaleziony. Proszę sprawdzić, czy podano poprawny adres.",
                ];
            }
        }

        return $data;
    }

    public static function ajax_resetPassword($get = []): array
    {
        $data = [
            'success' => true,
            'alert'   => false,
            'message' => "",
        ];

        if (empty($get['password']) || empty('r-password')) {
            $data = [
                'success' => false,
                'alert'   => "warning",
                'message' => "Proszę uzupełnić wszystkie pola",
            ];
        } else {
            if ($get['password'] !== $get['r-password']) {
                $data = [
                    'success' => false,
                    'alert'   => "warning",
                    'message' => "Powtórzone hasło nie jest takie samo",
                ];
            }
        }

        if ($data['success']) {
            $usersID = $get['user_id'] ?? NULL;

            try {
                $user = new User($usersID);

                $password = filter_var($get['password'], FILTER_SANITIZE_STRING);
                if (md5($password . $user->salt) === $user->password) {
                    $data = [
                        'success' => false,
                        'alert'   => "warning",
                        'message' => "Nowe hasło powinno być inne niż obecne",
                    ];
                } else {
                    if (!$user->updatePassword($password)) {
                        fs::log("Error: function User->updatePassword returned false for password=[{$password}]");
                        $data = [
                            'success' => false,
                            'alert'   => "warning",
                            'message' => "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie.",
                        ];
                    } else {
                        $data['success'] &= $user->setOption('reset-password', NULL);
                    }
                }
            } catch (Exception $e) {
                fs::log("Error: " . $e->getMessage());
                $data = [
                    'success' => false,
                    'alert'   => "warning",
                    'message' => "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie.",
                ];
            }
        }

        return $data;
    }

    public static function ajax_chgpswd($get = []): array
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
            $data['message'] = "Proszę podać obecne hasło";
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
            $data['message'] = "Proszę uzupełnić wszystkie pola";
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
            $message = ($success ? "Poprawnie zaktualizowałeś hasło" : "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie.");
            $alert   = ($success ? 'success' : 'warning');
            $data    = [
                'success' => $success,
                'message' => $message,
                'alert'   => $alert,
            ];

        }

        return $data;
    }

    public static function ajax_address($get = []): array
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

        $priv = $get['type'] ?? USER_PRV;
        $priv = intval($priv);

//        if ($priv === User::USER_PRODUCER && empty($get['pinName'])) {
//            $data = [
//                'success' => false,
//                'alert'   => "warning",
//                'message' => "Pole 'Nazwa' nie może być puste",
//                'invalid' => $invalid,
//            ];
//        } else {
        if (empty($get['city']) || empty($get['street']) || empty($get['building'])) {
            $data = [
                'success' => false,
                'alert'   => "warning",
                'message' => "Proszę uzupełnić wymagane pola",
                'invalid' => $invalid,
            ];
        }
//        }

        if ($data['success']) {
            if (empty($get['pinName'])) {
                $pinName = NULL;
            } else {
                $pinName = filter_var($get['pinName'], FILTER_SANITIZE_STRING);
            }
            $city     = filter_var($get['city'], FILTER_SANITIZE_STRING);
            $street   = filter_var($get['street'], FILTER_SANITIZE_STRING);
            $building = filter_var($get['building'], FILTER_SANITIZE_STRING);
            if (empty($get['flat'])) {
                $flat = "NULL";
            } else {
                $flat = filter_var($get['flat'], FILTER_SANITIZE_STRING);
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
                    'message' => "Wystąpił nieznany błąd. Proszę odświeżyć stronę i spróbować ponownie.",
                    'invalid' => $invalid,
                ];
            }

            if ($data['success']) {
                $user->setPrivilege($priv);
            }
        }

        return $data;
    }

    public static function ajax_sendConfirm($get = []): array
    {
        $data = [
            'success' => false,
            'alert'   => "danger",
            'message' => "Wystąpił błąd podczas wysyłania wiadomości. Proszę odświeżyć stornę i spróbować ponownie.",
        ];
        $user = $get['user'] ?? new User;

        $hash = md5($user->email . time());
        $user->setOption('confirm-email', $hash);

        $subject = PAGE_NAME . " - " . "Potwierdzenie rejestracji";

        $text[] = "Dziękujemy za zarejestrowanie się na stronie " . PAGE_NAME;
        $text[] = "Proszę kliknąc w poniższy link, aby potwierdzić adres e-mail";

        $link = ROOT_URL . "/confirm/" . $hash;

        $text = implode("<br>", $text);
        // Message
        $message = <<< HTML
        <html lang="pl">
        <head>
            <title>{$subject}</title>
        </head>
        <body>
            <p>{$text}</p>
            <a href="{$link}">{$link}</a>
        </body>
        </html>
HTML;

        $replyTo = EMAIL;
        // To send HTML mail, the Content-type header must be set
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=UTF-8';
        // Additional headers
        $headers[] = "To: {$user->email}";
        $headers[] = "From: " . PAGE_NAME . "<no-reply@przylbicadlamedyka.pl>";
        $headers[] = "Reply-To: {$replyTo}";

        $data['success'] = mail($user->email, $subject, $message, implode("\r\n", $headers));
        if ($data['success']) {
            $data['alert']   = "success";
            $data['message'] = "Wiadomość została poprawnie wysłana na adres e-mail";
        }

        return $data;
    }

    public function content(array $args = [])
    {
        if (LOGGED_IN) {
            self::redirect("/");
            exit(0);
        }

        $this->title = "Zarejestruj się";

        switch ($this->view) {
            case 'login':
                $this->title = "Zaloguj się";
                break;
            case 'forgot':
                $this->title = "Zapomniałem hasła";
                break;
            case 'reset':
                $this->title = "Reset hasła";
                break;
        }

        if ($this->view === "reset") {

            $hash = $this->get('hash');

            try {
                $user = User::getByHash($hash);
            } catch (Exception $e) {
                fs::log("Error: " . $e->getMessage());
                self::redirect("/");
                exit(0);
            }

            $args['user'] = $user;
        }

        try {
            $delivered = 2650 + Request::count(NULL, "delivered");
        } catch (Exception $e) {
            $delivered = 0;
        }
        $args['view']      = $this->view;
        $args['delivered'] = $delivered;

        $this->view = "register";

        return $this->render($args);
    }
}