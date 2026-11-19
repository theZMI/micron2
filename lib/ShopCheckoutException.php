<?php

// Заказ нельзя оформить: товар закончился, промокод уже использован... Сообщение показывается покупателю
class ShopCheckoutException extends RuntimeException
{
}
