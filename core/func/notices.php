<?php

/**
 * Отправляет пользователю уведомление по нескольким каналам
 *
 * @param CommonUserModel $userTo - кому
 * @param string $message - текст
 * @param array $channels - каналы: telegram, email, sms. Канал срабатывает, только если у пользователя он подтверждён и включён
 * @param callable|null $prevFunc - вызывается перед отправкой в канал: fn($channel, $userTo)
 * @param callable|null $postFunc - вызывается после отправки в канал: fn($channel, $userTo)
 *
 * @return int - в сколько каналов удалось отправить
 */
function SendNotice(CommonUserModel $userTo, string $message, array $channels, $prevFunc = null, $postFunc = null): int
{
    $callbacks = [
        'telegram' => [
            'callback' => fn($userTo) => (new TelegramNotificator())->send($userTo, $message),
            'is_on'    => fn($userTo) => $userTo->notification_by_telegram && $userTo->is_telegram_verified,
        ],
        'email'    => [
            'callback' => fn($userTo) => (new EmailNotificator())->send($userTo, $message),
            'is_on'    => fn($userTo) => $userTo->notification_by_email && $userTo->is_email_verified,
        ],
        'sms'      => [
            'callback' => fn($userTo) => (new SmsNotificator())->send($userTo, $message),
            'is_on'    => fn($userTo) => $userTo->notification_by_phone && $userTo->is_phone_verified,
        ],
    ];

    $ret = 0;
    foreach ($channels as $channel) {
        if (!isset($callbacks[$channel]) || !$callbacks[$channel]['is_on']($userTo)) {
            continue;
        }
        $prevFunc ? $prevFunc($channel, $userTo) : null;
        try {
            $ret += $callbacks[$channel]['callback']($userTo) ? 1 : 0;
        } catch (\Throwable $e) {
            ToLog($e->getMessage());
        } finally {
            $postFunc ? $postFunc($channel, $userTo) : null;
        }
    }
    return $ret;
}
