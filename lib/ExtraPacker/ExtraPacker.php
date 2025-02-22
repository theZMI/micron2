<?php

require_once dirname(__FILE__) . "/Lib/CssPacker.php";
require_once dirname(__FILE__) . "/Lib/HtmlPacker.php";
require_once dirname(__FILE__) . "/Lib/JSMin.php";

/**
 * ExtraPacker - для склеивания и сжатия css и js файлов, а так же сжатия html-контента
 *
 * Выбирает все подключаемые js-ки (тоже с css-ками) собирает в 1 файл и пакует записывая файл info с информацией по
 * запакованным файлам. Поддерживает механизм транзакций.
 */
class ExtraPacker
{
    // Теги на странице куда нужно вставить спакованные файлы
    const TAG_CSS = '<!-- extraPackerCss -->';
    const TAG_JS = '<!-- extraPackerJs -->';

    // Только склеивать файлы
    const ONLY_MERGE_CSS = false;
    const ONLY_MERGE_JS = false;

    const ALWAYS_REPACK = false; // Перепаковывать при каждом вызове
    const ALWAYS_INDEPENDENT_PACK = false; // Перепаковывать независимо от собранной информации по списку необходимых файлов (обычно страницы дополняют список, а тут независимо от прошлых данных запаковка)

    const COMPRESS_LEVEL = 9;

    private bool $useGzip;
    private string $gzipExt = '.gz';

    private bool $useTransactions = true;

    private string $jsInfoFile;
    private string $jsCompressFile;
    private string $jsTransactionFile;

    private string $cssInfoFile;
    private string $cssCompressFile;
    private string $cssTransactionFile;

    private bool $packHtml;
    private bool $packCss;
    private bool $packJs;
    private bool $isActiveCss;
    private bool $isActiveJs;

    private $prepareJsFunc;
    private $prepareCssFunc;
    private $prepareEachFileFunc;

    private $pathToUrlForJsFunc;
    private $pathToUrlForCssFunc;
    private $urlToPathForJsFunc;
    private $urlToPathForCssFunc;

    private array $exceptionsJs;
    private array $exceptionsNotAddJs;
    private array $exceptionsCss;
    private array $exceptionsNotAddCss;

    private $preWriteCacheMiddleware;
    private $postWriteCacheMiddleware;

    private $statFilePath;
    private $repackedStat = [
        'css_pack_time' => 0,
        'css_repacked'  => false,
        'js_pack_time'  => 0,
        'js_repacked'   => false,
    ];

    private function prepareJsContent($jsContent)
    {
        return $this->prepareJsFunc ? call_user_func($this->prepareJsFunc, $jsContent) : $jsContent;
    }

    private function prepareCssContent($cssContent)
    {
        return $this->prepareCssFunc ? call_user_func($this->prepareCssFunc, $cssContent) : $cssContent;
    }

    private function getTrans($type): int
    {
        $file = $type == 'js' ? $this->jsTransactionFile : $this->cssTransactionFile;
        return is_readable($file) ? intval(FileSys::readFile($file)) : 0;
    }

    private function updateTrans($type)
    {
        FileSys::writeFile($type == 'js' ? $this->jsTransactionFile : $this->cssTransactionFile, time());
    }

    private function unique($files): array
    {
        $ret = [];
        array_walk(
            $files,
            function ($file) use (&$ret) {
                $ret[$file['addr']] = $file;
            }
        );
        return array_values($ret);
    }

    // По сути тот же самый array_merge
    private static function merge(): array
    {
        $arrs      = func_get_args();
        $ret       = [];
        $onlyAddrs = []; // Массив только адресов к файлам (нужен просто для более лаконичного вычисления)

        foreach ($arrs as $arr) {
            foreach ($arr as $elem) {
                if (in_array($elem['addr'], $onlyAddrs)) {
                    foreach ($ret as $k => $v) {
                        if ($v['addr'] == $elem['addr']) {
                            if ($v['time'] < $elem['time']) {
                                $ret[$k] = $elem;
                            }
                        }
                    }
                } else {
                    $ret[]          = $elem;
                    $onlyAddrs[] = $elem['addr'];
                }
            }
        }
        return $ret;
    }

    private function writeCompressFile($type, $files, $exceptions = [])
    {
        $this->repackedStat["{$type}_repacked"] = true;

        if ($this->preWriteCacheMiddleware) {
            call_user_func($this->preWriteCacheMiddleware);
        }

        if ($this->useTransactions) {
            $this->updateTrans($type);

            $filename = str_replace(".$type", self::getTrans($type) . ".$type", $type == 'js' ? $this->jsCompressFile : $this->cssCompressFile);
            $type == 'js' ? $this->jsCompressFile = $filename : $this->cssCompressFile = $filename;
        }

        // Удаляем файлы из списка для запаковки которых уже нет на сервере
        foreach ($files as $k => $v) {
            if (!file_exists($v['addr'])) {
                unset($files[$k]);
            }
        }

        $packInfoAboutFiles = serialize($files);
        FileSys::writeFile($type == 'js' ? $this->jsInfoFile : $this->cssInfoFile, $packInfoAboutFiles);

        foreach ($exceptions as $k => $v) {
            $exceptions[$k] = call_user_func($type == 'js' ? $this->urlToPathForJsFunc : $this->urlToPathForCssFunc, $v);
        }

        $endl             = $type == 'js' ? "\r\n;\r\n" : '';
        $exceptionContent = '';
        $content          = '';

        foreach ($files as $file) {
            $file       = $file['addr'];
            $curContent = file_get_contents($file);

            if ($this->prepareEachFileFunc) { // Если стоит обработка каждого файла перед добавлением в сжатый архив
                $curContent = call_user_func($this->prepareEachFileFunc, $curContent, $file, $type);
            }
            if (in_array($file, $exceptions)) {
                $exceptionContent .= $curContent . $endl;
            } else {
                $content .= $curContent . $endl;
            }
        }

        if ($type == 'js') {
            $packed = self::ONLY_MERGE_JS ? $content : JSMin::minify($content);
        } else {
            try {
                $parser = new \Less_Parser();
                $parser->parse($content);
                $content = $parser->getCss();
            } catch (Exception $e) {
                trigger_error("Can not less compile: " . print_r($e), E_USER_ERROR);
            }

            $packed = self::ONLY_MERGE_CSS ? $content : (new CssPacker($content))->pack();
        }


        $packed = $type == 'js'
            ? $this->prepareJsContent($packed . $exceptionContent)
            : $this->prepareCssContent($exceptionContent . $packed);

        $compressFile = $type == 'js' ? $this->jsCompressFile : $this->cssCompressFile;
        FileSys::writeFile($compressFile, $packed);
        FileSys::writeFile($compressFile . $this->gzipExt, gzencode($packed, self::COMPRESS_LEVEL));

        if ($this->postWriteCacheMiddleware) {
            call_user_func($this->postWriteCacheMiddleware);
        }
    }

    private function isNeedRePack($storage, $now): bool
    {
        if (self::ALWAYS_REPACK) {
            return true;
        }

        return !empty(array_diff(
            array_map(fn($v) => "{$v['time']}__{$v['addr']}", $now),
            array_map(fn($v) => "{$v['time']}__{$v['addr']}", $storage)
        ));
    }

    private static function canUseGZIP(): bool
    {
        return str_contains($_SERVER['HTTP_ACCEPT_ENCODING'] ?? '', 'gzip') && extension_loaded('zlib');
    }

    private function htmlPack($html)
    {
        return Minify_HTML::minify($html);
    }

    private function jsPack($html)
    {
        $exceptions       = $this->exceptionsJs;
        $exceptionsNotAdd = $this->exceptionsNotAddJs;

        $s = preg_replace('/<!--.*-->/Uis', '', $html);
        $m = [];
        preg_match("~<head.*?>(.*?)</head>~is", $s, $m);
        if (empty($m)) {
            return $html;
        }

        $s = $m[1];
        $m = [];
        preg_match_all("~<script.*?src=['\"](.*?).js['\"].*?></script>~", $s, $m);
        if (empty($m)) {
            return $html;
        }

        $files    = [];
        $replaces = [];
        foreach ($m[1] as $k => $url) {
            $url = $url . ".js";
            if (in_array($url, $exceptionsNotAdd)) {
                unset($m[1][$k]);
                continue;
            }
            $replaces[$m[0][$k]] = '';

            $url     = call_user_func($this->urlToPathForJsFunc, $url);
            $files[] = ['time' => filemtime($url), 'addr' => $url];
        }
        $html = strtr($html, $replaces);

        $files = self::unique($files);

        if (!count($files)) {
            return str_ireplace(self::TAG_JS, '', $html);
        }

        if ($this->isActiveJs) {
            if (file_exists($this->jsInfoFile)) {
                $info = unserialize(file_get_contents($this->jsInfoFile));
                if ($this->isNeedRePack($info, $files)) {
                    if (self::ALWAYS_INDEPENDENT_PACK) {
                        $info = [];
                    }
                    $newFiles = self::unique(self::merge($info, $files));
                    self::writeCompressFile('js', $newFiles, $exceptions);
                } else {
                    if ($this->useTransactions) {
                        $this->jsCompressFile = str_replace(".js", self::getTrans('js') . ".js", $this->jsCompressFile);
                    }
                }
            } else {
                self::writeCompressFile('js', $files, $exceptions);
            }

            $inc  = call_user_func(
                $this->pathToUrlForJsFunc,
                $this->useGzip ? $this->jsCompressFile . $this->gzipExt : $this->jsCompressFile
            );
            $html = str_ireplace(self::TAG_JS, self::TAG_JS . '<script type="text/javascript" charset="UTF-8" src="' . $inc . '"></script>', $html);
        } else {
            $inc = '';
            foreach ($files as $js) {
                $file = call_user_func($this->pathToUrlForJsFunc, $js['addr']);
                $inc  .= '<script type="text/javascript" charset="UTF-8" src="' . $file . '"></script>' . PHP_EOL;
            }

            $html = str_ireplace(self::TAG_JS, self::TAG_JS . $inc, $html);
        }

        return $html;
    }

    private function cssPack($html)
    {
        $exceptions       = $this->exceptionsCss;
        $exceptionsNotAdd = $this->exceptionsNotAddCss;

        $s = preg_replace('/<!--.*-->/Uis', '', $html);
        $m = [];
        preg_match("~<head[^e].*?>(.*?)</head>~is", $s, $m);
        if (empty($m)) {
            return $html;
        }

        $s = $m[1];
        $m = [];
        preg_match_all("~<link.*?href=['\"](.*?)\.(css|less)['\"].*?>~", $s, $m);
        if (empty($m)) {
            return $html;
        }

        $files    = [];
        $replaces = [];
        foreach ($m[1] as $k => $url) {
            $ext = $m[2][$k];
            $url .= ".$ext";
            if (in_array($url, $exceptionsNotAdd)) {
                unset($m[1][$k]);
                continue;
            }

            $replaces[$m[0][$k]] = '';
            $url                 = call_user_func($this->urlToPathForCssFunc, $url);
            if (!is_readable($url)) {
                trigger_error('Not found file for pack: ' . $url, E_USER_ERROR);
            }
            $files[] = ['time' => filemtime($url), 'addr' => $url];
        }
        $html = strtr($html, $replaces);

        $files = self::unique($files);

        if (!count($files)) {
            return str_ireplace(self::TAG_CSS, '', $html);
        }

        if ($this->isActiveCss) {
            if (file_exists($this->cssInfoFile)) {
                $info = unserialize(file_get_contents($this->cssInfoFile));
                if ($this->isNeedRePack($info, $files)) {
                    if (self::ALWAYS_INDEPENDENT_PACK) {
                        $info = [];
                    }
                    $newFiles = self::unique(self::merge($info, $files));
                    self::writeCompressFile('css', $newFiles, $exceptions);
                } else {
                    if ($this->useTransactions) {
                        $this->cssCompressFile = str_replace(".css", self::getTrans('css') . ".css", $this->cssCompressFile);
                    }
                }
            } else {
                self::writeCompressFile('css', $files, $exceptions);
            }

            $inc  = call_user_func(
                $this->pathToUrlForCssFunc,
                $this->useGzip ? $this->cssCompressFile . $this->gzipExt : $this->cssCompressFile
            );
            $html = str_ireplace(self::TAG_CSS, self::TAG_CSS . '<link rel="stylesheet" charset="UTF-8" type="text/css" href="' . $inc . '" />', $html);
        } else {
            $inc = '';
            foreach ($files as $css) {
                $file = call_user_func($this->pathToUrlForCssFunc, $css['addr']);
                $inc  .= '<link rel="stylesheet" charset="UTF-8" type="text/css" href="' . $file . '" />' . PHP_EOL;
            }

            $html = str_ireplace(self::TAG_CSS, self::TAG_CSS . $inc, $html);
        }

        return $html;
    }

    public function pack($html)
    {
        if (!str_contains($html, self::TAG_CSS) && !str_contains($html, self::TAG_JS)) {
            return $html;
        }

        ini_set('memory_limit', '1G');
        ini_set('max_execution_time', '99');

        $before = microtime(true);
        $html   = $this->packJs ? $this->jsPack($html) : $html;
        if ($this->repackedStat['js_repacked']) {
            $this->repackedStat['js_pack_time'] = microtime(true) - $before;
        }

        $before = microtime(true);
        $html   = $this->packCss ? $this->cssPack($html) : $html;
        if ($this->repackedStat['css_repacked']) {
            $this->repackedStat['css_pack_time'] = microtime(true) - $before;
        }

        $html = $this->packHtml ? $this->htmlPack($html) : $html;

        if ($this->statFilePath) {
            $stat = json_decode(FileSys::readFile($this->statFilePath), true);
            if ($this->repackedStat['js_pack_time']) {
                $stat['js_pack_time'] = $this->repackedStat['js_pack_time'];
            }
            if ($this->repackedStat['css_pack_time']) {
                $stat['css_pack_time'] = $this->repackedStat['css_pack_time'];
            }
            if ($stat) {
                FileSys::writeFile($this->statFilePath, json_encode($stat));
            }
        }

        return $html;
    }

    public function __construct(
        $urlToPathForJsFunc,
        $urlToPathForCssFunc,
        $pathToUrlForJsFunc,
        $pathToUrlForCssFunc,
        $preWriteCacheMiddleware,
        $postWriteCacheMiddleware,
        $jsInfoFile,
        $jsCompressFile,
        $jsTransactionFile,
        $cssInfoFile,
        $cssCompressFile,
        $cssTransactionFile,
        $packHtml,
        $packCss,
        $packJs,
        $isActiveCss,
        $isActiveJs,
        $exceptionsJs,
        $exceptionsNotAddJs,
        $exceptionsCss,
        $exceptionsNotAddCss,
        $useGzip,
        $prepareEachFileFunc,
        $prepareCssFunc,
        $prepareJsFunc,
        $statFilePath
    ) {
        $this->urlToPathForJsFunc       = $urlToPathForJsFunc;
        $this->urlToPathForCssFunc      = $urlToPathForCssFunc;
        $this->pathToUrlForJsFunc       = $pathToUrlForJsFunc;
        $this->pathToUrlForCssFunc      = $pathToUrlForCssFunc;
        $this->preWriteCacheMiddleware  = $preWriteCacheMiddleware;
        $this->postWriteCacheMiddleware = $postWriteCacheMiddleware;
        $this->jsInfoFile               = $jsInfoFile;
        $this->jsCompressFile           = $jsCompressFile;
        $this->jsTransactionFile        = $jsTransactionFile;
        $this->cssInfoFile              = $cssInfoFile;
        $this->cssCompressFile          = $cssCompressFile;
        $this->cssTransactionFile       = $cssTransactionFile;
        $this->packHtml                 = $packHtml;
        $this->packCss                  = $packCss;
        $this->packJs                   = $packJs;
        $this->isActiveCss              = $isActiveCss;
        $this->isActiveJs               = $isActiveJs;
        $this->exceptionsJs             = $exceptionsJs;
        $this->exceptionsNotAddJs       = $exceptionsNotAddJs;
        $this->exceptionsCss            = $exceptionsCss;
        $this->exceptionsNotAddCss      = $exceptionsNotAddCss;
        $this->useGzip                  = $useGzip && self::canUseGZIP();
        $this->prepareEachFileFunc      = $prepareEachFileFunc;
        $this->prepareCssFunc           = $prepareCssFunc;
        $this->prepareJsFunc            = $prepareJsFunc;
        $this->statFilePath             = $statFilePath;
    }
}
