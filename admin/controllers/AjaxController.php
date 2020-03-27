<?php

namespace admin\controllers;

class AjaxController extends AdminController
{

    public function content(array $args = [])
    {
        if (empty($_GET['filename'])) {
            header("Location: /");
            exit(0);
        }

        $filename = filter_var($_GET['filename'], FILTER_SANITIZE_STRING);
        $file     = ADMIN_DIR . "/ajax/" . $filename;

        if (!file_exists($file)) {
            header("Location: /");
            exit(0);
        }
        include_once($file);
        exit(0);
    }
}