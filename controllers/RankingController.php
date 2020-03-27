<?php

namespace controllers;

use Exception;
use classes\User;
use classes\Group;
use classes\Ranking;
use classes\Functions as fs;

class RankingController extends PageController
{
    public function content(array $args = [])
    {
        $groupsID = $this->get('group');

        [$groups, $users] = self::getGroupsAndUsers($groupsID);

        $ranking = new Ranking($users);

        foreach ($users as $usersID => &$user) {
            $user->ranking           = $ranking->table[$usersID];
            $user->ranking['avatar'] = "<img src='" . $user->getAvatar() . "' class='img-responsive img-circle img-small' alt='avatar'>";
        }
        unset($user);

        $data = [
            'users'       => $users,
            'groups'      => $groups,
            'curGroupsID' => $groupsID ?? 1,
        ];

        return parent::content(array_merge($args, $data));
    }

    public static function getGroupsAndUsers(?int $groupsID = NULL, string $redirect = "/ranking"): array
    {
        try {
            $groups = Group::getAll();
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            self::redirect("/error");
            exit(0);
        }

        if ($groupsID !== NULL && !in_array($groupsID, array_keys($groups))) {
            self::redirect($redirect);
            exit(0);
        }

        try {
            $users = User::getAll(true, false, NULL, $groupsID);
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            self::redirect("/error");
            exit(0);
        }

        return [$groups, $users];
    }
}