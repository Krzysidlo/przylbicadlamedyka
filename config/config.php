<?php

use classes\User;
use classes\Functions as fs;

require_once __DIR__ . "/autoload.php";

setlocale(LC_ALL, "pl_PL.utf-8");

$langArr = scandir(__DIR__ . "/../translations");
array_shift($langArr);
array_shift($langArr);

sort($langArr);

$CONST_MODE = false;

session_start();

require_once(__DIR__ . "/constants.php");

$LOGGED_IN = false;
$USER_ID   = "";
$USER_NAME = "";
if (!empty($_SESSION['usersID']) && !empty($_SESSION['userName'])) {
    $LOGGED_IN = true;
    $USER_ID   = $_SESSION['usersID'];
    $USER_NAME = $_SESSION['userName'];
} else {
    if (DB_CONN && !empty(fs::getACookie('usersID')) && (empty($_GET['page']) || $_GET['page'] !== "error")) {
        try {
            $loggedUser = new User(fs::getACookie('usersID'));
            $LOGGED_IN  = true;
            $USER_ID    = $loggedUser->id;
            $USER_NAME  = $loggedUser->name;

            /* Prolong the cookie for easy log in */
            if (!empty(fs::getACookie('easyLogIn'))) {
                fs::setACookie('easyLogIn', fs::getACookie('easyLogIn'), 3600 * 24 * 30);
            }
        } catch (Exception $e) {}
    }
}
if (!DB_CONN) {
    $LOGGED_IN = false;
    $USER_ID   = "";
}

define("LOGGED_IN", $LOGGED_IN);
define("USER_ID", $USER_ID);

if (LOGGED_IN) {
    $user      = new User;
    $USER_NAME = $user->name;
}
define("USER_NAME", $USER_NAME);

/* Define IS_ROOT and USER_PRV constants */
$IS_ROOT  = false;
$USER_PRV = User::USER_NO_CONFIRM;
if (LOGGED_IN) {
    if ($user->getOption('confirm-email') === NULL) {
        $USER_PRV = $user->getPrivilege();
    }

    if ($user->email === ROOT_EMAIL) {
        $USER_PRV = User::USER_ROOT;
    }

    if ($USER_PRV === User::USER_ROOT) {
        $IS_ROOT = true;
    }
}

define('USER_PRV', $USER_PRV);
define('IS_ROOT', $IS_ROOT);

if (IS_ROOT) {
    $CONST_MODE = false;
}

if (!DB_CONN) {
    $CONST_MODE = true;
}

define('CONST_MODE', $CONST_MODE);

$view = $_GET['view'] ?? MAIN_VIEW;
$page = $_GET['page'] ?? NULL;

$noLoggedIn = ['admin', 'confirm', 'error', 'register', 'reset', 'ajax', 'regulations'];

if (!LOGGED_IN && !in_array($view, $noLoggedIn)) {
    $view = 'register';
}

if (LOGGED_IN && $user->noAddress() && !in_array($view, $noLoggedIn)) {
    $view = 'address';
}

if (USER_PRV == User::USER_NO_ACCESS) {
    $view = 'noaccess';
}

$controllersPath = 'controllers\\';
if ($page == 'admin') {
    $view = $_GET['view'] ?? MAIN_VIEW;
    if (!LOGGED_IN) {
        $view = 'login';
    }
    $controllersPath = 'admin\controllers\\';
}

$controllerName = $controllersPath . ucfirst($view) . 'Controller';
if (!class_exists($controllerName) || $view === 'error') {
    $controllerName = $controllersPath . 'DefaultController';
}

$pageClass = new $controllerName($view);
