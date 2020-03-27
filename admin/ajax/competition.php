<?php

use classes\Functions as fs;

$return = [
    'success' => false,
    'message' => "Wystąpił nieznany błąd",
    'alert'   => "danger",
];

if (!empty($_POST['action'])) {
    $name  = $_POST['name'] ?? "";
    $competitionsID = $feedID = $_POST['id'] ?? "";
    switch ($_POST['action']) {
        case 'new':
            if ($name === "" || $feedID === "") {
                $return['message'] = "Proszę uzupełnić wszystkie pola";
                $return['alert']   = "warning";
            } else {
                $sql = "INSERT INTO `competitions` VALUES (NULL, '{$name}', '{$feedID}');";
                if (fs::$mysqli->query($sql)) {
                    $competitionsID = fs::$mysqli->insert_id ?? NULL;
                    if ($competitionsID !== NULL) {
                        $return = [
                            'success' => true,
                            'message' => "",
                            'alert'   => false,
                        ];
                    }
                }
                if (!empty($_FILES['picture']) && !empty($_FILES['picture']['tmp_name']) && $return['success']) {
                    $minWH        = 150;
                    $allowedTypes = [IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF];
                    $detectedType = exif_imagetype($_FILES['picture']['tmp_name']);

                    if (!in_array($detectedType, $allowedTypes)) {
                        $return = [
                            'success' => false,
                            'message' => fs::t("It looks like the file you sent is not an image"),
                            'alert'   => "warning",
                        ];
                    } else {
                        if (getimagesize($_FILES['picture']['tmp_name'])[0] < $minWH || getimagesize($_FILES['picture']['tmp_name'])[1] < $minWH) {
                            $return = [
                                'success' => false,
                                'message' => fs::t("File dimensions must be at least") . " $minWH x $minWH px",
                                'alert'   => "warning",
                            ];
                        } else {
                            if ($_FILES['picture']["size"] > 3000000) {
                                $return = [
                                    'success' => false,
                                    'message' => fs::t("File size is too large") . ". " . fs::t("Please select file that is smaller than 3 MB"),
                                    'alert'   => "warning",
                                ];
                            } else {
                                $fileNameArr = explode(".", $_FILES['picture']['name']);
                                move_uploaded_file($_FILES['picture']['tmp_name'], COMP_DIR . "/" . $competitionsID . "." . $fileNameArr[1]);
                            }
                        }
                    }
                    if (!$return['success']) {
                        fs::$mysqli->query("DELETE FROM `competitions` WHERE `id` = {$competitionsID};");
                    }
                }
            }
            break;
        case 'name':
            if (fs::$mysqli->query("UPDATE `competitions` SET `name` = '{$name}' WHERE `id` = {$competitionsID};")) {
                $return = [
                    'success' => true,
                    'message' => "",
                    'alert'   => false,
                ];
            } else {
                $return = [
                    'success' => false,
                    'message' => "Błąd podczas zapisu do bazy danych",
                    'alert'   => "warning",
                ];
            }
            break;
        case 'img':
            if (!empty($_FILES['picture']) && !empty($_FILES['picture']['tmp_name']) && $competitionsID !== "") {
                $minWH        = 150;
                $allowedTypes = [IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF];
                $detectedType = exif_imagetype($_FILES['picture']['tmp_name']);

                if (!in_array($detectedType, $allowedTypes)) {
                    $return = [
                        'success' => false,
                        'message' => fs::t("It looks like the file you sent is not an image"),
                        'alert'   => "warning",
                    ];
                } else {
                    if (getimagesize($_FILES['picture']['tmp_name'])[0] < $minWH || getimagesize($_FILES['picture']['tmp_name'])[1] < $minWH) {
                        $return = [
                            'success' => false,
                            'message' => fs::t("File dimensions must be at least") . " $minWH x $minWH px",
                            'alert'   => "warning",
                        ];
                    } else {
                        if ($_FILES['picture']["size"] > 3000000) {
                            $return = [
                                'success' => false,
                                'message' => fs::t("File size is too large") . ". " . fs::t("Please select file that is smaller than 3 MB"),
                                'alert'   => "warning",
                            ];
                        } else {
                            $types = ["png", "jpg", "jpeg", "gif"];
                            foreach ($types as $type) {
                                $compPicture = COMP_DIR . "/{$competitionsID}.{$type}";
                                if (file_exists($compPicture)) {
                                    @unlink($compPicture);
                                }
                            }
                            $fileNameArr = explode(".", $_FILES['picture']['name']);
                            if (move_uploaded_file($_FILES['picture']['tmp_name'], COMP_DIR . "/" . $competitionsID . "." . $fileNameArr[1])) {
                                $return = [
                                    'success' => true,
                                    'message' => "",
                                    'alert'   => false,
                                ];
                            }
                        }
                    }
                }
            }
            break;
        case 'get':
            fs::$mysqli->query("TRUNCATE TABLE `competitions_list`");

            if (!fs::$mysqli->error) {
                $url = "http://api.football-data.org/v2/competitions";
                $compInfo     = fs::getFromUrl($url);
                $competitions = [];
                $sql = "INSERT INTO `competitions_list` VALUES ";
                if (is_array($compInfo->competitions)) {
                    foreach ($compInfo->competitions as $comp) {
                        if ($comp->plan === "TIER_ONE") {
                            $sql .= "('{$comp->id}', '{$comp->name}', '{$comp->currentSeason->startDate}', '{$comp->currentSeason->endDate}'),";
                        }
                    }
                }
                $sql = rtrim($sql, ",") . ";";
                if (fs::$mysqli->query($sql)) {
                    $return = [
                        'success' => true,
                        'message' => "",
                        'alert'   => false,
                    ];
                } else {
                    $return = [
                        'success' => false,
                        'message' => "Błąd przy zapisie do bazy danych",
                        'alert'   => "danger",
                        'sql'     => $sql,
                    ];
                }
            } else {
                $return = [
                    'success' => false,
                    'message' => "Błąd przy zapisie do bazy danych",
                    'alert'   => "danger",
                    'sql'     => $sql,
                ];
            }
            break;
    }
}


if (empty($_GET['ajax'])) {
    header("Location: /admin/competitions");
} else {
    echo json_encode($return);
}

exit(0);
