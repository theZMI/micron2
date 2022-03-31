<?php

$g_config = [
    'charset'          => 'utf-8',
    'mainTpl'          => '_main_tpl',
    'defaultComponent' => 'home',
    'isLoadInMainTpl'  => true,
    'startExecTime'    => microtime(true)
];

$g_output = Output::getInstance();

// Ф-ии пост-обработки выводимого в браузер контента
$g_config['prepareFunctions'] = [
    [$g_output, 'htmlValidate'], // Объединяет множественные head-ы в один
    [$g_output, 'addDebug'] // Добавляет debug-информацию если это DEBUG_MODE и в запросе есть debug_panel=1
];

// Параметры которые подставятся в главный <html>
$g_config['htmlContainerAttrs'] = [
    'class' => 'is-usually-page'
];

// Чтобы ф-ии SiteRoot и Root работали с роутером в остальных файлах
require_once BASEPATH . 'core/config/router.php';
