<?php

use classes\Functions as fs;
use classes\User;

require_once __DIR__ . "/autoload.php";

$defLang = "pl";
if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $defLang = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);
}
$lang = $_COOKIE['lang'] ?? $defLang;
//Usunąć aby przywrócić opcję wyboru języka
$lang = "pl";

if (strlen($lang) <= 2) {
    $localeString = $lang . "_" . strtoupper($lang);
} else {
    $localeString = $lang;
    $lang         = substr($lang, 0, 2);
}

$localeString .= ".utf-8";
setlocale(LC_ALL, $localeString);
//setlocale(LC_TIME, $localeString);

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
        if ($query = fs::$mysqli->query("SELECT * FROM `users` WHERE `id` = '" . fs::getACookie('usersID') . "';")) {
            if (!empty($result = $query->fetch_assoc())) {
                $LOGGED_IN = true;
                $USER_ID   = $result['id'];
                $USER_NAME = $result['name'];

                /* Prolong the cookie for easy log in */
                if (!empty(fs::getACookie('easyLogIn'))) {
                    fs::setACookie('easyLogIn', fs::getACookie('easyLogIn'), 3600 * 24 * 30);
                }
            }
        }
    }
}
if (!DB_CONN) {
    $LOGGED_IN = false;
    $USER_ID   = "";
}

define("LOGGED_IN", $LOGGED_IN);
define("USER_ID", $USER_ID);

if (LOGGED_IN) {
    $user = new User;
    $USER_NAME = $user->name;
}
define("USER_NAME", $USER_NAME);

/* Define IS_ROOT and USER_PRV constants */
$IS_ROOT  = false;
$USER_PRV = User::PRIV_NO_CONFIRM;
if (LOGGED_IN) {
    $USER_PRV = $user->getPrivilege();
    if ($user->email === ROOT_EMAIL) {
        $USER_PRV = User::PRIV_ROOT;
    }
    if ($USER_PRV === User::PRIV_ROOT) {
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

/* Check if user uses dark theme */
$DARK_THEME = false;
if (LOGGED_IN && $user->getOption('darkTheme') && USER_PRV >= 2) {
    $DARK_THEME = true;
}
if ($view === 'admin') {
    $DARK_THEME = true;
}
if ($page == 'admin') {
	$DARK_THEME = true;
}
define("DARK_THEME", $DARK_THEME);

$COMPETITION_ID = NULL;
if (LOGGED_IN && !in_array($view, ['logout', 'confirm', 'admin'])) {
    $COMPETITION_ID = $user->getOption('competition') ?? (DEFAULT_OPTIONS['competition'] ?? NULL);
    if (!is_null($COMPETITION_ID)) {
        $query       = fs::$mysqli->query("SELECT `feed_competition_id` FROM `competitions` WHERE `id` = {$COMPETITION_ID};");
        $competition = $query->fetch_assoc() ?? [];

        if (empty($competition['feed_competition_id'])) {
            $view = "change";
        }
    } else {
        $view = "change";
    }
}
define("COMPETITION_ID", $COMPETITION_ID);

$noLoggedIn = ['admin', 'chatbot', 'confirm', 'error', 'facebook', 'google', 'register', 'reset', 'ajax'];

if (!LOGGED_IN && !in_array($view, $noLoggedIn)) {
    $view = 'register';
}

if (USER_PRV < 1) {
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
