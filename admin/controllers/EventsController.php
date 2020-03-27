<?php

namespace admin\controllers;

use classes\Event;
use classes\Functions as fs;

class EventsController extends AdminController
{
    public function content(array $args = [])
    {
    	$this->titile = "Mecze";
        $competitionsID = $this->get('compID') ?? 0;
        if ($competitionsID <= 0) {
            header("Location: /admin/");
            exit(0);
        }
        try {
            list($events, $eventDay, $eventDaysNum) = Event::getAll(false, false, NULL, $competitionsID);
        } catch (Exception $e) {
            fs::log($e->getMessage());
            header("Location: /error");
        }

        $data = [
            'events'         => $events,
            'eventDay'       => $eventDay,
            'eventDaysNum'   => $eventDaysNum,
            'competitionsID' => $competitionsID,
        ];

        return parent::content(array_merge($args, $data));
    }
}