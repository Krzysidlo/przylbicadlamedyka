<?php

namespace classes;

use DateTime;
use Exception;
use classes\Functions as fs;

class Frozen
{
    public int      $id;
    public User     $user;
    public DateTime $date;
    public Request  $request;
    public ?int     $bascinet;
    public ?int     $material;
    public bool     $delivered;
    public DateTime $created_at;

    /**
     * Frozen constructor.
     * @param int $frozenID
     *
     * @throws Exception
     */
    public function __construct(int $frozenID)
    {
        $info = false;

        $sql = "SELECT * FROM `frozen` WHERE `id` = '{$frozenID}'";

        if ($query = fs::$mysqli->query($sql)) {
            $info = $query->fetch_assoc() ?? false;
        }

        if ($info) {
            $this->id         = intval($info['id']);
            $this->request = new Request(intval($info['frozen']));
            $this->user       = new User($info['users_id']);
            $this->bascinet   = !empty($info['bascinet']) ? intval($info['bascinet']) : NULL;
            $this->material   = !empty($info['material']) ? intval($info['material']) : NULL;
            $this->delivered  = (bool)$info['delivered'];
            $this->created_at = new DateTime($info['created_at']);
        } else {
            throw new Exception("No request info found with id=[{$frozenID}]");
        }
    }

    /**
     * @param string|null $usersID
     * @param bool $delivered
     * @param bool $deleted
     *
     * @return array
     * @throws Exception
     */
    public static function getAll(?string $usersID = NULL, bool $delivered = true, bool $deleted = false): array
    {
        $sql      = "SELECT `id` FROM `frozen`";
        $whereAnd = "WHERE";
        if ($usersID !== NULL) {
            $sql      .= " WHERE `users_id` = '{$usersID}'";
            $whereAnd = "AND";
        }
        if (!$delivered) {
            $sql      .= " $whereAnd `delivered` = 0";
            $whereAnd = "AND";
        }
        if (!$deleted) {
            $sql .= " $whereAnd `deleted` = 0";
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

    public static function create(string $usersID, int $requestsID, DateTime $date, ?int $bascinet = NULL, ?int $material = NULL): array
    {
        $requestedQuantity = self::checkRequestedQuantity($requestsID);
        if ($bascinet === NULL && $material === NULL) {
            return [
                'success' => false,
                'alert'   => "warning",
                'message' => "Proszę podać ilość",
            ];
        }
        if (!$requestedQuantity) {
            return [
                'success' => false,
                'alert'   => "warning",
                'message' => "Nie można zamrozić tego zamówienia. Proszę odświeżyć stronę i spróbowac ponownie.",
            ];
        } else {
            if ($bascinet !== NULL && $bascinet > $requestedQuantity['bascinet']) {
                return [
                    'success' => false,
                    'alert'   => "warning",
                    'message' => "Podano zbyt dużą ilość. Można podać maksimum {$requestedQuantity['bascinet']}",
                ];
            }
            if ($material !== NULL && $material > $requestedQuantity['material']) {
                return [
                    'success' => false,
                    'alert'   => "warning",
                    'message' => "Podano zbyt dużą ilość. Można podać maksimum {$requestedQuantity['material']}",
                ];
            }
        }

        $date = $date->format("Y-m-d H:i:s");

        if ($bascinet === NULL) {
            $bascinet = 'NULL';
        }
        if ($material === NULL) {
            $material = 'NULL';
        }

        $sql = "INSERT INTO `requests` (`users_id`, `date`, `requests_id`, `bascinet`, `material`) VALUES ('{$usersID}', '{$date}', {$requestsID}, {$bascinet}, {$material});";

        if (!!fs::$mysqli->query($sql)) {
            $data = [
                'success' => true,
                'alert'   => "success",
                'message' => "Poprawnie utworzono zgłoszenie",
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

    public static function checkRequestedQuantity(int $requestsID)
    {
        $frozen = NULL;

        $sql = "SELECT `bascinet`, `material` FROM `frozen` WHERE `requests_id` = {$requestsID};";

        if ($query = fs::$mysqli->query($sql)) {
            $frozen = $query->fetch_assoc();
        }

        $sql = "SELECT `bascinet`, `material` FROM `requests` WHERE `id` = '{$requestsID}';";

        $requested = NULL;
        if ($query = fs::$mysqli->query($sql)) {
            $requested = $query->fetch_assoc();
        }

        if (!empty($requested)) {
            if (!empty($frozen)) {
                $bascinet = $requested['bascinet'] - $frozen['bascinet'];
                $material = $requested['material'] - $frozen['material'];
                return [
                    'bascinet' => $bascinet,
                    'material' => $material,
                ];
            } else {
                return $requested;
            }
        } else {
            return false;
        }
    }
}