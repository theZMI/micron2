<?php

Config('push_notificator', [
    'api_id'       => Env('PUSH_ONESIGNAL_APP_ID'),
    'rest_api_key' => Env('PUSH_ONESIGNAL_REST_API_KEY'),
]);
