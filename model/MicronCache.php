<?php

class MicronCache extends \MicronCache\DbCacheModel implements \MicronCache\ICache
{
    use SingletonTrait;
}