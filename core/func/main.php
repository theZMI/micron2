<?php

// Параметры какие защиты отключать
define('M_HTML_FILTER_OFF', 2); // Выкл. защиту от HTML текста
define('M_XSS_FILTER_OFF', 4); // Выкл. защиту от XSS вставок
define('M_DELETE_PARAM', 'micron_not_use_this_param__98gmkhgKfdg');


// Производит замену только первого вхождения подстроки в строку
function _StrReplaceFirst($search, $replace, $subject, $offset = 0)
{
    $ret = $subject;
    $pos = strpos($subject, $search, $offset);
    if ($pos !== false) {
        $ret = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $ret;
}

/**
 * Загрузка компонента
 *
 * Производит подключение файлов lang/src/tpl
 *
 * @param string $_micron_file - URI запроса т.е. подключаемый файл или строка вида file_name&par1=a&par2=b
 * @param array $_micron_params - массив параметров которые необходимо создать до подключения
 *
 * @return int - вовзращает кол-во подключенных файлов этого компонента
 * @global array $g_config
 *
 * @global array $g_lang
 */
function IncludeCom($_micron_file, $_micron_params = [])
{
    global $g_lang, $g_user, $g_admin;

    $_micron_file = GetQuery($_micron_file);
    foreach ($_micron_params as $micron_name => $micron_value) {
        $$micron_name = $micron_value;
    }

    $micron_request = Request::getInstance();
    $micron_has = 0;
    $micron_files = [ // Список всех файлов которые требуется подключить
        BASEPATH . 'lang/' . DEF_LANG . "/{$_micron_file}.php",
        BASEPATH . 'lang/' . $micron_request->getLastLangDetected() . "/{$_micron_file}.php",
        BASEPATH . "src/{$_micron_file}.php",
        BASEPATH . "tpl/{$_micron_file}.php",
    ];
    $micron_files = array_unique($micron_files);

    // Подключаем все возможные файлы компонента
    ob_start();
        foreach ($micron_files as $micron_f) {
            if (is_readable($micron_f)) {
                $micron_has++;
                require $micron_f;
                if (isset($GLOBALS['__breakCurrentCom__']) && $GLOBALS['__breakCurrentCom__']) {
                    $GLOBALS['__breakCurrentCom__'] = 0;
                    break;
                }
            }
        }
    echo ob_get_clean();

    return $micron_has;
}

/**
 * Фунция выхода из компонента что бы дальше файлы не подключала
 */
function ExitCom()
{
    $GLOBALS['__breakCurrentCom__'] = 1;
}

/**
 * Получает строку запроса к движку
 */
function GetQuery($q = null)
{
    return (Request::getInstance())->getQuery($q);
}

/**
 * Определяем язык под которым запущен сайт
 */
function DefineLang()
{
    GetQuery();
}

/**
 * Получение текущей строки запроса к движку (удобно юзать в action для формы если это компоннет)
 */
function GetCurUrl($pars = '')
{
    return (Request::getInstance())->getCurUrl($pars);
}

/**
 * Возвращает юрл до корня сайта, где в начале стоит / нужна для данных (image, js, css ...)
 */
function Root($uri = '')
{
    return "/{$uri}";
}

/**
 * Путь до корня сайта с подставкой языка, нужна для ссылок
 */
function SiteRoot($uri = '')
{
    $lang = LANG == DEF_LANG ? '' : (LANG . '/');
    $ret  = $lang || $uri ? "/?micron_query={$lang}{$uri}" : '';
    $ret  = empty($ret) ? '/' : $ret;
    $ret  = _StrReplaceFirst('/?micron_query=', '/', _StrReplaceFirst('&', '?', $ret));

    // Делаем замену вставлено URL на тот что в роутинге по правилу (если есть конечно)
    $router = Router::getInstance();
    $ret = EnvConfig::SITE_ROOT_URL . $router->getNewQuery(substr($ret, 1));

    // Заменяем начальную страницу в URL на просто корень сайта
    $ret = in_array(
        $ret,
        [
            EnvConfig::SITE_ROOT_URL . Config('defaultComponent'),
            EnvConfig::SITE_ROOT_URL . '?micron_query=' . Config('defaultComponent'),
        ]
    )
        ? EnvConfig::SITE_ROOT_URL
        : $ret;

    return $ret;
}

/**
 * Параметр из $_GET
 */
function Get($name, $def = false, $secureFlags = 0)
{
    return (Input::getInstance())->get($name, $def, $secureFlags);
}

/**
 * Параметр из $_POST
 */
function Post($name, $def = false, $secureFlags = 0)
{
    return (Input::getInstance())->post($name, $def, $secureFlags);
}

/**
 * Добавляем функцию автозагрузки классов (библиотек и моделей)
 */
spl_autoload_register(function ($className) {
    if (is_readable(BASEPATH . "lib/{$className}.php")) {
        require_once BASEPATH . "lib/{$className}.php";
    }
    if (is_readable(BASEPATH . "model/{$className}.php")) {
        require_once BASEPATH . "model/{$className}.php";
    }
});

/**
 * Возвращает параметр из языкового массива
 */
function L($name)
{
    return (Input::getInstance())->lang($name);
}
