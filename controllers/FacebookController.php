<?php

namespace controllers;

use Exception;
use Facebook\Facebook;

use classes\User;
use classes\Functions as fs;

ini_set("log_errors", 1);
ini_set("error_log", LOG_DIR . '/facebook.log');
fs::setLogFile('facebook.log');

class FacebookController extends PageController
{
    public function content(array $args = [])
    {
        $fClient = new Facebook(['app_id' => self::FB_APP_ID, 'app_secret' => self::FB_APP_SECRET, 'default_graph_version' => self::FB_GRAPH_VERSION,]);

        if (DB_CONN && !empty($_GET['code'])) {
            try {
                $helper      = $fClient->getRedirectLoginHelper();
                $accessToken = $helper->getAccessToken();

                $fClient->setDefaultAccessToken($accessToken);

                $permissions = $fClient->get('/me/permissions');
                $permissions = $permissions->getGraphEdge()->asArray();
                $declined    = [];
                foreach ($permissions as $permission) {
                    if ($permission['status'] == 'declined') {
                        $declined[] = $permission['permission'];
                    }
                }

                if (!empty($declined)) {
                    $url = $helper->getReRequestUrl(ROOT_URL . "/facebook", $declined);
                    header("Location: {$url}");
                    exit(0);
                }

                $graphResponse = $fClient->get('/me?fields=email,name,picture');
                $userData      = $graphResponse->getGraphUser();

                if (filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                    try {
                        $user = new User($userData['email']);
                        $_SESSION['usersID']  = $user->id;
                        $_SESSION['userName'] = $user->name;
                    } catch (Exception $e) {
                        try {
                            $user = User::newUser($userData['email'], $userData['name'], $userData['id'], "facebook", $userData['picture']['url']);
                            $_SESSION['usersID']  = $user->id;
                            $_SESSION['userName'] = $user->name;
                        } catch (Exception $e) {
                            fs::log("Error: " . $e->getMessage());
                        }
                    }
                }
            } catch (Exception $e) {
                fs::log("Error: " . $e->getMessage());
                if (DEV_MODE) {
                    echo $e->getMessage();
                }
            }
        }

        header("Location: " . ROOT_URL);
        exit(0);
    }
}