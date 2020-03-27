<?php

use classes\Functions as fs;

$return = [
    'success' => false,
    'message' => "",
    'alert'   => false,
];

if (isset($_POST['subscription'])) {
    $newSubscription     = json_decode($_POST['subscription']);
    $currentSubscription = fs::getOption('subscription');
    if (!in_array($newSubscription, $currentSubscription)) {
        $currentSubscription[] = $newSubscription;
        if (fs::setOption('subscription', $currentSubscription)) {
            fs::setOption('pushNotifications', true);
            $return['success'] = true;
        }
    }
} else {
    $return['message'] = "Brakuje danych";
}

if (empty($_GET['ajax'])) {
    header("Location: /");
} else {
    echo json_encode($return);
}

exit(0);