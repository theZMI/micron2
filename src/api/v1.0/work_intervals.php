<?php

$model = new WorkIntervalModel();
$list  = $model->find([
    'user_id' => $g_user->id,
    'from'    => strtotime( date('d-m-Y 00:00:00') )
]);
(new ApiResponse())->normal(
    array_map(fn($v) => $v->getData(), $list)
);