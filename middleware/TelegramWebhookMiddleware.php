<?php

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;

/**
 * Принимаем webhook только от Telegram: он присылает заголовок X-Telegram-Bot-Api-Secret-Token,
 * равный secret_token, указанному при вызове setWebhook (у нас это TELEGRAM_WEBHOOK_SECRET из .env)
 */
class TelegramWebhookMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        define('IS_API', true);
        Config('is_load_in_main_tpl', false);

        $secret = (string)Env('TELEGRAM_WEBHOOK_SECRET', '');
        $token  = (string)($_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] ?? '');
        if ($secret === '' || !hash_equals($secret, $token)) {
            header(\Php::status(403));
            die('Forbidden');
        }
    }
}
