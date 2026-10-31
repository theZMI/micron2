<?php

/**
 * Неудачные попытки входа (защита от подбора пароля).
 * Считаем их отдельно по логину (подбор пароля к одному аккаунту) и по IP (перебор многих аккаунтов с одного адреса).
 * Лимиты задаются в Config('login_throttle')
 */
class LoginAttemptModel extends SiteModel
{
    public function scheme()
    {
        return [
            'id'               => 'int',
            'scope'            => 'string',
            'login_hash'       => 'string',
            'ip'               => 'string',
            'create_time'      => 'int',
            'last_update_time' => 'int',
        ];
    }

    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
                `id` INT NOT NULL AUTO_INCREMENT,
                `scope` VARCHAR(64) NOT NULL,
                `login_hash` CHAR(64) NOT NULL,
                `ip` VARCHAR(45) DEFAULT NULL,
                `create_time` INT DEFAULT NULL,
                `last_update_time` INT DEFAULT NULL,
                KEY `by_login` (`scope`, `login_hash`, `create_time`),
                KEY `by_ip` (`scope`, `ip`, `create_time`),
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('login_attempts', $id);
    }

    // Логин храним хешем: в таблицу попадает всё, что вводят в поле (в том числе пароли, вписанные не в то поле)
    private static function loginHash(string $login): string
    {
        return hash('sha256', mb_strtolower(trim($login)));
    }

    // Заблокирован ли вход ($scope - что защищаем, например 'admin' или 'api')
    public function isBlocked(string $scope, string $login): bool
    {
        $conf  = Config('login_throttle');
        $since = time() - $conf['period'];

        $byLogin = $this->db->selectCell(
            "SELECT COUNT(*) FROM ?# WHERE `scope` = ? AND `login_hash` = ? AND `create_time` > ?d",
            $this->table, $scope, self::loginHash($login), $since
        );
        $byIp = $this->db->selectCell(
            "SELECT COUNT(*) FROM ?# WHERE `scope` = ? AND `ip` = ? AND `create_time` > ?d",
            $this->table, $scope, GetClientIP(), $since
        );
        return $byLogin >= $conf['max_by_login'] || $byIp >= $conf['max_by_ip'];
    }

    public function registerFail(string $scope, string $login): void
    {
        // Заодно чистим старые записи, чтобы таблица не росла
        $this->db->query("DELETE FROM ?# WHERE `create_time` <= ?d", $this->table, time() - Config(['login_throttle', 'period']));

        $model              = new self();
        $model->scope       = $scope;
        $model->login_hash  = self::loginHash($login);
        $model->ip          = GetClientIP();
        $model->create_time = time();
        $model->flush();
    }

    // После успешного входа счётчик по логину сбрасываем (по IP - нет, чтобы нельзя было чередовать свой аккаунт с чужими)
    public function clear(string $scope, string $login): void
    {
        $this->db->query("DELETE FROM ?# WHERE `scope` = ? AND `login_hash` = ?", $this->table, $scope, self::loginHash($login));
    }
}
