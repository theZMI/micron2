<?php

/**
 * Вход по паролю - только для админки (AdminModel). У пользователей сайта паролей нет: они входят по коду из письма.
 * Пароли храним через password_hash(). Старые md5-хеши (из прошлых версий) ещё принимаются и при входе пересохраняются
 */
trait PasswordAuthTrait
{
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
}
