<?php

$service         = PaySystem::getInstance();
$waitingPayments = (new PaymentModel())->getWaitingList();
foreach ($waitingPayments as $paymentModel) {
    $curStatus = $paymentModel->status;
    $newStatus = $service->getPaymentStatus($paymentModel->transfer_id);

    if ($curStatus !== $newStatus) {
        echo "Payment #{$paymentModel->id}: {$curStatus} -> {$newStatus}<br>";
        $paymentModel->status = $newStatus; // Если статус изменится, то автоматически запустятся ф-ии onPaid/onFailed
    }
}

die("END");