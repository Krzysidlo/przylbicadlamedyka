<?php

namespace controllers;

use stdClass;
use Exception;
use classes\User;
use classes\Event;
use classes\Functions as fs;

class UserController extends PageController
{
    public function content(array $args = [])
    {
        $login = $this->get('login');
        try {
            $user = new User($login);
            [$events] = Event::getAll(false, true, "FINISHED", NULL, $user->id);
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            self::redirect("/error");
        }

        $results = [];
        $sum = 0;
        foreach ($events as $eventsID => $event) {
            $results[$eventsID] = new stdClass;
            $results[$eventsID]->date   = $event->displayDate . ", " . $event->localDate->format("H:i");
            $results[$eventsID]->teams  = fs::t($event->homeTeamName) . "&nbsp;-&nbsp;" . fs::t($event->awayTeamName);
            $results[$eventsID]->result = ($event->result->homeTeam['FT'] . "&nbsp;-&nbsp;" . $event->result->awayTeam['FT']);
            if ($event->result->homeTeam['PS'] !== NULL) {
                $results[$eventsID]->result = ($event->result->homeTeam['FT'] . "&nbsp;(" . $event->result->homeTeam['PS'] . ")&nbsp;-&nbsp;" . $event->result->awayTeam['FT'] . "&nbsp;(" . $event->result->awayTeam['PS'] . ")");
            }
            $results[$eventsID]->prediction = ($event->prediction->homeTeam ?? fs::t("None")) . " - " . ($event->prediction->awayTeam ?? fs::t("None"));
            $results[$eventsID]->score      = $event->prediction->score ?? 0;
            $sum += $results[$eventsID]->score;
        }
        $results[] = $sum;
        $data = ['results' => $results];

        $this->view = 'results';
        $this->menu = $user->login;

        $currentUser = new User;
        $data['userInfo'] = ($user->login === $currentUser->login ? fs::t("My results") : $user->name . " - " . fs::t("results"));

        return parent::content(array_merge($args, $data));
    }
}