<?php

namespace Middlewares;

use \Pecee\Http\Middleware\IMiddleware;
use \Pecee\Http\Request;

class UserAuthMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        define('IS_USER_CABINET', true);

        (new CommonMiddleware())->handle($request);

        if (IS_USER_AUTH) {
            return;
        }
        if (in_array(GetQuery(), Config(['user_model', 'without_auth']))) {
            return;
        }

        \UrlRedirect::go(SiteRoot('user/login'));
    }
}