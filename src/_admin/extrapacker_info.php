<?php

$dirs = [
    'site'  => '_auto_merge_css_js',
    'admin' => '_admin_auto_merge_css_js'
];
foreach ($dirs as $site => $dir) {
    $fullDir     = BASEPATH . "tmp/{$dir}/";
    $timeStat    = is_readable($fullDir . 'time_stat.json') ? json_decode(FileSys::readFile($fullDir . 'time_stat.json'), true) : [];
    $execStat    = array_merge(
        ['css_pack_time' => 0, 'js_pack_time' => 0],
        $timeStat ?: []
    );
    $dirs[$site] = [];
    foreach (['css', 'js'] as $subDir) {
        $fullDir = BASEPATH . "tmp/{$dir}/{$subDir}/";
        $trans   = is_readable($fullDir . 'trans.txt') ? FileSys::readFile($fullDir . 'trans.txt') : 0;
        $info    = is_readable($fullDir . 'inf.txt') ? unserialize(FileSys::readFile($fullDir . 'inf.txt')) : [];

        $dirs[$site][$subDir] = [
            'pack_time' => $execStat,
            'timestamp' => $trans,
            'files'     => $info
        ];
    }
}