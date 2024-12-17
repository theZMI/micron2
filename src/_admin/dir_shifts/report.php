<?php

$model             = new DirShiftsModel(+Get('id'));
$users             = (new UserModel())->getList();
$allWorkersIds     = array_keys($users);
$currentWorkersIds = $model->user_ids;
$modelParam        = function ($param, $default = '') use (&$model) {
    return ModelParam($model, $param, Post($param, $default));
};
$shifts            = $model->shifts;
$getShiftByUserID  = function ($user_id) use ($shifts) {
    foreach ($shifts as $shift) {
        if (+$shift->user_id === +$user_id) {
            return $shift;
        }
    }
    return null;
};