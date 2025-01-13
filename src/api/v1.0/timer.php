<?php

$newTimerValue = intval(Post('value'));
if ($newTimerValue) {
    $g_user->active_timer_value = $newTimerValue;
}
(new ApiResponse())->normal($g_user->active_timer_value);