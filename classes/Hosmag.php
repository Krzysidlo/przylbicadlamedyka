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

    public static function create(int $pinsID, int $quantity, string $usersID = NULL): array
    {
        if ($usersID === NULL) {
            $user = new User();
            $usersID = $user->id;
        }
        $sql = "INSERT INTO `hos_mag` (`pins_id`, `users_id`, `quantity`) VALUES ({$pinsID}, '{$usersID}', {$quantity});";

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
        $sql       = "SELECT f.`id` FROM (SELECT * FROM `frozen` WHERE `users_id` = '{$usersID}' AND `delivered` = 0 AND `deleted` = 0) f LEFT JOIN `requests` r ON f.`requests_id` = r.`id` WHERE r.`deleted` = 0 AND r.`material` IS NULL;";
        $frozenArr = [];

        if ($query = fs::$mysqli->query($sql)) {
            while ($result = $query->fetch_assoc()) {
                $frozenArr[] = filter_var($result['id'], FILTER_SANITIZE_NUMBER_INT);
            }
        } else {
            return false;
        }

        try {
            $frozenIDs = implode(",", $frozenArr);
            $frozen  = new Frozen($frozenIDs);
            $success = $frozen->deliver();
        } catch (Exception $e) {
            fs::log("Error: " . $e->getMessage());
            $success = false;
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

    public static function count(string $usersID, $type = "material"): int
    {
        $return = 0;

        switch ($type) {
            case 'material':
                $sql = "SELECT SUM(h.`quantity`) FROM `hos_mag` h LEFT JOIN `pins` p ON h.`pins_id` = p.`id` WHERE h.`users_id` = '{$usersID}' AND h.`deleted` = 0 AND p.`type` = 'magazine' AND p.`deleted` = 0";

                if ($query = fs::$mysqli->query($sql)) {
                    $collectedMaterial = intval($query->fetch_row()[0] ?? 0);
                    $delivredMaterial  = Frozen::count($usersID, "material");
                    $return = $collectedMaterial - $delivredMaterial;
                    if ($return < 0) {
                        $return = 0;
                    }
                }
                break;
            case 'bascinet':
                $sql = "SELECT SUM(h.`quantity`) FROM `hos_mag` h LEFT JOIN `pins` p ON h.`pins_id` = p.`id` WHERE h.`users_id` = '{$usersID}' AND h.`deleted` = 0 AND p.`type` = 'hospital' AND p.`deleted` = 0";

                if ($query = fs::$mysqli->query($sql)) {
                    $return = intval($query->fetch_row()[0] ?? 0);
                }
                break;
        }

        return $return;
    }

    public function delete(): bool
    {
        $sql = "UPDATE `pins` SET `deleted` = 1 WHERE `id` = {$this->id};";

        return !!fs::$mysqli->query($sql);
    }
}