<?php

namespace controllers;

use Exception;
use classes\User;
use classes\Event;
use classes\Group;
use classes\Functions as fs;

class SummaryController extends PageController
{
    public function content(array $args = [])
    {
        $groupsID = $this->get('group');

        [$groups, $users] = RankingController::getGroupsAndUsers($groupsID, "/summary");

        try {
            [$events] = Event::getAll();
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            self::redirect("/error");
            exit(0);
        }

        foreach ($events as $eventsID => &$event) {
            foreach ($event->getUsersBets() as $usersID => $bet) {
                if (!in_array($usersID, array_keys($users))) {
                    $event->unsetUserBet($usersID);
                }
            }
        }
        unset($event);

        $data = [
            'events'      => $events,
            'groups'      => $groups,
            'curGroupsID' => $groupsID ?? 1,
        ];

        return parent::content(array_merge($args, $data));
    }
}