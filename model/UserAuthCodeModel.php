<?php

/**
 * Одноразовые коды для входа без пароля (сайт и приложение) и подтверждения e-mail.
 * Код живёт auth_code_ttl секунд, на него даётся auth_code_attempts попыток, после успешной проверки он удаляется.
 * Новый код можно запросить не чаще чем раз в auth_code_resend секунд (настройки - core/config/user_model.php)
 */
class UserAuthCodeModel extends SiteModel
{
    const CODE_LENGTH = 6; // Сколько цифр в коде

    const PURPOSE_LOGIN        = 'login';
    const PURPOSE_REGISTRATION = 'registration';

    const SEND_OK   = 'ok';
    const SEND_WAIT = 'wait'; // Прошлый код отправлен меньше auth_code_resend секунд назад
    const SEND_FAIL = 'fail';

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
        parent::__construct('user_auth_codes', $id);
    }

    private static function conf(string $key): int
    {
        return (int)Config(['user_model', $key]);
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
        return $this->resendAfter($user_id) === 0;
    }

    // Через сколько секунд можно запросить новый код (0 - уже можно)
    public function resendAfter($user_id): int
    {
        $last = new self($this->lastCodeId($user_id));
        return $last->isExists() ? max(0, $last->create_time + self::conf('auth_code_resend') - time()) : 0;
    }

    // Создаёт новый код (старые коды пользователя перестают действовать) и возвращает id записи
    public function generate($user_id)
    {
        $this->db->query("DELETE FROM ?# WHERE `user_id` = ?d", $this->table, $user_id);

        $this->user_id     = $user_id;
        $this->code        = str_pad((string)random_int(0, 10 ** self::CODE_LENGTH - 1), self::CODE_LENGTH, '0', STR_PAD_LEFT);
        $this->attempts    = 0;
        $this->ttl         = time() + self::conf('auth_code_ttl');
        $this->create_time = time();
        return $this->flush();
    }

    /**
     * Создаёт новый код и отправляет его на e-mail пользователя. Текст письма зависит от цели: вход или регистрация
     */
    public function sendByEmail(UserModel $user, string $purpose = self::PURPOSE_LOGIN): string
    {
        if (!$this->canResend($user->id)) {
            return self::SEND_WAIT;
        }
        $code     = (new self($this->generate($user->id)))->code;
        $minutes  = intdiv(self::conf('auth_code_ttl'), 60);
        $isSignup = $purpose === self::PURPOSE_REGISTRATION;
        $isSent   = (new EmailNotificator())->send(
            $user,
            '<p>' . ($isSignup ? 'Для завершения регистрации введите этот код:' : 'Для входа введите этот код:') . '</p>' .
            "<p style='padding: 30px 0; text-align: center; font-size: 40px; letter-spacing: 5px;'>{$code}</p>" .
            "<p>Код действует {$minutes} минут. Если вы не запрашивали код, просто проигнорируйте это письмо.</p>",
            $isSignup ? 'Подтверждение регистрации' : 'Код для входа'
        );
        return $isSent ? self::SEND_OK : self::SEND_FAIL;
    }

    // Проверяет код пользователя. Неверный ввод тратит попытку, верный код удаляется (одноразовый)
    public function isValidCode($user_id, $userCode): bool
    {
        $model = new self($this->lastCodeId((int)$user_id));
        if (!$model->isExists()) {
            return false;
        }
        if ($model->ttl < time() || $model->attempts >= self::conf('auth_code_attempts')) {
            $model->delete();
            return false;
        }

        if (!hash_equals($model->code, trim((string)$userCode))) {
            $model->attempts = $model->attempts + 1;
            $model->flush();
            return false;
        }

        $model->delete();
        return true;
    }
}
