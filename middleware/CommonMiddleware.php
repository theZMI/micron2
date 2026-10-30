<?php

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;

class CommonMiddleware implements IMiddleware
{
    private function checkCloseSite()
    {
        if (IS_ADMIN_AREA) {
            return;
        }
        if (IS_API) {
            return;
        }
        if (file_exists(BASEPATH . 'tmp/site_stopper.txt')) {
            IncludeCom('reconstruction'); // Temporary close site
        }
        return false;
    }

    private function forUser()
    {
        global $g_user;
        $g_user = new \UserModel(\UserModel::curId());
        define('IS_USER_AUTH', $g_user->isAuth());

        if (!defined('IS_USER_CABINET')) {
            define('IS_USER_CABINET', false);
        }
    }

    private function forAdmin()
    {
        global $g_admin;
        $g_admin = new \AdminModel(\AdminModel::curId());
        define('IS_ADMIN_AUTH', $g_admin->isAuth());

        if (!defined('IS_ADMIN_AREA')) {
            define('IS_ADMIN_AREA', false);
        }
    }

    private function forApi()
    {
        if (!defined('IS_API')) {
            define('IS_API', false);
        }
    }

    // Защита от CSRF: изменяющие запросы (POST, PUT...) с чужих сайтов не принимаем. API авторизуется заголовком, а не cookie, поэтому его не трогаем
    private function checkCsrf()
    {
        if (IS_API || !IsUnsafeHttpMethod() || !IsCrossSiteRequest()) {
            return;
        }
        header(\Php::status(403));
        die('Forbidden: cross-site request');
    }

    public function handle(Request $request): void
    {
        if (defined('IS_COMMON_MIDDLEWARE_DONE')) {
            return;
        }
        define('IS_COMMON_MIDDLEWARE_DONE', true);

        $this->forApi();
        $this->checkCsrf();
        $this->forAdmin();
        $this->forUser();
        $this->checkCloseSite();
    }
}