<?php

namespace controllers;

use Exception;
use Google_Client;
use Google_Service_Oauth2;
use classes\User;
use classes\Functions as fs;

class GoogleController extends PageController
{

    public function content(array $args = [])
    {
        if (DB_CONN && !empty($_GET['code'])) {
            //Google login
            try {
                $gClient = new Google_Client();
                $gClient->setClientId("758734978512-7787flc7237g5v8grplaqvprv887cd2v.apps.googleusercontent.com");
                $gClient->setClientSecret("uOQW_RHZSa7k1W6mIGGOBJWD");
                $gClient->setApplicationName(PAGE_NAME);
                $gClient->setRedirectUri(ROOT_URL . "/google");
                $gClient->addScope("https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email");
                $gClient->fetchAccessTokenWithAuthCode($_GET['code']);
                $oAuth    = new Google_Service_Oauth2($gClient);
                $userData = $oAuth->userinfo_v2_me->get();
                if (filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                    try {
                        $user = new User($userData['email']);
                        $_SESSION['usersID']  = $user->id;
                        $_SESSION['userName'] = $user->name;
                    } catch (Exception $e) {
                        try {
                            $user = User::newUser($userData['email'], $userData['name'], $userData['id'], "google", $userData['picture']);
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