<?php

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;

class PrepareManagerMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        define('IS_MANAGER_CABINET', true);
    }
}