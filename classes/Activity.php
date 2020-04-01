<?php

namespace classes;

use DateTime;
use Exception;
use classes\Functions as fs;

class Activity extends Action
{
    public int      $id;
    public User     $user;
    public string   $type;
    public ?Request $request;
    public DateTime $date;
    public string   $message;

    /**
     * Activity constructor.
     * @param int $activityID
     *
     * @throws Exception
     */
    public function __construct(int $activityID)
    {
        parent::__construct("activities");
        $info = false;

        $sql = "SELECT * FROM `activities` WHERE `id` = '{$activityID}'";

        if ($query = fs::$mysqli->query($sql)) {
            $info = $query->fetch_assoc() ?? false;
        }

        if ($info) {
            $this->id      = intval($info['id']);
            $this->user    = new User($info['users_id']);
            $this->type    = $info['type'];
            $this->date    = new DateTime($info['date']);
            $this->message = base64_decode($info['message']);

            if (!empty($info['requests_id'])) {
                $this->request = new Request($info['requests_id']);
            } else {
                $this->request = NULL;
            }

            if (!empty($info['frozen_id'])) {
                $this->frozen = new Frozen($info['frozen_id']);
            } else {
                $this->frozen = NULL;
            }
        } else {
            throw new Exception("No activity info found with id=[{$activityID}]");
        }
    }

    public static function create(string $usersID, DateTime $date, string $message, string $type = "action", ?int $requestsID = NULL): array
    {
        if ($requestsID === NULL) {
            $requestsID = "NULL";
        }
        $date = $date->format("Y-m-d H:i:s");

        $message = base64_encode($message);

        $sql = "INSERT INTO `activities` (`users_id`, `type`, `requests_id`, `date`, `message`) VALUES ('{$usersID}', '{$type}', {$requestsID}, '{$date}', '{$message}');";

        if (fs::$mysqli->query($sql)) {
            $data = [
                'success' => true,
                'alert'   => "success",
                'message' => "Poprawnie dodano aktywność",
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
     * @param string|null $usersID
     * @param bool $delivered
     * @param bool $deleted
     *
     * @return array
     * @throws Exception
     */
    public static function getAll(?string $usersID = NULL): array
    {
        $sql = "SELECT `id` FROM `activities` WHERE `deleted` = 0";
        if ($usersID !== NULL) {
            $sql .= " AND `users_id` = '{$usersID}'";
        }

        $sql .= " ORDER BY date DESC;";

        $return = [];
        if ($query = fs::$mysqli->query($sql)) {
            while ($result = $query->fetch_row()) {
                $activityID = $result[0] ?? false;
                if ($activityID) {
                    $return[] = new self($activityID);
                }
            }
        }

        return $return;
    }
}