<?php

Config('charset', 'utf-8');
Config('defaultComponent', 'home');
Config('isLoadInMainTpl', true);
Config('startExecTime', microtime(true));
Config('prepareFunctions', [ // Ф-ии пост-обработки выводимого в браузер контента
    [Output::getInstance(), 'htmlValidate'], // Объединяет множественные head-ы в один
    [Output::getInstance(), 'addDebug'] // Добавляет debug-информацию если это DEBUG_MODE и в запросе есть debug_panel=1
]);
Config('htmlContainerAttrs', [ // Параметры которые подставятся в главный <html>
    'class' => 'is-usually-page'
]);

// Подключаем файл роутинга, чтобы ф-ии SiteRoot и Root работали уже в остальных файлах
require_once BASEPATH . 'core/config/router.php';
