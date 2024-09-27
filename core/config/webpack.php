<?php

$cssFiles = [
    'i/webpack/dist/vendors~app.css',
    'i/webpack/dist/app.css'
];
$cssFiles = array_filter($cssFiles, fn($file) => is_readable(BASEPATH . $file));
Config(['webpack', 'cssFiles'], $cssFiles);

$jsFiles = [
    'i/webpack/dist/vendors~app.js',
    'i/webpack/dist/app.js'
];
$jsFiles = array_filter($jsFiles, fn($file) => is_readable(BASEPATH . $file));
Config(['webpack', 'jsFiles'], $jsFiles);