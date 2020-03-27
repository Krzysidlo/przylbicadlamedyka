<?php

use classes\Functions as fs;

$return = [
    'success' => false,
    'message' => fs::t('There was an unexpected error'),
    'alert'   => 'danger',
];

$competitionsID = COMPETITION_ID;
$usersID        = USER_ID;
$sql            = "SELECT `events_id` FROM `predictions` WHERE `users_id` = '{$usersID}' AND `competitions_id` = {$competitionsID} AND `joker` = 1 LIMIT 1;";
$return['sql']  = $sql;
if ($query = fs::$mysqli->query($sql)) {
    $id     = $query->fetch_assoc()['events_id'] ?? NULL;
    $return = [
        'success' => true,
        'message' => false,
        'alert'   => false,
        'id'      => $id,
    ];
}


if (empty($_GET['ajax'])) {
    if (!empty($_POST['eventDay'])) {
        header("Location: /" . $_POST['eventDay']);
    } else {
        header("Location: /");
    }
} else {
    echo json_encode($return);
}

exit(0);