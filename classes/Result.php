<?php

namespace classes;

use stdClass;
use Exception;
use classes\Functions as fs;

class Result
{
    public $homeTeam;
    public $awayTeam;
    public $final;

    private $eventsID = NULL;

    /**
     * Result constructor.
     * @param string $eventsID
     * @throws Exception
     */
    public function __construct(string $eventsID)
    {
        if (empty($eventsID)) {
            throw new Exception(fs::t("Result can be only a part of event. Event ID is required"));
        }
        $this->eventsID = filter_var($eventsID, FILTER_SANITIZE_STRING);

        $sql = "SELECT * FROM `results` WHERE `events_id` = '{$this->eventsID}';";
        if (!$query = fs::$mysqli->query($sql)) {
            throw new Exception(fs::t("Could not retrieve data from database"));
        }

        $result = $query->fetch_assoc();

        $this->homeTeam = [
            'FT' => $result['homeTeam'],
            'HT' => $result['HT_homeTeam'],
            'ET' => $result['ET_homeTeam'],
            'PS' => $result['PS_homeTeam'],
        ];
        $this->awayTeam = [
            'FT' => $result['awayTeam'],
            'HT' => $result['HT_awayTeam'],
            'ET' => $result['ET_awayTeam'],
            'PS' => $result['PS_awayTeam'],
        ];

        $this->final = false;
        if ($this->homeTeam['PS'] !== NULL || $this->awayTeam['PS'] !== NULL) {
            $this->final           = new StdClass;
            $this->final->homeTeam = max($this->homeTeam['FT'], $this->homeTeam['PS']);
            $this->final->awayTeam = max($this->awayTeam['FT'], $this->awayTeam['PS']);
        }
    }
}