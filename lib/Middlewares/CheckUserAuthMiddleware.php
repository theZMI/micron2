<?php

namespace Middlewares;

use \Pecee\Http\Middleware\IMiddleware;
use \Pecee\Http\Request;

class CheckUserAuthMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        if (IS_USER_AUTH) {
            return;
        }
        if (in_array(GetQuery(), Config(['user_model', 'without_auth']))) {
            return;
        }

        \UrlRedirect::go(SiteRoot('user/login'));
    }
}