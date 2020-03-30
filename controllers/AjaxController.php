<?php

namespace controllers;

use Exception;
use classes\User;
use classes\Functions as fs;

class AjaxController extends PageController
{

    public function content(array $args = [])
    {
        if (empty($this->get('file'))) {
            self::redirect("/");
            exit(0);
        }

        if (!in_array($this->get('file'), $this->allowedNoLog)) {
            try {
                new User;
            } catch (Exception $e) {
                fs::log("Error: Unauthorized ajax call", $e->getMessage());
                self::redirect("/");
                exit(0);
            }
        }

        $file   = filter_var($this->get('file'), FILTER_SANITIZE_STRING);
        $method = "ajax_" . filter_var($this->get('method'), FILTER_SANITIZE_STRING);
        [$fileName] = explode(".", $file);
        $controllerPath = ROOT_DIR . "/controllers/" . ucfirst($fileName) . "Controller.php";
        if (file_exists($controllerPath)) {
            $newControllerName = "controllers\\" . ucfirst($fileName) . "Controller";
            if (method_exists($newControllerName, $method)) {
                $newControllerName::$method($this->get());
                exit(0);
            }
        }

        $file = AJAX_DIR . "/" . $file;

        if (!file_exists($file) || is_dir($file)) {
            header("Location: /");
            exit(0);
        }
        include_once($file);
        exit(0);
    }
}