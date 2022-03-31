<?php

require_once BASEPATH . 'core/config/browser_data_cache.php';

// Разрешенные типы файлов для скачки
$g_config['download_allow_filetypes'] = [
    // '*' = Все типы файлов
    'doc', 'docx',
    'zip',
    'pdf',
    'xl', 'xls', 'xlsx',
    'bmp', 'png', 'jpg', 'jpeg', 'ico',
    'apk',
];

// Разрешенные папки из которых можно скачивать файлы
$g_config['download_allow_dirs'] = $g_config['browserdatacache_allow_dirs'];
