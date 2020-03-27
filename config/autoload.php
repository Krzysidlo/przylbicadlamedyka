<?php
include_once __DIR__ . "/../vendor/autoload.php";

spl_autoload_register(function ($name) {
    $name = str_replace('\\', '/', $name);

    $file = ROOT_DIR . "/" . $name . ".php";
    if (file_exists($file)) {
        require_once $file;
    }
});