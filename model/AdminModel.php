<?php

/**
 * Модель для работы администратора
 */
class AdminModel extends SiteModel
{
    use AuthTrait;

    const REMEMBER_PERIOD = 604800;

    public function scheme()
    {
        return [
            'id'               => 'int',
            'login'            => 'string',
            'pwd_hash'         => 'string',
            'name'             => 'string',
            'desc'             => 'string',
            'email'            => 'string',
            'phone'            => 'string',
            'create_time'      => 'int',
            'last_update_time' => 'int',
        ];
    }

    public function createTable()
    {
        $ret = $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
                `id` INT NOT NULL AUTO_INCREMENT,
                `login` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `pwd_hash` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `name` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `desc` VARCHAR(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `email` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `phone` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `create_time` INT DEFAULT NULL,
                `last_update_time` INT DEFAULT NULL,
                UNIQUE (`login`),
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );

        if (!$this->countAdmins()) {
            $this->db->query(
                "INSERT INTO ?# SET `login` = ?, `pwd_hash` = ?, `create_time` = ?d",
                $this->table,
                Config(['admin_area', 'def_login']),
                $this->makeHash(Config(['admin_area', 'def_pwd'])),
                time()
            );
        }

        return $ret;
    }

    public function __construct($id = null)
    {
        parent::__construct('admins', $id);
    }

    public static function getConfig(): array
    {
        return Config(['admin_area']);
    }

    public function countAdmins()
    {
        return $this->db->selectCell("SELECT COUNT(*) FROM ?# WHERE 1", $this->table);
    }

    public function __get($key)
    {
        switch ($key) {
            case 'timezone':
                $ret = DEFAULT_TIME_ZONE;
                break;
            default:
                $ret = parent::__get($key);
        }
        return $ret;
    }
}