<?php

namespace classes;

use DateTime;
use Exception;
use classes\Functions as fs;

class Hosmag
{
    public int      $id;
    public Pin      $pin;
    public int      $quantity;
    public DateTime $created_at;

    /**
     * Hosmag constructor.
     * @param int $id
     *
     * @throws Exception
     */
    public function __construct(int $id)
    {
        $this->id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $info     = false;

        $sql = "SELECT * FROM `hos_mag` WHERE `id` = '{$this->id}';";

        if ($query = fs::$mysqli->query($sql)) {
            $info = $query->fetch_assoc() ?? false;
        }

        if ($info) {
            $this->pin        = new Pin(intval($info['pins_id']));
            $this->quantity   = intval($info['quantity']);
            $this->created_at = new DateTime($info['created_at']);
        } else {
            throw new Exception("No pins info found with id=[{$this->id}]");
        }
    }

    public static function create(int $pinsID, int $quantity): array
    {
        $sql = "INSERT INTO `hos_mag` (`pins_id`, `quantity`) VALUES ({$pinsID}, {$quantity});";

        if (!!fs::$mysqli->query($sql)) {
            return [
                'success' => true,
                'alert'   => "success",
                'message' => "Poprawnie zarejestrowano wpis",
            ];
        } else {
            return [
                'success' => false,
                'alert'   => "danger",
                'message' => "Błąd przy zapisie do bazy danych. Proszę odświeżyć stronę i spróbować ponownie.",
            ];
        }
    }

    public static function deliverBascinet(string $usersID): bool
    {
        $success = true;
        $sql     = "SELECT f.`id` FROM `requests` r LEFT JOIN (SELECT * FROM `frozen` WHERE `users_id` = '{$usersID}' AND `delivered` = 0 AND `deleted` = 0) f ON r.`id` = f.`requests_id` WHERE r.`delivered` = 0 AND r.`deleted` = 0 AND r.`material` IS NULL;";
        if ($query = fs::$mysqli->query($sql)) {
            while ($result = $query->fetch_assoc()) {
                try {
                    $frozenID = filter_var($result['id'], FILTER_SANITIZE_NUMBER_INT);
                    $frozen   = new Frozen($frozenID);
                    $success  &= $frozen->deliver();
                } catch (Exception $e) {
                    fs::log("Error: " . $e->getMessage());
                    $success = false;
                }
            }
        } else {
            return false;
        }
        return $success;
    }

    /**
     * @param bool $deleted
     *
     * @return array
     * @throws Exception
     */
    public static function getAll(bool $deleted = false): array
    {
        $return = [];

        $sql = "SELECT `id` FROM `hos_mag`";

        if (!$deleted) {
            $sql .= " WHERE `deleted` = 0;";
        }

        if ($query = fs::$mysqli->query($sql)) {
            while ($result = $query->fetch_row()) {
                $id = $result[0] ?? false;
                $id = intval($id);
                if ($id) {
                    $return[] = new self($id);
                }
            }
        }

        return $return;
    }

    public function delete(): bool
    {
        $sql = "UPDATE `pins` SET `deleted` = 1 WHERE `id` = {$this->id};";

        return !!fs::$mysqli->query($sql);
    }
}