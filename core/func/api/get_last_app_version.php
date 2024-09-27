<?php

function GetLastAppVersion(): string
{
    $dir  = BASEPATH . "i/apps/";
    $apps = array_keys(FileSys::readList($dir));
    $apps = array_map(function ($v) {
        return str_replace(".apk", '', $v);
    }, $apps);
    natsort($apps);
    return array_pop($apps);
}