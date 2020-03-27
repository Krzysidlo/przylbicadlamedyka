<?php

use classes\Functions as fs;

$return = [
    'success' => false,
    'message' => false,
    'alert'   => false,
    'output'  => [],
];

if (!empty($_POST['action'])) {
    $action = filter_var($_POST['action'], FILTER_SANITIZE_STRING);

    $now = date("Y-m-d H:i:s");

    $timezone = fs::getClientsTimezone();
    $now      = new DateTime($now);
    $now->setTimezone(new DateTimeZone($timezone));
    switch ($action) {
        case 'shouldStart':
            if (!empty($_POST['events'])) {
                $eventsToCheck = implode("','", $_POST['events']);
                $sql           = "SELECT `id`, `date`, `status` FROM `events` WHERE `id` IN ('{$eventsToCheck}');";
                $return['sql'] = $sql;
                if ($query = fs::$mysqli->query($sql)) {
                    $output = [];
                    while ($result = $query->fetch_assoc()) {
                        $eventDate = $result['date'] ?? time();
                        $eventDate = new DateTime($eventDate);
                        $eventDate->setTimezone(new DateTimeZone($timezone));
                        if ($now > $eventDate && $result['status'] === "TIMED") {
                            $result['status'] = "DATE";
                        }
                        $output[$result['id']] = $result['status'];
                    }
                    $return = [
                        'success'  => true,
                        'message'  => false,
                        'alert'    => false,
                        'statuses' => $output,
                    ];
                }
            }
            break;
        case 'inPlay':
            if (!empty($_POST['events'])) {
                $eventsToCheck = implode("','", $_POST['events']);
                $sql           = "SELECT e.`id`, e.`date` e.`status`, r.`homeTeam`, r.`awayTeam` FROM `events` e LEFT JOIN `results` r ON e.`id` = r.`events_id` WHERE e.`id` IN ('{$eventsToCheck}');";
                $return['sql'] = $sql;
                if ($query = fs::$mysqli->query($sql)) {
                    $events = [];
                    while ($result = $query->fetch_assoc()) {
                        $eventDate = $result['date'] ?? time();
                        $eventDate = new DateTime($eventDate);
                        $eventDate->setTimezone(new DateTimeZone($timezone));
                        if ($now > $eventDate && $result['status'] === "TIMED") {
                            $result['status'] = "DATE";
                        }
                        $events[$result['id']] = $result;
                    }
                    $return = [
                        'success' => true,
                        'message' => false,
                        'alert'   => false,
                        'events'  => $events,
                    ];
                }
            }
            break;
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