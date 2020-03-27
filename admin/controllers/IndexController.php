<?php

namespace admin\controllers;

use classes\Functions as fs;

class IndexController extends AdminController
{
    public function content(array $args = [])
    {
    	$this->titile = "Mecze";
    	$this->menu   = "events";
        $data = [];
        $data['competitions'] = fs::getCompetitions();

        $this->view = "change";

        return parent::content(array_merge($args, $data));
    }
}