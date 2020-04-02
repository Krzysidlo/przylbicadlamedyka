<?php

namespace classes;

use Exception;
use classes\Functions as fs;

class Hospital extends Point
{
    protected static string $table = "hospitals";

    /**
     * Hospital constructor.
     * @param int $hospitalID
     *
     * @throws Exception
     */
    public function __construct(int $hospitalID)
    {
        parent::__construct(self::$table, $hospitalID);
    }

    /**
     * @param bool $deleted
     *
     * @return array
     * @throws Exception
     */
    public static function getAll(bool $deleted = false): array
    {
        $sql = "SELECT `id` FROM `hospitals`";

        if (!$deleted) {
            $sql .= " WHERE `deleted` = 0";
        }

        $sql .= ";";

        return parent::getAllPoints($sql);
    }
}