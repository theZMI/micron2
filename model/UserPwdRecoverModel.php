<?php

class UserPwdRecoverModel extends SiteModel
{
    public function scheme()
    {
        return [
            'id'               => 'int',
            'user_id'          => 'int',
            'code'             => 'string',
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
                `code` VARCHAR(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
                `ttl` INT DEFAULT NULL,
                `create_time` INT DEFAULT NULL,
                `last_update_time` INT DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('user_pwd_recover', $id);
    }

    public function generate($user_id)
    {
        $this->user_id     = $user_id;
        $this->code        = strval(mt_rand(10000, 99999));
        $this->ttl         = time() + Config(['user_model', 'pwd_recover_time']);
        $this->create_time = time();
        return $this->flush();
    }

    public function isValidCode($userCode, $userEmail)
    {
        $user  = (new UserModel())->findOne(['email' => $userEmail]) ?: new UserModel();
        $id    = $this->db->selectCell("SELECT `id` FROM ?# WHERE `user_id` = ? ORDER BY `create_time` DESC LIMIT 1", $this->table, $user->id);
        $model = new self($id);
        return $model->isExists() && $model->code === strval($userCode) && +$model->ttl >= time();
    }
}