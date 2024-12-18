<?php

$dir_id     = +Get('id');
$model      = new DirShiftsModel($dir_id);
$modelParam = function ($param, $default = '') use (&$model) {
    return ModelParam($model, $param, Post($param, $default));
};