<?php

$action   = (string)Post('a'); // Открыть/закрыть сайт - только POST: ссылку может дёрнуть prefetch или сканер
$lockFile = BASEPATH . 'tmp/site_stopper.txt';
$isLocked = file_exists($lockFile);

if ($action === 'open') {
    @unlink($lockFile);
    UrlRedirect::go(GetCurUrl('a=' . M_DELETE_PARAM));
} elseif ($action === 'close') {
    FileSys::writeFile($lockFile, '1');
    UrlRedirect::go(GetCurUrl('a=' . M_DELETE_PARAM));
}