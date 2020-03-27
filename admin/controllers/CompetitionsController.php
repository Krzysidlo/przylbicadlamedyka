<?php

namespace admin\controllers;

use classes\Functions as fs;

class CompetitionsController extends AdminController
{
    public function content(array $args = [])
    {
    	$this->titile = "Turnieje";
        $data = [
            'competitions'          => fs::getCompetitions(),
            'availableCompetitions' => fs::getAvailableCompetitions(),
        ];

        return parent::content(array_merge($args, $data));
    }
}