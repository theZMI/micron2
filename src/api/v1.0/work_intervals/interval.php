<?php

(new ApiResponse())->normal(['id' => 1001, 'start' => time() - 100, 'stop' => 0, 'create_time' => time() - 50, 'last_update_time' => time(), 'user_id' => $g_user->id]);