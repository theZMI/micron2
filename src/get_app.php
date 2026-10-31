<?php

// Скачивание последней версии приложения: /get_app (или /get_app?app=courier, если приложений несколько)
$key = Config(['apps', 'download_key']);
if ($key !== '' && !hash_equals($key, (string)Get('key'))) {
    IncludeCom('403');
    ExitCom();
    return;
}

$app     = (string)Get('app');
$version = GetLastAppVersion($app);
if (is_null($version)) {
    IncludeCom('404');
    ExitCom();
    return;
}

$ext  = Config(['apps', 'ext']);
$name = ($app ?: Config(['apps', 'download_name'])) . "_{$version}.{$ext}";
(new Downloader())->download(AppsDir($app) . "{$version}.{$ext}", $name);
