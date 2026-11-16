<?php

/**
 * Уведомления банка о смене статуса платежа (webhook). Адрес для настроек эквайринга: https://site.ru/pay_system/notify
 *  - ЮKassa: личный кабинет -> Интеграция -> HTTP-уведомления, события payment.succeeded и payment.canceled
 *  - Сбер: callback-уведомления (адрес передаётся менеджеру банка)
 * Телу уведомления не верим: берём из него только ID платежа и спрашиваем статус у банка сами
 */
Config('is_load_in_main_tpl', false);

$transfer_id = PaySystem::getInstance()->getTransferIdFromNotification($_GET, (string)file_get_contents('php://input'));
$payment     = PaymentModel::findByTransferId((string)$transfer_id);
if ($payment->isExists()) {
    try {
        $payment->checkStatusInBank();
    } catch (Throwable $e) {
        ToLog("Payment #{$payment->id}: notify error: " . $e->getMessage());
        header(Php::status(500)); // Банк повторит уведомление позже
        die('ERROR');
    }
}
die('OK'); // Неизвестные платежи тоже подтверждаем, иначе банк будет слать их повторно
