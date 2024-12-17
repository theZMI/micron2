<?php

namespace Middlewares;

use \Pecee\Http\Middleware\IMiddleware;
use \Pecee\Http\Request;

class CheckManagerAuthMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        if (IS_MANAGER_CABINET) {
            return;
        }
        if (in_array(GetQuery(), Config(['manager_model', 'without_auth']))) {
            return;
        }

        \UrlRedirect::go(SiteRoot('manager/login'));
    }
}