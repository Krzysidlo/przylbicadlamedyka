<?php

namespace admin\controllers;

use controllers\PageController;

class DefaultController extends PageController
{
    public function content(array $args = [])
    {
        $data = [];
        return parent::content(array_merge($args, $data));
    }
}