<?php

namespace controllers;

class IndexController extends PageController
{
    public function content(array $args = [])
    {
        $data = [];
        return parent::content(array_merge($args, $data));
    }
}
