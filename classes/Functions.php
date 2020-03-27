<?php

namespace classes;

use mysqli;
use DateTime;
use stdClass;
use Exception;
use DateTimeZone;
use controllers\PushController;

class Functions
{
    public static string $lang;
    public static mysqli $mysqli;
    public static array $langArr;

    public static array $privilegeRoles;
    public static array $countryCodes;

    private static array $defaultEmpty;
    private static stdClass $ipInfo;
    private static string $logFile;
    private static bool $logging;

    public static function init()
    {
        global $mysqli, $lang, $langArr;

        self::$logFile            = "log.txt";
        self::$logging            = true;
        self::$lang               = $lang ?? NULL;
        self::$langArr            = $langArr ?? NULL;
        self::$mysqli             = $mysqli ?? NULL;
        self::$privilegeRoles     = [
            0 => self::t('Red card'),
            1 => self::t('Not confirmed'),
            2 => self::t('Confirmed'),
            3 => self::t('Summary access'),
            4 => self::t('Check for errors'),
            5 => self::t('Root'),
        ];
        self::$countryCodes       = (include CONF_DIR . "/countryCodes.php");

        self::$defaultEmpty = ['avatar'];
    }

    public static function t(string $string)
    {
        $translationFile = TRANS_DIR . "/" . self::$lang . "/index.php";
        if (!file_exists($translationFile)) {
            self::$lang = 'en';
        }
        $translationFile = TRANS_DIR . "/" . self::$lang . "/index.php";

        $translations = (include $translationFile);

        if (!empty($translations) && is_array($translations)) {
            return !empty($translations[$string]) ? $translations[$string] : $string;
        } else {
            return $string;
        }
    }

    public static function setLogFile(string $fileName)
    {
        if (self::$logging) {
            ini_set("log_errors", 1);
        }
        ini_set("error_log", LOG_DIR . "/" . $fileName);
        self::$logFile = $fileName;
    }

    public static function getCountryCode(string $name, bool $lowercase = false)
    {
        if (!empty(self::$countryCodes[$name])) {
            if ($lowercase) {
                $return = strtolower(self::$countryCodes[$name]);
            } else {
                $return = self::$countryCodes[$name];
            }
            return $return;
        } else {
            return "";
        }
    }

    public static function getDbData(bool $thisEventDay = false, string $status = NULL, bool $onlyNull = false, int $competitionsID = NULL)
    {
        $outputEvents = [];

        $eventDay       = $_GET['eventday'] ?? NULL;
        $competitionsID = $competitionsID ?? COMPETITION_ID;

        if ($eventDay === NULL) {
            $sql = "SELECT max(eventday) AS eventday FROM `events` WHERE `status` = 'FINISHED' AND `competitions_id` = {$competitionsID};";
            if ($query = self::$mysqli->query($sql)) {
                $eventDay = $query->fetch_assoc()['eventday'];
                if (empty($eventDay)) {
                    $eventDay = 1;
                }
            }
        }
        $eventDay = (int)$eventDay;

        $infoToSelect = [
            'e.`id`',
            'e.`date`',
            'e.`status`',
            'e.`eventday`',
            'e.`homeTeamName`',
            'e.`awayTeamName`',
            'r.`homeTeam`',
            'r.`awayTeam`',
            'r.`HT_homeTeam`',
            'r.`HT_awayTeam`',
            'r.`ET_homeTeam`',
            'r.`ET_awayTeam`',
            'r.`PS_homeTeam`',
            'r.`PS_awayTeam`',
        ];
        $infoToSelect = implode(",", $infoToSelect);

        $dbDataQuery = "SELECT {$infoToSelect} FROM `events` e LEFT JOIN results r ON e.`id` = r.`events_id` WHERE e.`competitions_id` = {$competitionsID}";

        if ($thisEventDay && $eventDay !== NULL) {
            $dbDataQuery .= " AND e.`eventday` = {$eventDay}";
        }

        $dbDataQuery .= " ORDER BY e.`date`;";

        if ($dbDataQuery = self::$mysqli->query($dbDataQuery)) {
            while ($dbData = $dbDataQuery->fetch_assoc()) {
                $event = new stdClass;

                foreach ($dbData as $name => $value) {
                    switch ($name) {
                        case 'homeTeam':
                        case 'awayTeam':
                            if ($value !== NULL) {
                                $value = (int)$value;
                            }
                            if (empty($event->result)) {
                                $event->result = new stdClass;
                            }
                            $event->result->{$name} = $value;
                            break;
                        case 'HT_homeTeam':
                        case 'HT_awayTeam':
                        case 'ET_homeTeam':
                        case 'ET_awayTeam':
                        case 'PS_homeTeam':
                        case 'PS_awayTeam':
                            if ($value !== NULL) {
                                $value = (int)$value;
                            }

                            if (empty($event->result->{substr($name, 0, 2)})) {
                                $event->result->{substr($name, 0, 2)} = new stdClass;
                            }
                            $event->result->{substr($name, 0, 2)}->{substr($name, 3)} = $value;
                            break;
                        case 'eventday':
                            $event->{$name} = (int)$value;
                            break;
                        default:
                            $event->{$name} = $value;
                            break;
                    }
                }

                if (empty($status) || $event->status === $status) {
                    $outputEvents[$event->id] = $event;
                }
            }
        }

        foreach ($outputEvents as $id => &$event) {
            $sql = "SELECT `home_team`, `away_team`, `score`, `joker` FROM `predictions` WHERE `events_id` = '{$id}'";

            if (empty($status)) {
                $sql .= " AND `users_id` = '" . USER_ID . "'";
            }

            if ($onlyNull) {
                $sql .= " AND `score` IS NULL";
            }

            $sql .= ";";

            $event->user           = new stdClass;
            $event->user->homeTeam = false;
            $event->user->awayTeam = false;
            $event->user->score    = false;
            $event->user->joker    = false;
            $event->user->bet      = false;
            if ($query = self::$mysqli->query($sql)) {
                if (!empty($result = $query->fetch_assoc())) {
                    $event->user->homeTeam = ($result['home_team'] === NULL ? NULL : (int)$result['home_team']);
                    $event->user->awayTeam = ($result['away_team'] === NULL ? NULL : (int)$result['away_team']);
                    $event->user->score    = (int)$result['score'];
                    $event->user->joker    = (int)$result['joker'] ? "joker" : "";
                    $event->user->bet      = (($result['away_team'] !== NULL || $result['away_team'] !== NULL) ?: false);
                }
            }

            if ($onlyNull && $event->user->homeTeam === false) {
                unset($outputEvents[$id]);
            }
        }
        unset($event);

        $eventDaysNum = 1;
        if ($thisEventDay) {
            $sql = "SELECT max(eventday) as eventDaysNum FROM `events` WHERE `competitions_id` = {$competitionsID};";
            if ($query = self::$mysqli->query($sql)) {
                $eventDaysNum = (int)($query->fetch_object()->eventDaysNum);
            }
        } else {
            $eventDaysNum = !empty($outputEvents) ? (int)end($outputEvents)->eventday : 4;
        }

        return ['events' => $outputEvents, 'eventDay' => $eventDay, 'eventDaysNum' => $eventDaysNum];
    }

    public static function countPoints(int $competitionsID, string $usersID = NULL)
    {
        if ($usersID === NULL) {
            $success = true;
            foreach (User::getAll(true) as $usersID => $user) {
                $success &= self::countPoints($competitionsID, $usersID);
            }
            return $success;
        } else {
            $points = self::getPoints($usersID, $competitionsID);
            $success = !!$points;
            if ($success) {
                [$score, $perfect] = $points;
                if ($score === NULL || $perfect === NULL) {
                    self::log("Nie ma więcej punktów do podliczenia usersID=[{$usersID}] competitionsID=[{$competitionsID}] lub wystąpił błąd");
                    return true;
                }
                [$prevScore, $prevPosition] = Ranking::getOldRanking($usersID, $competitionsID);

                //Update ranking_old with current ranking
                self::$mysqli->query("INSERT INTO `ranking_old` SELECT * FROM `ranking` WHERE `users_id` = '{$usersID}' AND `competitions_id` = {$competitionsID} ON DUPLICATE KEY UPDATE `score` = VALUES(score), `perfect` = VALUES(perfect), `last_five` = VALUES(last_five), `position` = VALUES(position);");
                [$currentScore, $currentPerfect] = (self::$mysqli->query("SELECT `score`, `perfect` FROM `ranking_old` WHERE `users_id` = '{$usersID}' AND `competitions_id` = {$competitionsID};")->fetch_row()) ?? [0, 0];

                $newScore   = $currentScore + $score;
                $newPerfect = $currentPerfect + $perfect;
                $lastFive   = self::getLastEventsScore($usersID, $competitionsID, 5);
                //Update current ranking
                self::$mysqli->query("INSERT INTO `ranking` (`users_id`, `competitions_id`, `score`, `perfect`, `last_five`) VALUES ('{$usersID}', {$competitionsID}, {$newScore}, {$newPerfect}, {$lastFive}) ON DUPLICATE KEY UPDATE `score` = VALUES(score), `perfect` = VALUES(perfect), `last_five` = VALUES(last_five);");
                self::log("Podliczono punkty dla usersID=[{$usersID}] competitionsID=[{$competitionsID}]");
            } else {
                self::log("Error: Wystąpił błąd podczas liczenia punktów usersID=[{$usersID}] competitionsID=[{$competitionsID}]");
            }

            if ($success && $query = self::$mysqli->query("SELECT `users_id` FROM `ranking` WHERE `competitions_id` = {$competitionsID} ORDER BY `score` DESC, `perfect` DESC;")) {
                $position = 1;
                while ($result = $query->fetch_row()) {
                    self::$mysqli->query("UPDATE `ranking` SET `position` = {$position} WHERE `users_id` = '{$result[0]}' AND `competitions_id` = {$competitionsID};");
                    $position++;
                }
            }
            if (strlen(self::$mysqli->error)) {
                self::$mysqli->query("INSERT INTO `cron_errors` (`name`, `value`) VALUES ('" . basename(__FILE__) . "', 'error');");
                self::log("Error:");
                self::log(self::$mysqli->error);
            }
        }
        return true;
    }

    public static function getPoints(string $usersID, int $competitionsID)
    {
        [$events] = Event::getAll(false, false, "FINISHED", $competitionsID);
        if (empty($events)) {
            return [NULL, NULL];
        }
        $newScore    = false;
        $userScore   = 0;
        $userPerfect = 0;
        $success     = true;
        foreach ($events as $eventsID => &$event) {
            $sql = "SELECT `home_team`, `away_team` FROM `predictions` WHERE `users_id` = '{$usersID}' AND `events_id` = '{$eventsID}' AND `score` IS NULL;";
            if ($query = self::$mysqli->query($sql)) {
                $prediction = $query->fetch_assoc();
                if ($prediction === NULL) {
                    continue;
                }
                $countScore = self::countScore($prediction, $event->result);
                $success &= !!$countScore;
                if (!$success) {
                    break;
                }
                [$score, $perfect] = $countScore;

                $userScore   += intval($score);
                $userPerfect += intval($perfect);
                $newScore    = true;
                self::$mysqli->query("UPDATE `predictions` SET `score` = {$score} WHERE `events_id` = '{$eventsID}' AND `users_id` = '{$usersID}';");
            }
        }
        return $success ? ($newScore ? [$userScore, $userPerfect] : [NULL, NULL]) : false;
    }

    public static function countScore($prediction, $result)
    {
    	if (empty($prediction) || empty($result)) {
    	    return false;
    	}

        $res  = [
            'h' => intval($result->homeTeam['FT']),
            'a' => intval($result->awayTeam['FT']),
        ];
        $pred = [
            'h' => intval($prediction['home_team']),
            'a' => intval($prediction['away_team']),
        ];

        if ($pred['h'] === $res['h'] && $pred['a'] === $res['a']) {
        	return[100, true];
        }

        $resAdvantage  = $res['h'] - $res['a'];
        $predAdvantage = $pred['h'] - $pred['a'];
        if ($predAdvantage === $resAdvantage) {
            return[30, false];
        }

        $resWinner  = ($res['h'] > $res['a'] ? 1 : ($res['h'] < $res['a'] ? 2 : 0));
        $predWinner = ($pred['h'] > $pred['a'] ? 1 : ($pred['h'] < $pred['a'] ? 2 : 0));
        if ($predWinner === $resWinner) {
            return[10, false];
        }

        return[0, false];
    }

    public static function getLastEventsScore(string $emailID = NULL, $competitionsID = NULL, int $limit = 5): int
    {
        if ($limit <= 0) {
            $limit = 5;
        }
        $usersID = $emailID;
        if ($emailID === NULL || filter_var($emailID, FILTER_VALIDATE_EMAIL)) {
            $usersID = self::getUserID($emailID);
        }
        $competitionsID = $competitionsID ?? COMPETITION_ID;

        $sql = "SELECT SUM(score) FROM (SELECT (p.`score`) FROM `predictions` p LEFT JOIN `events` e ON p.`events_id` = e.`id` WHERE p.`users_id` = '{$usersID}' AND p.`competitions_id` = {$competitionsID} ORDER BY e.`date` DESC LIMIT {$limit}) s;";
        return self::$mysqli->query($sql)->fetch_row()[0] ?? 0;
    }

    public static function getEventsIds(bool $thisEventDay = false, string $status = NULL, int $competitionsID = NULL)
    {
        $eventsIds = [];

        if ($competitionsID === NULL) {
            if (!LOGGED_IN) {
                return false;
            }
            $competitionsID = COMPETITION_ID;
        }

        $eventDay = $_GET['eventday'] ?? NULL;//TODO: Wymiślić coś lepszego. Nie fajne takie obczajanie _GET :)
        if ($eventDay === NULL) {
            $eventDay = 1;
            $sql      = "SELECT max(eventday) AS eventday FROM `events` WHERE `status` = 'FINISHED' AND `competitions_id` = {$competitionsID};";
            if ($query = self::$mysqli->query($sql)) {
                $eventDay = (int)($query->fetch_assoc()['eventday'] ?? 1);
            }
        }

        $sql = "SELECT `id` FROM `events` WHERE `competitions_id` = {$competitionsID}";

        if ($thisEventDay && $eventDay !== NULL) {
            $sql .= " AND `eventday` = {$eventDay}";
        }

        if ($status !== NULL) {
            if (in_array($status, ["TIMED", "IN_PLAY", "FINISHED"])) {
                $sql .= " AND `status` = '{$status}'";
            }
        }

        $sql .= " ORDER BY `date`;";
        if ($query = self::$mysqli->query($sql)) {
            while ($result = $query->fetch_assoc()) {
                $eventsIds[] = $result['id'];
            }
        }

        $sql = "SELECT max(eventday) FROM `events` WHERE `competitions_id` = {$competitionsID};";
        if ($query = self::$mysqli->query($sql)) {
            $eventDaysNum = (int)($query->fetch_row()[0] ?? 4);
        }
        $eventDaysNum = $eventDaysNum ?? 4;

        return [$eventsIds, $eventDay, $eventDaysNum];
    }

    public function resetRanking($competitionsID = NULL): bool
    {
    	$success = true;
    	$sql = "UPDATE `predictions` SET `score` = NULL";
    	if ($competitionsID === NULL) {
    	    $success &= self::truncate("ranking", "ranking_old");
    	} else {
    		self::$mysqli->query("DELETE FROM `ranking` WHERE `competitions_id` = {$competitionsID};");
    		self::$mysqli->query("DELETE FROM `ranking_old` WHERE `competitions_id` = {$competitionsID};");
    		$sql .= " WHERE `competitions_id` = {$competitionsID}";
    	}
        if (self::$mysqli->query($sql)) {
            $success &= self::$mysqli->error == "";
        } else {
            $success = false;
            self::log(self::$mysqli->error);
        }
        return $success;
    }

    public static function truncate(...$tables): bool
    {
    	$success = true;
        foreach ($tables as $tableName) {
            $sql = "TRUNCATE TABLE `{$tableName}`;";
            if (self::$mysqli->query($sql)) {
                $success &= self::$mysqli->error == "";
            } else {
                $success = false;
                self::log(self::$mysqli->error);
            }
        }
        return $success;
    }

    public static function getData(int $competitionsID = 0)
    {
        $outputEvents = [];
        $message      = false;

        if (!empty($competitionsID)) {
            if ($result = self::$mysqli->query("SELECT `name`, `feed_competition_id` as v2 FROM `competitions` WHERE `id` = {$competitionsID};")->fetch_assoc()) {
                $v2_id = $result['v2'] ?? 0;
                $name  = $result['name'] ?? "";

                if (!empty($v2_id)) {
                    if ($v2_id === 'test') {
                        $output = self::getFromUrl(ROOT_URL . "/media/testCompetition.json");
                    } else {
                        $output = self::getFromUrl("http://api.football-data.org/v2/competitions/{$v2_id}/matches");
                    }
                    if (empty($output->message)) {
                        $events = $output->matches;

                        $eventDay = 1;
                        foreach ($events as $event) {
                            $ownEvent = new stdClass;

                            $ownEvent->date = $event->utcDate;

                            $ownEvent->status = $event->status;

                            $ownEvent->eventday = $event->matchday ?? $eventDay;
                            $eventDay           = $ownEvent->eventday;

                            $ownEvent->homeTeamName = $event->homeTeam->name;
                            $ownEvent->awayTeamName = $event->awayTeam->name;

                            $ownEvent->result                = new stdClass;
                            $ownEvent->result->goalsHomeTeam = $event->score->fullTime->homeTeam;
                            $ownEvent->result->goalsAwayTeam = $event->score->fullTime->awayTeam;

                            $ownEvent->result->halfTime                = new stdClass;
                            $ownEvent->result->halfTime->goalsHomeTeam = $event->score->halfTime->homeTeam;
                            $ownEvent->result->halfTime->goalsAwayTeam = $event->score->halfTime->awayTeam;

                            $ownEvent->result->extraTime                = new stdClass;
                            $ownEvent->result->extraTime->goalsHomeTeam = $event->score->extraTime->homeTeam;
                            $ownEvent->result->extraTime->goalsAwayTeam = $event->score->extraTime->awayTeam;

                            $ownEvent->result->penaltyShootout                = new stdClass;
                            $ownEvent->result->penaltyShootout->goalsHomeTeam = $event->score->penalties->homeTeam;
                            $ownEvent->result->penaltyShootout->goalsAwayTeam = $event->score->penalties->awayTeam;

                            $ownEvent->status = ($event->status === "SCHEDULED" ? "TIMED" : $event->status);

                            $ownEvent->id = md5($event->utcDate . $event->homeTeam->name . $event->awayTeam->name);

                            $outputEvents[] = $ownEvent;
                        }
                    } else {
                        $message = $output->message;
                    }
                }
            }
        }

        $eventDaysNum = !empty($outputEvents) ? (int)end($outputEvents)->eventday : 4;

        return ['events' => $outputEvents, 'eventDaysNum' => $eventDaysNum, 'message' => $message];
    }

    public static function getFromUrl(string $url)
    {
        $ch = curl_init($url);

        $header   = [];
        $header[] = "X-Response-Control: full";
        $header[] = "X-Auth-Token: c7cfb7a3c22f42fd986faada4c614e9b";

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $output = curl_exec($ch);
        $err    = curl_errno($ch);
        $errmsg = curl_error($ch);

        if (empty($err)) {
            return json_decode($output);
        } else {
            return $errmsg;
        }
    }

    public static function getEvent(string $eventsID)
    {
        $eventsID = filter_var($eventsID, FILTER_SANITIZE_STRING);
        $sql      = "SELECT * FROM `events` WHERE `id` = '{$eventsID}';";
        return self::$mysqli->query($sql)->fetch_assoc() ?? false;
    }

    public static function getAvailableCompetitions(): array
    {
        $return = [];

        if ($query = self::$mysqli->query("SELECT * FROM `competitions_list`")) {
            while ($result = $query->fetch_assoc()) {
                $result['name'] = self::t($result['name']);
                $return[$result['feed_id']] = [
                    'name'       => $result['name'],
                    'start_date' => date("d.m.Y", strtotime($result['start_date'])),
                    'end_date'   => date("d.m.Y", strtotime($result['end_date'])),
                    'feed_id'    => $result['feed_id'],
                ];
            }
        }
        return $return;
    }

    public static function getCompetitions(string $emailID = NULL): array
    {
        // TODO: Na razie na nic to nie wpływa, ale docelowo trzeba będzie sprawdzić, do których turniejów dany użytkownik ma dostęp
        $usersID = $emailID;
        if (filter_var($emailID, FILTER_VALIDATE_EMAIL)) {
            $usersID = self::getUserID($emailID);
        }
        $return = [];

        $types = ["png", "jpg", "jpeg", "gif"];
        if ($query = self::$mysqli->query("SELECT `id`, `name` FROM `competitions`")) {
            while ($result = $query->fetch_assoc()) {
                foreach ($types as $type) {
                    if (file_exists(COMP_DIR . "/{$result['id']}.{$type}")) {
                        $result['picture'] = COMP_URL . "/{$result['id']}.{$type}";
                        break;
                    }
                }
                $result['name'] = self::t($result['name']);
                $return[$result['id']] = $result;
            }
        }

        return $return;
    }

    public static function getCompName(int $competitionsID = NULL, string $emailID = NULL)
    {
        if ($competitionsID === NULL) {
            if ($emailID !== NULL) {
                $competitionsID = self::getOption('competition', $emailID);
            }
        }
        $competitionsID = $competitionsID ?? COMPETITION_ID;
        if ($competitionsID === NULL) {
            return false;
        }

        $return = false;

        $sql = "SELECT `name` FROM `competitions` WHERE `id` = {$competitionsID};";
        if ($query = self::$mysqli->query($sql)) {
            if ($result = $query->fetch_assoc()) {
                $return = self::t($result['name']);
            }
        }

        return $return;
    }

    public static function getRanking(): array
    {
        $sql = "SELECT `users_id`, `score`, `perfect`, `last_five`, `position` FROM `ranking` WHERE `competitions_id` = " . COMPETITION_ID . " ORDER BY `position`;";

        $return = [];
        if ($query = self::$mysqli->query($sql)) {
            while ($result = $query->fetch_assoc()) {
                $result['score_old'] = 0;
                if ($prevScorePos = self::getOldRanking($result['users_id'], COMPETITION_ID)) {
                    [$oldScore, $oldPosition] = $prevScorePos;
                    $result['score_old']    = $oldScore;
                    $result['position_old'] = $oldPosition;
                }
                $return[$result['users_id']] = $result;
                unset($return[$result['users_id']]['users_id']);
            }
        }
        $return['empty'] = [
            'score'        => 0,
            'perfect'      => 0,
            'last_five'    => 0,
            'position'     => 0,
            'score_old'    => 0,
            'position_old' => 0,
        ];

        return $return;
    }

    public static function compareResult(stdClass $result, stdClass $compareObj)
    {
        if ($compareObj->homeTeam === NULL && $compareObj->awayTeam === NULL) {
            return false;
        }

        $homeTeam = $result->homeTeam;
        if ($compareObj->homeTeam !== NULL) {
            $homeTeam = max($result->homeTeam, $compareObj->homeTeam);
        }

        $awayTeam = $result->awayTeam;
        if ($compareObj->awayTeam !== NULL) {
            $awayTeam = max($result->awayTeam, $compareObj->awayTeam);
        }

        return [$homeTeam, $awayTeam];
    }

    public static function mb_ucfirst(string $string, $encoding = NULL): string
    {
        if (!empty($encoding)) {
            $strlen    = mb_strlen($string, $encoding);
            $firstChar = mb_substr($string, 0, 1, $encoding);
            $then      = mb_substr($string, 1, $strlen - 1, $encoding);
            return mb_strtoupper($firstChar, $encoding) . $then;
        } else {
            $strlen    = mb_strlen($string);
            $firstChar = mb_substr($string, 0, 1);
            $then      = mb_substr($string, 1, $strlen - 1);
            return mb_strtoupper($firstChar) . $then;
        }
    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
     * @param string $d Default imageset to use [ 404 | mp | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param bool $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @return String containing either just a URL or a complete image tag
     * @source https://gravatar.com/site/implement/images/php/
     */
    public static function getGravatar(string $email, string $s = "80", string $d = 'mp', string $r = 'g', bool $img = false, array $atts = [])
    {
        $url = 'https://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$s&d=$d&r=$r";
        if ($img) {
            $url = '<img src="' . $url . '"';
            foreach ($atts as $key => $val) {
                $url .= ' ' . $key . '="' . $val . '"';
            }
            $url .= ' />';
        }

        return $url;
    }

    public static function sendNotification(string $content, string $link = NULL, string $emailID = NULL)
    {
        if (empty($content)) {
            return false;
        }

        try {
            $user = new User($emailID);
        } catch (Exception $e) {
            self::log("Error: " . $e->getMessage());
            return false;
        }
        $usersID = $user->id;

        if ($link === NULL) {
            $href = "null";
        } else {
            $href = "'" . $link . "'";
        }

        $sql = "INSERT INTO `notifications` (`users_id`, `content`, `href`) VALUES ('{$usersID}', '{$content}', {$href});";

        self::$mysqli->query($sql);

        $success = (self::$mysqli->affected_rows > 0);

        if ($success) {
            $push    = new PushController();
            $success &= $push->content(['mailID' => $usersID, 'type' => 'notification', 'body' => $content, 'link' => $link]);
        }

        return $success;
    }

    /**
     * Deprecated Make sure there no more uses in code and delete (take under consideration that for page options this is still being used.
     * Maybe just change this function to: getPageOption with only one parameter
     * Function to get user option value
     * @param string|null $optionName name of user option
     * @param string|null $emailID user email or id (optional)
     * @return mixed option value
     */
    public static function getOption(string $optionName, string $emailID = NULL)
    {
        $return  = NULL;
        $usersID = NULL;

        if ($emailID == 'page') {
            if (($query = self::$mysqli->query("SELECT `value` FROM `options_page` WHERE `name` = '{$optionName}';"))->num_rows) {
                while ($result = $query->fetch_assoc()) {
                    $jsonValue = json_decode($result['value']);
                    $return    = $jsonValue !== NULL ? $jsonValue : $result['value'];
                }
            }
        } else {
            if ($emailID !== NULL) {
                $usersID = self::$mysqli->query("SELECT `id` FROM `users` WHERE `email` = '{$emailID}' OR `id` = '{$emailID}';")->fetch_row()[0];
            } else {
                if (LOGGED_IN) {
                    $usersID = USER_ID;
                }
            }
            if ($usersID !== NULL) {
                if (($query = self::$mysqli->query("SELECT `value` FROM `options` WHERE `users_id` = '{$usersID}' AND `name` = '{$optionName}';"))->num_rows) {
                    while ($result = $query->fetch_assoc()) {
                        $jsonValue = json_decode($result['value']);
                        $return    = $jsonValue !== NULL ? $jsonValue : $result['value'];
                    }
                }

                if ($return === NULL || (in_array($optionName, self::$defaultEmpty) && empty($return))) {
                    if (array_key_exists($optionName, DEFAULT_OPTIONS)) {
                        $return = DEFAULT_OPTIONS[$optionName];
                    }
                }
            }
        }

        return $return;
    }

    /**
     * Deprecated Make sure there no more uses in code and delete (take under consideration that for page options this is still being used.
     * Maybe just change this function to: getPageOption with only two parameters
     * Function to add new or update existing user option
     * @param string $optionName
     * @param mixed $optionValue
     * @param string $emailID optional) email or id of user
     * @return bool whether the option was (inserted or updated) or not
     */
    public static function setOption(string $optionName, $optionValue, string $emailID = NULL): bool
    {
        if ($optionName === NULL) {
            return false;
        }

        if ($emailID == 'page') {
            if ($optionValue === NULL) {
                $sql = "DELETE FROM `options_page` WHERE `name` = '{$optionName}';";
                self::$mysqli->query($sql);

                return self::$mysqli->affected_rows > 0;
            }

            $optionValue = (!is_string($optionValue) ? json_encode($optionValue) : $optionValue);

            $sql = "INSERT INTO `options_page` VALUES ('{$optionName}', '{$optionValue}') ON DUPLICATE KEY UPDATE `value` = '{$optionValue}';";
        } else {
            try {
                $usersID = (new User($emailID))->id;
            } catch (Exception $e) {
                self::log("Error: " . $e->getMessage());
            }

            if ($optionValue === NULL) {
                $sql = "DELETE FROM `options` WHERE `users_id` = '{$usersID}' AND `name` = '{$optionName}';";
                self::$mysqli->query($sql);

                return self::$mysqli->affected_rows > 0;
            }

            $optionValue = (!is_string($optionValue) ? json_encode($optionValue) : $optionValue);

            $sql = "INSERT INTO `options` VALUES ('{$usersID}', '{$optionName}', '{$optionValue}') ON DUPLICATE KEY UPDATE `value` = '{$optionValue}';";
        }

        return !!self::$mysqli->query($sql);
    }

    public static function setNextEventPrediction(int $goals, int $team, string $emailID = NULL)
    {
        $usersID   = self::getUserID($emailID);
        $nextEvent = self::nextEventInfo(self::getOption('competition', $usersID));
        if (!$nextEvent) {
            return false;
        }
        $nextEventID   = $nextEvent['id'];
        $nextEventComp = $nextEvent['competitions_id'];

        $homeScore = $awayScore = "NULL";
        $sql       = "SELECT `home_team`, `away_team` FROM `predictions` WHERE `events_id` = '{$nextEventID}' AND `users_id` = '{$usersID}' AND `competitions_id` = {$nextEventComp};";
        if ($query = self::$mysqli->query($sql)) {
            if ($result = $query->fetch_assoc()) {
                $homeScore = $result['home_team'];
                $awayScore = $result['away_team'];
            }
        }

        $homeScore = ($team == HOME_TEAM ? $goals : $homeScore);
        $awayScore = ($team == AWAY_TEAM ? $goals : $awayScore);
        $sql       = "INSERT INTO `predictions` (`events_id`, `users_id`, `competitions_id`, `home_team`, `away_team`) VALUES ('{$nextEventID}', '{$usersID}', {$nextEventComp}, {$homeScore}, {$awayScore}) ON DUPLICATE KEY UPDATE `home_team` = {$homeScore}, `away_team` = {$awayScore};";
        if ($query = self::$mysqli->query($sql)) {
            return true;
        }
        return false;
    }

    public static function nextEventInfo(int $competitionsID = NULL)
    {
        if ($competitionsID === NULL) {
            if (!LOGGED_IN) {
                return false;
            }
            $competitionsID = self::getOption('competition');
        }

        $sql = "SELECT `id`, `competitions_id`, `date`, `homeTeamName`, `awayTeamName` FROM `events` WHERE `competitions_id` = {$competitionsID} ORDER BY `date`;";
        if ($query = self::$mysqli->query($sql)) {
            while ($result = $query->fetch_assoc()) {
                $timezone = self::getClientsTimezone();
                $date     = new DateTime($result['date']);
                $date->setTimezone(new DateTimeZone($timezone));

                $now = date("Y-m-d H:i:s");
                $now = new DateTime($now);
                $now->setTimezone(new DateTimeZone($timezone));

                if ($now > $date) {
                    continue;
                }

                return $result;
            }
        }

        return false;
    }

    public static function getClientsTimezone(string $ip = NULL, string $emailID = NULL): string
    {
    	$ipInfo = self::ipInfo($ip, $emailID);
        return (!empty($ipInfo->timezone) ? $ipInfo->timezone : date_default_timezone_get());
    }

    private static function ipInfo(string $ip = NULL, string $emailID = NULL): stdClass
    {
        if (empty(self::$ipInfo) || isset(self::$ipInfo->error)) {
            if ($ipInfo = self::getOption('ipInfo', $emailID)) {
                self::$ipInfo = $ipInfo;
            } else {
            	self::$ipInfo = new stdClass;
            	if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            		$ip = $_SERVER["REMOTE_ADDR"] ?? NULL;
            	
            		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
            			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            		}
            		if (isset($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
            			$ip = $_SERVER['HTTP_CLIENT_IP'];
            		}
            	}
            	
            	if (filter_var($ip, FILTER_VALIDATE_IP)) {
            		if ($ipInfo = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip))) {
            			foreach ($ipInfo as $name => $value) {
            				//Removing geoplugin_ from attribute names
            				$name            = substr($name, 10);
            				if ($name == "credit") {
            				    continue;
            				}
            				self::$ipInfo->{$name} = $value;
            			}
            		} else {
            			self::$ipInfo->error = "ERROR";
            		}
            	} else {
            		self::$ipInfo->error = "ERROR";
            	}

                if (!isset(self::$ipInfo->error)) {
                    self::setOption('ipInfo', self::$ipInfo, $emailID);
                }
            }
        }
        return self::$ipInfo;
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

    public static function isNotPredictedNextEvent(string $emailID = NULL)
    {
        $usersID = $emailID;
        if ($emailID === NULL || filter_var($emailID, FILTER_VALIDATE_EMAIL)) {
            $usersID = self::getUserID($emailID);
        }

        $competitionsID = self::getOption('competition', $usersID);
        if ($competitionsID === NULL) {
            return false;
        }

        $eventsID = NULL;
        $sql      = "SELECT `id`, `date` FROM `events` WHERE `status` = 'TIMED' AND `competitions_id` = {$competitionsID} ORDER BY `date`;";
        if ($query = self::$mysqli->query($sql)) {
            while ($result = $query->fetch_assoc()) {
                $timezone = self::getClientsTimezone();
                $date     = new DateTime($result['date']);
                $date->setTimezone(new DateTimeZone($timezone));

                $now = date("Y-m-d H:i:s");
                $now = new DateTime($now);
                $now->setTimezone(new DateTimeZone($timezone));

                if ($now > $date) {
                    continue;
                }

                $now->modify('+1 hour');
                if ($now < $date) {
                    continue;
                }
                $eventsID = $result['id'];
                break;
            }
        } else {
            return false;
        }

        if ($eventsID === NULL) {
            return false;
        }

        $return = [
            'home_team' => false,
            'away_team' => false,
        ];
        $sql    = "SELECT `home_team`, `away_team` FROM `predictions` WHERE `users_id` = '{$usersID}' AND `events_id` = '{$eventsID}';";
        if ($result = self::$mysqli->query($sql)->fetch_assoc()) {
            $return = [
                'home_team' => isset($result['home_team']) && is_numeric($result['home_team']) ?: false,
                'away_team' => isset($result['away_team']) && is_numeric($result['away_team']) ?: false,
            ];
        }

        return (!$nextEvent['home_team'] || !$nextEvent['away_team']) ? $eventsID : false;
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

    public static function findUserByHash(string $hash)
    {
        $return = false;

        $hash = filter_var($hash, FILTER_SANITIZE_STRING);
        $sql  = "SELECT `users_id` FROM `options` WHERE `value` = '{$hash}' LIMIT 1;";
        if ($query = self::$mysqli->query($sql)) {
            if ($result = $query->fetch_row()) {
                $usersID = $result[0];
                $return  = self::getUser($usersID);
            }
        }

        return $return;
    }

    public static function log_error(string $message): void
    {
        file_put_contents(ROOT_DIR . "/../logs/error_log.txt", date("d-m-Y H:i:s") . ": " . $message . "\n", FILE_APPEND);
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

    /**
     * Function for transforming base64 string to profile picture
     * @param string $base64_string with picture
     * @param string $usersID (optional) ID of user for which is the profile picture. Default value = current user
     * @return string url to created picture
     */
    public static function base64_to_jpeg(string $base64_string, string $usersID = NULL): string
    {
        if ($usersID === NULL) {
            $usersID = USER_ID;
        }

        if (!file_exists(TMP_DIR)) {
        	mkdir(TMP_DIR);
        }

        $newFilePath = TMP_DIR . "/" . $usersID . ".jpg";
        $newFileUrl  = TMP_URL . "/" . $usersID . ".jpg";

        if ($ifp = fopen($newFilePath, 'wb')) {
            $data = explode(',', $base64_string);

            fwrite($ifp, base64_decode($data[1]));

            fclose($ifp);

            if (!file_exists($newFilePath)) {
                return false;
            }
        } else {
            return false;
        }

        return $newFileUrl;
    }

    public static function transliterateString(string $text): string
    {
        $transliterationTable = ['á' => 'a', 'Á' => 'A', 'à' => 'a', 'À' => 'A', 'ă' => 'a', 'Ă' => 'A', 'â' => 'a', 'Â' => 'A', 'å' => 'a', 'Å' => 'A', 'ã' => 'a', 'Ã' => 'A', 'ą' => 'a',
                                 'Ą' => 'A', 'ā' => 'a', 'Ā' => 'A', 'ä' => 'ae', 'Ä' => 'AE', 'æ' => 'ae', 'Æ' => 'AE', 'ḃ' => 'b', 'Ḃ' => 'B', 'ć' => 'c', 'Ć' => 'C', 'ĉ' => 'c', 'Ĉ' => 'C', 'č' => 'c', 'Č' => 'C',
                                 'ċ' => 'c', 'Ċ' => 'C', 'ç' => 'c', 'Ç' => 'C', 'ď' => 'd', 'Ď' => 'D', 'ḋ' => 'd', 'Ḋ' => 'D', 'đ' => 'd', 'Đ' => 'D', 'ð' => 'dh', 'Ð' => 'Dh', 'é' => 'e', 'É' => 'E', 'è' => 'e',
                                 'È' => 'E', 'ĕ' => 'e', 'Ĕ' => 'E', 'ê' => 'e', 'Ê' => 'E', 'ě' => 'e', 'Ě' => 'E', 'ë' => 'e', 'Ë' => 'E', 'ė' => 'e', 'Ė' => 'E', 'ę' => 'e', 'Ę' => 'E', 'ē' => 'e', 'Ē' => 'E',
                                 'ḟ' => 'f', 'Ḟ' => 'F', 'ƒ' => 'f', 'Ƒ' => 'F', 'ğ' => 'g', 'Ğ' => 'G', 'ĝ' => 'g', 'Ĝ' => 'G', 'ġ' => 'g', 'Ġ' => 'G', 'ģ' => 'g', 'Ģ' => 'G', 'ĥ' => 'h', 'Ĥ' => 'H', 'ħ' => 'h',
                                 'Ħ' => 'H', 'í' => 'i', 'Í' => 'I', 'ì' => 'i', 'Ì' => 'I', 'î' => 'i', 'Î' => 'I', 'ï' => 'i', 'Ï' => 'I', 'ĩ' => 'i', 'Ĩ' => 'I', 'į' => 'i', 'Į' => 'I', 'ī' => 'i', 'Ī' => 'I',
                                 'ĵ' => 'j', 'Ĵ' => 'J', 'ķ' => 'k', 'Ķ' => 'K', 'ĺ' => 'l', 'Ĺ' => 'L', 'ľ' => 'l', 'Ľ' => 'L', 'ļ' => 'l', 'Ļ' => 'L', 'ł' => 'l', 'Ł' => 'L', 'ṁ' => 'm', 'Ṁ' => 'M', 'ń' => 'n',
                                 'Ń' => 'N', 'ň' => 'n', 'Ň' => 'N', 'ñ' => 'n', 'Ñ' => 'N', 'ņ' => 'n', 'Ņ' => 'N', 'ó' => 'o', 'Ó' => 'O', 'ò' => 'o', 'Ò' => 'O', 'ô' => 'o', 'Ô' => 'O', 'ő' => 'o', 'Ő' => 'O',
                                 'õ' => 'o', 'Õ' => 'O', 'ø' => 'oe', 'Ø' => 'OE', 'ō' => 'o', 'Ō' => 'O', 'ơ' => 'o', 'Ơ' => 'O', 'ö' => 'oe', 'Ö' => 'OE', 'ṗ' => 'p', 'Ṗ' => 'P', 'ŕ' => 'r', 'Ŕ' => 'R', 'ř' => 'r',
                                 'Ř' => 'R', 'ŗ' => 'r', 'Ŗ' => 'R', 'ś' => 's', 'Ś' => 'S', 'ŝ' => 's', 'Ŝ' => 'S', 'š' => 's', 'Š' => 'S', 'ṡ' => 's', 'Ṡ' => 'S', 'ş' => 's', 'Ş' => 'S', 'ș' => 's', 'Ș' => 'S',
                                 'ß' => 'SS', 'ť' => 't', 'Ť' => 'T', 'ṫ' => 't', 'Ṫ' => 'T', 'ţ' => 't', 'Ţ' => 'T', 'ț' => 't', 'Ț' => 'T', 'ŧ' => 't', 'Ŧ' => 'T', 'ú' => 'u', 'Ú' => 'U', 'ù' => 'u', 'Ù' => 'U',
                                 'ŭ' => 'u', 'Ŭ' => 'U', 'û' => 'u', 'Û' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ű' => 'u', 'Ű' => 'U', 'ũ' => 'u', 'Ũ' => 'U', 'ų' => 'u', 'Ų' => 'U', 'ū' => 'u', 'Ū' => 'U', 'ư' => 'u',
                                 'Ư' => 'U', 'ü' => 'ue', 'Ü' => 'UE', 'ẃ' => 'w', 'Ẃ' => 'W', 'ẁ' => 'w', 'Ẁ' => 'W', 'ŵ' => 'w', 'Ŵ' => 'W', 'ẅ' => 'w', 'Ẅ' => 'W', 'ý' => 'y', 'Ý' => 'Y', 'ỳ' => 'y', 'Ỳ' => 'Y',
                                 'ŷ' => 'y', 'Ŷ' => 'Y', 'ÿ' => 'y', 'Ÿ' => 'Y', 'ź' => 'z', 'Ź' => 'Z', 'ž' => 'z', 'Ž' => 'Z', 'ż' => 'z', 'Ż' => 'Z', 'þ' => 'th', 'Þ' => 'Th', 'µ' => 'u', 'а' => 'a', 'А' => 'a',
                                 'б' => 'b', 'Б' => 'b', 'в' => 'v', 'В' => 'v', 'г' => 'g', 'Г' => 'g', 'д' => 'd', 'Д' => 'd', 'е' => 'e', 'Е' => 'E', 'ё' => 'e', 'Ё' => 'E', 'ж' => 'zh', 'Ж' => 'zh', 'з' => 'z',
                                 'З' => 'z', 'и' => 'i', 'И' => 'i', 'й' => 'j', 'Й' => 'j', 'к' => 'k', 'К' => 'k', 'л' => 'l', 'Л' => 'l', 'м' => 'm', 'М' => 'm', 'н' => 'n', 'Н' => 'n', 'о' => 'o', 'О' => 'o',
                                 'п' => 'p', 'П' => 'p', 'р' => 'r', 'Р' => 'r', 'с' => 's', 'С' => 's', 'т' => 't', 'Т' => 't', 'у' => 'u', 'У' => 'u', 'ф' => 'f', 'Ф' => 'f', 'х' => 'h', 'Х' => 'h', 'ц' => 'c',
                                 'Ц' => 'c', 'ч' => 'ch', 'Ч' => 'ch', 'ш' => 'sh', 'Ш' => 'sh', 'щ' => 'sch', 'Щ' => 'sch', 'ъ' => '', 'Ъ' => '', 'ы' => 'y', 'Ы' => 'y', 'ь' => '', 'Ь' => '', 'э' => 'e', 'Э' => 'e',
                                 'ю' => 'ju', 'Ю' => 'ju', 'я' => 'ja', 'Я' => 'ja'];
        return str_replace(array_keys($transliterationTable), array_values($transliterationTable), $text);
    }

    public static function setLogging(bool $flag)
    {
        self::$logging = $flag;
    }
}
