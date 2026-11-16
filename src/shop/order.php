<?php

// Страница заказа: shop/order&id=ID&key=KEY (владелец и админ открывают и без ключа)
$order = ShopOrderModel::findForCurrentVisitor();
if (!$order) {
    IncludeCom('404');
    ExitCom();
    return;
}
BreadCrumb::getInstance()->add(['text' => "Заказ №{$order->id}"]);
