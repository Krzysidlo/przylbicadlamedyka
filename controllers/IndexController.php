<?php

namespace controllers;

use Exception;
use classes\User;
use classes\Frozen;
use classes\Request;

class IndexController extends PageController
{
    public function content(array $args = [])
    {
        switch (USER_PRV) {
            case User::USER_NO_ACCESS:
                self::redirect("/error");
                break;
        }

        try {
            $requests = Request::getAll(USER_ID);
        } catch (Exception $e) {
            $requests = [];
        }
        try {
            $frozen = Frozen::getAll(USER_ID);
        } catch (Exception $e) {
            $frozen = [];
        }

        $data = array_merge($requests, $frozen);
        usort($data, fn($a, $b) => strcmp($b->created_at->format("Y-m-d H:i:s"), $a->created_at->format("Y-m-d H:i:s")));

        $activities = [];

        $data = [
            'activities' => $activities,
        ];

        return parent::content(array_merge($args, $data));
    }
}
