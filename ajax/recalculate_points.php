<?php

use classes\Functions as fs;

$return = [
    'success' => true,
    'message' => fs::t('Recalculated successfully'),
    'alert'   => 'success',
];

if (fs::$mysqli->query("UPDATE `predictions` SET score = NULL;")) {
    $ch = curl_init(ROOT_URL . "/cron/countPoints.php");

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $output = curl_exec($ch);
    $err    = curl_errno($ch);
    $errmsg = curl_error($ch);

    var_dump($output);
    die();

    if ($output !== "success") {
        $return = [
            'success' => false,
            'message' => fs::t('Recalculation failed, countPoints error') . $output,
            'alert'   => 'danger',
        ];
    }
} else {
    $return = [
        'success' => false,
        'message' => fs::t('Recalculation failed, database error'),
        'alert'   => 'danger',
    ];
}

if (empty($_GET['ajax'])) {
    header("Location: /settings");
} else {
    echo json_encode($return);
}

exit(0);