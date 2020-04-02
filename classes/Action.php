<?php

namespace classes;

use classes\Functions as fs;

abstract class Action
{
    public int $id;

    protected string $table;

    function __construct(string $table)
    {
        $this->table = $table;
    }
}