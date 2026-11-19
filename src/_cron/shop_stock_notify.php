<?php

// «Сообщить о поступлении»: письма по товарам, которые снова в наличии (на случай, если остаток поменяли не через админку и не импортом):
// wget -qO- "https://site.ru/_cron/shop_stock_notify?key=CRON_SECRET"
echo 'Sent: ' . ShopStockSubscriptionModel::notifyAvailable() . '<br>';
die('END');
