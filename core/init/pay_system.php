<?php

// Какую платёжную систему используем (pay_system_yookassa или pay_system_sber, см. core/config/pay_systems)
Config('pay_system', Config('pay_system_yookassa'));

// Обработчики смены статуса платежа (в них приходит PaymentModel). По умолчанию - заказы магазина (ShopOrderModel)
Config(['pay_system', 'on_paid'], [ShopOrderModel::class, 'onPaymentPaid']);
Config(['pay_system', 'on_failed'], [ShopOrderModel::class, 'onPaymentFailed']);
