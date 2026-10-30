<?php

/**
 * Авторизация для моделей (AdminModel, UserModel)
 *
 * Пароли храним через password_hash(), а клиенту отдаём случайный токен сессии (см. AuthTokenModel).
 * Токен лежит в HttpOnly-cookie (для сайта) или передаётся в заголовке API-User-Auth-Token (для API).
 */
trait AuthTrait
{
    private static array $curIdCache = [];

    abstract public static function getConfig(): array;

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    // Старые хеши были вида md5(пароль + соль), такие ещё поддерживаем, но при входе пересохраняем через password_hash()
    private static function isLegacyHash(string $hash): bool
    {
        return (bool)preg_match('/^[a-f0-9]{32}$/', $hash);
    }

    public function verifyPassword(string $password): bool
    {
        $hash = (string)$this->pwd_hash;
        if ($password === '' || $hash === '') {
            return false;
        }

        if (self::isLegacyHash($hash)) {
            $isValid = hash_equals($hash, md5($password . (static::getConfig()['salt'] ?? '')));
            if ($isValid) {
                $this->pwd_hash = static::hashPassword($password);
                $this->flush();
            }
            return $isValid;
        }

        $isValid = password_verify($password, $hash);
        if ($isValid && password_needs_rehash($hash, PASSWORD_DEFAULT)) {
            $this->pwd_hash = static::hashPassword($password);
            $this->flush();
        }
        return $isValid;
    }

    public function getIdByLogin($login)
    {
        return empty($login) ?
            false :
            $this->db->selectCell("SELECT `id` FROM ?# WHERE `login` = ?", $this->table, $login);
    }

    public function isLoginBusy($login)
    {
        return $this->db->selectCell("SELECT COUNT(`id`) FROM ?# WHERE `login` = ?", $this->table, $login);
    }

    private static function cookieName(): string
    {
        return static::getConfig()['cookie_token_param'];
    }

    private static function setAuthCookie(string $value, int $expires): void
    {
        $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        setcookie(self::cookieName(), $value, [
            'expires'  => $expires,
            'path'     => '/',
            'domain'   => (string)Env('DOMAIN_COOKIE'),
            'secure'   => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    // id авторизованного (по cookie) владельца или false. Результат кешируется на время запроса
    public static function curId()
    {
        if (!array_key_exists(static::class, self::$curIdCache)) {
            $token = (string)($_COOKIE[self::cookieName()] ?? '');
            self::$curIdCache[static::class] = (new AuthTokenModel())->findOwnerId(static::class, $token);
        }
        return self::$curIdCache[static::class];
    }

    // id владельца по токену API или false
    public static function idByApiToken(string $token)
    {
        return (new AuthTokenModel())->findOwnerId(static::class, $token);
    }

    // Авторизован ли текущий юзер
    public function isAuth()
    {
        return static::curId() !== false;
    }

    // Проверяет логин/пароль и, если всё верно, авторизует (ставит cookie)
    public function login($login, $password)
    {
        if (empty($login) || empty($password)) {
            return false;
        }

        $model = new static($this->getIdByLogin($login));
        if (!$model->isExists() || !$model->verifyPassword((string)$password)) {
            return false;
        }

        $model->startSession();
        return true;
    }

    // Авторизует текущую модель без проверки пароля (например, после подтверждения кода из письма)
    public function startSession(): void
    {
        $token = (new AuthTokenModel())->issue(static::class, (int)$this->id, static::REMEMBER_PERIOD);
        self::setAuthCookie($token, time() + static::REMEMBER_PERIOD);
        self::$curIdCache[static::class] = (int)$this->id;
    }

    // Выдаёт токен для API (возвращается клиенту один раз)
    public function issueApiToken(): string
    {
        return (new AuthTokenModel())->issue(static::class, (int)$this->id, static::REMEMBER_PERIOD);
    }

    public static function revokeApiToken(string $token): void
    {
        (new AuthTokenModel())->revoke($token);
    }

    // Завершает все сессии модели (нужно после смены пароля)
    public function revokeAllSessions(): void
    {
        (new AuthTokenModel())->revokeAll(static::class, (int)$this->id);
    }

    public function logout()
    {
        $token = (string)($_COOKIE[self::cookieName()] ?? '');
        if ($token !== '') {
            (new AuthTokenModel())->revoke($token);
        }
        self::setAuthCookie('', 1);
        self::$curIdCache[static::class] = false;
    }
}
