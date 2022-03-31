<?php

/**
 * Класс используется для наделения файла перед отдачей в браузер клиента хедарами для кеширования
 */
class BrowserDataCache
{
    /**
     * Время хранения файла в кеше юзера
     */
    const CACHE_TIME = 31536000; // Год

    /**
     * Время открытого соединения при запросе пользователя
     */
    const TIME_KEEP_ALIVE = 250;

    /**
     * Использовать ли ETag для предварительного просмотра кеша перед его перезаписью
     */
    const USE_ETAGS = true;

    private function __construct($conf)
    {
        $this->config = $conf;
    }

    /**
     * Отправить хедеры, что бы браузер не кешировал эти файлы
     *
     * @static
     */
    public static function notCache()
    {
        header("Expires: Sun, 1 Jan 2000 12:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

    private static function getFileETag($filename)
    {
        $etag = md5($filename . filemtime($filename));

        $etag = substr_replace($etag, '-', 8, 0);
        $etag = substr_replace($etag, '-', 13, 0);
        $etag = substr_replace($etag, '-', 18, 0);
        $etag = substr_replace($etag, '-', 23, 0);

        return $etag;
    }

    private static function toCache($file, $ETag = false)
    {
        header('Date: ' . date('r') . ' GMT');
        header('Keep-Alive: ' . self::TIME_KEEP_ALIVE);
        header('Pragma: public');
        header('Expires: ' . date('r', time() + self::CACHE_TIME));
        header('Cache-control: max-age=' . self::CACHE_TIME);
        header('Last-Modified: ' . date('r', filemtime($file)) . ' GMT');
        if (self::USE_ETAGS) {
            header('ETag: "' . ($ETag ?: self::getFileETag($file)) . '"');
        }
        header('Content-Length: ' . filesize($file));
    }

    private static function chkCache($file, $ETag)
    {
        if (function_exists('getallheaders')) {
            $headers = getallheaders();

            if (isset($headers['If-None-Match'])) {
                if ($headers['If-None-Match'] == '"' . $ETag . '"') {
                    header('HTTP/1.1 304 Not Modified');
                    exit();
                }
            } else {
                if (isset($headers['If-Modified-Since'])) {
                    if ($headers['If-Modified-Since'] == date('r', filemtime($file))) {
                        header('HTTP/1.1 304 Not Modified');
                        exit();
                    }
                }
            }
        }
    }

    public static function ext($addrFile)
    {
        return pathinfo(strtolower($addrFile), PATHINFO_EXTENSION);
    }

    private static function secure($file)
    {
        global $g_config;
        $file = strtr($file, ['..' => '']); // Что бы запретить попытки возврата вверх по файлам
        $file = realpath($file);
        $file = str_replace("\\", "/", $file);
        $ext  = self::ext($file);

        if (!in_array($ext, $g_config['browserdatacache_allow_filetypes'])) {
            trigger_error("Not allowed file type ($file -> $ext) for BrowserDataCache!", E_USER_ERROR);
        }

        $isInAllowDir = false;
        foreach ($g_config['browserdatacache_allow_dirs'] as $dir) {
            if (stripos($file, $dir) !== false) {
                $isInAllowDir = true;
                break;
            }
        }
        if (!$isInAllowDir) {
            trigger_error("File {$file} from disallow directory for BrowserDataCache", E_USER_ERROR);
        }

        return $file;
    }

    /**
     * Вывод файла в браузер с тегами для кеширования
     *
     * @static
     *
     * @param string $file - path к файлу который надо вывести
     * @param callable $pPrepareOutFile - функция обработчик выводных данных
     */
    public static function outFile($file, $pPrepareOutFile = null)
    {
        global $g_config;

        // Если это файл и он доступен для чтения
        if (is_file($file) && is_readable($file)) {
            $file = self::secure($file);

            header('HTTP/1.0 200 OK');

            // Если файлы в кеше нужно помечать ETag-ами для сверки
            if (self::USE_ETAGS) {
                $ETag = self::getFileETag($file);
                self::chkCache($file, $ETag);
            }
            self::toCache($file, self::USE_ETAGS ? $ETag : false);

            // Отдаём хедер о типе файла
            $ext = self::ext($file);
            if (stripos($file, '.css.gz')) {
                header('Content-Encoding: gzip');
                header('Content-type: text/css');
            } elseif (stripos($file, '.js.gz')) {
                header('Content-Encoding: gzip');
                header('Content-type: application/x-javascript');
            } elseif ($ext == 'gz') {
                header('Content-Encoding: gzip');
            } elseif ($ext == 'zip') {
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($file) . '"');
                header('Content-Transfer-Encoding: binary');
            } elseif (isset($g_config['browserdatacache_mime_types'][$ext])) {
                header('Content-type: ' . $g_config['browserdatacache_mime_types'][$ext]);
            }
            header('X-App-Content-Output-From: BrowserDataCache');

            // Выводим содержимое файла
            if ($pPrepareOutFile != null) // Если установлена ф-я вывода, то выводим через неё
            {
                call_user_func($pPrepareOutFile, $file);
            } else {
                readfile($file);
            }

            exit();
        } else {
            return false;
        }
    }
}
