<?php

namespace classes;

use DateTime;
use Exception;
use classes\Functions as fs;

class Pin
{
    public int      $id;
    public string   $name;
    public string   $description;
    public string   $latLng;
    public string   $type;
    public ?string  $bascinet = NULL;
    public ?string  $material = NULL;
    public DateTime $created_at;

    /**
     * Point constructor.
     * @param string $table
     * @param int $id
     *
     * @throws Exception
     */
    public function __construct(int $id)
    {
        $this->id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $info     = false;

        $sql = "SELECT * FROM `pins` WHERE `id` = '{$this->id}';";

        if ($query = fs::$mysqli->query($sql)) {
            $info = $query->fetch_assoc() ?? false;
        }

        if ($info) {
            $this->name        = trim((string)$info['name']);
            $this->description = trim((string)$info['description']);
            $this->latLng      = trim((string)$info['latLng']);
            $this->type        = trim((string)$info['type']);
            $this->bascinet    = !empty($info['bascinet']) ? intval($info['bascinet']) : NULL;
            $this->material    = !empty($info['material']) ? intval($info['material']) : NULL;
            $this->created_at  = new DateTime($info['created_at']);
        } else {
            throw new Exception("No pins info found with id=[{$this->id}]");
        }

        $sql = "SELECT SUM(`quantity`) FROM `hos_mag` WHERE `pins_id` = {$this->id} AND `deleted` = 0;";
        if ($query = fs::$mysqli->query($sql)) {
            if ($result = $query->fetch_row()) {
                $quantity = intval(filter_var($result[0], FILTER_SANITIZE_NUMBER_INT));
                switch ($this->type) {
                    case 'hospital':
                        $this->bascinet -= $quantity;
                        break;
                    case 'magazine':
                        $this->material -= $quantity;
                        break;
                }
            }
        }
    }

    /**
     * @param string $name
     * @param string $description
     * @param string $latLng
     * @param string $type
     * @param int|null $bascinet
     * @param int|null $material
     *
     * @return array
     */
    public static function create(string $name, string $description, string $latLng, string $type = "hospital", ?int $bascinet = NULL, ?int $material = NULL): array
    {
        if ($bascinet === NULL) {
            $bascinet = "NULL";
        }
        if ($material === NULL) {
            $material = "NULL";
        }

        $sql = "INSERT INTO `pins` (`name`, `description`, `latLng`, `type`, `bascinet`, `material`) VALUES ('{$name}', '{$description}', {$latLng}, '{$type}', {$bascinet}, {$material});";

        if (!!fs::$mysqli->query($sql)) {
            $data = [
                'success' => true,
                'alert'   => "success",
                'message' => "Poprawnie dodano nowy szpital",
            ];
        } else {
            $data = [
                'success' => false,
                'alert'   => "danger",
                'message' => "Błąd przy zapisie do bazy danych. Proszę odświeżyć stronę i spróbować ponownie.",
            ];
        }

        return $data;
    }

    /**
     * @param bool $deleted
     *
     * @return array
     * @throws Exception
     */
    public static function getAll(bool $deleted = false): array
    {
        $return = [];

        $sql = "SELECT `id` FROM `pins`";

        if (!$deleted) {
            $sql .= " WHERE `deleted` = 0;";
        }

        if ($query = fs::$mysqli->query($sql)) {
            while ($result = $query->fetch_row()) {
                $id = $result[0] ?? false;
                $id = intval($id);
                if ($id) {
                    $return[] = new self($id);
                }
            }
        }

        return $return;
    }

    public function delete(): bool
    {
        $sql = "UPDATE `pins` SET `deleted` = 1 WHERE `id` = {$this->id};";

        return !!fs::$mysqli->query($sql);
    }
}