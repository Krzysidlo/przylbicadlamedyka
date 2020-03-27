<?php

namespace controllers;

use classes\Functions as fs;

class ChangeController extends PageController
{
    public function content(array $args = [])
    {
        if (isset($_POST['competition'])) {
            fs::setOption('competition', $_POST['competition']);
            header('Location: /');
            exit(0);
        }

        $data = [];

        $data['competitions'] = fs::getCompetitions();

        return parent::content(array_merge($args, $data));
    }
}