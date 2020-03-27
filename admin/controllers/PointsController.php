<?php

namespace admin\controllers;

use classes\Functions as fs;
use classes\Event;

class PointsController extends AdminController
{
    public function content(array $args = [])
    {
        $this->titile   = "Punkty";
        $competitionsID = $this->get('compID');

        $data = [
    	    'events' => [],
    	    'competitions' => [],
        ];
        if ($competitionsID !== NULL) {
            try {
                list($events) = Event::getAll(false, false, NULL, $competitionsID);
                foreach ($events as $eventID => &$event) {
                    $event->results = [];
                    $sql = "SELECT `users_id`, `home_team`, `away_team`, `score` FROM `predictions` WHERE `events_id` = '{$eventID}';";
                    if ($query = fs::$mysqli->query($sql)) {
                        while ($result = $query->fetch_assoc()) {
                            if ($result['score'] === NULL) {
                                continue;
                            }
                            $event->results[$result['users_id']] = [
                                'home_team' => $result['home_team'],
                                'away_team' => $result['away_team'],
                                'score'     => $result['score'],
                            ];
                        }
                    }
                }
                $data['events']         = $events;
                $data['competitionsID'] = $competitionsID;
                $this->view = "recalculate";
            } catch (Exception $e) {
                fs::log($e->getMessage());
                header("Location: /error");
            }
        } else {
            $data['competitions'] = fs::getCompetitions();
        }

        return parent::content(array_merge($args, $data));
    }
}