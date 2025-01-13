<?php
//sleep(3);
//(new ApiResponse())->error('Temporary closed');
$dirs = (new DirShiftsModel())->find(['user_id' => +$g_user->id, 'is_template' => false]);
$all  = array_values( array_map(
    fn($dirModel) => (new ShiftModel())->findOne(['dir_id' => $dirModel->id, 'user_id' => +$g_user->id]),
    $dirs
) );
$all  = array_map(fn($v) => $v->getDataToApi(), $all);
(new ApiResponse())->normal(
    $all
);