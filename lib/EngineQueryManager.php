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

    private static function tryRouting()
    {
        $router = Router::getInstance();

        $curQuery = ltrim($_SERVER['REQUEST_URI'], '/');
        $oldQuery = $router->getOldQuery($curQuery);
        $newQuery = $router->getNewQuery($curQuery);

        if ($curQuery !== $oldQuery) // Если URL по которому имеет старый то зашли мы на новый => записываем в q старый что бы движ нашёл
        {
            $_GET['micron_query'] = $oldQuery;
        }
        if ($curQuery !== $newQuery) // Если был новый адрес
        {
            if (empty($_POST)) {
                UrlRedirect::go(
                    SiteRoot($newQuery),
                    301
                );
            }
        }
    }

    private static function tryOutStaticData($q)
    {
        $file = BASEPATH . $q;
        $ext  = BrowserDataCache::ext(strtolower($file));
        if (in_array($ext, self::$disallowFileTypes)) {
            return false;
        }
        BrowserDataCache::outFile($file);
    }

    public static function tryAllBranches($q)
    {
        self::tryRouting();
        self::tryOutStaticData($q);
    }
}
