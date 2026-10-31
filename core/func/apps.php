<?php

// Папка с версиями приложения ($app - под-папка, если приложений несколько) или null, если имя приложения недопустимое
function AppsDir(string $app = ''): ?string
{
    if ($app !== '' && !preg_match('/^[a-z0-9_-]+$/i', $app)) {
        return null;
    }
    return Config(['apps', 'dir']) . ($app === '' ? '' : "{$app}/");
}

// Последняя версия приложения по именам файлов (1.0.10.apk новее 1.0.9.apk) или null, если файлов нет
function GetLastAppVersion(string $app = ''): ?string
{
    $dir = AppsDir($app);
    $ext = Config(['apps', 'ext']);
    if (is_null($dir)) {
        return null;
    }

    $versions = [];
    foreach (glob("{$dir}*.{$ext}") ?: [] as $file) {
        $version = basename($file, ".{$ext}");
        if (preg_match('/^\d+(\.\d+)*$/', $version)) { // Файлы не по формату версии (app_old.apk и т.п.) пропускаем
            $versions[] = $version;
        }
    }
    usort($versions, 'version_compare');
    return array_pop($versions);
}
