<?php

/**
 * Платёж в локальной БД (см. PaymentModel)
 */
interface IPaymentModel
{
    public function flush();

    public function applyStatus(string $status): bool;
}
