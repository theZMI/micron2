<?php

/**
 * Пользовательская модель
 */
class CommonUserModel extends \Models\ModelExtends
{
    use AuthTrait;

    const REMEMBER_PERIOD = 31536000; // Время хранения авторизации (в секундах)

    const STATUS_ACTIVE = 0;
    const STATUS_DELETE = -1;

    const GENDER_UNKNOWN = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    const ROLE_REGULAR_WORKER = 1; // Сотрудник
    const ROLE_CHIEF = 2; // Начальник смены
    const ROLE_ACTING_CHIEF = 3; // ИО начальника смены

    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
              `id` INT NOT NULL AUTO_INCREMENT,
              `login` VARCHAR(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `email` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `phone` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `avatar` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `device_id` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `telegram_login` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `telegram_chat_id` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `pwd_hash` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `first_name` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `surname` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `patronymic` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `status` INT DEFAULT NULL,
              `gender` INT DEFAULT NULL,
              `birthday_date` DATE DEFAULT NULL,
              `is_phone_verified` INT DEFAULT NULL,
              `notification_by_phone` INT DEFAULT NULL,
              `is_email_verified` INT DEFAULT NULL,
              `notification_by_email` INT DEFAULT NULL,
              `is_telegram_verified` INT DEFAULT NULL,
              `notification_by_telegram` INT DEFAULT NULL,
              `last_location` VARCHAR(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `role` INT DEFAULT NULL,
              `create_time` INT DEFAULT NULL,
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
            case 'full_name':
                return implode(' ', [$this->surname, $this->first_name, $this->patronymic]);
            case 'timezone':
                return DEFAULT_TIME_ZONE;
            case 'avatar_full_url':
                return Root(Config(['uploader', $this->table, 'upload_path']) . $this->avatar);
            case 'department_id':
                $m = new UserDepartmentModel();
                $list = $m->find(['user_id' => $this->id]);
                return empty($list) ? [] : $list[0]->department_id;
            case 'department':
                return new DepartmentModel($this->department_id);
            default:
                $ret = parent::__get($key);
        }
        return $ret;
    }

    public function __set($key, $value)
    {
        if ($key === 'department_id') {
            $m = new UserDepartmentModel();
            $m->remove($this->id);
            $m->user_id = $this->id;
            $m->department_id = $value;
            return $m;
        }
        return parent::__set($key, $value);
    }

    public static function genPassword()
    {
        $pwd = md5(uniqid(mt_rand(), 1));
        return substr($pwd, 0, 10);
    }

    public function findOne($params)
    {
        $id = null;
        if (isset($params['telegram_login'])) {
            $telegram_login = $params['telegram_login'];
            $id = empty($telegram_login) ?
                null :
                $this->db->selectCell("SELECT `id` FROM ?# WHERE `telegram_login` = ?", $this->table, $telegram_login);
        } elseif (isset($params['login'])) {
            $login = $params['login'];
            $id = $this->getIdByLogin($login);
        } elseif (isset($params['email'])) {
            $email = $params['email'];
            $id = empty($email) ?
                null :
                $this->db->selectCell("SELECT `id` FROM ?# WHERE `email` = ?", $this->table, $email);
        } elseif (isset($params['phone'])) {
            $phone = $params['phone'];
            $id = empty($phone) ?
                null :
                $this->db->selectCell("SELECT `id` FROM ?# WHERE `phone` = ?", $this->table, PhoneFilter($phone));
        }
        return $id ? new static($id) : null;
    }

    public function isEmailBusy($email)
    {
        return $this->db->selectCell("SELECT COUNT(`id`) FROM ?# WHERE `email` = ?", $this->table, $email);
    }

    public function isPhoneBusy($phone)
    {
        return $this->db->selectCell("SELECT COUNT(`id`) FROM ?# WHERE `phone` = ?", $this->table, $phone);
    }

    public function count($status = null)
    {
        return +$this->db->selectCell(
            "SELECT COUNT(*) FROM ?# { WHERE `status` = ?d }",
            $this->table,
            $status ?: DBSIMPLE_SKIP
        );
    }

    public function roles($role = null)
    {
        $all = [
            self::ROLE_REGULAR_WORKER => 'Сотрудник',
            self::ROLE_ACTING_CHIEF => 'ИО начальника смены',
            self::ROLE_CHIEF => 'Начальник смены',
        ];
        return empty($role) ? $all : $all[$role];
    }
}