<?php

namespace classes;

use DateTime;
use Exception;
use DateTimeZone;
use classes\Functions as fs;

class Ranking
{
	public $table = [];

    public function __construct(array $users, int $competitionsID = NULL)
    {
        if ($competitionsID === NULL) {
            $competitionsID = COMPETITION_ID;
        }
        $sql = "SELECT `users_id`, `score`, `perfect`, `last_five` FROM `ranking` WHERE `competitions_id` = {$competitionsID} ORDER BY `position`;";

        $ranking = [];
        if ($query = fs::$mysqli->query($sql)) {
            while ($result = $query->fetch_assoc()) {
                $result['score_old'] = self::getOldRanking($result['users_id'], $competitionsID);
                $ranking[$result['users_id']] = $result;
            }
        }
        $ranking['empty'] = [
            'score'        => 0,
            'perfect'      => 0,
            'last_five'    => 0,
            'position'     => 0,
            'score_old'    => 0,
        ];

        foreach ($users as $usersID => $user) {
            if (isset($ranking[$usersID])) {
                $this->table[$usersID] = $ranking[$usersID];
            } else {
                $this->table[$usersID] = $ranking['empty'];
            }
            $this->table[$usersID]['display_score'] = $this->table[$usersID]['score'];
            $difference = (intval($this->table[$usersID]['score']) - intval($this->table[$usersID]['score_old']));
            if ($difference > 0) {
                $difference = "<span class='success'><i class='fas fa-chevron-up'></i> {$difference}</span>";
                $this->table[$usersID]['display_score'] .= " ({$difference})";
            }
        }

        $score = array_column($this->table, 'score');
        array_multisort($score, SORT_DESC, $this->table);

        $position = 1;
        foreach ($this->table as &$userRanking) {
            $userRanking['position'] = $position;
            $position++;
        }
    }

    public function getOldRanking(string $usersID = NULL, $competitionsID = NULL)
    {
        $usersID = $usersID ?? USER_ID;
        $competitionsID = $competitionsID ?? COMPETITION_ID;

   	    $return = 0;
   	    if ($query = fs::$mysqli->query("SELECT `score` FROM `ranking_old` WHERE `users_id` = '{$usersID}' AND `competitions_id` = {$competitionsID};")) {
   	        if (isset($query->fetch_row()[0])) {
   	        	$return = $query->fetch_row()[0] ?? NULL;
   	        }
   	    }

   	    return $return;
    }
}