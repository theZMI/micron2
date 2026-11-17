<?php

// Товара не хватает на складе (при оформлении заказа). Сообщение можно показывать покупателю
class ShopOutOfStockException extends RuntimeException
{
}
