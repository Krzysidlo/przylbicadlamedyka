<?php

use classes\Functions as fs;

$return = [
    'success' => false,
    'message' => '',
    'alert'   => '',
];

if (!empty($_POST['action'])) {
    $action  = filter_var($_POST['action'], FILTER_SANITIZE_STRING);
    $usersID = USER_ID;
    switch ($action) {
        case "read":
            if (fs::$mysqli->query("UPDATE `notifications` SET `new` = 0 WHERE `users_id` = '{$usersID}';")) {
                $return['success'] = true;
            }
            break;
        case "get":
            $return['data'] = [];
            if ($query = fs::$mysqli->query("SELECT `id`, `content`, `href` FROM `notifications` WHERE `users_id` = '{$usersID}' AND `nd` = 1;")) {
                while ($result = $query->fetch_assoc()) {
                    fs::$mysqli->query("UPDATE `notifications` SET `nd` = 0 WHERE `id` = {$result['id']};");
                    $return['data'][] = $result;
                }
                $return['success'] = !empty($return['data']);
            }
            break;
    }
}

if (empty($_GET['ajax'])) {
    header("Location: /");
} else {
    echo json_encode($return);
}

exit(0);