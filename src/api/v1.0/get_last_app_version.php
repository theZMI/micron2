<?php

// Последняя версия приложения, чтобы само приложение могло проверить обновления: api/v1.0/get_last_app_version (?app=courier)
$version = GetLastAppVersion((string)Get('app'));
is_null($version)
    ? (new ApiResponse())->error('App not found', 404)
    : (new ApiResponse())->normal($version);
