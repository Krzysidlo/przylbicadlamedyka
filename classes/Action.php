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

    public function delete(): bool
    {
        $sql = "UPDATE `{$this->table}` SET `deleted` = 1 WHERE `id` = '{$this->id}';";

        $success = !!fs::$mysqli->query($sql);

        if ($success) {
            $sql = "UPDATE `activities` SET `deleted` = 1 WHERE (`requests_id` = {$this->id} AND `frozen_id` IS NULL) OR (`requests_id` IS NULL AND `frozen_id` = {$this->id});";
            $success &= !!fs::$mysqli->query($sql);
        }

        return $success;
    }
}