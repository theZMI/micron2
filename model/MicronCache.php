<?php

use MicronCache\ICache;

class MicronCache extends \MicronCache\DbCacheModel implements ICache
{
    use SingletonTrait;
}