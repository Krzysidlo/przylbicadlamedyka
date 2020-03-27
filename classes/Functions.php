<?php

namespace classes;

use mysqli;

class Functions
{
    public static ?mysqli $mysqli;

    public static array $privilegeRoles = [];

    private static string $logFile;
    private static bool   $logging;

    public static function init()
    {
        global $mysqli;

        self::$logFile        = "log.txt";
        self::$logging        = true;
        self::$mysqli         = $mysqli ?? NULL;
        self::$privilegeRoles = [
            0 => "",
            1 => "",
            2 => "",
            3 => "",
            4 => "",
            5 => "Root",
        ];
    }

    public static function setLogFile(string $fileName)
    {
        if (self::$logging) {
            ini_set("log_errors", 1);
        }
        ini_set("error_log", LOG_DIR . "/" . $fileName);
        self::$logFile = $fileName;
    }

    public static function getNotifications(int $limit = 0, string $usersID = NULL)
    {
        $new           = 0;
        $notifications = [];

        if ($usersID === NULL) {
            $usersID = USER_ID;
        }

        $sql = "SELECT * FROM `notifications` WHERE `users_id` = '{$usersID}' ORDER BY `created_at` DESC";
        if ($limit > 0) {
            $sql .= " LIMIT {$limit}";
        }
        $sql .= ";";
        if ($query = self::$mysqli->query($sql)) {
            while ($result = $query->fetch_assoc()) {
                self::$mysqli->query("UPDATE `notifications` SET `nd` = 0 WHERE `id` = {$result['id']};");
                $notifications[$result['id']] = $result;
                if ($result['new']) {
                    $new++;
                }
            }
        }

        return [$new, $notifications];
    }

    public static function getACookie(string $name)
    {
        if (isset($_COOKIE[$name])) {
            return json_decode($_COOKIE[$name]);
        }
        return false;
    }

    public static function setACookie(string $name, $value, float $milliseconds = 60 * 60 * 24, string $path = "/")
    {
        return setcookie($name, json_encode($value), time() + $milliseconds, $path);
    }

    public static function log(...$values)
    {
        foreach ($values as $value) {
            if (!is_string($value)) {
                ob_start();
                var_dump($value);
                $value = ob_get_clean();
            }
            if (self::$logging) {
                $logFile = LOG_DIR . '/' . self::$logFile;
                if (!file_exists($logFile) || is_writable($logFile)) {
                    file_put_contents($logFile, date("Y-m-d H:i:s") . ": " . $value . "\n", FILE_APPEND);
                } else {
                    file_put_contents(LOG_DIR . "/error.log", date("Y-m-d H:i:s") . ": Error: No access to file {$logFile}\n", FILE_APPEND);
                    file_put_contents(LOG_DIR . "/error.log", date("Y-m-d H:i:s") . ": " . $value . "\n", FILE_APPEND);
                }
                if (DEV_MODE) {
                    print($value . "\n");
                }
            }
        }
    }

    public static function setLogging(bool $flag)
    {
        self::$logging = $flag;
    }
}
