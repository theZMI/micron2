<?php

require_once BASEPATH . 'core/config/mime_types.php';

// Список mime-типов который используется при отдаче файлов через BrowserDataCache
$g_config['browserdatacache_mime_types'] = $g_config['mime_types'];

// Список типов файлов разрешённых для отдачи своего содержимого через BrowserDataCache
$g_config['browserdatacache_allow_filetypes'] = array_keys($g_config['browserdatacache_mime_types']);

// Список папок откуда можно брать файлы
$g_config['browserdatacache_allow_dirs'] = [
    // BASEPATH = Вариант когда разрешены все папки

    // Вариант более безопасный, предусматривает строгое перечисление хранилищ
    BASEPATH . 'i/',
    BASEPATH . 'upl/',
];
