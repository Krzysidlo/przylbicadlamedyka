<?php

namespace admin\controllers;

use classes\Functions as fs;

class IndexController extends AdminController
{
    public function content(array $args = [])
    {
        $data = [];

        return parent::content(array_merge($args, $data));
    }
}