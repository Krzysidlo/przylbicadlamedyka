<?php

namespace classes;

use DateTime;
use Exception;
use classes\Functions as fs;

class Frozen extends Action
{
    public User     $user;
    public DateTime $date;
    public Request  $request;
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
        parent::__construct("frozen");
        $info = false;

        $sql = "SELECT * FROM `frozen` WHERE `id` = '{$frozenID}'";

        if ($query = fs::$mysqli->query($sql)) {
            $info = $query->fetch_assoc() ?? false;
        }

        if ($info) {
            $this->id         = intval($info['id']);
            $this->request    = new Request(intval($info['requests_id']));
            $this->user       = new User($info['users_id']);
            $this->date       = new DateTime($info['date']);
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

    public static function create(string $usersID, array $requestsArr, DateTime $date, string $action = "both", string $producerID): array
    {
        if (empty($requestsArr) || (empty($requestsArr['bascinet']) && empty($requestsArr['material']))) {
            return [
                'success' => false,
                'alert'   => "danger",
                'message' => "Błąd przy zapisie do bazy danych. Proszę odświeżyć stronę i spróbować ponownie.",
            ];
        }

//        $requestedQuantity = self::checkRequestedQuantity($requestsArr);

//        if (!$requestedQuantity) {
//            return [
//                'success' => false,
//                'alert'   => "warning",
//                'message' => "Nie można zamrozić tego zamówienia. Proszę odświeżyć stronę i spróbowac ponownie.",
//            ];
//        } else {
//            if ($requestedQuantity['bascinet'] <= 0 && $requestedQuantity['material'] <= 0) {
//                return [
//                    'success' => false,
//                    'alert'   => "warning",
//                    'message' => "Wygląda na to, że to zgłoszenie zostało już zamrożone",
//                ];
//            } else {
        $dbDate = $date->format("Y-m-d H:i");

        $success          = true;
        $bascinetQuantity = 0;
        $materialQuantity = 0;
        switch ($action) {
            case 'bascinet':
            case 'both':
                if (!empty($requestsArr['bascinet'])) {
                    foreach ($requestsArr['bascinet'] as $requestsID) {
                        if ($query = fs::$mysqli->query("SELECT 1 FROM `frozen` WHERE `requests_id` = {$requestsID};")) {
                            if (!empty($query->fetch_row()[0])) {
                                $success = false;
                                continue;
                            }
                        }
                        $success &= fs::$mysqli->query("INSERT INTO `frozen` (`users_id`, `date`, `requests_id`) VALUES ('{$usersID}', '{$dbDate}', '{$requestsID}');");
                        if ($success) {
                            $bascinetQuantity += intval(fs::$mysqli->query("SELECT `bascinet` FROM `requests` WHERE `id` = {$requestsID}")->fetch_row()[0] ?? 0);
                        }
                    }
                }
            case 'material':
                if ($action !== "bascinet" && !empty($requestsArr['material'])) {
                    foreach ($requestsArr['material'] as $requestsID) {
                        if ($query = fs::$mysqli->query("SELECT 1 FROM `frozen` WHERE `requests_id` = {$requestsID};")) {
                            if (!empty($query->fetch_row()[0])) {
                                $success = false;
                                continue;
                            }
                        }
                        $success &= fs::$mysqli->query("INSERT INTO `frozen` (`users_id`, `date`, `requests_id`) VALUES ('{$usersID}', '{$dbDate}', '{$requestsID}');");
                        if ($success) {
                            $materialQuantity += intval(fs::$mysqli->query("SELECT `material` FROM `requests` WHERE `id` = {$requestsID}")->fetch_row()[0] ?? 0);
                        }
                    }
                }
                break;
        }
//            }
//        }

        if ($success) {
            try {
                $activityDate = new DateTime();
                $user         = new User($usersID);
                $frozenDate   = $date->format("H:i - d.m.Y");
                switch ($action) {
                    case 'bascinet':
                    case 'both':
                        $message = "<span class='name'>{$user->name}</span> (tel. {$user->tel}) odbierze od Ciebie <span class='quantity'>{$bascinetQuantity}</span> przyłbic o <span class='delivery'>{$frozenDate}</span>";
                        Activity::create($producerID, $activityDate, $message, "notification");
                    case 'material':
                        if ($action !== "bascinet") {
                            $message = "<span class='name'>{$user->name}</span> (tel. {$user->tel}) dostarczy Ci <span class='quantity'>{$materialQuantity}</span> materiału o <span class='delivery'>{$frozenDate}</span>";
                            Activity::create($producerID, $activityDate, $message, "notification");
                        }
                        break;
                }
            } catch (Exception $e) {
                fs::log("Error: " . $e->getMessage());
            }
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

//    public static function checkRequestedQuantity(array $requestsArr)
//    {
//        if (empty($requestsArr) || (empty($requestsArr['bascinet']) && empty($requestsArr['material']))) {
//            return false;
//        }
//
//        if (!empty($requestsArr['bascinet'])) {
//            $requestsID = array_shift($requestsArr['bascinet']);
//            $sql        = "SELECT `bascinet`, `material` FROM `frozen` WHERE `requests_id` = {$requestsID}";
//            foreach ($requestsArr['bascinet'] as $requestsID) {
//                $sql .= " OR `requests_id` = {$requestsID}";
//            }
//            $sql .= ";";
//        } else {
//            $requestsID = array_shift($requestsArr['material']);
//            $sql        = "SELECT `bascinet`, `material` FROM `frozen` WHERE `requests_id` = {$requestsID}";
//            foreach ($requestsArr['material'] as $requestsID) {
//                $sql .= " OR `requests_id` = {$requestsID}";
//            }
//            $sql .= ";";
//        }
//
//        $frozen = NULL;
//        if ($query = fs::$mysqli->query($sql)) {
//            while ($result = $query->fetch_assoc()) {
//                isset($frozen['bascinet']) ? $frozen['bascinet'] += intval($result['bascinet']) : $frozen['bascinet'] = intval($result['bascinet']);
//                isset($frozen['material']) ? $frozen['material'] += intval($result['material']) : $frozen['material'] = intval($result['material']);
//            }
//        }
//
//
//        if (!empty($requestsArr['bascinet'])) {
//            $requestsID = array_shift($requestsArr['bascinet']);
//            $sql        = "SELECT `bascinet`, `material` FROM `requests` WHERE `id` = {$requestsID}";
//            foreach ($requestsArr['bascinet'] as $requestsID) {
//                $sql .= " OR `requests_id` = {$requestsID}";
//            }
//            $sql .= ";";
//        } else {
//            $requestsID = array_shift($requestsArr['material']);
//            $sql        = "SELECT `bascinet`, `material` FROM `requests` WHERE `id` = {$requestsID}";
//            foreach ($requestsArr['material'] as $requestsID) {
//                $sql .= " OR `requests_id` = {$requestsID}";
//            }
//            $sql .= ";";
//        }
//
//        $requested = NULL;
//        if ($query = fs::$mysqli->query($sql)) {
//            while ($result = $query->fetch_assoc()) {
//                isset($requested['bascinet']) ? $requested['bascinet'] += intval($result['bascinet']) : $requested['bascinet'] = intval($result['bascinet']);
//                isset($requested['material']) ? $requested['material'] += intval($result['material']) : $requested['material'] = intval($result['material']);
//            }
//        }
//
//        if (!empty($requested)) {
//            if (!empty($frozen)) {
//                $bascinet = $requested['bascinet'] - $frozen['bascinet'];
//                $material = $requested['material'] - $frozen['material'];
//                return [
//                    'bascinet' => $bascinet,
//                    'material' => $material,
//                ];
//            } else {
//                return $requested;
//            }
//        } else {
//            return false;
//        }
//    }

    public static function count(string $usersID, string $type = "delivered"): int
    {
        $return = 0;
        switch ($type) {
//            case "material":
//                $sql = "SELECT SUM(`material`) FROM `requests` WHERE `users_id` = '{$usersID}' AND `deleted` = 0;";
//                break;
//            case "ready":
//                $sql = "SELECT SUM(`bascinet`) FROM `requests` WHERE `users_id` = '{$usersID}' AND `delivered` = 0 AND `deleted` = 0;";
//                break;
//            case "delivered":
//                $sql = "SELECT SUM(`bascinet`) FROM `requests` WHERE `users_id` = '{$usersID}' AND `delivered` = 1 AND `deleted` = 0;";
//                break;
            case "trips":
                $sql = "SELECT count(f.`id`) FROM `frozen` f LEFT JOIN `requests` r ON f.`requests_id` = r.`id` WHERE f.`users_id` = '{$usersID}' AND f.`delivered` = 0 AND f.`deleted` = 0 GROUP BY r.`users_id`;";
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

    public static function findByRequestID(int $requestsID): bool
    {
        $sql = "SELECT 1 FROM `frozen` WHERE `requests_id` = {$requestsID} AND `deleted` = 0";
        if ($query = fs::$mysqli->query($sql)) {
            return $query->fetch_row()[0] ?? false;
        }

        return false;
    }
}