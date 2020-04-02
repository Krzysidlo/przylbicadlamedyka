<?php

namespace classes;

use DateTime;
use Exception;
use classes\Functions as fs;

class Request extends Action
{
    public User     $user;
    public string   $latLng;
    public ?int     $bascinet;
    public ?int     $material;
    public ?string  $comments;
    public bool     $frozen;
    public bool     $delivered;
    public DateTime $created_at;

    /**
     * Request constructor.
     * @param int $requestsID
     *
     * @throws Exception
     */
    public function __construct(int $requestsID)
    {
        parent::__construct("requests");
        $info = false;

        $sql = "SELECT * FROM `requests` WHERE `id` = '{$requestsID}'";

        if ($query = fs::$mysqli->query($sql)) {
            $info = $query->fetch_assoc() ?? false;
        }

        if ($info) {
            $this->id         = intval($info['id']);
            $this->user       = new User($info['users_id']);
            $this->latLng     = trim($info['latLng'] ?? $this->user->getAddress()->location);
            $this->bascinet   = !empty($info['bascinet']) ? intval($info['bascinet']) : NULL;
            $this->material   = !empty($info['material']) ? intval($info['material']) : NULL;
            $this->comments   = !empty($info['comments']) ? trim($info['comments']) : NULL;
            $this->delivered  = (bool)$info['delivered'];
            $this->created_at = new DateTime($info['created_at']);
            $this->frozen     = Frozen::findByRequestID($this->id);
        } else {
            throw new Exception("No request info found with id=[{$requestsID}]");
        }
    }

    /**
     * @param string $usersID
     * @param string|null $latLng
     * @param int|null $bascinet
     * @param int|null $material
     * @param string|null $comments
     *
     * @return array
     * @throws Exception
     */
    public static function create(string $usersID, ?string $latLng = NULL, ?int $bascinet = NULL, ?int $material = NULL, ?string $comments = NULL): array
    {
        if ($latLng === NULL) {
            $latLng = 'NULL';
        } else {
            $latLng = "'{$latLng}'";
        }
        if ($bascinet === NULL) {
            $bascinet = 'NULL';
        }
        if ($material === NULL) {
            $material = 'NULL';
        }
        if ($comments === NULL || $comments === "") {
            $comments = 'NULL';
        } else {
            $comments = "'{$comments}'";
        }

        $sql = "INSERT INTO `requests` (`users_id`, `latLng`, `bascinet`, `material`, `comments`) VALUES ('{$usersID}', {$latLng}, {$bascinet}, {$material}, {$comments});";

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

        if ($data['success']) {
            $requestsID = fs::$mysqli->insert_id;
            $sql        = "SELECT `created_at` FROM `requests` WHERE `id`={$requestsID};";
            $date       = fs::$mysqli->query($sql)->fetch_row()[0] ?? NULL;
            if ($date !== NULL) {
                $date    = new DateTime($date);
                $message = "";
                if ($bascinet !== "NULL") {
                    $message = "Zgłoszono {$bascinet} gotowych przyłbic do odbioru";
                } else {
                    if ($material !== "NULL") {
                        $message = "Zgłoszono zapotrzebowanie na {$material} materiału";
                    }
                }
                Activity::create($usersID, $date, $message, "action", $requestsID);
            }
        }

        return $data;
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
        $sql      = "SELECT `id` FROM `requests`";
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

    public static function count(?string $usersID = NULL, string $type = "delivered"): int
    {
        $return = 0;
        switch ($type) {
            case "material":
                $sql = "SELECT SUM(`material`) FROM `requests` WHERE `deleted` = 0";
                if ($usersID !== NULL) {
                    $sql .= " AND `users_id` = '{$usersID}'";
                }
                $sql .= ";";
                break;
            case "ready":
                $sql = "SELECT SUM(`bascinet`) FROM `requests` WHERE `delivered` = 0 AND `deleted` = 0";
                if ($usersID !== NULL) {
                    $sql .= " AND `users_id` = '{$usersID}'";
                }
                $sql .= ";";
                break;
            case "delivered":
                $sql = "SELECT SUM(`bascinet`) FROM `requests` WHERE `delivered` = 1 AND `deleted` = 0";
                if ($usersID !== NULL) {
                    $sql .= " AND `users_id` = '{$usersID}'";
                }
                $sql .= ";";
                break;
            default:
                $sql = "SELECT 0";
                break;
        }

        if ($query = fs::$mysqli->query($sql)) {
            $return = intval($query->fetch_row()[0] ?? 0);
        }

        return $return;
    }

    public static function getIdsByUserID(string $usersID): array
    {
        $return = ['bascinet' => [], 'material' => []];
        $sql    = "SELECT r.`id`, r.`bascinet`, r.`material` FROM `requests` r LEFT JOIN `frozen` f ON r.`id` = f.`requests_id` WHERE r.`users_id` = '{$usersID}' AND r.`delivered` = 0 AND r.`deleted` = 0 AND f.`requests_id` IS NULL;";
        if ($query = fs::$mysqli->query($sql)) {
            while ($result = $query->fetch_assoc()) {
                if ($result['bascinet'] !== NULL) {
                    $return['bascinet'][] = $result['id'];
                }
                if ($result['material'] !== NULL) {
                    $return['material'][] = $result['id'];
                }
            }
        }

        return $return;
    }
}