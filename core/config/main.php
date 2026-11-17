<?php

Config('charset', 'utf-8');
Config('default_component', 'home');
Config('main_tpl', '_main_tpl');
Config('is_load_in_main_tpl', true);
Config('prepare_functions', [ // Ф-ии пост-обработки выводимого в браузер контента
    ['CodeHighlight', 'prepare'], // Блоки кода [code=язык] ... [/code] -> экранированный <pre><code>. Первым: теги внутри примеров кода не должны попасть в склейку <head> и css/js
    [Output::getInstance(), 'htmlValidate'], // Объединяет множественные head-ы (и bottom-ы) в один
    [Output::getInstance(), 'extraPackerPrepare'], // Склеивает подключённые css/js в один файл
    [Output::getInstance(), 'addDebug'], // Добавляет debug-панель (в DEBUG_MODE). Последним, чтобы замерить работу остальных обработчиков
]);
Config('html_container_attrs', [ // Параметры которые подставятся в главный <html>. Иногда удобно в контролере поменять если нужна страница с целиком отдельным дизайном
    'class' => 'is-usually-page site-sky' // site-sky - публичные страницы сайта: тёмная тема и стекло для готовых шаблонов (i/scss/components/theme.scss)
]);