<?php

namespace controllers;

use Exception;
use classes\User;
use classes\Frozen;
use classes\Request;
use classes\Activity;
use classes\Functions as fs;

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
            } else {
                if ($row->frozen !== NULL) {
                    $dataId   = "data-id='{$row->frozen->id}'";
                    $dataType = "data-type='frozen'";
                    $button = true;
                }
            }
            $button       = ($button ? "<a href='/ajax/map/delete' class='btn btn-transparent m-0 cancel' {$dataId} {$dataType}>Anuluj</a>" : "");
            $activities[] = <<< HTML
            <div class="activityBox {$type} container">
                <div class="content row">
                    <div class="text col-9">{$text}</div>
                    <div class="button col-3">{$button}</div>
                    <div class="date">{$date}</div>
                </div>
            </div>
HTML;

        }

        $data = [
            'activities' => $activities,
            'material'   => Request::count(USER_ID, "material"),
            'ready'      => Request::count(USER_ID, "ready"),
            'delivered'  => Request::count(USER_ID, "delivered"),
        ];

        return parent::content(array_merge($args, $data));
    }
}
