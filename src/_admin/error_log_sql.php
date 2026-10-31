<?php

ini_set('memory_limit', '100M');

$preparer = function ($c) {
    return strtr($c, [
        'iDbQueryRepeats' => 'iDbQueryRepeats_' . uniqid()
    ]);
};

$log        = new ErrorLoggerModel(intval(Get('id')));
$sqlQueries = json_decode($log->sql, true);
$sqlTable   = RenderDbLog($sqlQueries ?: []);

echo '<div id="i-debug-panel">';
echo $preparer($sqlTable);
echo '</div>';
die;