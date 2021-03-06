<?php

namespace classes;

use DateTime;
use Exception;
use classes\Functions as fs;

class Frozen
{
    public array    $id;
    public string   $requests;
    public User     $driver;
    public User     $producer;
    public DateTime $date;
    public int      $bascinet = 0;
    public int      $material = 0;

    /**
     * Frozen constructor.
     * @param $arrayID
     * @throws Exception
     */
    public function __construct($arrayID)
    {
        $info = false;

        if (is_array($arrayID)) {
            $frozenIDs = filter_var(array_shift($arrayID), FILTER_SANITIZE_STRING);
            $info      = $arrayID;
        } else {
            if (is_numeric($arrayID)) {
                $frozenID = intval($arrayID);

                $sql = "SELECT f.`id`, f.`users_id` AS `driver`, r.`users_id` AS `producer`, f.`date`, r.`id` AS `requests`, r.`bascinet`, r.`material` FROM `frozen` f LEFT JOIN `requests` r ON f.`requests_id` = r.`id` WHERE f.`id` = {$frozenID};";
            } else {
                $sql = "SELECT GROUP_CONCAT(DISTINCT f.`id` SEPARATOR ',') AS `id`, GROUP_CONCAT(DISTINCT f.`users_id`) AS `driver`,  GROUP_CONCAT(DISTINCT r.`users_id`) AS `producer`, f.`date`, GROUP_CONCAT(DISTINCT r.`id` SEPARATOR ',') AS `requests`, SUM(r.`bascinet`) AS `bascinet`, SUM(r.`material`) AS `material` FROM `frozen` f LEFT JOIN `requests` r ON f.`requests_id` = r.`id` WHERE f.`id` IN ({$arrayID}) GROUP BY f.`date`;";
            }
            if ($query = fs::$mysqli->query($sql)) {
                $info = $query->fetch_assoc() ?? false;
            }
            $frozenIDs = $info['id'];
        }

        if ($info) {
            $this->id       = explode(",", $frozenIDs);
            $this->requests = (string)$info['requests'];
            $this->driver   = new User($info['driver']);
            $this->producer = new User($info['producer']);
            $this->date     = new DateTime($info['date']);
            $this->bascinet = intval($info['bascinet']);
            $this->material = intval($info['material']);
        } else {
            if (is_array($arrayID)) {
                $stringArr = implode(", ", $arrayID);
            } else {
                $stringArr = $arrayID;
            }
            throw new Exception("No request info found with id in [{$stringArr}]");
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
    public static function getAll(?string $usersID = NULL, bool $delivered = false, bool $deleted = false): array
    {
        $sql      = "SELECT GROUP_CONCAT(DISTINCT f.`id` SEPARATOR ',') AS `ids`, GROUP_CONCAT(DISTINCT f.`users_id`) AS `driver`,  GROUP_CONCAT(DISTINCT r.`users_id`) AS `producer`, f.`date`, GROUP_CONCAT(DISTINCT r.`id` SEPARATOR ',') AS `requests`, SUM(r.`bascinet`) AS `bascinet`, SUM(r.`material`) AS `material` FROM `frozen` f LEFT JOIN `requests` r ON f.`requests_id` = r.`id`";
        $whereAnd = "WHERE";
        if ($usersID !== NULL) {
            $sql      .= " WHERE f.`users_id` = '{$usersID}'";
            $whereAnd = "AND";
        }
        if (!$delivered) {
            $sql      .= " $whereAnd f.`delivered` = 0";
            $whereAnd = "AND";
        }
        if (!$deleted) {
            $sql .= " $whereAnd f.`deleted` = 0";
        }

        $sql .= " GROUP BY f.`date`, r.`users_id` ORDER BY f.`date`;";

        $return = [];
        if ($query = fs::$mysqli->query($sql)) {
            while ($result = $query->fetch_assoc()) {
                $return[] = new self($result);
            }
        }

        return $return;
    }

    public static function create(string $driverID, array $requestsArr, DateTime $date, string $action = "both", string $producerID): array
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
        $frozenIDS        = [];
        switch ($action) {
            case 'bascinet':
            case 'both':
                if (!empty($requestsArr['bascinet'])) {
                    foreach ($requestsArr['bascinet'] as $requestsID) {
                        if ($query = fs::$mysqli->query("SELECT 1 FROM `frozen` WHERE `requests_id` = {$requestsID} AND `deleted` = 0;")) {
                            if (!empty($result = $query->fetch_row()) && $result[0] == 1) {
                                $success = false;
                                var_dump("EXISTS", "SELECT 1 FROM `frozen` WHERE `requests_id` = {$requestsID} AND `deleted` = 0;");
                                continue;
                            }
                        }
                        $success     &= fs::$mysqli->query("INSERT INTO `frozen` (`users_id`, `date`, `requests_id`) VALUES ('{$driverID}', '{$dbDate}', '{$requestsID}');");
                        $frozenIDS[] = fs::$mysqli->insert_id;
                        if ($success) {
                            $bascinetQuantity += intval(fs::$mysqli->query("SELECT `bascinet` FROM `requests` WHERE `id` = {$requestsID}")->fetch_row()[0] ?? 0);
                        }
                    }
                }
            case 'material':
                if ($action !== "bascinet" && !empty($requestsArr['material'])) {
                    foreach ($requestsArr['material'] as $requestsID) {
                        if ($query = fs::$mysqli->query("SELECT 1 FROM `frozen` WHERE `requests_id` = {$requestsID} AND `deleted` = 0;")) {
                            if (!empty($result = $query->fetch_row()) && $result[0] == 1) {
                                $success = false;
                                continue;
                            }
                        }
                        $success     &= fs::$mysqli->query("INSERT INTO `frozen` (`users_id`, `date`, `requests_id`) VALUES ('{$driverID}', '{$dbDate}', '{$requestsID}');");
                        $frozenIDS[] = fs::$mysqli->insert_id;
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
                $driver       = new User($driverID);
                $producer     = new User($producerID);
                $frozenDate   = $date->format("H:i - d.m.Y");
                $frozenIDS    = implode(",", $frozenIDS);
                switch ($action) {
                    case 'bascinet':
                        $message = "<span class='name'>{$driver->name}</span> (tel. <a href='tel:{$driver->tel}'>{$driver->tel}</a>) odbierze od Ciebie <span class='quantity'>{$bascinetQuantity}</span> przyłbic około <span class='delivery'>{$frozenDate}</span>";
                        Activity::create($producerID, $activityDate, $message, "notification", NULL, $frozenIDS);
                        $message = "Zadeklarowano odbiór <span class='quantity'>{$bascinetQuantity}</span> przyłbic od <span class='name'>{$producer->name}</span>  (tel. <a href='tel:{$producer->tel}'>{$producer->tel}</a>) o <span class='delivery'>{$frozenDate}</span>";
                        Activity::create($driverID, $activityDate, $message, "action", NULL, $frozenIDS);
                        break;
                    case 'material':
                        $message = "<span class='name'>{$driver->name}</span> (tel. <a href='tel:{$driver->tel}'>{$driver->tel}</a>) dostarczy Ci <span class='quantity'>{$materialQuantity}</span> sztuk materiału około <span class='delivery'>{$frozenDate}</span>";
                        Activity::create($producerID, $activityDate, $message, "notification", NULL, $frozenIDS);
                        $message = "Zadeklarowano dostarczenie <span class='quantity'>{$materialQuantity}</span> sztuk materiału do <span class='name'>{$producer->name}</span>  (tel. <a href='tel:{$producer->tel}'>{$producer->tel}</a>) o <span class='delivery'>{$frozenDate}</span>";
                        Activity::create($driverID, $activityDate, $message, "action", NULL, $frozenIDS);
                        break;
                    case 'both':
                        $message = "<span class='name'>{$driver->name}</span> (tel. <a href='tel:{$driver->tel}'>{$driver->tel}</a>) odbierze od Ciebie <span class='quantity'>{$bascinetQuantity}</span> przyłbic oraz dostarczy Ci <span class='quantity'>{$materialQuantity}</span> sztuk materiału około <span class='delivery'>{$frozenDate}</span>";
                        Activity::create($producerID, $activityDate, $message, "notification", NULL, $frozenIDS);
                        $message = "Zadeklarowano odbiór <span class='quantity'>{$bascinetQuantity}</span> przyłbic oraz dostarczenie <span class='quantity'>{$materialQuantity}</span> sztuk materiału do <span class='name'>{$producer->name}</span>  (tel. <a href='tel:{$producer->tel}'>{$producer->tel}</a>) o <span class='delivery'>{$frozenDate}</span>";
                        Activity::create($driverID, $activityDate, $message, "action", NULL, $frozenIDS);
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

    public static function count(string $usersID, string $type = "delivered"): int
    {
        $return = 0;

        $sql = NULL;
        switch ($type) {
            case "bascinet":
                $sql = "SELECT SUM(r.`bascinet`) FROM (SELECT * FROM `frozen` WHERE `users_id` = '{$usersID}' AND `delivered` = 0 AND `deleted` = 0) f LEFT JOIN `requests` r ON f.`requests_id` = r.`id` WHERE r.`delivered` = 1 AND r.`deleted` = 0;";

                if ($query = fs::$mysqli->query($sql)) {
                    $return = intval($query->fetch_row()[0] ?? 0);
                }

                $active = Activity::count($usersID);

                $return = $return - $active;
                break;
            case "trips":
                $sql = "SELECT COUNT(DISTINCT f.`date`) FROM `frozen` f LEFT JOIN `requests` r ON f.`requests_id` = r.`id` WHERE f.`users_id` = '{$usersID}' AND r.`delivered` = 0 AND f.`deleted` = 0 GROUP BY r.`users_id`;";
            case "material":
                if ($sql === NULL) {
                    $sql = "SELECT SUM(r.`material`) FROM (SELECT * FROM `frozen` WHERE `users_id` = '{$usersID}' AND `delivered` = 1 AND `deleted` = 0) f LEFT JOIN `requests` r ON f.`requests_id` = r.`id` WHERE r.`deleted` = 0;";
                }

                if ($query = fs::$mysqli->query($sql)) {
                    $return = intval($query->fetch_row()[0] ?? 0);
                }
                break;
        }

        return $return;
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

    public static function findByRequestID(int $requestsID): bool
    {
        $sql = "SELECT 1 FROM `frozen` WHERE `requests_id` = {$requestsID} AND `deleted` = 0";
        if ($query = fs::$mysqli->query($sql)) {
            return $query->fetch_row()[0] ?? false;
        }

        return false;
    }

    public function deliver(): bool
    {
        $success = true;

        foreach ($this->id as $frozenID) {
            if (!$success) {
                break;
            }

            $success &= !!fs::$mysqli->query("UPDATE `frozen` SET `delivered` = 1 WHERE `id` = {$frozenID};");
        }

        return $success;
    }

    public function delete(bool $activity = true): bool
    {
        $id  = implode(",", $this->id);
        $sql = "UPDATE `frozen` SET `deleted` = 1 WHERE `id` IN ({$id});";

        $success = !!fs::$mysqli->query($sql);

        if ($success && $activity) {
            $sql     = "UPDATE `activities` SET `deleted` = 1 WHERE '{$id}' LIKE CONCAT('%', `frozen_id`, '%');";
            $success &= !!fs::$mysqli->query($sql);
        }

        return $success;
    }
}