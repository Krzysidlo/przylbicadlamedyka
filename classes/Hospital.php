<?php

namespace classes;

use DateTime;
use Exception;
use classes\Functions as fs;

class Hospital extends Action
{
    public string   $name;
    public string   $latLng;
    public DateTime $created_at;

    /**
     * Hospital constructor.
     * @param int $hospitalID
     *
     * @throws Exception
     */
    public function __construct(int $hospitalID)
    {
        parent::__construct("requests");
        $info = false;

        $sql = "SELECT * FROM `hospitals` WHERE `id` = '{$hospitalID}'";

        if ($query = fs::$mysqli->query($sql)) {
            $info = $query->fetch_assoc() ?? false;
        }

        if ($info) {
            $this->id         = intval($info['id']);
            $this->name       = (string)$info['name'];
            $this->latLng     = trim((string)$info['location']);
            $this->created_at = new DateTime($info['created_at']);
        } else {
            throw new Exception("No hospital info found with id=[{$hospitalID}]");
        }
    }

    /**
     * @param string $name
     * @param string $latLng
     *
     * @return array
     */
    public static function create(string $name, string $latLng): array
    {
        $sql = "INSERT INTO `hospitals` (`name`, `latLng`) VALUES ('{$name}', {$latLng});";

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

        $return = [];
        if ($query = fs::$mysqli->query($sql)) {
            while ($result = $query->fetch_row()) {
                $requestsID = $result[0] ?? false;
                if ($requestsID) {
                    $return[] = new self($requestsID);
                }
            }
        }

        return $return;
    }

    public function delete(bool $delete = true): bool
    {
        return parent::delete(false);
    }
}