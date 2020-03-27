<?php

namespace admin\controllers;

class DefaultController extends AdminController
{
    public function content(array $args = [])
    {
        $data = [];
        return parent::content(array_merge($args, $data));
    }
}