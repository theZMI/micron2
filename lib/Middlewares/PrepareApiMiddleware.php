<?php

namespace Middlewares;

use \Pecee\Http\Middleware\IMiddleware;
use \Pecee\Http\Request;

class PrepareApiMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        define('IS_API', true);

        FillArrayFromPhpInput($_POST);
    }
}