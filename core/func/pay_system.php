<?php

function GetPaySystemService(): PaySystem
{
    static $o = null;
    if (is_null($o)) {
        $paySystemClass = Config(['pay_system', 'driver']);
        $o              = new $paySystemClass;
    }
    return $o;
}