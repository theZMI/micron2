<?php

require_once BASEPATH . 'core/config/browser_data_cache.php';

// Разрешенные типы файлов для скачки
Config('download_allow_filetypes', [
    // '*' = Все типы файлов
    'doc', 'docx',
    'zip',
    'pdf',
    'xl', 'xls', 'xlsx',
    'bmp', 'png', 'jpg', 'jpeg', 'ico',
    'apk',
]);

// Разрешенные папки из которых можно скачивать файлы
Config('download_allow_dirs', Config('browserdatacache_allow_dirs'));
