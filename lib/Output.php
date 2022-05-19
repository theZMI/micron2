<?php

class Output
{
    private function __construct()
    {
    }

    public static function getInstance()
    {
        static $o = null;
        if (is_null($o)) {
            $o = new self();
        }
        return $o;
    }

    public function htmlValidate($c)
    {
        return (new HtmlValidate($c))->get();
    }

    // Возвращает debug-панель располагаемую внизу страницу
    public function getDebug()
    {
        $ret = '';
        if (EnvConfig::DEBUG_MODE && Get('debug_panel')) {
            ob_start();
            IncludeCom('_dev/debug_panel/main');
            $ret = ob_get_clean();
        }

        return $ret;
    }

    public function addDebug($c)
    {
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
}
