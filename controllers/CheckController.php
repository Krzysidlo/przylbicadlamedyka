<?php

namespace controllers;

use DateTime;
use DateTimeZone;
use classes\Functions as fs;

class CheckController extends PageController
{

    public function content(array $args = [])
    {
        if (USER_PRV < 4) {
            header("Location: /");
            exit(0);
        }

        $cronErrors = [];
        $sql        = <<< SQL
		SELECT * from `cron_errors`;
SQL;

        if ($query = fs::$mysqli->query($sql)) {
            while ($result = $query->fetch_object()) {
                $timezone = fs::getClientsTimezone();

                $dateTime = new DateTime($result->datetime);
                $dateTime->setTimezone(new DateTimeZone($timezone));

                $cronErrors[] = [
                    'name'     => $result->name,
                    'value'    => $result->value,
                    'dateTime' => $dateTime->format("Y-m-d H:i:s"),
                ];
            }
        } else {
            if (DEV_MODE) {
                echo "<pre>";
                var_dump(fs::$mysqli->error);
                echo "</pre>";
                die();
            }
        }

        $data['cronErrors'] = $cronErrors;

        $eventErrors = [];
        $sql         = <<< SQL
		SELECT p.`events_id`, p.`users_id`, p.`created_at`, p.`updated_at`, u.`name`, e.`date` FROM `predictions` p
		LEFT JOIN `users` u ON p.`users_id` = u.`id` LEFT JOIN `events` e ON p.`events_id` = e.`id`;
SQL;

        if ($query = fs::$mysqli->query($sql)) {
            while ($result = $query->fetch_object()) {
                $timezone = fs::getClientsTimezone();

                $date = new DateTime($result->date);
                $date->setTimezone(new DateTimeZone($timezone));

                $created = new DateTime($result->created_at);
                $created->setTimezone(new DateTimeZone($timezone));

                $updated = ($result->updated_at != NULL ? new DateTime($result->updated_at) : $created);
                $updated->setTimezone(new DateTimeZone($timezone));

                if ($date < $created || $date < $updated) {
                    $eventErrors[] = [
                        'name'         => $result->name,
                        'users_id'     => $result->users_id,
                        'events_id'    => $result->events_id,
                        'event_date'   => $date->format("Y-m-d H:i:s"),
                        'created_date' => $created->format("Y-m-d H:i:s"),
                        'updated_date' => $updated->format("Y-m-d H:i:s"),
                    ];
                }
            }
        } else {
            if (DEV_MODE) {
                echo "<pre>";
                var_dump(fs::$mysqli->error);
                echo "</pre>";
                die();
            }
        }

        $data['eventErrors'] = $eventErrors;

        return parent::content(array_merge($args, $data));
    }
}