<?php

use classes\User;
use classes\Functions as fs;

$return = [
    'success' => false,
    'message' => fs::t('No changes detected or there was an error') . ". ",
    'alert'   => 'warning',
];

if (!empty($_POST['savePrivileges'])) {
    if (IS_ROOT) {
        $usersID = $_POST['usersID'] ?? NULL;
        $level   = $_POST['level'] ?? NULL;
        if (!empty($usersID) && $level !== NULL) {
            $level = (int)$level;
            $user = new User($usersID);
            if ($user->setPrivilege($level)) {
                $return = [
                    'success' => true,
                    'message' => fs::t('Privileges saved successfully') . ". ",
                    'alert'   => 'success',
                    'usersID' => $usersID,
                    'level'   => $level,
                ];
            }
        }
    }
}


if (empty($_GET['ajax'])) {
    header("Location: /privileges");
} else {
    echo json_encode($return);
}

exit(0);