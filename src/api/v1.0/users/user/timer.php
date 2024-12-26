<?php

$g_user->active_timer_value = intval( Post('value') );

(new ApiResponse())->normal('OK');