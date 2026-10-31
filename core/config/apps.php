<?php

require_once BASEPATH . 'core/config/downloader.php';

// Скомпилированные приложения (apk). Файлы называются номером версии: apps/1.0.2.apk, apps/1.0.10.apk...
// Если приложений несколько, то каждое лежит в своей под-папке: apps/courier/1.0.2.apk (тогда передаётся app=courier)
Config('apps', [
    'dir'           => BASEPATH . 'apps/',  // Снаружи напрямую недоступна, файлы отдаются только через /get_app
    'ext'           => 'apk',
    'download_name' => 'app',               // Имя скачиваемого файла: app_1.0.10.apk (для под-папок вместо app будет имя приложения)
    'download_key'  => (string)Env('APP_DOWNLOAD_KEY'), // Если задан, то скачать можно только по ссылке /get_app?key=...
]);

Config('download_allow_dirs', array_merge(Config('download_allow_dirs'), [Config(['apps', 'dir'])]));
