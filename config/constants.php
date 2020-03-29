<?php
header('Access-Control-Allow-Origin: *');

define("MYSQL_CONN_ERROR", "Unable to connect to database.");
mysqli_report(MYSQLI_REPORT_STRICT);

use classes\Functions as fs;

//Tryb developerski - wyświetlanie błędów (domyślnie włączony dla testu)
define('DEV_MODE', true);

if (DEV_MODE) {
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
    error_reporting(E_ALL);
}

//Use local database
//$DB_HOST = 'localhost';
$DB_HOST = '185.243.55.171';
$DB_NAME = 'przylbicadlamedyka';
$DB_USER = 'root';
$DB_PSWD = 'Krzysiek2413';
if (DEV_MODE) {
    $DB_NAME = 'przylbicadlamedyka_test';
}

//Wyświetla się komunikat, że strona w trakcie konstrukcji
$CONST_MODE = false;

/* Create variable to connect with database */
$DB_CONN = true;
try {
    $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PSWD, $DB_NAME);
    $mysqli->set_charset("utf8");
} catch (Exception $e) {
    $CONST_MODE = true;
    $DB_CONN    = false;
}

define('DB_CONN', $DB_CONN);

$rootDir = dirname(__DIR__);
chdir($rootDir);
/* ---- Paths ---- */
define('ROOT_DIR', $rootDir);
const MEDIA_DIR = ROOT_DIR . "/media";
const INC_DIR   = ROOT_DIR . "/includes";
const CONF_DIR  = ROOT_DIR . "/config";
const LOG_DIR   = ROOT_DIR . "/../logs";
const AJAX_DIR  = ROOT_DIR . "/ajax";
const ADMIN_DIR = ROOT_DIR . "/admin";

const IMG_DIR  = MEDIA_DIR . "/img";
const TMP_DIR  = IMG_DIR . "/tmp";
const CSS_DIR  = MEDIA_DIR . "/css";
const JS_DIR   = MEDIA_DIR . "/js";

/* ---- Urls ---- */
$ROOT_URL = "";
if (!empty($_SERVER['REQUEST_SCHEME']) && !empty($_SERVER['HTTP_HOST'])) {
    $ROOT_URL = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'];
} else {
    if (!empty($_SERVER['HTTP_REFERER'])) {
        $parts    = explode("/", $_SERVER['HTTP_REFERER']);
        $ROOT_URL = $parts[0] . "//" . $parts[2];
    } else {
        if (!empty($_SERVER["SCRIPT_URI"]) && !empty($_SERVER['HTTP_HOST'])) {
            $parts    = explode('/', $_SERVER["SCRIPT_URI"]);
            $ROOT_URL = array_shift($parts) . "//" . $_SERVER['HTTP_HOST'];
        } else {
            if (DEV_MODE) {
                $ROOT_URL = "https://test.przylbicadlamedyka.pl";
            } else {
                $ROOT_URL = "https://przylbicadlamedyka.pl";
            }
        }
    }
}

define('ROOT_URL', $ROOT_URL);

const MEDIA_URL = ROOT_URL . "/media";
const INC_URL   = ROOT_URL . "/includes";

const IMG_URL  = MEDIA_URL . "/img";
const MSC_URL  = MEDIA_URL . "/music";
const USR_URL  = IMG_URL . "/users";
const COMP_URL = IMG_URL . "/competitions";
const TMP_URL  = IMG_URL . "/tmp";
const CSS_URL  = MEDIA_URL . "/css";
const JS_URL   = MEDIA_URL . "/js";

if (isset($_SERVER['REQUEST_URI'])) {
    $request_uri = $_SERVER['REQUEST_URI'];
    if ($_SERVER['REQUEST_URI'] === "/") {
        $request_uri = "";
    }
    define('CURRENT_URL', ROOT_URL . $request_uri);
} else {
    define('CURRENT_URL', ROOT_URL);
}
if (DB_CONN && $query = $mysqli->query("SELECT `value` FROM `options_page` WHERE `name` = 'CONST_MODE';")) {
    if ($result = ($query->fetch_row() ?? [NULL])) {
        $CONST_MODE = ($result[0] === 'true' ? true : false);
    }
}

/* -------------- Settings (you can change that) -------------- */
//E-mail address from which messages should be sent
//Default e-mail address (if not changed in website setiings)
const ROOT_EMAIL = "krzychu.janiszewski@gmail.com";
$EMAIL = ROOT_EMAIL;

if (DB_CONN && $query = $mysqli->query("SELECT `value` FROM `options_page` WHERE `name` = 'EMAIL';")) {
    if ($result = ($query->fetch_row() ?? [NULL])) {
        $EMAIL = ($result[0] ?? NULL);
    }
}
define('EMAIL', $EMAIL);

//Default values of options (returned by getOption function if no value is set)
define("DEFAULT_OPTIONS", [
    'changed_name'  => '',
    'login'         => '',
]);
/* ------------------------ /Settings ------------------------- */

fs::init();

$pageName = "Przyłbica dla medyka";
if (DEV_MODE) {
    $pageName .= " DEV";
}
define('PAGE_NAME', $pageName);

const MAIN_VIEW = "index";
