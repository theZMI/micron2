<?php

// Отмена брошенных неоплаченных заказов (товар возвращается на склад): wget -qO- "https://site.ru/_cron/shop_cancel_unpaid?key=CRON_SECRET"
$canceled = ShopOrderModel::cancelUnpaid((int)Config('shop')['unpaid_order_ttl_hours']);
echo 'Canceled: ' . ($canceled ? implode(', ', $canceled) : 'none') . '<br>';
die('END');
