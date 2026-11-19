<?php

// Оформление заказа: контакты, доставка, оплата -> заказ -> оплата онлайн (или страница заказа при оплате при получении)
$items = ShopCart::getItems();
if (empty($items)) {
    UrlRedirect::go(SiteRoot('shop/cart'));
}

$deliveryMethods = ShopDelivery::methods();
$paymentMethods  = ShopDelivery::paymentMethods();
$form = [
    'name'     => (string)Post('name', IS_USER_AUTH ? $g_user->full_name : ''),
    'email'    => trim((string)Post('email', IS_USER_AUTH ? $g_user->email : '')),
    'phone'    => (string)Post('phone', IS_USER_AUTH ? $g_user->phone : ''),
    'comment'  => (string)Post('comment', ''),
    'delivery' => (string)Post('delivery_method', ShopDelivery::defaultMethod()),
    'address'  => trim((string)Post('delivery_address', '')),
    'payment'  => (string)Post('payment_method', ShopDelivery::defaultPaymentMethod()),
];
if (!preg_match('/\d/', $form['phone'])) {
    $form['phone'] = ''; // Маска ввода присылает пустой шаблон «+_(___)___-__-__»
}
if (!ShopDelivery::exists($form['delivery'])) {
    $form['delivery'] = ShopDelivery::defaultMethod();
}
if (!isset($paymentMethods[$form['payment']])) {
    $form['payment'] = ShopDelivery::defaultPaymentMethod();
}
$totals = ShopCart::totals($items, $form['delivery']);

// Промокод в корзине перестал подходить (истёк, кончился лимит, уменьшилась сумма) - говорим об этом до оформления,
// иначе заказ молча уйдёт по полной цене
$promoWarning = '';
if (ShopCart::promoCode() !== '' && $totals['promo_error'] !== '') {
    $promoWarning = MsgErr('Промокод ' . ShopCart::promoCode() . ' не применился: ' . $totals['promo_error'] . '. <a href="' . SiteRoot('shop/cart') . '">Изменить корзину</a>');
}

// Одноразовый токен формы: без него двойной submit создавал два заказа и дважды списывал остаток
if (!SessionManager::has('shop_checkout_token')) {
    SessionManager::set('shop_checkout_token', bin2hex(random_bytes(16)));
}
$formToken = (string)SessionManager::get('shop_checkout_token');

$msg = '';
if (Post('is_set')) {
    // Токен проверяем первым: если форма устарела (заказ уже создан), дальше не идём
    $isTokenValid = $formToken !== '' && hash_equals($formToken, (string)Post('checkout_token'));
    $errs = [];
    if (!$isTokenValid) {
        $errs[] = 'Форма устарела: заказ уже отправлен. Проверьте <a href="' . SiteRoot('shop/cart') . '">корзину</a> или обновите страницу';
    }
    if (trim($form['name']) === '') {
        $errs[] = 'Укажите имя';
    }
    if (!IsValidEmail(html_entity_decode($form['email']))) {
        $errs[] = 'Укажите правильный e-mail: на него придёт чек и ссылка на заказ';
    }
    if ($form['phone'] !== '' && !IsValidPhone($form['phone'])) {
        $errs[] = 'Неправильный телефон';
    }
    if (ShopDelivery::needAddress($form['delivery']) && mb_strlen($form['address']) < 5) {
        $errs[] = 'Укажите адрес доставки';
    }
    if ($form['payment'] === ShopOrderModel::PAYMENT_ON_DELIVERY && $form['phone'] === '') {
        $errs[] = 'Для оплаты при получении укажите телефон - мы позвоним, чтобы подтвердить заказ';
    }

    if ($errs) {
        $msg = MsgErr(implode('<br>', $errs));
    } else {
        // Токен съедаем до создания заказа: повторная отправка формы уже не пройдёт
        SessionManager::delete('shop_checkout_token');
        try {
            $order = ShopOrderModel::createFromCart($items, array_merge($form, [
                'phone'   => $form['phone'] !== '' ? PhoneFilter($form['phone']) : '',
                'user_id' => IS_USER_AUTH ? (int)$g_user->id : null,
            ]), [
                'delivery_method'  => $form['delivery'],
                'delivery_address' => $form['address'],
                'payment_method'   => $form['payment'],
                'promo_code'       => $totals['promo'] ? $totals['promo']->code : '',
            ]);
            ShopCart::clear();
            UrlRedirect::go($order->canPay() ? $order->url('pay_system/pay_form') : $order->url() . '&is_new=1');
        } catch (ShopCheckoutException $e) {
            // Пока покупатель оформлял, товар купили другие или закончился промокод - показываем, что осталось
            $msg   = MsgErr(htmlspecialchars($e->getMessage()) . '. Проверьте <a href="' . SiteRoot('shop/cart') . '">корзину</a>.');
            $items = ShopCart::getItems();
            if (empty($items)) {
                UrlRedirect::go(SiteRoot('shop/cart'));
            }
            $totals = ShopCart::totals($items, $form['delivery']);
        }
    }
}

// Ошибка или показ формы - выдаём новый токен, чтобы страницу можно было отправить снова
if (!SessionManager::has('shop_checkout_token')) {
    SessionManager::set('shop_checkout_token', $formToken = bin2hex(random_bytes(16)));
}

BreadCrumb::getInstance()->add(['text' => 'Корзина', 'link' => SiteRoot('shop/cart')]);
BreadCrumb::getInstance()->add(['text' => 'Оформление заказа']);
