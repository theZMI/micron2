<?php

trait SingletonTrait
{
    private function __construct()
    {
    }

    public static function getInstance(): static
    {
        static $o = null;
        if (is_null($o)) {
            $o = new static();
        }
        return $o;
    }
}