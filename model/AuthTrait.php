<?php

trait AuthTrait
{
    abstract public static function getConfig(): array;

    public static function makeHash($word)
    {
        return md5($word . self::getConfig()['salt']);
    }

    public function getIdByLogin($login)
    {
        return empty($login) ?
            false :
            $this->db->selectCell("SELECT `id` FROM ?# WHERE `login` = ?", $this->table, $login);
    }

    public static function curId()
    {
        $config   = self::getConfig();
        $login    = $_COOKIE[$config['cookie_login_param']] ?? '';
        $pwd_hash = $_COOKIE[$config['cookie_password_param']] ?? '';
        $ret      = false;
        $uid      = (new self())->getIdByLogin($login);

        if ($uid) {
            $u   = new self($uid);
            $ret = $u->login == $login && $u->pwd_hash == $pwd_hash
                ? $uid
                : false;
        }

        return $ret;
    }

    public function isLoginBusy($login)
    {
        return $this->db->selectCell("SELECT COUNT(`id`) FROM ?# WHERE `login` = ?", $this->table, $login);
    }

    // Авторизован ли текущей юзер
    public function isAuth()
    {
        return !!$this->curId();
    }

    public function login($login, $pwd_hash)
    {
        if (empty($login) || empty($pwd_hash)) {
            trigger_error("Can not login: login or pwd_hash are empty!", E_USER_ERROR);
        }

        $config = self::getConfig();
        $ret    = false;
        $uid    = $this->getIdByLogin($login);

        if ($uid) {
            $u = new self($uid);
            if ($u->login == $login && $u->pwd_hash == $pwd_hash) {
                setcookie($config['cookie_login_param'], $login, time() + self::REMEMBER_PERIOD, '/', EnvConfig::DOMAIN_COOKIE);
                setcookie($config['cookie_password_param'], $pwd_hash, time() + self::REMEMBER_PERIOD, '/', EnvConfig::DOMAIN_COOKIE);
                $ret = true;
            }
        }

        return $ret;
    }

    public function logout()
    {
        $config = self::getConfig();
        setcookie($config['cookie_login_param'], '', -1, '/', EnvConfig::DOMAIN_COOKIE);
        setcookie($config['cookie_password_param'], '', -1, '/', EnvConfig::DOMAIN_COOKIE);
    }
}