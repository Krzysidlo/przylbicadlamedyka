<?php

use classes\Group;
use classes\Functions as fs;

$return = [
    'success' => false,
    'message' => fs::t("There was an unexpected error"),
    'alert'   => "warning",
];

function newRow(int $id, string $createdBy, string $name, string $code) {
    $leaveText = fs::t("Leave");
    $leaveBtn  = ($id !== 1 ? "<button class=\"btn btn-warning btn-sm leave\">{$leaveText}</button>" : "");
    $deleteBtn = ($createdBy == USER_ID && $id !== 1 ? "<button class=\"btn btn-danger btn-sm delete\"><i class=\"fas fa-trash\"></i></button>" : "");
    return <<< HTML
    <tr class="new" data-id="{$id}">
        <td>{$name}</td>
        <td>{$code}</td>
        <td>{$leaveBtn}</td>
        <td>{$deleteBtn}</td>
    </tr>
HTML;
}

if (!empty($_POST['action'])) {
    $action = filter_var($_POST['action'], FILTER_SANITIZE_STRING);

    switch ($action) {
        case 'new':
            $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
            $code = filter_var($_POST['code'], FILTER_SANITIZE_STRING);
            $group = Group::create($name, $code);
            if (is_string($group)) {
                $return['message'] = $group;
            } else {
                $newRow = newRow($group->id, $group->createdBy, $group->name, $group->code);
                $return = [
                    'success' => true,
                    'message' => "",
                    'alert'   => false,
                    'newRow'  => $newRow,
                ];
            }
            break;
        case 'join':
            $code = filter_var($_POST['code'], FILTER_SANITIZE_STRING);
            $group = Group::join($code);
            if (is_string($group)) {
                $return['message'] = $group;
            } else {
                $newRow = newRow($group->id, $group->createdBy, $group->name, $group->code);
                $return = [
                    'success' => true,
                    'message' => "",
                    'alert'   => false,
                    'newRow'  => $newRow,
                ];
            }
            break;
        case 'leave':
            $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
            $group = Group::getOne($id);
            if ($group->leave()) {
                $return = [
                    'success' => true,
                    'message' => "",
                    'alert'   => false,
                ];
            }
            break;
        case 'delete':
            $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
            $group = Group::getOne($id);
            if ($group->delete()) {
                $return = [
                    'success' => true,
                    'message' => "",
                    'alert'   => false,
                ];
            }
            break;
    }
}

if (empty($_GET['ajax'])) {
    header("Location: /");
} else {
    echo json_encode($return);
}

exit(0);