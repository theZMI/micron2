<?php

// Оборачиваем главный контент
$htmlAttrs = '';
array_walk(
    $g_config['htmlContainerAttrs'],
    function ($v, $k) use (&$htmlAttrs) {
        $htmlAttrs .= " $k='{$v}'";
    }
);

// Если мы в пользовательском кабинете, то у него ещё свой внутренний шаблон
//if (IS_USER_CABINET) {
//    ob_start();
//        IncludeCom('user/_main_tpl', ['content' => $content]);
//    $content = ob_get_clean();
//}

AutoTitle($content);

$cssFiles = [
    'i/webpack/dist/app.css'
];
$jsFiles = [
    'i/webpack/dist/app.js'
];