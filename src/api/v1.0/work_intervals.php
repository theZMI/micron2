<?php

$model = new WorkIntervalModel();

$list  = $model->find([
    'user_id' => $g_user->id,
    'from'    => 'active_day'
]);
(new ApiResponse())->normal(
    array_map(fn($v) => $v->getData(), $list)
);