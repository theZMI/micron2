<?php

$shifts  = (new ShiftModel())->find(['user_id' => +$g_user->id, 'is_template' => false]);
$all     = array_reduce(
    $shifts,
    fn($tasks, $shift) => array_merge($tasks, $shift->tasks),
    []
);
$all     = array_map(fn($task) => $task->getDataToApi(), $all);
(new ApiResponse())->normal(
    array_values($all)
);