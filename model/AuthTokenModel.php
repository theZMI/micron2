<?php

/**
 * Токены авторизации (сессии) пользователей, админов и API-клиентов.
 * В БД храним только sha256 от токена, сам токен знает только клиент (cookie или заголовок API)
 */
class AuthTokenModel extends SiteModel
{
    public function scheme()
    {
        return [
            'id'               => 'int',
            'owner_type'       => 'string',
            'owner_id'         => 'int',
            'token_hash'       => 'string',
            'ip'               => 'string',
            'user_agent'       => 'string',
            'expire_time'      => 'int',
            'create_time'      => 'int',
            'last_update_time' => 'int',
        ];
    }

    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
                `id` INT NOT NULL AUTO_INCREMENT,
                `owner_type` VARCHAR(64) NOT NULL,
                `owner_id` INT NOT NULL,
                `token_hash` CHAR(64) NOT NULL,
                `ip` VARCHAR(45) DEFAULT NULL,
                `user_agent` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
                `expire_time` INT NOT NULL,
                `create_time` INT DEFAULT NULL,
                `last_update_time` INT DEFAULT NULL,
                UNIQUE (`token_hash`),
                KEY `owner` (`owner_type`, `owner_id`),
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('auth_tokens', $id);
    }

    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    // Создаёт новый токен и возвращает его (в открытом виде он больше нигде не хранится)
    public function issue(string $owner_type, int $owner_id, int $ttl): string
    {
        $token = bin2hex(random_bytes(32));

        $model              = new self();
        $model->owner_type  = $owner_type;
        $model->owner_id    = $owner_id;
        $model->token_hash  = self::hashToken($token);
        $model->ip          = GetClientIP();
        $model->user_agent  = mb_substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
        $model->expire_time = time() + $ttl;
        $model->flush();

        return $token;
    }

    // Возвращает id владельца токена или false, если токен неверный или просрочен
    public function findOwnerId(string $owner_type, string $token): int|false
    {
        if ($token === '') {
            return false;
        }
        $owner_id = $this->db->selectCell(
            "SELECT `owner_id` FROM ?# WHERE `token_hash` = ? AND `owner_type` = ? AND `expire_time` > ?d",
            $this->table,
            self::hashToken($token),
            $owner_type,
            time()
        );
        return $owner_id ? (int)$owner_id : false;
    }

    public function revoke(string $token): void
    {
        $this->db->query("DELETE FROM ?# WHERE `token_hash` = ?", $this->table, self::hashToken($token));
    }

    // Удаляет все сессии владельца (например, после смены пароля)
    public function revokeAll(string $owner_type, int $owner_id): void
    {
        $this->db->query("DELETE FROM ?# WHERE `owner_type` = ? AND `owner_id` = ?d", $this->table, $owner_type, $owner_id);
    }

    public function deleteExpired(): void
    {
        $this->db->query("DELETE FROM ?# WHERE `expire_time` <= ?d", $this->table, time());
    }
}
