<?php

class SessionManager
{
    public static function get($key)
    {
        @session_start();
        header("Cache-Control: no-cache");
        header("Pragma: no-cache");

        $ret = null;
        if (isset($_SESSION[$key])) {
            $ret = $_SESSION[$key];
        }
        @session_write_close();
        return $ret;
    }

    public static function set($key, $value)
    {
        @session_start();
        header("Cache-Control: no-cache");
        header("Pragma: no-cache");

        $_SESSION[$key] = $value;
        @session_write_close();
    }

    public static function has($key)
    {
        @session_start();
        header("Cache-Control: no-cache");
        header("Pragma: no-cache");

        $ret = isset($_SESSION[$key]);
        @session_write_close();
        return $ret;
    }

    public static function delete($key)
    {
        @session_start();
        header("Cache-Control: no-cache");
        header("Pragma: no-cache");

        unset($_SESSION[$key]);
        @session_write_close();
    }
}