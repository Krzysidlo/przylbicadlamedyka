<?php

namespace controllers;

class DefaultController extends PageController
{
    public function content(array $args = [])
    {
        $data = [];
        return parent::content(array_merge($args, $data));
    }
}