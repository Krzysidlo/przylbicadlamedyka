<?php

namespace classes;

use DateTime;
use Exception;
use classes\Functions as fs;

abstract class Point
{
    protected static string $table;

    public int      $id;
    public string   $name;
    public string   $description;
    public string   $latLng;
    public DateTime $created_at;

    /**
     * Point constructor.
     * @param string $table
     * @param int $id
     *
     * @throws Exception
     */
    public function __construct(string $table, int $id)
    {
        self::$table = filter_var($table, FILTER_SANITIZE_STRING);
        $table       = self::$table;
        $this->id    = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $info        = false;

        $sql = "SELECT * FROM `{$table}` WHERE `id` = '{$this->id}'";

        if ($query = fs::$mysqli->query($sql)) {
            $info = $query->fetch_assoc() ?? false;
        }

        if ($info) {
            $this->name        = trim((string)$info['name']);
            $this->description = trim((string)$info['description']);
            $this->latLng      = trim((string)$info['location']);
            $this->created_at  = new DateTime($info['created_at']);
        } else {
            $table = self::$table;
            throw new Exception("No [{$table}] info found with id=[{$this->id}]");
        }
    }

    /**
     * @param string $name
     * @param string $description
     * @param string $latLng
     *
     * @return array
     */
    public static function create(string $name, string $description, string $latLng): array
    {
        $table = self::$table;
        $sql   = "INSERT INTO `{$table}` (`name`, `description`, `latLng`) VALUES ('{$name}', '{$description}', {$latLng});";

        if (!!fs::$mysqli->query($sql)) {
            $data = [
                'success' => true,
                'alert'   => "success",
                'message' => "Poprawnie dodano nowy szpital",
            ];
        } else {
            $data = [
                'success' => false,
                'alert'   => "danger",
                'message' => "Błąd przy zapisie do bazy danych. Proszę odświeżyć stronę i spróbować ponownie.",
            ];
        }

        return $data;
    }

    public static function getAllPoints(string $sql): array
    {
        $class = get_called_class();
        $return = [];
        if ($query = fs::$mysqli->query($sql)) {
            while ($result = $query->fetch_row()) {
                $id = $result[0] ?? false;
                $id = intval($id);
                if ($id) {
                    $return[] = new $class($id);
                }
            }
        }

        return $return;
    }

    public function delete(): bool
    {
        $table = self::$table;
        $sql   = "UPDATE `{$table}` SET `deleted` = 1 WHERE `id` = {$this->id};";

        return !!fs::$mysqli->query($sql);
    }
}