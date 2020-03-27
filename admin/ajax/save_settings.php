<?php

use classes\Functions as fs;

$return = [
    'success' => true,
    'message' => fs::t("Settings saved successfully") . ". ",
    'alert'   => "success",
];

$skipNames = [
    'saveSettings',
];

if (!empty($_POST['saveSettings'])) {
    foreach ($_POST as $name => $value) {
        if (in_array($name, $skipNames)) {
            continue;
        }

        foreach ($value as $pageOptionName => $pageOptionValue) {
            if ($pageOptionValue === "on") {
                $pageOptionValue = true;
            }

            if (!fs::setOption($pageOptionName, $pageOptionValue, "page")) {
                $return = [
                    'success' => false,
                    'message' => fs::t("Function setOption error while post"),
                    'alert'   => "danger",
                ];
            }
        }
    }
}


if (empty($_GET['ajax'])) {
    header("Location: /admin/settings");
} else {
    echo json_encode($return);
}

exit(0);
