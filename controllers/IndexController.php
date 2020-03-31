<?php

namespace controllers;

use Exception;
use classes\User;
use classes\Frozen;
use classes\Request;
use classes\Activity;

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

            $type   = $row->type;
            $text   = $row->message;
            $date   = $row->date->format("d.m.Y - H:i");
            $button = "";
            if ($row->request !== NULL) {
                if ($row->request->frozen === NULL) {
                    $button = "<button class=\"btn btn-transparent my-0 cancel right\">Anuluj</button>";
                }
            }
            $dataId       = ($row->request !== NULL ? "data-id='{$row->request->id}'" : "");
            $activities[] = <<< HTML
            <div class="activityBox {$type}" {$dataId}>
                <div class="content">
                    <div class="text">{$text}</div>
                    {$button}
                    <div class="date">{$date}</div>
                </div>
            </div>
HTML;

        }

        $data = [
            'activities' => $activities,
            'material' => Request::count(USER_ID, "material"),
            'ready' => Request::count(USER_ID, "ready"),
            'delivered' => Request::count(USER_ID, "delivered"),
        ];

        return parent::content(array_merge($args, $data));
    }
}
