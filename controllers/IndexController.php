<?php

namespace controllers;

use Exception;
use classes\User;
use classes\Frozen;
use classes\Hosmag;
use classes\Request;
use classes\Activity;

class IndexController extends PageController
{
    public function content(array $args = [])
    {
        try {
            $rows = Activity::getAll(USER_ID);
        } catch (Exception $e) {
            $rows = [];
        }

        $activities = [];

        foreach ($rows as $row) {
            $type     = $row->type;
            $text     = $row->message;
            $date     = $row->date->format("d.m.Y - H:i");
            $button   = false;
            $dataId   = "";
            $dataType = "";

            if ($row->request !== NULL) {
                $dataId   = "data-id='{$row->request->id}'";
                $dataType = "data-type='request'";
                if (!$row->request->frozen) {
                    $button = true;
                }
            } else if ($row->hosMag !== NULL) {
                $dataId   = "data-id='{$row->hosMag->id}'";
                $dataType = "data-type='hosMag'";
                if (!$row->hosMag->collected) {
                    $button = true;
                }
            }
            $button       = ($button ? "<div class='button col-3'><a href='/ajax/map/delete' class='btn btn-transparent m-0 cancel' {$dataId} {$dataType}>Anuluj</a></div>" : "");
            $activities[] = <<< HTML
            <div class="activityBox {$type} container">
                <div class="content row">
                    <div class="text col">{$text}</div>
                    {$button}
                    <div class="date col-12">{$date}</div>
                </div>
            </div>
HTML;
        }

        $data = [
            'activities' => [],
            'material'   => 0,
            'bascinet'   => 0,
            'deliveredM' => 0,
            'deliveredB' => 0,
            'ready'      => 0,
            'delivered'  => 0,
        ];

        if (USER_PRV === User::USER_PRODUCER) {
            $data = [
                'activities' => $activities,
                'material'   => Request::count(USER_ID, "material"),
                'ready'      => Request::count(USER_ID, "ready"),
                'delivered'  => Request::count(USER_ID, "delivered"),
            ];
        } else if (USER_PRV === User::USER_DRIVER) {
            $data = [
                'activities' => $activities,
                'material'   => Hosmag::count(USER_ID, "material"),
                'bascinet'   => Frozen::count(USER_ID, "bascinet"),
                'deliveredM' => Frozen::count(USER_ID, "material"),
                'deliveredB' => Hosmag::count(USER_ID, "bascinet"),
            ];
        }

        $user = new User;

        $data['userPrv'] = $user->getPrivilege();

        return parent::content(array_merge($args, $data));
    }
}
