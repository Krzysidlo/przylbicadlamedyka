<?php

namespace controllers;

use classes\User;
use classes\Functions as fs;
use classes\exceptions\UserNotFoundException;

class ConfirmController extends PageController
{
    public function content(array $args = [])
    {
        $type = filter_var($this->get('type'), FILTER_SANITIZE_STRING);

        switch ($type) {
            case 'chatbot':
                $hash = filter_var($this->get('hash'), FILTER_SANITIZE_STRING);
                $user = fs::findUserByHash($hash);
                if (!empty($user)) {
                    $fbId = filter_var($this->get('fbId'), FILTER_SANITIZE_STRING);
                    fs::$mysqli->query("UPDATE `users_additional_info` SET `facebook_id` = {$fbId} WHERE `users_id` = '{$user['id']}';");
                    if (fs::$mysqli->affected_rows > 0) {
                        fs::setOption('confirm-chatbot', NULL, $user['id']);
                        fs::sendNotification(fs::t("Your e-mail address has been connected with facebook messenger"), NULL, $user['id']);
                    }
                    $_SESSION['usersID']  = $user['id'];
                    $_SESSION['userName'] = $user['name'];

                    $jsonData = "{'recipient': {'id': '{$fbId}'},'message': {'text': 'Widzę, że właśnie udało Ci się potwierdzić adres e-mail. Super, możesz teraz pytać mnie o różne rzeczy. Aby uzyskać listę dostępnych komend napisz \'pomoc\'.'}}";
                    ChatbotController::send($jsonData);
                }
                break;
            default:
                $hash = filter_var($this->get('hash'), FILTER_SANITIZE_STRING);
                try {
                    $user = User::getByHash($hash);
                } catch (UserNotFoundException $e) {
                    fs::log("Error: " . $e->getMessage());
                    self::redirect("/");
                    exit(0);
                }

                if ($user->setPrivilege(2)) {
                    $user->setOption('confirm-email', NULL);
                    $user->sendNotification(fs::t("Your e-mail address has been successfully confirmed"), NULL);
                }
                $_SESSION['usersID']  = $user->id;
                $_SESSION['userName'] = $user->name;
                break;
        }
        self::redirect("/");
        exit(0);
    }
}