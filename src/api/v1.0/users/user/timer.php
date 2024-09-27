<?php

FillArrayFromPhpInput($_POST);

$shifts         = (new ShiftModel())->getListByUserID(+$g_user->id);
$shiftsOkByTime = [];
foreach ($shifts as $shift) {
    if (+$shift->status !== ShiftModel::STATUS_DONE
        &&
        $shift->start_time <= time() && time() <= $shift->end_time) {
        $shiftsOkByTime[$shift->id] = $shift;
    }
}

foreach ($shiftsOkByTime as $shift) {
    $shift->work_time       = intval(Post('value'));
    $shift->_work_intervals = json_encode(
        Post('workIntervals')
    );
}

$model                     = new UserModel(+$g_user->id);
$model->active_timer_value = intval(Post('value'));

(new ApiResponse())->normal('OK');