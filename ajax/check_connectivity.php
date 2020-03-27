<?php

use classes\Functions as fs;

$return = [
    'success' => true,
    'message' => fs::t("You are offline") . ". " . fs::t("What you see might be outdated") . ".",
];

if (empty($_GET['ajax'])) {
    header("Location: /");
} else {
    echo json_encode($return);
}

exit(0);