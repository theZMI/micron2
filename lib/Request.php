<?php

/**
 * Класс работы с запросом к системе
 */
class Request
{
    private function __construct() { }

    use SingletonTrait;

    private $lastLangDetect = null;

    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * Получает строку запроса к движку
     *
     * Так же данная функция занимается созданием параметров в $_GET если они были переданы в micron_query и созданием константы LANG если ее еще не было
     *
     * @param string $q - строка запроса при ее отсутствии то что было в $_GET[micron_query]
     *
     * @return string
     * @global array $g_config
     *
     */
    public function getQuery($q = null)
    {
        $langs   = array_keys(Config('langs'));
        $defPage = Config('default_component');
        $q       = is_null($q)
            ? (empty($_GET['micron_query']) ? $defPage : trim($_GET['micron_query'], "/"))
            : $q;
        $q       = _StrReplaceFirst('&', '?', $q);
        $parse   = parse_url($q);
        $q       = FileSys::filenameSecurity($parse['path']);

        if (isset($parse['query'])) {
            foreach (explode('&', $parse['query']) as $elem) {
                if (str_contains($elem, '=')) {
                    $elem           = explode('=', $elem);
                    $_GET[$elem[0]] = $elem[1] ?? null;
                }
            }
        }
        $parts = explode('/', $q);

        $this->lastLangDetect = isset($parts[0]) && in_array($parts[0], $langs) ? $parts[0] : (defined('LANG') ? LANG : DEF_LANG);

        if (!defined('LANG')) {
            define('LANG', $this->lastLangDetect);
        }

        if (isset($parts[0]) && in_array($parts[0], $langs)) {
            $q = implode('/', array_splice($parts, 1));
        }

        return empty($q) ? $defPage : (new InputClean(Config('charset')))->_clean_input_data($q);
    }

    /**
     * Текущий url с изменёнными GET-параметрами
     *
     * @param string $appendQuery - параметры вида a=1&b=2, которые добавятся (или заменят текущие). Значение M_DELETE_PARAM удаляет параметр
     */
    public function getCurUrl($appendQuery = '')
    {
        parse_str((string)$appendQuery, $appendParams);
        $getParams = array_merge($_GET, $appendParams); // То что передано в $appendQuery перетирает то что в $_GET
        $getParams = array_filter($getParams, function ($v, $k) { // Убираем служебный micron_query и параметры, помеченные на удаление
            return $k !== 'micron_query' && $v !== M_DELETE_PARAM;
        }, ARRAY_FILTER_USE_BOTH);
        $newQuery  = http_build_query($getParams); // Корректно кодирует значения и массивы

        // Путь берём из getQuery() (без языкового префикса), язык добавит SiteRoot
        return SiteRoot($this->getQuery() . ($newQuery ? "&{$newQuery}" : ''));
    }

    public function getLastLang()
    {
        return $this->lastLangDetect;
    }
}
