<?php

$dirs = (new DirShiftsModel())->find([
    'is_template' => false,
    'user_id'     => +$g_user->id,
    'status'      => ShiftModel::STATUS_CREATED,
]);
$all  = array_values( array_map(
    fn($dirModel) => (new ShiftModel())->findOne([
        'dir_id'  => $dirModel->id,
        'user_id' => +$g_user->id,
        'status'  => ShiftModel::STATUS_CREATED,
    ]),
    $dirs
) );
$all  = array_map(fn($v) => $v->getDataToApi(), $all);
(new ApiResponse())->normal(
    $all
);