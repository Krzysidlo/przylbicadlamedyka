<?php

namespace classes;

class dateClass
{
    private $timestamp;

    function __construct(string $timestamp = NULL)
    {
        if (!empty($timestamp)) {
            $this->timestamp = $timestamp;
        } else {
            $this->timestamp = time();
        }
    }

    public function format(string $format = "Y-m-d", int $timestamp = NULL)
    {
        $return = NULL;
        if (!empty($timestamp)) {
            $return = date($format, $timestamp);
        } else {
            $return = date($format, $this->timestamp);
        }

        return $return;
    }
}