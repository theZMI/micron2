<?php

Config('charset', 'utf-8');
Config('default_component', 'home');
Config('main_tpl', '_main_tpl');
Config('is_load_in_main_tpl', true);
Config('start_exec_time', microtime(true));
Config('prepare_functions', [ // Ф-ии пост-обработки выводимого в браузер контента
    [Output::getInstance(), 'htmlValidate'], // Объединяет множественные head-ы в один
    [Output::getInstance(), 'addDebug'], // Добавляет debug-информацию если это DEBUG_MODE и в запросе есть debug_panel=1
    [Output::getInstance(), 'extraPackerPrepare'],
    ['SyntaxHighlighter', 'highlight'], // Компонент для подсветки кода как в редакторе
]);
Config('html_container_attrs', [ // Параметры которые подставятся в главный <html>. Иногда удобно в контролере поменять если нужна страница с целиком отдельным дизайном
    'class' => 'is-usually-page'
]);