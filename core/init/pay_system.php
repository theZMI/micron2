<?php

// Какую платёжную систему используем (pay_system_yookassa или pay_system_sber, см. core/config/pay_systems)
Config('pay_system', Config('pay_system_yookassa'));

// Обработчики смены статуса платежа (в них приходит PaymentModel), например:
// Config(['pay_system', 'on_paid'], fn(PaymentModel $payment) => (new ShopOrderModel($payment->shop_order_id))->markPaid());
