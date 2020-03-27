<?php

namespace classes;

use Exception;
use classes\Functions as fs;

class Prediction
{
    public $homeTeam;
    public $awayTeam;
    public $score;
    public $joker;
    public $bet;
    public $success;

    private $eventsID = NULL;
    private $usersID = NULL;

    /**
     * Prediction constructor.
     * @param string $eventsID
     * @param string|NULL $emailID
     * @throws Exception
     */
    public function __construct(string $eventsID, string $emailID = NULL)
    {
        if (empty($eventsID)) {
            throw new Exception(fs::t("Prediction requires event ID"));
        }

        $user = new User($emailID);
        $this->eventsID = filter_var($eventsID, FILTER_SANITIZE_STRING);
        $this->usersID  = filter_var($user->id, FILTER_SANITIZE_STRING);

        if (empty($this->usersID)) {
            throw new Exception(fs::t("Prediction requires user ID"));
        }

        $sql = "SELECT `home_team`, `away_team`, `score`, `joker` FROM `predictions` WHERE `events_id` = '{$this->eventsID}' AND `users_id` = '{$this->usersID}';";

        if (!$query = fs::$mysqli->query($sql)) {
            throw new Exception(fs::t("Could not retrieve data from database"));
        }

        $result = $query->fetch_assoc();

        $this->homeTeam = $result['home_team'] ?? NULL;
        $this->awayTeam = $result['away_team'] ?? NULL;
        $this->score    = $result['score'] ?? NULL;
        $this->joker    = $result['joker'] ?? NULL;
        $this->bet      = ($this->homeTeam !== NULL && $this->awayTeam !== NULL);
    }

    public function setSuccess(Result &$result)
    {
        $this->success = $this->bet;
        $this->success &= ($this->homeTeam == $result->homeTeam['FT']);
        $this->success &= ($this->awayTeam == $result->awayTeam['FT']);
        $this->success = (bool)$this->success;
    }
}
