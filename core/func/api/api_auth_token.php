<?php

// Токен, присланный API-клиентом в заголовке API-User-Auth-Token
function ApiAuthToken(): string
{
    $headers = array_change_key_case(function_exists('getallheaders') ? getallheaders() : [], CASE_LOWER);
    return trim((string)($headers['api-user-auth-token'] ?? ''));
}
