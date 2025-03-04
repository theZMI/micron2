<?php

$action = Get('a', 'list');
$id     = +Get('id');
$print  = function ($data) {
    try {
        echo __($data);
    } catch (\Throwable $e) {
        Xmp($e);
    }
};

ini_set('memory_limit', '1G');

if ($action == 'delete') {
    (new ErrorLoggerModel($id))->delete();
    UrlRedirect::go(GetCurUrl('a=' . M_DELETE_PARAM . '&id=' . M_DELETE_PARAM));
}
if ($action == 'clear') {
    (new ErrorLoggerModel())->clear();
    UrlRedirect::go(GetCurUrl('a=' . M_DELETE_PARAM));
} elseif ($action == 'list') {
    $page   = +Get('page', 1);
    $logger = new ErrorLoggerModel();
    $count  = $logger->count();
    $all    = $logger->getList($page);
}