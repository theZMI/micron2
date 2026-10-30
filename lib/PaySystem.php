<?php

/**
 * Точка входа в платёжную систему: PaySystem::getInstance() вернёт драйвер, выбранный в core/init/pay_system.php
 */
class PaySystem
{
    public static function getInstance(): \PaySystem\PaySystem_Base
    {
        static $driver = null;
        if (is_null($driver)) {
            $class  = Config(['pay_system', 'driver']);
            $driver = new $class();
        }
        return $driver;
    }
}
