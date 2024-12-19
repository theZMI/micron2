<?php

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;

class PrepareUserMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        define('IS_USER_CABINET', true);
    }
}