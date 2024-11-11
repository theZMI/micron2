<?php

$secretCode = Get('secretCode');

if ($secretCode === 'Sid1o8dXYDG17dhasigAGFOdvuaid') {
    $lastVersion = GetLastAppVersion();
    $downloader  = new Downloader();
    $downloader->download(BASEPATH . "i/apps/{$lastVersion}.apk", "kkosa_tasks_{$lastVersion}.apk");
    die;
}

IncludeCom('403');
return;