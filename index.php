<?php

use \Pecee\SimpleRouter\SimpleRouter;
use \Dotenv\Dotenv;

// While engine starting, we show all errors
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

define('BASEPATH', dirname(__FILE__) . '/');

if (is_readable(BASEPATH . 'vendor/autoload.php')) {
    require_once BASEPATH . 'vendor/autoload.php';
}

$dotenv = Dotenv::createImmutable(BASEPATH);
$_ENV = array_merge($_ENV, $dotenv->load()); // Include environment variables

ini_set('display_errors', $_ENV['DEBUG_MODE']); // Now we know site's mode, and we can change mode of show/log errors

require_once BASEPATH . 'core/core.php'; // Include engine (it replace display_errors mode)

ob_start();
SimpleRouter::start();
$content = ob_get_clean();

// If it's regular page, then we show it into main template
if (Config('is_load_in_main_tpl')) {
    ob_start();
    IncludeCom(Config('main_tpl'), ['content' => $content]);
    $content = ob_get_clean();
}

// Send ready content into browser
echo (Output::getInstance())->getContent($content);
