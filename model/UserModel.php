<?php

/**
 * Пользовательская модель
 */
class UserModel extends \Models\ModelExtends
{
    use AuthTrait;

    const REMEMBER_PERIOD = 31536000; // Время хранения авторизации (в секундах)

    const STATUS_ACTIVE = 0;
    const STATUS_DELETE = -1;

    const GENDER_UNKNOWN = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

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
              `last_name` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
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
              `create_time` INT DEFAULT NULL,
              UNIQUE (`login`),
              PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('users', $id);
    }

    public static function getConfig(): array
    {
        return Config(['user_model']);
    }

    public function getIdByTelegramLogin($telegram_login)
    {
        return empty($telegram_login) ?
            null :
            $this->db->selectCell("SELECT `id` FROM ?# WHERE `telegram_login` = ?", $this->table, $telegram_login);
    }

    public function getIdByLogin($login)
    {
        return empty($login) ?
            null :
            $this->db->selectCell("SELECT `id` FROM ?# WHERE `login` = ?", $this->table, $login);
    }

    public function getIdByEmail($email)
    {
        return empty($email) ?
            null :
            $this->db->selectCell("SELECT `id` FROM ?# WHERE `email` = ?", $this->table, $email);
    }

    public function getIdByPhone($phne)
    {
        return empty($phne) ?
            null :
            $this->db->selectCell("SELECT `id` FROM ?# WHERE `phone` = ?", $this->table, PhoneFilter($phone));
    }

    public function isLoginBusy($login)
    {
        return $this->db->selectCell("SELECT COUNT(`id`) FROM ?# WHERE `login` = ?", $this->table, $login);
    }

    public function isEmailBusy($email)
    {
        return $this->db->selectCell("SELECT COUNT(`id`) FROM ?# WHERE `email` = ?", $this->table, $email);
    }

    public function isPhoneBusy($phone)
    {
        return $this->db->selectCell("SELECT COUNT(`id`) FROM ?# WHERE `phone` = ?", $this->table, $phone);
    }

    private static function genUniqueWord()
    {
        $pwd = md5(uniqid(mt_rand(), 1));
        return substr($pwd, 0, 10);
    }

    public static function genPassword()
    {
        return self::genUniqueWord();
    }

    public function __get($key)
    {
        switch ($key) {
            case 'full_name':
                $ret = implode(' ', [$this->first_name, $this->last_name]);
                break;
            case 'timezone':
                $ret = DEFAULT_TIME_ZONE;
                break;
            case 'avatar_full_url':
                $ret = Root(Config(['uploader', 'users', 'upload_path']) . $this->avatar);
                break;
            default:
                $ret = parent::__get($key);
        }
        return $ret;
    }

    public function count($status = null)
    {
        return +$this->db->selectCell(
            "SELECT COUNT(*) FROM ?# { WHERE `status` = ?d }",
            $this->table,
            $status ?: DBSIMPLE_SKIP
        );
    }

    public function isCorrectAuth($login, $password_hash)
    {
        $user_id = (new self())->getIdByLogin($login);
        $model   = new self($user_id);
        return $model->isExists() && $model->pwd_hash === $password_hash;
    }
}