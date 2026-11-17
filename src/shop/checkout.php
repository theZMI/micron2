<?php

// Оформление заказа: контакты покупателя -> заказ -> оплата
$items = ShopCart::getItems();
if (empty($items)) {
    UrlRedirect::go(SiteRoot('shop/cart'));
}
$total = ShopCart::total($items);

$form = [
    'name'    => (string)Post('name', IS_USER_AUTH ? $g_user->full_name : ''),
    'email'   => trim((string)Post('email', IS_USER_AUTH ? $g_user->email : '')),
    'phone'   => (string)Post('phone', IS_USER_AUTH ? $g_user->phone : ''),
    'comment' => (string)Post('comment', ''),
];

$msg = '';
if (Post('is_set')) {
    $errs = [];
    if (trim($form['name']) === '') {
        $errs[] = 'Укажите имя';
    }
    if (!IsValidEmail(html_entity_decode($form['email']))) {
        $errs[] = 'Укажите правильный e-mail: на него придёт чек и ссылка на заказ';
    }
    if ($form['phone'] !== '' && !IsValidPhone($form['phone'])) {
        $errs[] = 'Неправильный телефон';
    }

    if ($errs) {
        $msg = MsgErr(implode('<br>', $errs));
    } else {
        try {
            $order = ShopOrderModel::createFromCart($items, array_merge($form, [
                'phone'   => $form['phone'] !== '' ? PhoneFilter($form['phone']) : '',
                'user_id' => IS_USER_AUTH ? (int)$g_user->id : null,
            ]));
            ShopCart::clear();
            UrlRedirect::go($order->url('pay_system/pay_form'));
        } catch (ShopOutOfStockException $e) {
            // Пока покупатель оформлял, товар купили другие - показываем, что осталось
            $msg   = MsgErr(htmlspecialchars($e->getMessage()) . '. Проверьте <a href="' . SiteRoot('shop/cart') . '">корзину</a>.');
            $items = ShopCart::getItems();
            $total = ShopCart::total($items);
            if (empty($items)) {
                UrlRedirect::go(SiteRoot('shop/cart'));
            }
        }
    }
}

BreadCrumb::getInstance()->add(['text' => 'Корзина', 'link' => SiteRoot('shop/cart')]);
BreadCrumb::getInstance()->add(['text' => 'Оформление заказа']);
