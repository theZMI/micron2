<?php

// Проверка ожидающих платежей (на случай, если уведомление банка не дошло). Статус paid/failed сам запустит обработчики on_paid/on_failed
foreach ((new PaymentModel())->getWaitingList() as $payment) {
    $oldStatus = $payment->status;
    try {
        $newStatus = $payment->checkStatusInBank();
    } catch (Throwable $e) {
        echo "Payment #{$payment->id}: error " . htmlspecialchars($e->getMessage()) . "<br>";
        continue;
    }
    if ($oldStatus !== $newStatus) {
        echo "Payment #{$payment->id}: {$oldStatus} -> {$newStatus}<br>";
    }
}

die("END");
