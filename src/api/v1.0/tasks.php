<?php

$model  = new ShiftModel();
$shifts = $model->find(['user_id' => +$g_user->id, 'is_template' => false]);
$tasks  = array_reduce(
    $shifts,
    fn($tasks, $shift) => array_merge($tasks, array_map(fn($task) => $task->getData(), $shift->tasks)),
    []
);
(new ApiResponse())->normal(
    array_values($tasks)
);