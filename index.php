<?php

// While engine start, we show all errors
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

// Include environment variables
define('BASEPATH', str_replace('\\', '/', dirname(__FILE__)) . '/');
require_once BASEPATH . '_env.php';

// Now we know mode of our site, and we can change settings
ini_set('display_errors', DEBUG_MODE);

if (is_readable(BASEPATH . 'vendor/autoload.php')) {
    require_once BASEPATH . 'vendor/autoload.php';
}

// After this include, display_errors will be replaced to value in settings
require_once BASEPATH . 'core/core.php';

// Temporary close site
// IncludeCom('reconstruction');

// Include current page
ob_start();
    header(Php::status(200));
Config('isControllerLoad', IncludeCom(GetQuery()));
$content = ob_get_clean();

// If current page not found => show 404
if (!Config('isControllerLoad')) {
    ob_start();
    IncludeCom('404');
    $content = ob_get_clean();
}

// If it is regular page of our site, we show it into main_template
if (Config('isLoadInMainTpl')) {
    ob_start();
    IncludeCom(Config('mainTpl'), ['content' => $content]);
    $content = ob_get_clean();
}

// Show ready content
echo (Output::getInstance())->getContent($content);
