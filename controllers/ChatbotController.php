<?php

namespace controllers;

use DateTime;
use Exception;
use DateTimeZone;
use Facebook\Facebook;
use classes\Functions as fs;

fs::setLogFile('chatbot.log');
//Gdy już wszystko będzie śmigało, wystarczy wywołać tę funkcję zamiast usuwać wszystkie funkcje log z kodu oraz usunąć, lub zakomentować ini_set
//fs::setLogging(false);

class ChatbotController extends PageController
{
    private static $accessToken = 'EAADAIjUiQnkBAK6ZCfpIPGbXnyVySgdf2zeTajYD5kOYirhosXZAt8EjDFbA4oLZBHuPLJDlIoZAVWBDEEuBlZAkbMa2Phm1HPwSpPELW3JCZBVoQUZADGKZCnveIXPpjrykfRMmyL3vHCpobM2epIjMZAVYdMhksKQZCTpzcIdRHXEjGmcA31aoP4';
    private static $messengerID;
    private static $usersID;
    private static $name;
    private static $gender;
    private static $timezone;

    public function content(array $args = [])
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] != "facebookexternalua") {
            header("Location: /");
            exit(0);
        }
        ob_start();

        $challenge = $_REQUEST['hub_challenge'] ?? "";
        $token     = $_REQUEST['hub_verify_token'] ?? "";

        //Dla potwierdzenia dla facebooka
        if ($token === "sA0dwzp9jTTAdRBBSrTE") {
            echo $challenge;
            exit(0);
        }

        $input = file_get_contents('php://input');
        $json  = json_decode($input, true);

//		fs::log($json);

        if (isset($json['entry'][0]['messaging'][0]['message']) || isset($json['entry'][0]['messaging'][0]['postback']['payload'])) {
            $messengerID       = $json['entry'][0]['messaging'][0]['sender']['id'] ?? "";
            $message           = $json['entry'][0]['messaging'][0]['message']['text'] ?? "";
            self::$messengerID = $messengerID;
            fs::log($messengerID);

            try {
                $fClient = new Facebook(['app_id' => self::FB_APP_ID, 'app_secret' => self::FB_APP_SECRET, 'default_graph_version' => self::FB_GRAPH_VERSION,]);
                $fClient->setDefaultAccessToken(self::$accessToken);

                $graphResponse = $fClient->get("/{$messengerID}?fields=name,gender,timezone");
                $userData      = $graphResponse->getGraphUser();
            } catch (Exception $e) {
                fs::log($e->getMessage());
                exit(0);
            }
            self::$name     = $userData['name'];
            self::$gender   = substr($userData['gender'], 0, 1);
            self::$timezone = timezone_name_from_abbr("", $userData['timezone'] * 60 * 60, 0);

            $userEmail = fs::$mysqli->query("SELECT u.`email` FROM `users` u LEFT JOIN `users_additional_info` a ON u.`id` = a.`users_id` WHERE a.`facebook_id` = '{$messengerID}';")->fetch_row()[0] ?? NULL;

            $userFound = filter_var($userEmail, FILTER_VALIDATE_EMAIL);

            if (!$userFound) {
                self::user_not_found($message);
                exit(0);
            }

            $type = NULL;

            if (isset($json['entry'][0]['messaging'][0]['message']['quick_reply']['payload'])) {
                $type = $json['entry'][0]['messaging'][0]['message']['quick_reply']['payload'];
                if (substr($type, 0, 11) == "change-comp") {
                    $competitionsID = substr($type, 12);
                    $type           = "change-comp";
                }
            }
            if (isset($json['entry'][0]['messaging'][0]['postback']['payload'])) {
                $type = $json['entry'][0]['messaging'][0]['postback']['payload'];
            }

            if ($type === NULL) {
                $checkMessage = mb_strtolower($message);
                $checkMessage = fs::transliterateString($checkMessage);
                $checkMessage = preg_replace('/[-\-]/', '', $checkMessage);
                $checkMessage = str_replace(' ', '-', $checkMessage);
                $checkMessage = preg_replace('/[^A-Za-z0-9\-]/', '', $checkMessage);
                $checkMessage = str_replace('-', ' ', $checkMessage);
                $checkMessage = trim($checkMessage);
                $type         = $checkMessage;
            }

            if ($type === NULL || $type == "") {
                if (isset($json['entry'][0]['messaging'][0]['message']['sticker_id'])) {
                    $stickerID = $json['entry'][0]['messaging'][0]['message']['sticker_id'];
                    if ($stickerID == 369239263222822) {
                        $type = "thumbs_up";
                    } else {
                        $type = "sticker";
                    }
                }
            }

            self::$usersID = fs::getUserID($userEmail);

            $func = NULL;
            //Obstawianie meczu
            $goalsNextEvent = fs::getOption('goalsNextEvent', self::$usersID);
            if ($goalsNextEvent && $type !== 'anuluj') {
                $func = "obstaw_mecz_wynik";
            }

            if (!fs::getCompName(NULL, self::$usersID)) {
                $func = "no_comp";
            }

            if ($func === NULL) {
                switch ($type) {
                    case 'anuluj':
                    case 'anuiluj':
                    case 'aniuluj':
                    case 'aniuiluj':
                        fs::setOption('goalsNextEvent', NULL, self::$usersID);
                        self::send("{'recipient': {'id': '" . self::$messengerID . "'},'sender_action': 'mark_seen'}");
                        exit(0);
                    case 'new-user':
                        self::sendMessage("{'text': 'Witaj " . self::$name . ". Z tego co widzę masz już powiązane konto messenger z kontem na stronie " . PAGE_NAME . ". Możesz zatem swobodnie korzystać z moich usług :)'}");
                        exit(0);
                    case 'change-comp':
                        $func = "change_comp";
                        break;
                    case 'no-ranking':
                        self::sendMessage("{'text': 'Proszę, proszę, któs tu się wstydzi swojego wyniku :D'}");
                        exit(0);
                    case 'zmien turniej':
                    case 'zmien truniej':
                    case 'zmiana turnieju':
                    case 'zmiana trunieju':
                        $func = "zmien_truniej";
                        break;
                    case 'truniej':
                    case 'turniej':
                    case 'jaki turniej':
                    case 'obecny turniej':
                    case 'obecny truniej':
                    case 'jaki mam turniej':
                    case 'jaki mam truniej':
                    case 'jaki mam wybrany turniej':
                    case 'jaki mam wybrany truniej':
                        $compName = fs::getCompName(NULL, self::$usersID);
                        self::sendMessage("{'text': 'Obecnie masz wybrany turniej: {$compName}'}");
                        exit(0);
                    case 'jestem madry':
                    case 'lubisz mnie':
                        $messages = ["{'text': 'No, a jak :D'}", "{'text': 'Wiadomo :)'}", "{'text': 'No raczej :P'}", "{'text': 'Pewnie, że tak :D'}", "{'text': 'No oczywiście, że tak ;P'}", "{'text': 'No jasne :P'}"];
                        self::sendMessage($messages[array_rand($messages)]);
                        exit(0);
                    case 'pozycja':
                    case 'ranking':
                    case 'moja pozycja':
                    case 'pozycja w rankingu':
                    case 'ktore mam miejsce':
                    case 'moja pozycja w rankingu':
                        $func = "ranking";
                        break;
                    case 'jak mi idzie':
                    case 'jestem dobry':
                    case 'jestem dobra':
                    case 'dobry jestem':
                    case 'dobra jestem':
                        $func = "progres";
                        break;
                    case 'zmiana email':
                    case 'zmiana maila':
                    case 'zmien email':
                    case 'zmien mail':
                    case 'zmien maila':
                    case 'zmien moj mail':
                    case 'zmien moj email':
                    case 'zmiana adresu email':
                    case 'zmien adres email':
                    case 'zmien moj adres email':
                    case 'zmien mojego maila':
                    case 'zmien mojego emaila':
                        self::sendMessage("{'text': 'Niestety nie mogę zmienić twojego adresu e-mail. Jeżeli chcesz powiązać to konto messenger z innym użytkownikiem na stronie " . PAGE_NAME . ", poproś o to administartora strony.'}");
                        exit(0);
                    case 'nastepny mecz':
                    case 'kolejny mecz':
                    case 'najblizszy mecz':
                    case 'zblizajacy sie mecz':
                        $func = "nastepny_mecz";
                        break;
                    case 'obstaw mecz':
                        $func = "obstaw_mecz";
                        break;
                    case 'pomoc':
                    case 'komenda':
                    case 'komendy':
                    case 'lista komend':
                    case 'co mam wpisac':
                    case 'co moge napisac':
                    case 'o co moge spytac':
                    case 'o co moge zapytac':
                        $func = "pomoc";
                        break;
                    case 'sticker':
                    case 'thumbs_up':
                        $func = $type;
                        break;
                    default:
                        $func = "default";
                        break;
                }
            }

            $funcName = "message_{$func}";
            if (!method_exists(get_called_class(), $funcName)) {
                $funcName = "message_default";
            }
            self::$funcName($type);
        }

        $log = ob_get_clean();
//         fs::log($log);
    }

    private static function user_not_found($message)
    {
        $atPos = strpos($message, '@');
        if ($atPos) {
            $found        = 0;
            $messageArray = explode(' ', $message);
            foreach ($messageArray as $word) {
                if (strpos($word, '@')) {
                    $userEmail = mb_strtolower($word);
                    $found++;
                }
            }

            if ($found > 1) {
                $text = "Wygląda na to, że " . (self::$gender === "m" ? "wysłałeś" : "wysłałaś") . " mi więcej niż jeden adres e-mail. Proszę wyślij ponownie i upewnij się, że jest tylko jeden :)";
            } else {
                if (filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
                    $id = fs::$mysqli->query("SELECT `id` FROM `users` WHERE `email` = '{$userEmail}';")->fetch_row()[0];
                    if ($id !== NULL) {
                        $hash = md5($id . time() . rand(1000, 9999));
                        fs::setOption('confirm-chatbot', $hash, $id);

                        $subject = PAGE_NAME . " - " . fs::t("E-mail address confirmation");

                        $text[] = fs::t("Thank you for connecting messenger with") . " " . PAGE_NAME;
                        $text[] = fs::t("Please click the link below to confirm your e-mail address");
                        $link   = ROOT_URL . "/confirm/chatbot/" . self::$messengerID . "/" . $hash;

                        $text = implode("<br>", $text);

                        $message = "<html lang='pl'>
    					<head><title>{$subject}</title></head>
    					<body>
    					<p>{$text}</p>
    					<a href='{$link}'>{$link}</a>
    					</body></html>";

                        $email     = EMAIL;
                        $headers[] = 'MIME-Version: 1.0';
                        $headers[] = 'Content-type: text/html; charset=UTF-8';
                        $headers[] = "To: {$userEmail}";
                        $headers[] = "From: " . PAGE_NAME . "<no-reply@cotyp.pl>";
                        $headers[] = "Reply-To: {$email}";

                        mail($userEmail, $subject, $message, implode("\r\n", $headers));

                        $text = "Dzięki wielkie :) Znalazłem twoje konto na stronie " . PAGE_NAME . ". Wysłałem wiadomość na podany adres e-mail. Otwórz ją proszę i kliknij w link, żeby potwierdzić, że ten adres rzeczywiście należy do Ciebie :)";
                    } else {
                        $text = "Hmm... Nie udało mi się odnaleźć twojego konta na stronie " . PAGE_NAME . " :/ Jesteś " . (self::$gender === "m" ? "pewien" : "pewna") . ", że " . (self::$gender === "m" ? "podałeś" : "podałaś") . " ten sam adres e-mail, którym logujesz się na stronie " . PAGE_NAME . "?";
                    }
                } else {
                    $text = "Wygląda na to, że adres e-mail, który mi " . (self::$gender === "m" ? "wysłałeś" : "wysłałaś") . " jest niepoprawny. Proszę spróbuj wysłać jeszcze raz.";
                }
            }

            self::sendMessage("{'text': '{$text}'}");
            exit(0);
        }

        $text     = "Witaj " . self::$name . ". Aby móc uzyskać ode mnie informacje potrzebuję na początku twój adres e-mail, żeby powiązać twoje konto na messengerze z kontem na stronie " . PAGE_NAME . ".";
        $jsonData = "{'recipient': {'id': '" . self::$messengerID . "'},'message': }";
        self::sendMessage("{'text': '{$text}'}");
    }

    //Reply functions
    private static function message_no_comp($type)
    {
        $replies = "{'content_type': 'text','title': 'Tak', 'payload': 'zmien turniej'},";
        $replies .= "{'content_type': 'text','title': 'Nie', 'payload': 'anuluj'}";
        self::sendMessage("{'text': 'Obecnie nie masz wybranego turnieju. Chcesz to zrobić teraz?','quick_replies': [{$replies}]}");
    }

    private static function message_change_comp($type)
    {
        $message = "{'text': 'Ups... Niestety nie udało mi się zmienić trunieju. Może spróbuj jeszcze raz? :)'}";
        if (isset ($competitionsID) && fs::setOption('competition', $competitionsID, self::$usersID)) {
            $compName = fs::getCompName($competitionsID);
            $message  = "{'text': 'No i dobre. Zmieniłeś turniej na {$compName} :)'}";
        }
        self::sendMessage($message);
    }

    private static function message_zmien_truniej($type)
    {
        $replies = "";
        foreach (fs::getCompetitions(self::$usersID) as $competitionsID => $comp) {
            $replies .= "{'content_type': 'text','title': '{$comp['name']}', 'payload': 'change-comp {$competitionsID}'},";
        }
        $replies .= "{'content_type': 'text','title': 'Anuluj', 'payload': 'anuluj'}";
        self::sendMessage("{'text': 'Wybierz turniej:','quick_replies': [{$replies}]}");
    }

    private static function message_ranking($type)
    {
        $userRanking = fs::getUserRanking(self::$usersID);
        $position    = (int)$userRanking['position'];
        switch ($position) {
            case 0:
                self::sendMessage("{'text': 'Hmm... Wygląda na to, że jeszcze nie " . (self::$gender === "m" ? "zająłeś" : "zajęłaś") . " żadnego miejsca w rankingu. Być może turniej jeszcze się nie rozpoczął, lub nie " . (self::$gender === "m" ? "obstawiłeś" : "obstawiłaś") . " żadnego z meczy, które już się odbyły.'}");
                break;
            case 1:
                self::sendMessage("{'text': 'Gratulację! :O Na chwilę obecną zajmujesz pierwsze miejsce w rankingu. :D'}");
                break;
            case 2:
            case 3:
                self::sendMessage("{'text': 'Nieźle nieźle. :D Jesteś na podium. Na chwilę obecną masz {$position} miejsce.'}");
                break;
            case 4:
            case 5:
            case 6:
            case 7:
            case 8:
            case 9:
            case 10:
                self::sendMessage("{'text': 'Pierwsza dziesiątka, nie najgorzej. :) Na chwilę obecną masz {$position} miejsce.'}");
                break;
            default:
                self::sendMessage("{'text': 'Ugh... :/ Nie wiem jak Ci to powiedzieć, ale...'}");
                self::send("{'recipient': {'id': '" . self::$messengerID . "'},'sender_action': 'typing_on'}");
                sleep(2);
                self::sendMessage("{'text': 'Na chwilę obecną masz {$position} miejsce'}");
                break;
        }
        $link = ROOT_URL . "/ranking";
        self::sendMessage("{'text': 'Jeżeli chcesz zobaczyć pełny ranking, przejdź do strony {$link}'}");
    }

    private static function message_progres($type)
    {
        $genderText = (self::$gender === "m" ? "powinieneś sam" : "powinnaś sama");
        self::sendMessage("{
    	    'text': 'Myślę, że na to pytanie {$genderText} sobie odpowiedzieć :) Wyświetlić ranking?',
    		'quick_replies': [
    		    {'content_type': 'text','payload': 'ranking','title': 'Tak'},
    		    {'content_type': 'text','payload': 'no-ranking','title': 'Nie'}
    		]}"
        );
    }

    private static function message_nastepny_mecz($type)
    {
        //Trzy kropeczki
        self::send("{'recipient': {'id': '" . self::$messengerID . "'},'sender_action': 'typing_on'}");

        if ($event = fs::nextEventInfo(fs::getOption('competition', self::$usersID))) {
            $date = new DateTime($event['date']);
            $date->setTimezone(new DateTimeZone(self::$timezone));
            $time  = $date->format("H:i");
            $day   = $date->format("d");
            $month = fs::t($date->format("F"));
            $year  = $date->format("Y");

            $homeTeamName = fs::t($event['homeTeamName']);
            $awayTeamName = fs::t($event['awayTeamName']);

            $message = "W najbliższym meczu, zmierzy się {$homeTeamName} z {$awayTeamName}. Mecz rozpocznie się dnia {$day} {$month} {$year} o {$time} w strefie czasowej " . self::$timezone . ".";
            self::sendMessage("{'text': '{$message}'}");
            $userBet = fs::getUserBet($event['id'], self::$usersID);
            if ($userBet) {
                if ($userBet['home_team'] === NULL && $userBet['away_team'] === NULL) {
                    exit(0);
                }
                if ($userBet['home_team'] === NULL || $userBet['away_team'] === NULL) {
                    $message = "Ten mecz nie jest w pełni obstawiony.";
                    if ($userBet['away_team'] === NULL) {
                        $message .= " Twój zakład to {$userBet['home_team']} dla {$homeTeamName}.";
                    } else {
                        $message .= " Twój zakład to {$userBet['away_team']} dla {$awayTeamName}.";
                    }
                    if ($userBet['joker']) {
                        $message .= " Joker został aktywowany dla tego meczu.";
                    }
                } else {
                    $message = " Twój zakład to: {$userBet['home_team']} dla {$homeTeamName} oraz {$userBet['away_team']} dla {$awayTeamName}.";
                    if ($userBet['joker']) {
                        $message .= " Joker został aktywowany dla tego meczu.";
                    }
                }
                self::sendMessage("{'text': '{$message}'}");
            } else {
                $message = "Nie " . (self::$gender === "m" ? "obstawiłeś" : "obstawiłaś") . " wyniku tego meczu. Chcesz to zrobić teraz?";
                $replies = "{'content_type': 'text','title': 'Tak', 'payload': 'obstaw mecz'},";
                $replies .= "{'content_type': 'text','title': 'Nie', 'payload': 'anuluj'}";
                self::sendMessage("{'text': '{$message}','quick_replies': [{$replies}]}");
            }
        } else {
            self::sendMessage("{'text': 'Hmm... Wygląda na to, że nie mogę znaleźć następnego meczu w trunieju, który obecnie masz wybrany. Być może turniej już się zakończył?'}");
        }
    }

    private static function message_obstaw_mecz($type)
    {
        if ($event = fs::nextEventInfo(fs::getOption('competition', self::$usersID))) {
            $userBet = fs::getUserBet($event['id'], self::$usersID);
            $date    = new DateTime($event['date']);
            $date->setTimezone(new DateTimeZone(self::$timezone));
            $time  = $date->format("H:i");
            $day   = $date->format("d");
            $month = fs::t($date->format("F"));
            $year  = $date->format("Y");

            $homeTeamName = fs::t($event['homeTeamName']);
            $awayTeamName = fs::t($event['awayTeamName']);

            $message = "W najbliższym meczu, zmierzy się {$homeTeamName} z {$awayTeamName}. Mecz rozpocznie się dnia {$day} {$month} {$year} o {$time} w strefie czasowej " . self::$timezone . ".";
            self::sendMessage("{'text': '{$message}'}");
            $message = "Podaj liczbę goli dla drużyny {$homeTeamName}.";
            self::sendMessage("{'text': '{$message}','quick_replies': [{'content_type': 'text','title': 'Anuluj', 'payload': 'anuluj'}]}");
            fs::setOption('goalsNextEvent', HOME_TEAM, self::$usersID);
        } else {
            self::sendMessage("{'text': 'Hmm... Wygląda na to, że nie mogę znaleźć następnego meczu w trunieju, który obecnie masz wybrany. Być może turniej już się zakończył?'}");
        }
    }

    private static function message_obstaw_mecz_wynik($type)
    {
        $goalsNextEvent = fs::getOption('goalsNextEvent', self::$usersID);
        if ($type !== 'anuluj' && !is_numeric($type)) {
            self::sendMessage("{'text': 'Proszę, podaj proszę liczbę goli, lub napisz anuluj, aby zrezygnować.','quick_replies': [{'content_type': 'text','title': 'Anuluj', 'payload': 'anuluj'}]}");
            exit(0);
        } else {
            if (is_numeric($type)) {
                if (fs::setNextEventPrediction($type, $goalsNextEvent, self::$usersID)) {
                    if ($goalsNextEvent == HOME_TEAM) {
                        if ($event = fs::nextEventInfo(fs::getOption('competition', self::$usersID))) {
                            $awayTeamName = fs::t($event['awayTeamName']);
                            $message      = "Podaj liczbę goli dla drużyny {$awayTeamName}.";
                            self::sendMessage("{'text': '{$message}','quick_replies': [{'content_type': 'text','title': 'Anuluj', 'payload': 'anuluj'}]}");
                            fs::setOption('goalsNextEvent', AWAY_TEAM, self::$usersID);
                        } else {
                            self::sendMessage("{'text': 'Hmm... Wygląda na to, że nie mogę znaleźć następnego meczu w trunieju, który obecnie masz wybrany. Być może turniej już się zakończył?'}");
                        }
                    } else {
                        if ($goalsNextEvent == AWAY_TEAM) {
                            fs::setOption('goalsNextEvent', NULL, self::$usersID);
                            self::sendMessage("{'text': 'Poprawnie obstawiłeś najbliższy mecz.'}");
                        } else {
                            fs::setOption('goalsNextEvent', NULL, self::$usersID);
                            self::sendMessage("{'text': 'Wystąpił nieznany błąd. Przepraszam.'}");
                        }
                    }
                } else {
                    fs::log("Failed to set prediction");
                }
            }
        }
    }

    private static function message_pomoc($type)
    {
        self::sendMessage("{
            'text': 'Oto lista dosępnych komend:',
            'quick_replies': [
                {'content_type': 'text','payload': 'ranking','title': 'Ranking'},
                {'content_type': 'text','payload': 'nastepny mecz','title': 'Następny mecz'},
                {'content_type': 'text','payload': 'obstaw mecz','title': 'Obstaw mecz'},
                {'content_type': 'text','payload': 'zmien turniej','title': 'Zmień turniej'},
                {'content_type': 'text','payload': 'turniej','title': 'Obecny turniej'},
                {'content_type': 'text','payload': 'anuluj','title': 'Anuluj'},
            ]}"
        );
    }

    private static function message_sticker($type)
    {
        self::sendMessage("{'text': 'Ładna naklejka :)'}");
    }

    private static function message_thumbs_up($type)
    {
        self::sendMessage("{'text': 'Jest OK :)'}");
    }

    private static function message_default($type)
    {
        $explanation = "";
        if (fs::getPrivilege(self::$usersID) == 5) {
            $explanation = " Oto co zrozumiałem: {$type}";
        }
        self::sendMessage("{'text': 'Przepraszam, ale niestety nie wiem jak na to odpowiedzieć. Aby uzyskać listę dostepnych komend napisz \'pomoc\' {$explanation}'}");
    }

    //Send functions
    private static function sendMessage($message)
    {
        self::send("{'recipient': {'id': '" . self::$messengerID . "'},'message': {$message}}");
    }

    public static function send($json)
    {
        $url = "https://graph.facebook.com/" . self::FB_GRAPH_VERSION . "/me/messages?access_token=" . self::$accessToken;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        if (!curl_exec($ch)) {
            fs::log(curl_error($ch));
        }

        curl_close($ch);
    }
}