<?php

use \Pecee\SimpleRouter\SimpleRouter;

// While engine starting, we show all errors
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

define('BASEPATH', dirname(__FILE__) . '/');

require_once BASEPATH . '_env.php'; // Include environment variables
ini_set('display_errors', EnvConfig::DEBUG_MODE); // Now we know site's mode, and we can change mode of show/log errors

if (is_readable(BASEPATH . 'vendor/autoload.php')) {
    require_once BASEPATH . 'vendor/autoload.php';
}

require_once BASEPATH . 'core/core.php'; // Include engine (it replace display_errors mode)

ob_start();
SimpleRouter::start();
$content = ob_get_clean();

// If it's regular page, then we show it into main template
if (Config('isLoadInMainTpl')) {
    ob_start();
    IncludeCom(Config('mainTpl'), ['content' => $content]);
    $content = ob_get_clean();
}

// Send ready content into browser
echo (Output::getInstance())->getContent($content);
