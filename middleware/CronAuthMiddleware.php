<?php

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;

/**
 * Доступ к /_cron/*: либо с ключом CRON_SECRET (?key=... или заголовок X-Cron-Key), либо для авторизованного админа
 * Пример для crontab: wget -qO- "https://site.ru/_cron/db_backup?key=CRON_SECRET"
 */
class CronAuthMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        (new CommonMiddleware())->handle($request);
        Config('is_load_in_main_tpl', false);

        $secret = (string)Env('CRON_SECRET', '');
        $key    = (string)($_SERVER['HTTP_X_CRON_KEY'] ?? ($_GET['key'] ?? ''));
        if ($secret !== '' && hash_equals($secret, $key)) {
            return;
        }
        if (IS_ADMIN_AUTH && !IsCrossSiteRequest()) {
            return;
        }

        header(\Php::status(403));
        die('Forbidden');
    }
}
