<?php

$lang     = Get('lang');
$file     = Get('file');
$langFile = realpath(BASEPATH . "lang/{$lang}/{$file}");
// Страница создаётся сразу для всех языков (_admin/list/add), поэтому и удаляем её языковые файлы во всех языках
$files = array_map(fn($l) => realpath(BASEPATH . "lang/{$l}/{$file}"), array_keys(Config('langs')));
$files[] = realpath(BASEPATH . "src/{$file}");
$files[] = realpath(BASEPATH . "tpl/{$file}");
$files = array_filter($files);

// Путь проверяем по языковому файлу, а удаляем ещё и src/tpl с тем же именем, поэтому выход из папки ("..") запрещаем совсем
if (!str_contains((string)$file, '..') && $langFile && str_starts_with($langFile, BASEPATH . "lang/")) {
    array_walk($files, fn($f) => @unlink($f));
    UrlRedirect::go(
        SiteRoot("_admin/list/list&lang={$lang}")
    );
}