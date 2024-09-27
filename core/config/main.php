<?php

Config('charset', 'utf-8');
Config('defaultComponent', 'home');
Config('mainTpl', '_main_tpl');
Config('isLoadInMainTpl', true);
Config('startExecTime', microtime(true));
Config('prepareFunctions', [ // Ф-ии пост-обработки выводимого в браузер контента
    [Output::getInstance(), 'htmlValidate'], // Объединяет множественные head-ы в один
    [Output::getInstance(), 'addDebug'], // Добавляет debug-информацию если это DEBUG_MODE и в запросе есть debug_panel=1
    [Output::getInstance(), 'extraPackerPrepare'],
    ['SyntaxHighlighter', 'highlight'],
]);
Config('htmlContainerAttrs', [ // Параметры которые подставятся в главный <html>. Иногда удобно в контролере поменять если нужна страница с целиком отдельным дизайном
    'class' => 'is-usually-page'
]);