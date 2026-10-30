<?php

use \Pecee\SimpleRouter\SimpleRouter;
use \Dotenv\Dotenv;

// Mark when script started
$GLOBALS['micron_start_time'] = microtime(true);

// While engine starting, we show all errors
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

define('BASEPATH', dirname(__FILE__) . '/');

// Адрес вида "//..." роутер (он создаётся при подключении движка) не может разобрать и падает с фатальной ошибкой, поэтому схлопываем ведущие слэши
if (isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = '/' . ltrim($_SERVER['REQUEST_URI'], '/');
}

require_once BASEPATH . 'core/func/benchmark.php'; // Helpers to benchmarking. Include here because we don't include engine yet

BM('Request');
BM('Request / Engine');
BM('Request / Engine / Composer');
if (is_readable(BASEPATH . 'vendor/autoload.php')) {
    require_once BASEPATH . 'vendor/autoload.php';
}
BM('Request / Engine / Composer');

BM('Request / Engine / Env');
$dotenv = Dotenv::createImmutable(BASEPATH);
$_ENV = array_merge($_ENV, $dotenv->load()); // Include environment variables
ini_set('display_errors', $_ENV['DEBUG_MODE']); // Now we know site's mode, and we can change mode of show/log errors
BM('Request / Engine / Env');

require_once BASEPATH . 'core/core.php'; // Include engine (it replace display_errors mode)
BM('Request / Engine');

ob_start();
BM('Request / Routing');
SimpleRouter::start();
BM('Request / Routing');
$content = ob_get_clean();

// If it's regular page, then we show it into main template
BM('Request / MainTpl component');
if (Config('is_load_in_main_tpl')) {
    ob_start();
    IncludeCom(Config('main_tpl'), ['content' => $content]);
    $content = ob_get_clean();
}
BM('Request / MainTpl component');

// Send ready content into browser
BM('Request / Prepare content');
$readyContent = (Output::getInstance())->getContent($content);
BM('Request / Prepare content');
BM('Request');
echo $readyContent;
