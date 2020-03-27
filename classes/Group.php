<?php

namespace classes;

use Exception;
use classes\Functions as fs;
use classes\exceptions\NoGroupIDException;
use classes\exceptions\GroupExistsException;

class Group
{
    public $id;
    public $name;
    public $code;
    public $createdBy;
    public $users;

    /**
     * Group constructor.
     * @param int|NULL $groupsID
     *
     * @throws Exception
     */
    public function __construct(int $groupsID = NULL)
    {
    	$this->id = NULL;
        if ($groupsID !== NULL) {
            $this->init($groupsID);
        }
    }

    /**
     * @param string $name
     *
     * @throws Exception
     */
    public function __get(string $name)
    {
        if ($this->id === NULL) {
            throw new Exception("Group not initiated");
        }
    }

    /**
     * @param int $groupsID
     *
     * @return bool
     * @throws NoGroupIDException
     * @throws GroupExistsException
     */
    public function init(int $groupsID)
    {
        if ($this->id !== NULL) {
            if ($this->id == $groupsID) {
                return true;
            } else {
                throw new GroupExistsException(fs::t("Group already initiated with a different id"));
            }
        }

        if ($groupsID === 0) {
            throw new NoGroupIDException(fs::t("No groupsID"));
        }

        $sql = "SELECT * FROM `groups` WHERE id = {$groupsID};";
        if ($query = fs::$mysqli->query($sql)) {
        	$result = $query->fetch_assoc();
            if ($result !== NULL) {
            	$this->id        = intval($result['id']);
                $this->name      = $result['name'];
                $this->code      = $result['code'];
                $this->createdBy = $result['created_by'];
                $this->users     = (array)json_decode($result['users']);
            }
        }

        return true;
    }

    public function save()
    {
        $users = json_encode($this->users);
        $sql = "UPDATE `groups` SET `name` = '{$this->name}', `code` = '{$this->code}', `users` = '{$users}' WHERE `id` = {$this->id};";
        return fs::$mysqli->query($sql);
    }

    /**
     * @param string|NULL $usersID
     *
     * @return bool
     */
    public function leave(string $usersID = NULL)
    {
        $success = false;

        $usersID = $usersID ?? USER_ID;

        $groups = fs::getOption('groups', $usersID);
        if (($key = array_search($this->id, $groups)) !== false) {
            unset($groups[$key]);
            $success = fs::setOption('groups', $groups, $usersID);
        }

        if ($success) {
            if (($key = array_search($usersID, $this->users)) !== false) {
                unset($this->users[$key]);
            }
            $success = $this->save();
        }

        return $success;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function delete()
    {
        if ($this->id === 1) {
            throw new Exception(fs::t("Trying to delete default group"));
        }
        $success = true;
        foreach ($this->users as $usersID) {
            $userGroups = fs::getOption('groups', $usersID);
            if (($key = array_search($this->id, $userGroups)) !== false) {
                unset($userGroups[$key]);
            }
            $success |= fs::setOption('groups', $userGroups, $usersID);
        }

        if ($success) {
            $sql = "DELETE FROM `groups` WHERE `id` = {$this->id};";
            $success = fs::$mysqli->query($sql);
        }

        return $success;
    }

    /**
     * @param string $name
     * @param string $code
     * @param string|NULL $usersID
     *
     * @return Group|mixed|string
     * @throws Exception
     */
    public static function create(string $name, string $code, string $usersID = NULL)
    {
        $usersID = $usersID ?? USER_ID;

        if (self::getByCode($code)) {
            return fs::t("A group with this code already exists") . ". " . fs::t("If you would like to join it please clear the name input") . ".";
        }

        $output = fs::t("There was an unexpected error");
        $users = json_encode([$usersID]);
        $sql = "INSERT INTO `groups` (`name`, `code`, `created_by`, `users`) VALUES('{$name}', '{$code}', '{$usersID}', '{$users}');";
        if (fs::$mysqli->query($sql)) {
        	$id = fs::$mysqli->insert_id ?? NULL;
        	if ($id !== NULL) {
        		$groups = fs::getOption('groups', $usersID);
        		if (!in_array($id, $groups)) {
        			$groups[] = $id;
        			if (fs::setOption('groups', $groups, $usersID)) {
        				$output = new self($id);
        			}
        		} else {
        			$output = new self($id);
        		}
        	}
        }

        return $output;
    }

    /**
     * @param string $code
     * @param string|NULL $usersID
     *
     * @return bool|Group|mixed|string
     * @throws Exception
     */
    public static function join(string $code, string $usersID = NULL)
    {
        $usersID ??= USER_ID;

        $output = fs::t("There was an unexpected error");
        if ($group = self::getByCode($code)) {
            if (!in_array($usersID, $group->users)) {
                $group->users[] = $usersID;
                $group->save();
                $groups = fs::getOption('groups', $usersID);
                if (!in_array($group->id, $groups)) {
                    $groups[] = $group->id;
                    if (fs::setOption('groups', $groups, $usersID)) {
                        $output = $group;
                    }
                } else {
                    $output = $group;
                }
            } else {
                $output = fs::t("You are already in this group");
            }
        } else {
            $output = fs::t("The group with that code does not exists") . ". " . fs::t("If you want to create a new group please input it's name") . ".";
        }

        return $output;
    }

    /**
     * @param string $code
     *
     * @return bool|Group
     * @throws Exception
     */
    public static function getByCode(string $code)
    {
        $output = false;

    	$sql = "SELECT `id` FROM `groups` WHERE `code` = '{$code}';";
        if ($query = fs::$mysqli->query($sql)) {
        	$result = $query->fetch_assoc();
            $id = ($result !== NULL ? intval($result['id']) : 0);
            if ($id) {
                $output = new self($id);
            }
        }

        return $output;
    }

    /**
     * @param int $groupsID
     *
     * @return Group
     * @throws Exception
     */
    public static function getOne(int $groupsID)
    {
        return new self($groupsID);
    }

    /**
     * @param string|NULL $usersID
     *
     * @return array
     * @throws Exception
     */
    public static function getAll(string $usersID = NULL): array
    {
        $usersID ??= USER_ID;

        $groups = [];
        $userGroups = fs::getOption('groups', $usersID);
        foreach ($userGroups as $groupsID) {
            $groups[$groupsID] = new self($groupsID);
        }

        return $groups;
    }
}
