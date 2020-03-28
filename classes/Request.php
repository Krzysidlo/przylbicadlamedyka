<?php

namespace classes;

use Exception;
use classes\Functions as fs;

class Request
{
    public string     $id;
    public User       $user;
    public string     $latLng;
    public int        $bascinet;
    public int        $material;
    public ?string    $comments;
    public bool       $frozen;
    public string     $delivered;

    /**
     * Request constructor.
     * @param string $usersID
     *
     * @throws Exception
     */
    public function __construct(string $usersID)
    {
        $this->id = $usersID;
        if ($info = $this->getMapInfo()) {
            $this->user      = new User($this->id);
            $this->latLng    = trim($info['latLng'] ?? $this->user->address);
            $this->bascinet  = intval($info['bascinet']);
            $this->material  = intval($info['material']);
            $this->comments  = ($info['comments'] !== NULL ? trim($info['comments']) : NULL);
            $this->frozen    = (bool)$info['frozen'];
            $this->delivered = (string)$info['delivered'];
        } else {
            throw new Exception("No request info found with id=[{$this->id}]");
        }
    }

    private function getMapInfo()
    {
        $return = false;

        $sql = "SELECT `users_id`, `latLng`, `bascinet`, `material`, `comments`, `frozen`, `delivered` FROM `requests` WHERE `users_id` = '{$this->id}' AND `delivered` <> 1 AND `deleted` = 0;";

        if ($query = fs::$mysqli->query($sql)) {
            $return = $query->fetch_assoc() ?? false;
        }

        return $return;
    }

    public static function getAll(): array
    {
        $return = [];

        $sql = "SELECT `users_id` FROM `requests` WHERE `delivered` <> 1 AND `deleted` = 0";

        if ($query = fs::$mysqli->query($sql)) {
            while ($result = $query->fetch_row()) {
                $usersID = $result[0] ?? false;
                if ($usersID) {
                    $return[] = new self($usersID);
                }
            }
        }

        return $return;
    }
}