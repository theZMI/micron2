<?php

require_once BASEPATH . 'core/config/webpack.php';

Config('extra_packer', [
    'dir' => '_auto_merge_css_js',

    'packHtml' => false,
    'packCss'  => true,
    'packJs'   => true,

    'arrExceptions_js'        => [], // Включается в конец запакованного файла в несжатом виде
    'arrExceptionsNotAdd_js'  => Config(['webpack', 'jsFiles']), // NotAdd-исключения не включаются в единый, запакованный файл, а остаются отдельными скриптами (но убираются повторные включая)
    'arrExceptions_css'       => [],
    'arrExceptionsNotAdd_css' => Config(['webpack', 'cssFiles']),

    'buffering'    => false, // Включает GZIP для склеенных css/js
    'statFilePath' => BASEPATH . 'tmp/_auto_merge_css_js/time_stat.json',
]);