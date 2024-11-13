<?php

namespace Middlewares;

use \Pecee\Http\Middleware\IMiddleware;
use \Pecee\Http\Request;

class CheckApiUserAuthMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        global $g_user;

        $headers = getallheaders();
        foreach ($headers as $k => $v) {
            $headers[strtolower($k)] = $v;
        }

        $apiResponse   = new \ApiResponse();
        $login         = $headers[strtolower('API-User-Auth-Login')] ?? '';
        $password_hash = $headers[strtolower('API-User-Auth-Token')] ?? '';
        $isAuth        = (new \UserModel())->isCorrectAuth($login, $password_hash);
        $user_id       = (new \UserModel())->getIdByLogin($login);
        $g_user        = new \UserModel($user_id);

        if (!$isAuth) {
            $apiResponse->error("Access denied", 401);
        }
    }
}