<?php

/**
 * Параметры запоминаемые на 1 или несколько переходов/обновлений страницы
 */
class FlashParam
{
    const SESSION_PAR = '__engineSessionFlashParamSector__';

    // Флаг проводилась ли очистка параметров
    private static bool $cleanParams = false;
    private bool $opened = false;

    public function __construct()
    {
        if (!session_id() && !headers_sent()) {
            session_start();
            $this->opened = true;
        }
        if (!self::$cleanParams) {
            self::$cleanParams = true;
            $this->autoClean();
        }
    }

    public function get($name)
    {
        return $_SESSION[self::SESSION_PAR][$name]['value'] ?? false;
    }

    public function set($name, $value, $countSessionRemember = 1): void
    {
        $elem = [
            'value'            => $value,
            'countSessionPass' => $countSessionRemember,
            'time'             => time()
        ];
        if (!isset($_SESSION[self::SESSION_PAR])) {
            $_SESSION[self::SESSION_PAR] = [];
        }
        $_SESSION[self::SESSION_PAR][$name] = $elem;
    }

    private function autoClean(): void
    {
        if (!isset($_SESSION[self::SESSION_PAR])) {
            return;
        }
        foreach ($_SESSION[self::SESSION_PAR] as $k => $v) {
            if ($v['countSessionPass'] < 1) {
                unset($_SESSION[self::SESSION_PAR][$k]);
                continue;
            }
            $v['countSessionPass']           = $v['countSessionPass'] - 1;
            $_SESSION[self::SESSION_PAR][$k] = $v;
        }
    }

    public function __destruct()
    {
        if ($this->opened) {
            session_write_close();
        }
    }
}