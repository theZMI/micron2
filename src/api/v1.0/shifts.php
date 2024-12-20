<?php

$dirs = (new DirShiftsModel())->find(['user_id' => +$g_user->id, 'is_template' => false]);
$all  = array_values( array_map(
    fn($dirModel) => (new ShiftModel())->findOne(['dir_id' => $dirModel->id, 'user_id' => +$g_user->id]),
    $dirs
) );
$all  = array_map(fn($v) => $v->getDataToApi(), $all);
Xmp($all);
echo PHP_EOL;
$m = new ShiftModel(15);
//Xmp( array_map(fn($task) => $task->getDataToApi(), $m->tasks));
Xmp($m->getDataToApi());
(new ApiResponse())->normal(
    $all
);