<?php

namespace controllers;

use Exception;
use classes\Frozen;
use classes\Functions as fs;

abstract class PageController
{
    public array   $allowedNoLog = [];
    public string  $menu         = "";
    public string  $view         = "index";
    public ?string $title        = NULL;

    protected ?string $file = NULL;
    protected array   $get;

    public function __construct($view = NULL)
    {
        if (CONST_MODE) {
            $view = "construction";
        }

        if ($view === NULL) {
            self::redirect("/error");
        }

        if ($view === 'logout') {
            $this->logout();
            exit(0);
        }

        $this->view = $view;

        $get       = $_GET ?? [];
        $post      = $_POST ?? [];
        $this->get = array_merge($get, $post);

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
            'tel'        => NULL,
            'address'    => NULL,
            'password'   => NULL,
            'r-password' => NULL,
            //ajax
            'file'       => NULL,
            'method'     => NULL,
        ];

        $this->allowedNoLog = [
            'register',
        ];

        foreach ($get_post_variables as $name => $defaultValue) {
            $this->get[$name] = $this->get[$name] ?? $defaultValue;
        }
        $this->menu = $this->view;
    }

    public static function redirect($path)
    {
        header("Location: " . $path);
        exit(0);
    }

    private function logout()
    {
        fs::setACookie('usersID', NULL, -1);
        session_destroy();
        header("Location: /logout");
        exit(0);
    }

    public function content(array $args = [])
    {
        return $this->render($args);
    }

    public function render(array $args = [])
    {
        if ($this->file === NULL) {
            $viewsDir = ROOT_DIR . "/views";

            if (!file_exists($viewsDir . "/" . $this->view . ".php")) {
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
        if (LOGGED_IN && !in_array($this->view, ['construction', 'error', 'noaccess', 'offline'])) {
            $this->file = INC_DIR . "/menu.php";
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

    public function modals(array $args = [])
    {
        if (LOGGED_IN && !in_array($this->view, ['construction', 'error', 'noaccess', 'offline'])) {
            $this->file = INC_DIR . "/modals.php";

            $args['activities'] = TripsController::frozenActivities(true);
        }

        return $this->render($args);
    }

    protected function get($var = NULL)
    {
        if ($var === NULL) {
            return $this->get;
        }

        return (isset($this->get[$var]) ? $this->get[$var] : NULL);
    }
}