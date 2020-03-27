<?php

use classes\Functions as fs;

$return = [
    'success' => false,
    'message' => fs::t('There was an unexpected error'),
    'alert'   => 'danger',
];

if (!empty($_POST['eventId'])) {
    $id        = $_POST['eventId'];
    $homeTeam  = strlen($_POST['homeTeam']) > 0 ? intval($_POST['homeTeam']) : "null";
    $awayTeam  = strlen($_POST['awayTeam']) > 0 ? intval($_POST['awayTeam']) : "null";

    $updatedAt = date("Y-m-d H:i:s");

    if ($homeTeam === "null" && $awayTeam === "null") {
        $sql = <<< SQL
        INSERT INTO `results` (`events_id`, `homeTeam`, `awayTeam`, `modified`) VALUES ('{$id}', NULL, NULL, NULL)
        ON DUPLICATE KEY UPDATE
        `homeTeam` = NULL,
        `awayTeam` = NULL,
        `modified` = NULL;
SQL;
        $sql = "DELETE FROM `results` WHERE `events_id` = '{$id}';";
        if (fs::$mysqli->query($sql)) {
            $sql = "UPDATE `events` SET `status` = 'IN_PLAY' WHERE `id` = '{$id}';";
            fs::$mysqli->query($sql);
            $return = [
                'success' => true,
                'message' => false,
                'alert'   => false,
            ];
        } else {
            $return['message'] = fs::t('Database error');
            $return['sql']     = $sql;
            $return['error']   = fs::$mysqli->error;
        }
    } else {
        $sql = <<< SQL
        INSERT INTO `results` (`events_id`, `homeTeam`, `awayTeam`, `modified`) VALUES ('{$id}', {$homeTeam}, {$awayTeam}, NOW())
        ON DUPLICATE KEY UPDATE
        `homeTeam` = VALUES(homeTeam),
        `awayTeam` = VALUES(awayTeam),
        `modified` = VALUES(modified);
SQL;
        if (fs::$mysqli->query($sql)) {
            $return = [
                'success' => true,
                'message' => false,
                'alert'   => false,
            ];
        } else {
            $return['message'] = fs::t('Database error');
            $return['sql']     = $sql;
            $return['error']   = fs::$mysqli->error;
        }
    }
}

if (empty($_GET['ajax'])) {
    header("Location: /admin/events");
} else {
    echo json_encode($return);
}

exit(0);