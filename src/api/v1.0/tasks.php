<?php

$model = new ShiftModel();
$list = $model->find(['user_id' => +$g_user->id, ['is_template' => false]]);
//$list = array_filter($list, fn($v) => !$v->is_template);
$tasks = array_reduce($list, fn(&$tasks, $v) => $tasks = array_merge($tasks, $v->tasks));
//$tasks = [];
//foreach ($list as $shift) {
//    $tasks = array_merge($tasks, $shift->tasks);
//}
/*
// Берём смены для данного юзера которые подходят под текущий временной промежуток (то есть уже начались, но ешё не закончились)
$shifts         = $model->getListByUserID(+$g_user->id);
$shiftsOkByTime = [];
foreach ($shifts as $shift) {
    if (+$shift->status !== ShiftModel::STATUS_DONE
        &&
        $shift->start_time <= time() && time() <= $shift->end_time) {
        $shiftsOkByTime[$shift->id] = $shift;
    }
}

$tasks = [];
foreach ($shiftsOkByTime as $shift) {
    foreach ($shift->tasks as $task) {
        $tasks[] = $task->getData();
    }
}
*/
(new ApiResponse())->normal( array_values($tasks) );