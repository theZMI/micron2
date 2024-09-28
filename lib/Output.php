<?php

class Output
{
    use SingletonTrait;

    public function htmlValidate($c)
    {
        return (new HtmlValidate($c))->get();
    }

    // Возвращает debug-панель располагаемую внизу страницу
    public function getDebug()
    {
        ob_start();
        IncludeCom('_dev/debug_panel/main');
        return ob_get_clean();
    }

    public function addDebug($c)
    {
        if (!Env('DEBUG_MODE')) {
            return $c;
        }
        return $c . $this->getDebug();
    }

    /**
     * Получение подготовленного для вывода контента
     */
    public function getContent($c)
    {
        foreach (Config('prepareFunctions') as $func) {
            $c = call_user_func($func, $c);
        }
        return $c;
    }

    public function extraPackerPrepare($c)
    {
        require_once BASEPATH . 'lib/ExtraPacker/Config.php';
        require_once BASEPATH . 'lib/ExtraPacker/ExtraPacker.php';

        $conf           = Config('extraPacker');
        $packedFilesDir = BASEPATH . 'tmp/' . $conf['dir'];
        $extraPacker    = new ExtraPacker(
            ['ExtraPacker_Config', 'getPathJsFileFromUrl'],
            ['ExtraPacker_Config', 'getPathCssFileFromUrl'],
            ['ExtraPacker_Config', 'getAddrJsPackFile'],
            ['ExtraPacker_Config', 'getAddrCssPackFile'],
            null,
            null,
            $packedFilesDir . '/js/inf.txt',
            $packedFilesDir . '/js/js.js',
            $packedFilesDir . '/js/trans.txt',
            $packedFilesDir . '/css/inf.txt',
            $packedFilesDir . '/css/css.css',
            $packedFilesDir . '/css/trans.txt',
            $conf['packHtml'],
            $conf['packCss'],
            $conf['packJs'],
            true,
            true,
            $conf['arrExceptions_js'],
            $conf['arrExceptionsNotAdd_js'],
            $conf['arrExceptions_css'],
            $conf['arrExceptionsNotAdd_css'],
            $conf['buffering'],
            ['ExtraPacker_Config', 'prepareEachFile'],
            ['ExtraPacker_Config', 'prepareAllCss'],
            ['ExtraPacker_Config', 'prepareAllJs'],
            $conf['statFilePath']
        );

        return $extraPacker->pack($c);
    }
}
