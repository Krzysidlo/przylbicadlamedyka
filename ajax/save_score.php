<?php

use classes\Functions as fs;

$return = [
    'success' => false,
    'message' => fs::t('There was an unexpected error'),
    'alert'   => 'danger',
];

if (!empty($_POST['eventDate']) && !empty($_POST['eventId'])) {
    $homeTeam  = strlen($_POST['homeTeam']) > 0 ? $_POST['homeTeam'] : "null";
    $awayTeam  = strlen($_POST['awayTeam']) > 0 ? $_POST['awayTeam'] : "null";
    $eventDate = $_POST['eventDate'];
    $id        = $_POST['eventId'];

    $updatedAt = date("Y-m-d H:i:s");

    $timezone   = fs::getClientsTimezone();
    $updateDate = new DateTime($updatedAt);
    $updateDate->setTimezone(new DateTimeZone($timezone));

    $date = new DateTime($eventDate);
    $date->setTimezone(new DateTimeZone($timezone));

    if (USER_PRV <= 1) {
        $return = [
            'success' => true,
            'message' => fs::t("You did not confirm your e-mail address") . ". " . fs::t("You cannot predict events") . ".",
            'alert'   => 'warning',
        ];
    } else {
        if ($updateDate > $date) {
            $return = [
                'success' => true,
                'message' => fs::t("The event has already started") . ". " . fs::t("You cannot change the score anymore") . ".",
                'alert'   => 'warning',
            ];
        } else {
            $competitionsID = COMPETITION_ID;
            $usersID        = USER_ID;
            if ($homeTeam === "null" && $awayTeam === "null") {
                $sql = "DELETE FROM `predictions` WHERE `events_id` = '{$id}' AND `users_id` = '{$usersID}';";
            } else {
                $sql = <<< SQL
			INSERT INTO `predictions` (`events_id`, `users_id`, `competitions_id`, `home_team`, `away_team`) VALUES
			('{$id}', '{$usersID}', {$competitionsID}, {$homeTeam}, {$awayTeam})
			ON DUPLICATE KEY UPDATE home_team = VALUES(home_team), away_team = VALUES(away_team);
SQL;
            }

            if (fs::$mysqli->query($sql)) {
                $return = [
                    'success' => true,
                    'message' => false,
                    'alert'   => false,
                ];
            } else {
                $return['message'] = fs::t('Database error');
                $return['sql']     = $sql;
            }
        }
    }
    if ($updateDate > $date) {
        $return['start'] = true;
    }
}

if (empty($_GET['ajax'])) {
    if (!empty($_POST['eventDay'])) {
        header("Location: /index/" . $_POST['eventDay']);
    } else {
        header("Location: /");
    }
} else {
    echo json_encode($return);
}

exit(0);