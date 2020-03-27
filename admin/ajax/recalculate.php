<?php

use classes\Functions as fs;

$return = [
    'success' => false,
    'message' => "Wystąpił nieznany błąd",
    'alert'   => "danger",
];

$competitionsID = $_POST['compID'] ?? NULL;
if ($competitionsID !== NULL) {
    if (fs::resetRanking($competitionsID)) {
        shell_exec("php cron/countPoints.php {$competitionsID}");
        $return = [
            'success'        => true,
            'message'        => "",
            'alert'          => false,
            'competitionsID' => $competitionsID,
        ];
    }
}

if (empty($_GET['ajax'])) {
	if ($competitionsID !== NULL) {
        header("Location: /admin/points/{$competitionsID}");
	} else {
        header("Location: /admin/points");
	}
} else {
    echo json_encode($return);
}

exit(0);