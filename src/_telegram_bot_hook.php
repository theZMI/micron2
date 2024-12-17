<?php

$output      = json_decode(file_get_contents('php://input'), true);
$chat_id     = $output['message']['chat']['id'] ?? '';
$first_name  = $output['message']['chat']['first_name'] ?? '';
$username    = $output['message']['chat']['username'] ?? '';
$message     = $output['message']['text'] ?? '';
$userModel   = (new UserModel())->findOne(['telegram_login' => $username]);
$isUserFound = $userModel->isExists();
$notificator = new TelegramNotificator();

switch ($message) {
    case '/start':
        if ($isUserFound) {
            $userModel->telegram_chat_id         = $chat_id;
            $userModel->is_telegram_verified     = true;
            $userModel->notification_by_telegram = true;

            $notificator->send($userModel->telegram_chat_id, 'Уведомления подключены :-)');
        } else {
            $notificator->send(
                $chat_id,
                "Не удалось найти пользователя с вашим username-телеграма.\n
                Пожалуйста, сначала впишите ваш username-телеграма на странице настроек сайта, а потом возвращайтесь сюда и напишите сообщение: /start"
            );
        }
        break;
}
die;