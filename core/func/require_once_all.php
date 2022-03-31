<?php

/**
 * Ф-я подключает все файлы в дирректории в такой последовательности: сначала main.php потом все остальные
 *
 * @param $dir - Дирректория из которой надо подключить все файлы
 */
function RequireOnceAll($dir)
{
    global $g_config, $g_lang, $g_user, $g_admin;

    // Если в папке есть файл main.php то включаем его 1-ым
    if (is_readable($dir . '/main.php')) {
        require_once $dir . '/main.php';
    }

    // Далее сканируем каталог и включаем все файлы
    $scan = glob("$dir/*");
    foreach ($scan as $path) {
        if (preg_match('/\.php$/', $path)) {
            require_once $path;
        } elseif (is_dir($path)) {
            RequireOnceAll($path);
        }
    }
}
