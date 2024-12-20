<?php

$shifts  = (new ShiftModel())->find(['user_id' => +$g_user->id, 'is_template' => false]);
$all     = array_reduce(
    $shifts,
    fn($shiftParams, $shift) => array_merge($shiftParams, $shift->params),
    []
);
$all     = array_map(fn($shiftParam) => $shiftParam->getDataToApi(), $all);
(new ApiResponse())->normal(
    array_values($all)
);