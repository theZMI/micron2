<?php

trait AuthTrait
{
    abstract public static function getConfig(): array;

    public static function makeHash($word)
    {
        return md5($word . static::getConfig()['salt']);
    }

    public function getIdByLogin($login)
    {
        return empty($login) ?
            false :
            $this->db->selectCell("SELECT `id` FROM ?# WHERE `login` = ?", $this->table, $login);
    }

    public function isLoginBusy($login)
    {
        return !!$this->getIdByLogin($login);
    }

    public function isCorrectAuth($login, $password_hash)
    {
        $id    = (new static())->getIdByLogin($login);
        $model = new static($id);
        return $model->isExists() && $model->pwd_hash === $password_hash;
    }

    public static function curId()
    {
        $config        = static::getConfig();
        $login         = $_COOKIE[$config['cookie_login_param']] ?? '';
        $password_hash = $_COOKIE[$config['cookie_password_param']] ?? '';
        $isCorrectAuth = (new static())->isCorrectAuth($login, $password_hash);
        $user_id       = (new static())->getIdByLogin($login);

        return $isCorrectAuth ? $user_id : false;
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

        $config = static::getConfig();
        $ret    = false;
        $uid    = $this->getIdByLogin($login);

        if ($uid) {
            $u = new static($uid);
            if ($u->login == $login && $u->pwd_hash == $pwd_hash) {
                setcookie($config['cookie_login_param'], $login, time() + static::REMEMBER_PERIOD, '/', Env('DOMAIN_COOKIE'));
                setcookie($config['cookie_password_param'], $pwd_hash, time() + static::REMEMBER_PERIOD, '/', Env('DOMAIN_COOKIE'));
                $ret = true;
            }
        }

        return $ret;
    }

    public function logout()
    {
        $config = static::getConfig();
        setcookie($config['cookie_login_param'], '', -1, '/', Env('DOMAIN_COOKIE'));
        setcookie($config['cookie_password_param'], '', -1, '/', Env('DOMAIN_COOKIE'));
    }
}