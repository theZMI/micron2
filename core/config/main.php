<?php

Config('charset', 'utf-8');
Config('default_component', 'home');
Config('main_tpl', '_main_tpl');
Config('is_load_in_main_tpl', true);
Config('prepare_functions', [ // Ф-ии пост-обработки выводимого в браузер контента
    [Output::getInstance(), 'htmlValidate'], // Объединяет множественные head-ы (и bottom-ы) в один
    [Output::getInstance(), 'extraPackerPrepare'], // Склеивает подключённые css/js в один файл
    ['SyntaxHighlighter', 'highlight'], // Компонент для подсветки кода как в редакторе
    [Output::getInstance(), 'addDebug'], // Добавляет debug-панель (в DEBUG_MODE). Последним, чтобы замерить работу остальных обработчиков
]);
Config('html_container_attrs', [ // Параметры которые подставятся в главный <html>. Иногда удобно в контролере поменять если нужна страница с целиком отдельным дизайном
    'class' => 'is-usually-page'
]);