<?php

namespace classes;

use DateTime;
use Exception;
use classes\Functions as fs;

class Request
{
    public int      $id;
    public User     $user;
    public string   $latLng;
    public ?int     $bascinet;
    public ?int     $material;
    public ?string  $comments;
    public ?Frozen  $frozen;
    public bool     $delivered;
    public DateTime $created_at;

    /**
     * Request constructor.
     * @param int $requestID
     *
     * @throws Exception
     */
    public function __construct(int $requestID)
    {
        $info = false;

        $sql = "SELECT * FROM `requests` WHERE `id` = '{$requestID}'";

        if ($query = fs::$mysqli->query($sql)) {
            $info = $query->fetch_assoc() ?? false;
        }

        if ($info) {
            $this->id         = intval($info['id']);
            $this->user       = new User($info['users_id']);
            $this->latLng     = trim($info['latLng'] ?? $this->user->getAddress()->location);
            $this->bascinet   = !empty($info['bascinet']) ? intval($info['bascinet']) : NULL;
            $this->material   = !empty($info['material']) ? intval($info['material']) : NULL;
            $this->comments   = (!empty($info['comments']) ? trim($info['comments']) : NULL);
            $this->delivered  = (bool)$info['delivered'];
            $this->created_at = new DateTime($info['created_at']);

            $frozen = !empty($info['frozen']) ? intval($info['frozen']) : NULL;
            if ($frozen === NULL) {
                $this->frozen = NULL;
            } else {
                $this->frozen = new Frozen($frozen);
            }
        } else {
            throw new Exception("No request info found with id=[{$requestID}]");
        }
    }

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
        if ($comments === NULL) {
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
                'message' => "Błąd przy zapisie do bazy danych. Proszę spróbować ponownie.",
            ];
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
                $requestID = $result[0] ?? false;
                if ($requestID) {
                    $return[] = new self($requestID);
                }
            }
        }

        return $return;
    }
}