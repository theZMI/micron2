<?php

require_once BASEPATH . 'core/config/mime_types.php';

// Список mime-типов который используется при отдаче файлов через BrowserDataCache
Config('browserdatacache_mime_types', Config('mime_types'));

// Список типов файлов разрешённых для отдачи своего содержимого через BrowserDataCache
Config('browserdatacache_allow_filetypes', array_keys(Config('browserdatacache_mime_types')));

// Список папок откуда можно скачивать файлы
Config('browserdatacache_allow_dirs', [
    // BASEPATH = Вариант когда разрешены все папки

    // Вариант более безопасный, предусматривает строгое перечисление хранилищ
    BASEPATH . 'i/',
    BASEPATH . 'upl/',
    BASEPATH . 'tmp/',
    BASEPATH . 'lib/',
]);