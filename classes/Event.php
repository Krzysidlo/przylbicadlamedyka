<?php

namespace classes;

use DateTime;
use Exception;
use DateTimeZone;
use classes\Functions as fs;
use classes\exceptions\EventNotFoundException;

class Event
{
    public $id          = NULL;
    public $competition = [];
    public $status;
    public $displayStatus;
    public $date;
    public $localDate;
    public $displayDate;
    public $label;
    public $homeTeamName;
    public $awayTeamName;
    public $result;
    public $prediction  = NULL;

    private $userBets = [];

    /**
     * Event constructor.
     * @param string|NULL $eventsID
     * @param bool $getUserPediction
     * @param string|NULL $emailID
     * @throws Exception
     */
    public function __construct(string $eventsID = NULL, bool $getUserPediction = false, string $emailID = NULL)
    {
        if ($eventsID !== NULL) {
            try {
                $this->getOne($eventsID, $getUserPediction, $emailID);
            } catch (Exception $e) {
                throw $e;
            }
        }
    }

    /**
     * @param string $eventsID
     * @param bool $getUserPediction
     * @param string|NULL $emailID
     *
     * @return bool
     * @throws Exception
     * @throws EventNotFoundException
     */
    public function getOne(string $eventsID, bool $getUserPediction = false, string $emailID = NULL)
    {
        if ($this->id !== NULL) {
            if ($this->id == $eventsID) {
                return true;
            } else {
                throw new Exception(fs::t("Event already initiated with a different id"));
            }
        }
        if (!$event = fs::getEvent($eventsID)) {
            throw new EventNotFoundException(fs::t("Event not found with id") . " " . $eventsID);
        }

        $this->id           = $event['id'];
        $this->competition  = [
            'id'   => $event['competitions_id'],
            'name' => fs::getCompName($event['competitions_id']),
        ];
        $this->date         = $event['date'];
        $this->status       = $event['status'];
        $this->homeTeamName = $event['homeTeamName'];
        $this->awayTeamName = $event['awayTeamName'];

        $this->result = new Result($this->id);

        if ($getUserPediction) {
            $this->prediction = new Prediction($this->id, $emailID);
            $this->prediction->setSuccess($this->result);
        }

        $timezone = fs::getClientsTimezone(NULL, $emailID);
        $now      = date("Y-m-d H:i:s");
        $now      = new DateTime($now);
        $now->setTimezone(new DateTimeZone($timezone));

        $this->localDate = new DateTime($this->date);
        $this->localDate->setTimezone(new DateTimeZone($timezone));

        if ($now > $this->localDate && $this->status === "TIMED") {
            $this->status = "DATE";
        }

        $interval = (int)(new DateTime(date("Y-m-d")))->diff(new DateTime(date("Y-m-d", $this->localDate->getTimestamp())))->format('%r%a');
        if (IS_ROOT) {
            $interval = -2;
        }
        switch ($interval) {
            case -1:
                $displayDate = fs::t("Yesterday");
                break;
            case 0:
                $displayDate = fs::t("Today");
                break;
            case 1:
                $displayDate = fs::t("Tomorrow");
                break;
            default:
                $displayDate = $this->localDate->format("d") . "&nbsp;" . fs::mb_ucfirst(strftime("%b", $this->localDate->getTimestamp()));
                if (date("Y") !== date("Y", $this->localDate->getTimestamp())) {
                    $displayDate .= "&nbsp;" . $this->localDate->format("Y");
                }
                break;
        }
        $this->displayDate = $displayDate;

        $this->displayStatus = $this->localDate->format("H:i");

        $this->label = ($this->status === "TIMED" ? "input" : "");

        $this->result->homeTeam['DISP'] = $this->result->homeTeam['FT'];
        $this->result->awayTeam['DISP'] = $this->result->awayTeam['FT'];
        if ($this->result->homeTeam['PS'] !== NULL) {
            $this->result->homeTeam['DISP'] = $this->result->homeTeam['FT'] . "&nbsp;(" . $this->result->homeTeam['PS'] . ")";
        }
        if ($this->result->awayTeam['PS'] !== NULL) {
            $this->result->awayTeam['DISP'] = $this->result->awayTeam['FT'] . "&nbsp;(" . $this->result->awayTeam['PS'] . ")";
        }

        $this->setCorrectTime();

        return true;
    }

    private function setCorrectTime()
    {

    }

    /**
     * @param bool $thisMatchDay
     * @param bool $getUserPediction
     * @param string|NULL $status
     * @param string|NULL $competitionsID
     * @param string|NULL $emailID
     *
     * @return array
     * @throws Exception
     */
    public static function getAll(bool $thisMatchDay = false, bool $getUserPediction = false, string $status = NULL, string $competitionsID = NULL, string $emailID = NULL)
    {
        [$eventsIds, $eventDay, $eventDaysNum] = fs::getEventsIds($thisMatchDay, $status, $competitionsID);

        $events = [];
        foreach ($eventsIds as $eventsID) {
            $events[$eventsID] = new self($eventsID, $getUserPediction, $emailID);
        }

        return [$events, $eventDay, $eventDaysNum];
    }

    public function __toString()
    {
        if ($this->id === NULL) {
            return "Event not initiated";
        }

        $return = "Competition: [{$this->competition['name']}], \nDate: [{$this->localDate->format('Y-m-d H:i')}]";
        $return .= ", \nStatus: [{$this->status}]";
        $return .= ", \nHome team: [{$this->homeTeamName}], \nAway team: [{$this->awayTeamName}]";
        $return .= ", \nResult: [{$this->result->homeTeam['FT']} : {$this->result->awayTeam['FT']}]";

        return $return;
    }

    /**
     * @param string $name
     * @throws Exception
     */
    public function __get(string $name)
    {
        if ($this->id === NULL) {
            throw new Exception("Event not initiated");
        }
    }

    public function unsetUserBet(string $usersID): bool
    {
        if (isset($this->getUsersBets()[$usersID])) {
            unset($this->userBets[$usersID]);
        }
        return true;
    }

    public function getUsersBets(): array
    {
        if (empty($this->userBets)) {
            if ($query = fs::$mysqli->query("SELECT `users_id`, `home_team`, `away_team` FROM `predictions` WHERE `events_id` = '{$this->id}';")) {
                while ($scoreResult = $query->fetch_object()) {
                    if (!empty($scoreResult->users_id)) {
                        $success = false;
                        $sql     = "SELECT `homeTeam`, `awayTeam` FROM `results` WHERE `events_id` = '{$this->id}';";
                        if (!empty($eventResult = fs::$mysqli->query($sql)->fetch_object())) {
                            if ($scoreResult->home_team === $eventResult->homeTeam && $scoreResult->away_team === $eventResult->awayTeam) {
                                $success = " success";
                            }
                        }
                        $this->userBets[$scoreResult->users_id] = (object)[
                            'id'       => $scoreResult->users_id,
                            'homeTeam' => $scoreResult->home_team,
                            'awayTeam' => $scoreResult->away_team,
                            'success'  => $success,
                        ];
                    }
                }
            }
        }
        return $this->userBets;
    }
}