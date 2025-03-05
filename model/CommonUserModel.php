<?php

/**
 * Пользовательская модель
 */
class CommonUserModel extends SiteModel
{
    use AuthTrait;

    const PAGE_LIMIT = 999;

    const REMEMBER_PERIOD = 31536000; // Время хранения авторизации (в секундах)

    const STATUS_ACTIVE = 0;
    const STATUS_DELETE = -1;

    const GENDER_UNKNOWN = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    public function scheme()
    {
        return [
            'id'                       => 'int',
            'login'                    => 'string',
            'pwd_hash'                 => 'string',
            'status'                   => 'int',

            'avatar'                   => 'string',
            'device_id'                => 'string',
            'first_name'               => 'string',
            'surname'                  => 'string',
            'patronymic'               => 'string',
            'gender'                   => 'int',
            'birthday_date'            => 'string',
            'last_location'            => 'string',

            'email'                    => 'string',
            'is_email_verified'        => 'int',
            'notification_by_email'    => 'int',

            'phone'                    => 'string',
            'is_phone_verified'        => 'int',
            'notification_by_phone'    => 'int',

            'telegram_login'           => 'string',
            'telegram_chat_id'         => 'string',
            'is_telegram_verified'     => 'int',
            'notification_by_telegram' => 'int',

            'create_time'              => 'int',
            'last_update_time'         => 'int',
        ];
    }

    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
              `id` INT NOT NULL AUTO_INCREMENT,
              `login` VARCHAR(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `pwd_hash` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `status` INT DEFAULT NULL,

              `avatar` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `device_id` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `first_name` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `surname` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `patronymic` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `gender` INT DEFAULT NULL,
              `birthday_date` DATE DEFAULT NULL,
              `last_location` VARCHAR(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,

              `email` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `is_email_verified` INT DEFAULT NULL,
              `notification_by_email` INT DEFAULT NULL,

              `phone` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `is_phone_verified` INT DEFAULT NULL,
              `notification_by_phone` INT DEFAULT NULL,

              `telegram_login` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `telegram_chat_id` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `is_telegram_verified` INT DEFAULT NULL,
              `notification_by_telegram` INT DEFAULT NULL,

              `create_time` INT DEFAULT NULL,
              `last_update_time` INT DEFAULT NULL,
              UNIQUE (`login`),
              PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($table, $id = null)
    {
        parent::__construct($table, $id);
    }

    public static function getConfig(): array
    {
        return Config([static::$prefix . '_model']);
    }

    public function __get($key)
    {
        switch ($key) {
            case 'timezone':
                return DEFAULT_TIME_ZONE;
            case 'full_name':
                return implode(' ', [$this->surname, $this->first_name, $this->patronymic]);
            case 'avatar_url':
                return Root(Config(['uploader', $this->table, 'upload_path']) . $this->avatar);
            default:
                $ret = parent::__get($key);
        }
        return $ret;
    }

    public function __set($key, $value)
    {
        if ($key === 'phone') {
            $value = PhoneFilter($value);
            return parent::__set($key, $value);
        }
        return parent::__set($key, $value);
    }

    public static function genPassword()
    {
        $pwd = md5(uniqid(mt_rand(), 1));
        return substr($pwd, 0, 10);
    }

    public function isEmailBusy($email)
    {
        return $this->db->selectCell("SELECT COUNT(`id`) FROM ?# WHERE `email` = ?", $this->table, $email);
    }

    public function isPhoneBusy($phone)
    {
        return $this->db->selectCell("SELECT COUNT(`id`) FROM ?# WHERE `phone` = ?", $this->table, $phone);
    }

    public function findOne($params)
    {
        $ids = $this->db->selectCol("SELECT `id` FROM ?#", $this->table);
        if (isset($params['login'])) {
            $found = [$this->getIdByLogin($params['login'])];
            $ids   = array_intersect($ids, $found);
        }
        if (isset($params['email'])) {
            $found = $this->db->selectCol("SELECT `id` FROM ?# WHERE `email` = ?", $this->table, $params['email']);
            $ids   = array_intersect($ids, $found);
        }
        if (isset($params['phone'])) {
            $found = $this->db->selectCol("SELECT `id` FROM ?# WHERE `phone` = ?", $this->table, PhoneFilter($params['phone']));
            $ids   = array_intersect($ids, $found);
        }
        if (isset($params['telegram_login'])) {
            $found = $this->db->selectCol("SELECT `id` FROM ?# WHERE `telegram_login` = ?", $this->table, $params['telegram_login']);
            $ids   = array_intersect($ids, $found);
        }
        if (isset($params['telegram_chat_id'])) {
            $found = $this->db->selectCol("SELECT `id` FROM ?# WHERE `telegram_chat_id` = ?", $this->table, $params['telegram_chat_id']);
            $ids   = array_intersect($ids, $found);
        }
        // ...
        $id = count($ids) ? $ids[0] : null;
        return $id ? new static($id) : null;
    }

    public function find($params)
    {
        $ids = $this->db->selectCol("SELECT `id` FROM ?#", $this->table);
        if (isset($params['status'])) {
            $found = $this->db->selectCol("SELECT `id` FROM ?# WHERE `status` = ?d", $this->table, $params['status']);
            $ids   = array_intersect($ids, $found);
        }
        if (isset($params['surname'])) {
            $found = $this->db->selectCol("SELECT `id` FROM ?# WHERE `surname` = ?", $this->table, $params['surname']);
            $ids   = array_intersect($ids, $found);
        }
        if (isset($params['first_name'])) {
            $found = $this->db->selectCol("SELECT `id` FROM ?# WHERE `first_name` = ?", $this->table, $params['first_name']);
            $ids   = array_intersect($ids, $found);
        }
        if (isset($params['patronymic'])) {
            $found = $this->db->selectCol("SELECT `id` FROM ?# WHERE `patronymic` = ?", $this->table, $params['patronymic']);
            $ids   = array_intersect($ids, $found);
        }
        // ...
        return array_map(fn($ids) => new static($ids), $ids);
    }

    public function count($statuses = [])
    {
        return +$this->db->selectCell(
            "SELECT COUNT(*) FROM ?# { WHERE `status` IN (?a) }",
            $this->table,
            $statuses ?: DBSIMPLE_SKIP
        );
    }

    public function getDataToApi()
    {
        $data = $this->getData();
        $data['use_timer'] = $this->use_timer;
        return $data;
    }

    public function countNotifies() {
        // TODO: Здесь должно вычисляться количество нотификаций у юзера
        return 1;
    }
}