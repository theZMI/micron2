<?php

$lang     = Get('lang');
$file     = Get('file');
$langFile = realpath(BASEPATH . "lang/{$lang}/{$file}");
$files    = [
    realpath(BASEPATH . "lang/" . DEF_LANG . "/{$file}"),
    $langFile,
    realpath(BASEPATH . "src/{$file}"),
    realpath(BASEPATH . "tpl/{$file}")
];

// Путь проверяем по языковому файлу, а удаляем ещё и src/tpl с тем же именем, поэтому выход из папки ("..") запрещаем совсем
if (!str_contains((string)$file, '..') && $langFile && str_starts_with($langFile, BASEPATH . "lang/")) {
    array_walk($files, fn($f) => @unlink($f));
    UrlRedirect::go(
        SiteRoot("_admin/list/list&lang={$lang}")
    );
}