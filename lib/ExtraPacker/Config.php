<?php

/**
 * Класс настроечных функций ExtraPacker-а
 */
class ExtraPacker_Config
{
    private static function getDocRoot()
    {
        return BASEPATH;
    }

    private static function getPathFromUrl($url)
    {
        $url = str_replace(SiteRoot(), "/", $url);
        return self::getDocRoot() . ltrim($url, "/");
    }

    public static function getPathJsFileFromUrl($url)
    {
        return self::getPathFromUrl($url);
    }

    public static function getPathCssFileFromUrl($url)
    {
        return self::getPathFromUrl($url);
    }

    private static function getAddrPackFile($path)
    {
        return str_replace(self::getDocRoot(), SiteRoot(), $path);
    }

    public static function getAddrJsPackFile($path)
    {
        return self::getAddrPackFile($path);
    }

    public static function getAddrCssPackFile($path)
    {
        return self::getAddrPackFile($path);
    }

    public static function changeCssUrl($m)
    {
        // Если сайт в папке то надо добавить эту папку в пути поиска
        $docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
        $docRoot = substr($docRoot, -1, 1) == '/' ? substr($docRoot, 0, -1) : $docRoot;

        $dir = str_replace($docRoot, '', BASEPATH);
        $dir = trim($dir, '/');
        $dir = empty($dir) ? '' : "/{$dir}";

        $curUrl = $dir . $GLOBALS['__engineExtraPackerChangeCssUrl__curUrl__'];
        $url    = isset($m[1]) ? trim($m[1], '\'"') : '';
        if ($url) {
            // Это не Less-переменная, не путь вида http://google.com/ и не url(data:image/png;base64,....
            if (strpos($url, "@") === false && strpos($url, "://") === false && strpos($url, "data:") !== 0) {
                // Это не абсолютный адрес
                if ($url[0] !== '/') {
                    $url = "$curUrl/$url";
                } // Если это абсолтный путь но к корню это сайта, но сайт запущен из каталога то переделываем пути так что бы они были как-будто из каталога
                elseif (strlen($dir) && strpos($url, $dir) === false) {
                    $url = $dir . $url;
                }
            }
        }

        return $url;
    }

    public static function changeCssUrl_Url($url)
    {
        $url = self::changeCssUrl($url);

        return "url('$url')";
    }

    public static function changeCssUrl_Src($url)
    {
        $url = self::changeCssUrl($url);

        return "src='$url'";
    }

    public static function lessImport($content, $path)
    {
        $dir          = pathinfo($path, PATHINFO_DIRNAME) . '/';
        $oldImportDir = strval(Config('extrapacker__tempImportDir'));

        Config('extraPacker__tempImportDir', $dir);

        if (stripos($content, '@import') !== false) {
            $content = preg_replace_callback(
                "~@import (.*?);~is",
                function ($m) {
                    $importFile  = $m[1];
                    $importFile  = trim($importFile, '"\'');
                    $dir         = Config('extraPacker__tempImportDir');
                    $file        = is_readable($dir . $importFile) ? $dir . $importFile : ($dir . $importFile . '.less');
                    $lessContent = '';
                    if (is_readable($file)) {
                        $lessContent = file_get_contents($file);
                        $lessContent = self::lessImport($lessContent, $file);
                    }

                    return $lessContent;
                },
                $content
            );
        }

        Config('extraPacker__tempImportDir', $oldImportDir);

        return $content;
    }

    /**
     * Предобработка каждого файла перед помещением в общий архив
     *
     * @param string $content
     * @param string $file
     * @param string $type либо js либо css
     *
     * @return string
     */
    public static function prepareEachFile($content, $path, $type)
    {
        $url = str_replace(BASEPATH, '/', $path);
        if ($type == 'css') {
            // Меняем пути к файлам
            $GLOBALS['__engineExtraPackerChangeCssUrl__curUrl__'] = dirname($url);
            $content                                              = preg_replace_callback("~url\((.*?)\)~is", [__CLASS__, "ChangeCssUrl_Url"], $content);
            unset($GLOBALS['__engineExtraPackerChangeCssUrl__curUrl__']);

            // Replace imports
            $content = self::lessImport($content, $path);
        }

        return $content;
    }

    /**
     * Подготовка спакованного js-контента перед записью в файл
     *
     * Данная функция по умолчанию дописывает пустой обработчик ошибок в начало js файла если это production
     */
    public static function prepareAllJs($content)
    {
        $noErrorsCode = '';

        // Если не debug режим, то выключаем javascript ошибки
        if (!Config(['phpIni', 'display_errors'])) {
            ob_start();
            ?>
            function __MyErrHandler(msg) {
            return true;
            }
            window.onerror = __MyErrHandler;
            <?php
            $noErrorsCode = ob_get_clean();
        }

        return $noErrorsCode . $content;
    }

    /**
     * Подготовка всего запакованного контента css перед записью в файл
     *
     * Пример использования:
     *      Пусть у нас глючит ф-я переписывания путей к картинкам в css, и она ставит BASEPATH тогда можно сделать
     *      что-то вроде такого: return str_replace("/home/Sites/test.com/i/", "/i/")
     */
    public static function prepareAllCss($content)
    {
        return $content;
    }
}
