<?php

/**
 * Библиотека выбирает что делать по присланному запросу
 */
class EngineQueryManager
{
    // Типы файлов скачка которых запрещена
    private static $disallowFileTypes = [
        'php3',
        'php4',
        'php',
        'phps',
        'phtml',
        'phtm',
        'html',
        'htm',
        'xhtml',
        'xhtm',
        'xht',
    ];

    // Пробуем найти по присланную url-у подходящий asset (картинку, скрипт, шрифт и пр) и вывести его в браузер
    public static function tryOutStaticData($q)
    {
        $file = BASEPATH . $q;
        $ext  = BrowserDataCache::ext(strtolower($file));
        if (in_array($ext, self::$disallowFileTypes)) {
            return;
        }
        BrowserDataCache::outFile($file);
    }
}
