<?php

namespace admin\controllers;

use classes\User;
use controllers\PageController;

abstract class AdminController extends PageController
{
    public function __construct(string $view = NULL)
    {
        parent::__construct($view);
        if (!LOGGED_IN || USER_PRV !== User::USER_ROOT) {
            self::redirect("/");
            exit(0);
        }

        if ($view === NULL) {
            self::redirect("/error");
            exit(0);
        }

        $this->view = $view;

        $this->menu = $this->view;
    }

    public function render(array $args = [])
    {
        if ($this->file === NULL) {
            $viewsDir   = ADMIN_DIR . "/views";

            if (!file_exists($viewsDir . "/" . $this->view . ".php")) {
                $viewsDir   = ROOT_DIR . "/views";
                $this->view = 'error';
            }
            $this->file = $viewsDir . "/" . $this->view . ".php";
        }
        ob_start();
        foreach ($args as $name => $value) {
            ${$name} = $value;
        }
        if (file_exists($this->file)) {
            include $this->file;
        }
        $var = ob_get_contents();
        ob_end_clean();

        return $var;
    }

    public function menu(array $args = [])
    {
        if (LOGGED_IN && $this->view !== 'error') {
            $this->file = ADMIN_DIR . "/includes/menu.php";
        } else {
            $this->file = "";
        }

        return $this->render($args);
    }
}