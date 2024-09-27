<?php

// Оборачиваем главный контент
$htmlAttrs = '';
array_filter(
    Config('htmlContainerAttrs'),
    function ($v, $k) use (&$htmlAttrs) {
        $htmlAttrs .= " $k='{$v}'";
    },
    ARRAY_FILTER_USE_BOTH
);

// Если мы в пользовательском кабинете, то у него ещё свой внутренний шаблон
if (IS_USER_CABINET) {
    ob_start();
    IncludeCom('user/_main_tpl', ['content' => $content]);
    $content = ob_get_clean();
}

AutoTitle($content);