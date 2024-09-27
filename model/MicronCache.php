<?php

use MicronCache\ICache;

class MicronCache extends \MicronCache\DbCacheModel implements ICache
{
    public static function getInstance(): ICache
    {
        static $o = null;
        if (!$o) {
            $o = new self();
        }
        return $o;
    }
}