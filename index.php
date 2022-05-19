<?php

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

// IncludeCom('reconstruction'); // Temporary close site

// Include current page
ob_start();
header(Php::status(200));
Config('isControllerLoad', IncludeCom(GetQuery()));
$content = ob_get_clean();

// If current page doesn't exist, then we include 404-page
if (!Config('isControllerLoad')) {
    ob_start();
    IncludeCom('404');
    $content = ob_get_clean();
}

// If it's regular page, then we show it into main template
if (Config('isLoadInMainTpl')) {
    ob_start();
    IncludeCom('_main_tpl', ['content' => $content]);
    $content = ob_get_clean();
}

// Send ready content into browser
echo (Output::getInstance())->getContent($content);
