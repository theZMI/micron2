<?php

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;

class CheckApiUserAuthMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        global $g_user;

        $user_id = \UserModel::idByApiToken(ApiAuthToken());
        if (!$user_id) {
            (new \ApiResponse())->error("Access denied", 401);
        }
        $g_user = new \UserModel($user_id);
    }
}
