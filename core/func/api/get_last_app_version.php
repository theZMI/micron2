<?php

function GetLastAppVersion(): string
{
    $dir  = BASEPATH . 'i/apps/';
    $apps = array_filter(array_keys(FileSys::readList($dir)), fn ($v) => $v[0] !== '.'); // Remove all hidden files
    $apps = array_map(fn ($v) => str_replace('.apk', '', $v), $apps);
    natsort($apps);
    return array_pop($apps);
}