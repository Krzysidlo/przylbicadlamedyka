<?php

namespace admin\controllers;

abstract class AdminController
{
    public string  $menu  = "";
    public string  $view  = "index";
    public ?string $title = NULL;
    public string  $style = "";
    public array   $js    = [];

    protected string $file;

    private array $get;

    public function __construct(string $view = NULL)
    {
        if (LOGGED_IN && USER_PRV < 5) {
            header("Location: /");
            exit(0);
        }

        if ($view === NULL) {
            header("Location: /error");
            exit(0);
        }

        $this->view = $view;
        $viewsDir   = ADMIN_DIR . "/views";

        if (!file_exists($viewsDir . "/" . $this->view . ".php")) {
            $viewsDir   = ROOT_DIR . "/views";
            $this->view = 'error';
        }
        $this->file = $viewsDir . "/" . $this->view . ".php";

        $this->get = array_merge($_GET, $_POST);

        $get_post_variables = [
            'hash'       => NULL,
            //login
            'lemail'     => NULL,
            'lpassword'  => NULL,
            'lremember'  => NULL,
            //register
            'name'       => NULL,
            'login'      => NULL,
            'email'      => NULL,
            'password'   => NULL,
            'r-password' => NULL,
            //ajax
            'file'       => NULL,
            'method'     => NULL,
        ];
        foreach ($get_post_variables as $name => $defaultValue) {
            $this->get[$name] = $this->get[$name] ?? $defaultValue;
        }
        $this->menu = $this->view;
    }

    public function content(array $args = [])
    {
        return $this->render($args);
    }

    public function render(array $args = [])
    {
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

    public function head(array $args = [])
    {
        $this->file = INC_DIR . "/header.php";

        return $this->render($args);
    }

    public function foot(array $args = [])
    {
        $this->file = INC_DIR . "/footer.php";

        return $this->render($args);
    }

    protected function get($var)
    {
        return (isset($this->get[$var]) ? $this->get[$var] : NULL);
    }
}