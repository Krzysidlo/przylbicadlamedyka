<?php

namespace controllers;

use Exception;
use classes\User;
use classes\Functions as fs;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class PushController extends PageController
{

    private $vapidPubKey;
    private $vapidPriKey;

    public function __construct()
    {
        parent::__construct("push");
        $this->vapidPubKey = "BKQjBUuGol8D5pFJDN9AHuRZL9vYQ94TnzuVR6-tYyfbwMXhrZ_ORzJaZCovdAB-546eTvzrP5ibzJ-JUuTr6es";
        $this->vapidPriKey = "gdGJS_2LNzMODazUXSMv3qOAoueSECtbqeSP4vcohOs";
    }

    public function content(array $args = [])
    {
        if ((empty($_POST['emailID']) || empty($_POST['type'])) && (empty($args['emailID']) || empty($args['type']))) {
            return false;
        }

        $emailID = $_POST['emailID'] ?? $args['emailID'];
        $type    = $_POST['type'] ?? $args['type'];

        try {
            $user = new User($emailID);
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            return false;
        }

        if (!$user->getOption('pushNotifications')) {
            return true;
        }

        $title = "";
        $body  = "";
        $icon  = IMG_URL . "/logo.png";
        $link  = "/";
        switch ($type) {
            case 'notification':
                $title = $_POST['title'] ?? $args['title'] ?? PAGE_NAME;
                $body  = $_POST['body'] ?? $args['body'] ?? "";
                $icon  = $_POST['icon'] ?? $args['icon'] ?? $icon;
                $link  = $_POST['link'] ?? $args['link'] ?? $link;
                break;
        }

        $subscriptions = $user->getOption('subscription');
        if (!empty($subscriptions)) {
            foreach ($subscriptions as $subscription) {
                try {
                    $endpoint = $subscription->endpoint;

                    $auth = [
                        'VAPID' => [
                            'subject'    => ROOT_URL,
                            'publicKey'  => $this->vapidPubKey,
                            'privateKey' => $this->vapidPriKey,
                        ],
                    ];

                    $subscription = Subscription::create([
                        'endpoint' => $endpoint,
                        'keys'     => [
                            'p256dh' => $subscription->keys->p256dh,
                            'auth'   => $subscription->keys->auth,
                        ],
                    ]);

                    $payload = [
                        'title' => $title,
                        'body'  => $body,
                        'icon'  => $icon,
                        'data'  => [
                            'link'   => $link,
                            'origin' => ROOT_URL,
                        ],
                    ];

                    //Ogarnąć te opcje ($auth jest ogarnięte, ale nie wiem czemu reszta jest w taki sposób)
                    $webPush = new WebPush($auth);
                    $webPush->setReuseVAPIDHeaders(true);

                    $webPush->sendNotification($subscription, json_encode($payload));

                    foreach ($webPush->flush() as $report) {
                        fs::log($report->isSuccess());
                    }
                } catch (Exception $e) {
                    fs::log("Error: " . $e->getMessage());
                    return false;
                }
            }
        }

        return true;
    }
}