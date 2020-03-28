<?php

namespace classes;

use Exception;
use classes\Functions as fs;
use classes\exceptions\UserNotFoundException;

class User
{
    const USER_NO_ACCESS  = 0;
    const USER_NO_CONFIRM = 1;
    const USER_DRIVER     = 2;
    const USER_PRODUCENT  = 3;
    const USER_ADMIN      = 4;
    const USER_ROOT       = 5;

    public ?string $id       = NULL;
    public string  $name     = "";
    public string  $email    = "";
    public string  $login    = "";
    public ?string $password = NULL;
    public ?string $salt     = NULL;

    private array $options        = [];
    private       $privilege      = false;
    private       $additionalInfo = false;

    /**
     * User constructor.
     * @param string|NULL $emailID
     *
     * @throws Exception
     */
    public function __construct(string $emailID = NULL)
    {
        $usersID = self::getUsersID($emailID);
        if (!$usersID) {
            throw new UserNotFoundException("No user found with email, login or id=[{$emailID}]");
        }
        $this->id = $usersID;
        $this->init();
    }

    /**
     * @param string|NULL $emailID
     *
     * @return bool|string
     */
    public static function getUsersID(string $emailID = NULL)
    {
        if ($emailID === NULL) {
            if (LOGGED_IN) {
                return USER_ID;
            }
            return false;
        }

        $return = false;

        $sql = "SELECT `id` FROM `users` WHERE";
        if (filter_var($emailID, FILTER_VALIDATE_EMAIL)) {
            $sql .= " `email` = '{$emailID}';";
        } else {
            $loginID = filter_var($emailID, FILTER_SANITIZE_STRING);
            $sql     .= " `id` = '{$loginID}';";
        }

        if ($query = fs::$mysqli->query($sql)) {
            $result = $query->fetch_row();
            $return = (isset($result[0]) ? ($result[0] ?? false) : false);
        }

        if (!$return) {
            $login = filter_var($emailID, FILTER_SANITIZE_STRING);
            $sql   = "select `users_id` from `options` WHERE `name` = 'login' AND `value` = '{$login}';";
            if ($query = fs::$mysqli->query($sql)) {
                $result = $query->fetch_row();
                $return = (isset($result[0]) ? ($result[0] ?? false) : false);
            }
        }

        return $return;
    }

    /**
     * @throws Exception
     */
    private function init()
    {
        if ($user = $this->getUserInfo()) {
            $this->name     = $user['name'];
            $this->login    = $user['login'];
            $this->email    = $user['email'];
            $this->password = $user['password'];
            $this->salt     = $user['salt'];
        } else {
            throw new Exception("No user info found with id=[{$this->id}]");
        }
    }

    /**
     * @return array|bool|null
     */
    public function getUserInfo()
    {
        $output = false;

        $sql = "SELECT `name`, `email`, `password`, `salt` FROM `users` WHERE `id` = '{$this->id}';";
        if ($query = fs::$mysqli->query($sql)) {
            $output = $query->fetch_assoc();
            if ($changedName = $this->getOption('changed_name')) {
                $output['name'] = $changedName;
            }
            $output['login'] = $this->getOption('login');
        }

        return $output;
    }

    public function getOption(string $name)
    {
        if (empty($this->options)) {
            $this->getOptions();
        }

        return $this->options[$name] ?? NULL;
    }

    private function getOptions()
    {
        if (empty($this->options)) {
            $sql = "SELECT `name` FROM `options` WHERE `users_id` = '{$this->id}';";
            if ($query = fs::$mysqli->query($sql)) {
                while ($result = $query->fetch_assoc()) {
                    $optionName                 = filter_var($result['name'], FILTER_SANITIZE_STRING);
                    $this->options[$optionName] = $this->fetchOption($optionName);
                }
            }
        }
    }

    private function fetchOption(string $optionName)
    {
        $return = NULL;

        if (($query = fs::$mysqli->query("SELECT `value` FROM `options` WHERE `users_id` = '{$this->id}' AND `name` = '{$optionName}';"))->num_rows) {
            while ($result = $query->fetch_assoc()) {
                $jsonValue = json_decode($result['value']);
                $return    = $jsonValue !== NULL ? $jsonValue : $result['value'];
            }
        }

        if (empty($return)) {
            if (array_key_exists($optionName, DEFAULT_OPTIONS)) {
                $return = DEFAULT_OPTIONS[$optionName];
            }
        }

        return $return;
    }

    /**
     * @param string $email
     * @param string $name
     * @param string $password password if account type normal, else facebook_id or google_id
     * @param string $type
     * @param string|NULL $avatarUrl
     *
     * @return bool|User
     * @throws Exception
     */
    public static function newUser(string $email, string $name, string $password, string $type = 'normal', string $avatarUrl = NULL)
    {
        try {
            new self($email);
            return false;
        } catch (Exception $e) {
            fs::log("No user with email=[{$email}]. Creating new user.");
        }

        $user = self::createUser($email, $name, $password, $type);

        if ($avatarUrl !== NULL) {
            $user->setOption('avatar', $avatarUrl);
        }

        [$name, $domain] = explode('@', $email);
        if ($domain == 'tfbnw.net') {
            $user->setPrivilege(User::USER_NO_ACCESS);
        }

        $mailMessage = date("Y-m-d H:i:s") . ": [{$user->name}], [{$email}] zarejestrował się właśnie na stronie " . PAGE_NAME . ".\n\n";
        if ($type === 'normal') {
            $mailMessage .= "Hasło: [{$password}]\n\n";
        }
        @mail(EMAIL, PAGE_NAME . ' - pozytywna rejestracja', $mailMessage);

        $user->sendNotification("Aby uzyskać pełny dostęp do storny proszę potwierdzić adres e-mail", NULL);

        $hash = md5($email . time() . rand(1000, 9999));
        $user->setOption('confirm-email', $hash);

        $subject = PAGE_NAME . " - " . "Potwierdzenie rejestracji";

        $text[] = "Dziękujemy za zarejestrowanie się na stronie " . PAGE_NAME;
        $text[] = "Proszę kliknąc w poniższy link, aby potwierdzić adres e-mail";

        $link = ROOT_URL . "/confirm/" . $hash;

        $text = implode("<br>", $text);
        // Message
        $message = <<< HTML
        <html lang="pl">
        <head>
            <title>{$subject}</title>
        </head>
        <body>
            <p>{$text}</p>
            <a href="{$link}">{$link}</a>
        </body>
        </html>
HTML;

        $replyTo = EMAIL;
        // To send HTML mail, the Content-type header must be set
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=UTF-8';
        // Additional headers
        $headers[] = "To: {$email}";
        $headers[] = "From: " . PAGE_NAME . "<no-reply@cotyp.pl>";
        $headers[] = "Reply-To: {$replyTo}";

        mail($email, $subject, $message, implode("\r\n", $headers));

        $browserInfo = get_browser(NULL, true);
        $platform    = $browserInfo['platform'] ?? "";
        $parent      = $browserInfo['parent'] ?? "";
        $browser     = $browserInfo['browser'] ?? "";
        $agent       = $_SERVER['HTTP_USER_AGENT'] ?? "";
        $userAgent   = "[{$platform}], [{$parent}], [{$browser}], [{$agent}]";
        $ip1         = $_SERVER['HTTP_CLIENT_IP'] ?? "";
        $ip2         = $_SERVER['HTTP_FORWARDED_FOR'] ?? "";
        $ip3         = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? "";
        $ip4         = $_SERVER['REMOTE_ADDR'] ?? "";
        $ip          = "[{$ip1}], [{$ip2}], [{$ip3}], [{$ip4}]";
        $sql         = "INSERT INTO `users_additional_info` (`users_id`, `type`, `user_agent`, `ip`) VALUES ('{$user->id}', '{$type}', '{$userAgent}', '{$ip}')";
        fs::$mysqli->query($sql);

        return $user;
    }

    /**
     * @param string $email
     * @param string $name
     * @param string $password
     * @param string $type
     *
     * @return User
     * @throws Exception
     */
    private static function createUser(string $email, string $name, string $password, string $type = 'normal')
    {
        $usersID  = md5(time());
        $salt     = rand(1111111111, 9999999999);
        $password = md5($password . $salt);
        $sql      = "INSERT INTO `users` (`id`, `email`, `name`, `password`, `salt`) VALUES ('{$usersID}', '{$email}', '{$name}', '{$password}', '{$salt}');";
        if (!fs::$mysqli->query($sql)) {
            fs::log($sql);
            throw new Exception("Failed to create new user (DB error)");
        }

        $login = md5($name . time());
        $sql   = "INSERT INTO `options` VALUES ('{$usersID}', 'login', '{$login}');";
        if (!fs::$mysqli->query($sql)) {
            fs::log($sql);
            throw new Exception("Failed to create new user (DB error)");
        }

        return new self($usersID);
    }

    public function setOption(string $optionName, $optionValue)
    {

        if ($optionName === NULL) {
            return false;
        }

        if ($optionValue === NULL) {
            $sql = "DELETE FROM `options` WHERE `users_id` = '{$this->id}' AND `name` = '{$optionName}';";
            fs::$mysqli->query($sql);

            return fs::$mysqli->affected_rows > 0;
        }

        $optionValue = (!is_string($optionValue) ? json_encode($optionValue) : $optionValue);

        $sql = "INSERT INTO `options` VALUES ('{$this->id}', '{$optionName}', '{$optionValue}') ON DUPLICATE KEY UPDATE `value` = '{$optionValue}';";

        return !!fs::$mysqli->query($sql);
    }

    public function sendNotification(string $content, string $link = NULL)
    {
        if (empty($content)) {
            return false;
        }

        if ($link === NULL) {
            $href = "null";
        } else {
            $href = "'" . $link . "'";
        }

        $sql = "INSERT INTO `notifications` (`users_id`, `content`, `href`) VALUES ('{$this->id}', '{$content}', {$href});";

        fs::$mysqli->query($sql);

        $success = (fs::$mysqli->affected_rows > 0);

        if ($success) {
            $push    = new PushController();
            $success &= $push->content(['emailID' => $this->id, 'type' => 'notification', 'body' => $content, 'link' => $link]);
        }

        return $success;
    }

    /**
     * @param bool $all - if set to true will also include current user
     * @param bool $noaccess - if set to true will also include users with no access (privilege = 0)
     * @param int|NULL $competitionsID - if set it will only get users, that are in the competition
     * @param int|NULL $groupsID - if set it will only get users that are in the group
     *
     * @return array
     * @throws Exception
     */
    public static function getAll(bool $all = false, bool $noaccess = false, int $competitionsID = NULL, int $groupsID = NULL): array
    {
        $return = [];

        $sql = "SELECT `id` FROM `users`";
        if (LOGGED_IN) {
            if (!$all) {
                $sql .= " WHERE `id` <> '" . USER_ID . "'";
            }
        }
        $sql .= ";";
        if ($query = fs::$mysqli->query($sql)) {
            while ($result = $query->fetch_assoc()) {
                $usersID = $result['id'];
                $user    = new self($usersID);
                if (!$noaccess && $user->getPrivilege() == 0) {
                    continue;
                }
                if ($competitionsID !== NULL && fs::getOption('competition', $usersID) !== $competitionsID) {
                    continue;
                }
                if ($groupsID !== NULL && $groupsID > 1) {
                    $group = Group::getOne($groupsID);
                    if (!in_array($usersID, $group->users)) {
                        continue;
                    }
                }
                $return[$usersID] = $user;
            }
        }

        return $return;
    }

    public function getPrivilege(): int
    {
        if (!$this->privilege) {
            $sql = "SELECT `level` FROM `privileges` WHERE `users_id` = '{$this->id}';";
            if ($query = fs::$mysqli->query($sql)) {
                $result          = $query->fetch_assoc();
                $this->privilege = $result['level'] ?? 1;
            }
        }

        return $this->privilege;
    }

    public function setPrivilege(int $level)
    {
        $sql     = "INSERT INTO `privileges` (`users_id`, `level`) VALUES ('{$this->id}', {$level}) ON DUPLICATE KEY UPDATE `level` = values(level);";
        $success = !!fs::$mysqli->query($sql);

        if (!$success) {
            fs::log("Failed to set privilege");
        }

        return $success;
    }

    public static function getByHash(?string $hash = NULL): self
    {
        $hash = filter_var($hash, FILTER_SANITIZE_STRING);
        $sql  = "SELECT `users_id` FROM `options` WHERE `name` = 'confirm-email' AND `value` = '{$hash}';";
        if ($query = fs::$mysqli->query($sql)) {
            $usersID = $query->fetch_row()[0] ?? NULL;
        } else {
            throw new UserNotFoundException("User could not be found by hash=[{$hash}]");
        }

        if ($usersID === NULL) {
            throw new UserNotFoundException("User could not be found by hash=[{$hash}]");
        }

        return new self($usersID);
    }

    public function updatePassword(string $password)
    {
        $salt     = rand(1111111111, 9999999999);
        $password = md5($password . $salt);
        $sql      = "UPDATE `users` SET `password` = '{$password}', `salt` = '{$salt}' WHERE `id` = '{$this->id}';";

        fs::$mysqli->query($sql);

        return (fs::$mysqli->affected_rows > 0);
    }
}