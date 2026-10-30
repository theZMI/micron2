<?php

class BreadCrumb
{
    use SingletonTrait;

    private static array $steps = [];

    private function __construct()
    {
        self::$steps[] = ['link' => SiteRoot(), 'text' => '<i class="bi bi-house-fill"></i>', 'css' => ''];
    }

    public function steps()
    {
        return self::$steps;
    }

    public function add($step)
    {
        $step = array_merge(['link' => '', 'text' => '', 'css' => ''], $step);
        self::$steps[] = $step;
    }
}