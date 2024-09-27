<?php

namespace Middlewares;

use \Pecee\Http\Middleware\IMiddleware;
use \Pecee\Http\Request;

class AdminAuthMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        define('IS_ADMIN_AREA', true);
        Config(['extraPacker', 'dir'], '_admin_auto_merge_css_js');
        Config('mainTpl', '_admin/_main_tpl');
        Config(['extraPacker', 'statFilePath'], BASEPATH . 'tmp/_admin_auto_merge_css_js/time_stat.json');

        (new CommonMiddleware())->handle($request);

        if (IS_ADMIN_AUTH) {
            return;
        }
        if (in_array(GetQuery(), ['_admin/login'])) {
            return;
        }

        \UrlRedirect::go(SiteRoot('_admin/login'));
    }
}