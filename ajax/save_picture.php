<?php

use classes\Functions as fs;

global $colorForBG;

$return = [
    'success' => true,
    'message' => "",
    'alert'   => false,
];

if (!empty($_POST['file'])) {
    $file    = filter_var($_POST['file'], FILTER_SANITIZE_STRING);
    $newFile = fs::base64_to_jpeg($file);
    if ($newFile) {
        $return['picture'] = $newFile;
    } else {
        $return = [
            'success' => false,
            'message' => fs::t("The new profile picture was not created"),
            'alert'   => "warning",
        ];
    }
} else {
    $return = [
        'success' => false,
        'message' => fs::t("No data sent"),
        'alert'   => "danger",
    ];
}


if (empty($_GET['ajax'])) {
    header("Location: /settings");
} else {
    echo json_encode($return);
}

exit(0);