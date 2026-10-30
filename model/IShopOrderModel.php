<?php

/**
 * Заказ, который оплачивается через PaySystem. Модель заказа (например ShopOrderModel) делается в проекте и должна давать поля:
 *  - id, total_amount (сумма заказа), currency (RUB, USD...)
 *  - items - список позиций заказа, у каждой: name, amount, vendor_code
 */
interface IShopOrderModel
{
}
