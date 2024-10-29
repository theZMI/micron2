<?php

ini_set('memory_limit', '100M');

$preparer = function ($c) {
    return strtr($c, [
        'iDbQueryRepeats' => 'iDbQueryRepeats_' . uniqid()
    ]);
};

$log        = new ErrorLoggerModel(+Get('id'));
$sqlQueries = json_decode($log->sql, true);
$sqlTable   = MyDataBaseLog::render($sqlQueries);

echo '<div id="i-debug-panel">';
echo $preparer($sqlTable);
echo '</div>';
die;