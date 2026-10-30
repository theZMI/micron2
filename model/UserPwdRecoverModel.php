<?php

/**
 * Одноразовые коды подтверждения email / входа без пароля.
 * Код живёт pwd_recover_time секунд, на него даётся pwd_recover_attempts попыток, после успешной проверки он удаляется.
 */
class UserPwdRecoverModel extends SiteModel
{
    public function scheme()
    {
        return [
            'id'               => 'int',
            'user_id'          => 'int',
            'code'             => 'string',
            'attempts'         => 'int',
            'ttl'              => 'int',
            'create_time'      => 'int',
            'last_update_time' => 'int',
        ];
    }

    public function createTable()
    {
        $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
                `id` INT NOT NULL AUTO_INCREMENT,
                `user_id` INT DEFAULT NULL,
                `code` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
                `attempts` INT NOT NULL DEFAULT 0,
                `ttl` INT DEFAULT NULL,
                `create_time` INT DEFAULT NULL,
                `last_update_time` INT DEFAULT NULL,
                KEY `user_id` (`user_id`),
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('user_pwd_recover', $id);
    }

    private function lastCodeId($user_id)
    {
        return $this->db->selectCell(
            "SELECT `id` FROM ?# WHERE `user_id` = ?d ORDER BY `id` DESC LIMIT 1",
            $this->table,
            $user_id
        );
    }

    // Можно ли уже отправить пользователю новый код (защита от спама письмами)
    public function canResend($user_id): bool
    {
        $last = new self($this->lastCodeId($user_id));
        return !$last->isExists() || $last->create_time + Config(['user_model', 'pwd_recover_resend']) <= time();
    }

    public function generate($user_id)
    {
        // Старые коды пользователя больше не действуют
        $this->db->query("DELETE FROM ?# WHERE `user_id` = ?d", $this->table, $user_id);

        $this->user_id     = $user_id;
        $this->code        = sprintf('%06d', random_int(0, 999999));
        $this->attempts    = 0;
        $this->ttl         = time() + Config(['user_model', 'pwd_recover_time']);
        $this->create_time = time();
        return $this->flush();
    }

    public function isValidCode($userCode, $userEmail)
    {
        $user = (new UserModel())->findOne(['email' => $userEmail]);
        if (!$user) {
            return false;
        }

        $model = new self($this->lastCodeId($user->id));
        if (!$model->isExists()) {
            return false;
        }
        if ($model->ttl < time() || $model->attempts >= Config(['user_model', 'pwd_recover_attempts'])) {
            $model->delete();
            return false;
        }

        if (!hash_equals($model->code, trim((string)$userCode))) {
            $model->attempts = $model->attempts + 1;
            $model->flush();
            return false;
        }

        $model->delete(); // Код одноразовый
        return true;
    }
}
