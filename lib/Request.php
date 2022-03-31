<?php

/**
 * Класс работы с запросом к системе
 */
class Request
{
    private $lastLangDetect = null;

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

    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * Получает строку запроса к движку
     *
     * Так же данная функция занимается созданием параметров в $_GET если они были переданны в q и созданием константы LANG если ее еще не было
     *
     * @param string $q - строка запроса при ее отсутвии то что было в $_GET[q]
     *
     * @return string
     * @global array $g_config
     *
     */
    public function getQuery($q = null)
    {
        global $g_config;

        $langs   = array_keys($g_config['langs']);
        $defPage = $g_config['defaultComponent'];
        $q       = is_null($q) ? empty($_GET['micron_query']) ? $defPage : trim($_GET['micron_query'], "/") : $q;
        $q       = _StrReplaceFirst('&', '?', $q);
        $parse   = parse_url($q);
        $q       = FileSys::filenameSecurity($parse['path']);

        if (isset($parse['query'])) {
            foreach (explode('&', $parse['query']) as $elem) {
                if (strpos($elem, '=') !== false) {
                    $elem           = explode('=', $elem);
                    $_GET[$elem[0]] = isset($elem[1]) ? $elem[1] : null;
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

        return empty($q) ? $defPage : (new InputClean($g_config['charset']))->_clean_input_data($q);
    }

    public function getCurUrl($_pars = '')
    {
        $pars = '';
        $all  = $_GET;

        foreach (array_filter(explode('&', $_pars)) as $v) {
            if (strpos($v, '=') === false) {
                $all[$v] = null;
            } else {
                $t = explode('=', $v);
                list($id, $val) = $t;
                $all[$id] = urldecode($val);
            }
        }

        foreach ($all as $k => $v) {
            if ($k == 'micron_query') {
                continue;
            }
            if ($v == M_DELETE_PARAM) {
                continue;
            }
            $pars .= ("{$k}=" . urlencode($v) . "&");
        }
        $pars = substr($pars, 0, -1) ? ('&' . substr($pars, 0, -1)) : '';

        return SiteRoot($this->getQuery() . $pars);
    }

    public function getLastLangDetected()
    {
        return $this->lastLangDetect;
    }
}
