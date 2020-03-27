<?php

use classes\Functions as fs;

$return = [
    'success' => false,
    'message' => fs::t('There was an unexpected error'),
    'alert'   => 'danger',
];

if (!empty($_POST['eventDate']) && !empty($_POST['eventId'])) {
    $eventDate  = $_POST['eventDate'];
    $id         = $_POST['eventId'];
    $usersID    = USER_ID;
    $removeOnly = (!empty($_POST['removeOnly']) ? ($_POST['removeOnly'] === 'true' ? true : false) : false);

    $updatedAt = date("Y-m-d H:i:s");

    $timezone   = fs::getClientsTimezone();
    $updateDate = new DateTime($updatedAt);
    $updateDate->setTimezone(new DateTimeZone($timezone));

    $date = new DateTime($eventDate);
    $date->setTimezone(new DateTimeZone($timezone));

    $return['date']       = $date;
    $return['updateDate'] = $updateDate;
    if (USER_PRV <= 1) {
        $return = [
            'success' => true,
            'message' => fs::t("You did not confirm your e-mail address") . ". " . fs::t("You cannot predict events") . ".",
            'alert'   => 'warning',
        ];
    } else {
        if ($updateDate > $date) {
            $return = [
                'success' => false,
                'message' => fs::t("The event has already started") . ". " . fs::t("You cannot set the joker for this event anymore") . ".",
                'alert'   => 'warning',
            ];
        } else {
            $prevId           = (!empty($_POST['prevId']) ? $_POST['prevId'] : false);
            $return['prevId'] = $prevId;
            if ($prevId) {
                if ($query = fs::$mysqli->query("SELECT `date` FROM `events` WHERE `id` = '{$prevId}'")) {
                    $eventDate = $query->fetch_assoc()['date'] ?? time();
                    $eventDate = new DateTime($eventDate);
                    $eventDate->setTimezone(new DateTimeZone($timezone));
                    if ($updateDate > $eventDate) {
                        $return = [
                            'success' => false,
                            'message' => fs::t("Joker already used in this matchday"),
                            'alert'   => 'warning',
                        ];
                    } else {
                        if (fs::$mysqli->query("UPDATE `predictions` SET `joker` = 0 WHERE `events_id` = '{$prevId}' AND `users_id` = '{$usersID}'")) {
                            $return['success'] = true;
                            $return['alert']   = false;
                        } else {
                            $return['message'] = fs::t('Database error');
                            $return['sql']     = $sql;
                        }
                    }
                }
            } else {
                $return['success'] = "success";
            }

            if ($return['success'] && !$removeOnly) {
                $return = [
                    'success' => false,
                    'message' => fs::t("You cannot set joker for event not fully predicted"),
                    'alert'   => "warning",
                ];
                $sql    = "SELECT `home_team`, `away_team` FROM `predictions` WHERE `events_id` = '{$id}' AND `users_id` = '{$usersID}';";
                if ($query = fs::$mysqli->query($sql)) {
                    $result = $query->fetch_assoc();
                    if (isset ($result['home_team']) && $result['home_team'] !== NULL && isset($result['away_team']) && $result['away_team'] !== NULL) {
                        $sql           = "UPDATE `predictions` SET `joker` = 1 WHERE `events_id` = '{$id}' AND `users_id` = '{$usersID}';";
                        $return['sql'] = $sql;
                        if (fs::$mysqli->query($sql)) {
                            $return = [
                                'success' => true,
                                'message' => false,
                                'alert'   => false,
                            ];
                        }
                    }
                }
            }
        }
    }
    if ($updateDate > $date) {
        $return['start'] = true;
    }
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