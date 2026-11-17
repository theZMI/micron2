<?php

/**
 * Сессии для моделей (AdminModel, UserModel): после входа клиент получает случайный токен (см. AuthTokenModel).
 * Токен лежит в HttpOnly-cookie (для сайта) или передаётся в заголовке API-User-Auth-Token (для API).
 * Как человек доказывает, что он - это он, решает модель: пользователи - одноразовым кодом из письма (UserAuthCodeModel),
 * админы - паролем (PasswordAuthTrait)
 */
trait AuthTrait
{
    private static array $curIdCache = [];

    abstract public static function getConfig(): array;

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

    // Авторизует текущую модель (вызывается после проверки кода из письма или пароля админа)
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

    // Завершает все сессии модели (на всех устройствах)
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
