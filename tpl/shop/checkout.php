<div class="shop-page">
    <h1 class="page-title">Оформление заказа</h1>
    <div class="cart-layout">
        <form action="<?= GetCurUrl() ?>" method="post" class="checkout-form" data-checkout data-currency-sign="<?= Config('shop')['currency_sign'] ?>">
            <input type="hidden" name="is_set" value="1">
            <input type="hidden" name="checkout_token" value="<?= $formToken ?>">
            <?php if (!IS_USER_AUTH): ?>
                <div class="checkout-auth">
                    <i class="bi bi-person-circle"></i>
                    <div>Уже покупали у нас? <a href="<?= SiteRoot('user/login&back_url=' . urlencode(SiteRoot('shop/checkout'))) ?>">Войдите</a> — заказы будут в личном кабинете. Или оформите без регистрации.</div>
                </div>
            <?php endif ?>
            <?= $msg ?>
            <?= $promoWarning ?>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Имя *</label>
                    <input type="text" name="name" class="form-control" value="<?= $form['name'] ?>" required autocomplete="name">
                </div>
                <div class="col-sm-6">
                    <label class="form-label">E-mail *</label>
                    <input type="email" name="email" class="form-control" value="<?= $form['email'] ?>" required autocomplete="email">
                    <div class="form-text">Пришлём чек и ссылку на заказ</div>
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Телефон</label>
                    <input type="tel" name="phone" class="form-control phone-mask" value="<?= $form['phone'] ?>" autocomplete="tel">
                </div>
                <div class="col-12">
                    <div class="form-label fw-semibold mt-2">Доставка</div>
                    <div class="checkout-options">
                        <?php foreach ($deliveryMethods as $key => $method): $afterDiscount = $totals['items'] - $totals['discount'] ?>
                            <label class="checkout-option">
                                <input type="radio" name="delivery_method" value="<?= $key ?>" class="form-check-input" <?= $key === $form['delivery'] ? 'checked' : '' ?>
                                       data-price="<?= ShopDelivery::price($key, $afterDiscount) ?>" data-price-text="<?= ShopDelivery::price($key, $afterDiscount) > 0 ? strip_tags(ShopPrice(ShopDelivery::price($key, $afterDiscount))) : 'Бесплатно' ?>" data-need-address="<?= ShopDelivery::needAddress($key) ? 1 : 0 ?>">
                                <span class="checkout-option-body">
                                    <span class="checkout-option-title"><?= $method['name'] ?><span class="checkout-option-price"><?= ShopDelivery::priceText($key, $afterDiscount) ?></span></span>
                                    <?php if (!empty($method['description'])): ?><span class="checkout-option-text"><?= $method['description'] ?></span><?php endif ?>
                                </span>
                            </label>
                        <?php endforeach ?>
                    </div>
                </div>
                <div class="col-12" data-address-block<?= ShopDelivery::needAddress($form['delivery']) ? '' : ' hidden' ?>>
                    <label class="form-label" for="checkout-address">Адрес доставки *</label>
                    <textarea name="delivery_address" id="checkout-address" class="form-control" rows="2" autocomplete="street-address" placeholder="Город, улица, дом, квартира, индекс"><?= $form['address'] ?></textarea>
                </div>
                <?php if (count($paymentMethods) > 1): ?>
                    <div class="col-12">
                        <div class="form-label fw-semibold mt-2">Оплата</div>
                        <div class="checkout-options checkout-options-inline">
                            <?php foreach ($paymentMethods as $key => $name): ?>
                                <label class="checkout-option">
                                    <input type="radio" name="payment_method" value="<?= $key ?>" class="form-check-input" <?= $key === $form['payment'] ? 'checked' : '' ?> data-online="<?= $key === ShopOrderModel::PAYMENT_ONLINE ? 1 : 0 ?>">
                                    <span class="checkout-option-body"><span class="checkout-option-title"><?= $name ?></span></span>
                                </label>
                            <?php endforeach ?>
                        </div>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="payment_method" value="<?= array_key_first($paymentMethods) ?>">
                <?php endif ?>
                <div class="col-12">
                    <label class="form-label">Комментарий к заказу</label>
                    <textarea name="comment" class="form-control" rows="3"><?= $form['comment'] ?></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-dark btn-lg w-100" data-submit-online="Перейти к оплате" data-submit-offline="Оформить заказ"><?= $form['payment'] === ShopOrderModel::PAYMENT_ONLINE ? 'Перейти к оплате' : 'Оформить заказ' ?></button>
                </div>
            </div>
        </form>
        <aside class="cart-summary">
            <div class="fw-semibold mb-3">Ваш заказ</div>
            <?php foreach ($items as $item): ?>
                <div class="cart-summary-row small">
                    <span class="text-truncate pe-3"><?= $item['product']->name ?> × <?= ShopQuantity($item['quantity'], $item['product']->isPieceUnit() ? '' : $item['product']->unitKey()) ?><?php if ($item['addons']): ?><br><small class="text-secondary">+ <?= implode(', ', array_map(fn($a) => $a->name, $item['addons'])) ?></small><?php endif ?></span>
                    <span class="text-nowrap"><?= ShopPrice($item['amount']) ?></span>
                </div>
            <?php endforeach ?>
            <?php if ($totals['promo']): ?>
                <div class="cart-summary-row cart-summary-discount"><span>Промокод <b><?= $totals['promo']->code ?></b></span><span>−<?= ShopPrice($totals['discount']) ?></span></div>
            <?php endif ?>
            <div class="cart-summary-row"><span>Доставка</span><span data-delivery-price><?= $totals['delivery'] > 0 ? ShopPrice($totals['delivery']) : 'Бесплатно' ?></span></div>
            <div class="cart-summary-row cart-summary-total"><span>Итого</span><span data-total data-total-without-delivery="<?= $totals['total'] - $totals['delivery'] ?>"><?= ShopPrice($totals['total']) ?></span></div>
            <a href="<?= SiteRoot('shop/cart') ?>" class="btn btn-link link-secondary w-100">Изменить корзину</a>
        </aside>
    </div>
</div>
