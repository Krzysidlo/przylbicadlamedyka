<?php

use classes\User;
use classes\Functions as fs;

$return = [
    'success' => true,
    'message' => fs::t("Settings saved successfully") . ". " . fs::t("Some settings needs the page to be reloaded") . ".",
    'alert'   => "success",
];

$skipNames = [
    "saveSettings",
    "save_avatar",
    "page",
];

if (!empty($_POST['saveSettings'])) {
    foreach ($_POST as $name => $value) {
        if (in_array($name, $skipNames)) {
            continue;
        }

        if ($name === "page") {
            continue;
        }

        if ($value === "on") {
            $value = true;
        }

        if ($name === "login") {
            try {
                $user = new User($value);
                if ($user->id !== USER_ID) {
                    $return = [
                        'success' => false,
                        'message' => fs::t("This url address is already used by another user") . ". " . fs::t("Please choose different") . ".",
                        'alert'   => "warning",
                    ];
                    break;
                }
            } catch (Exception $e) {}
        }

        if (!fs::setOption($name, $value)) {
            $return = [
                'success' => false,
                'message' => fs::t("Function setOption error while post"),
                'alert'   => "danger",
            ];
        }
    }

    if ($return['success']) {
        if (empty($_POST['save_avatar'])) {
            if (fs::getOption('avatar') !== 'default.png') {
                @unlink(USR_DIR . fs::getOption('avatar'));
                if (!fs::setOption('avatar', false)) {
                    $return = [
                        'success' => false,
                        'message' => fs::t("Cannot remove avatar"),
                        'alert'   => "danger",
                    ];
                }
            }
        } else {
            if ($_POST['save_avatar'] === "capture" && file_exists($newPicture = TMP_DIR . "/" . USER_ID . ".jpg")) {
                $newName = USER_ID . ".jpg";
                if (rename($newPicture, USR_DIR . "/" . $newName)) {
                    if (!fs::setOption('avatar', $newName)) {
                        $return = [
                            'success' => false,
                            'message' => fs::t("Function setOption error while files"),
                            'alert'   => "danger",
                        ];
                    }
                } else {
                    $return = [
                        'success' => false,
                        'message' => fs::t("The new profile picture was not saved"),
                        'alert'   => "warning",
                    ];
                }
            } else {
                if (!empty($_FILES['avatar']) && !empty($_FILES['avatar']['tmp_name'])) {
                    foreach ($_FILES as $name => $valueArr) {
                        $minWH        = 150;
                        $allowedTypes = [IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF];
                        $detectedType = exif_imagetype($_FILES['avatar']['tmp_name']);

                        if (!in_array($detectedType, $allowedTypes)) {
                            $return = [
                                'success' => false,
                                'message' => fs::t("It looks like the file you sent is not an image"),
                                'alert'   => "warning",
                            ];
                        } else {
                            if (getimagesize($valueArr['tmp_name'])[0] < $minWH || getimagesize($valueArr['tmp_name'])[1] < $minWH) {
                                $return = [
                                    'success' => false,
                                    'message' => fs::t("File dimensions must be at least") . " $minWH x $minWH px",
                                    'alert'   => "warning",
                                ];
                            } else {
                                if ($valueArr["size"] > 3000000) {
                                    $return = [
                                        'success' => false,
                                        'message' => fs::t("File size is too large") . ". " . fs::t("Please select file that is smaller than 3 MB"),
                                        'alert'   => "warning",
                                    ];
                                } else {
                                    $fileNameArr = explode(".", $valueArr['name']);

                                    $newName = USER_ID . "." . $fileNameArr[1];
                                    if (move_uploaded_file($valueArr['tmp_name'], USR_DIR . "/" . $newName)) {
                                        if (!fs::setOption($name, $newName)) {
                                            $return = [
                                                'success' => false,
                                                'message' => fs::t("Function setOption error while files"),
                                                'alert'   => "danger",
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}


if (empty($_GET['ajax'])) {
    header("Location: /settings");
} else {
    echo json_encode($return);
}

exit(0);
