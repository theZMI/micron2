<div class="shop-page">
    <h1 class="page-title">Оформление заказа</h1>
    <div class="cart-layout">
        <form action="<?= GetCurUrl() ?>" method="post" class="checkout-form">
            <input type="hidden" name="is_set" value="1">
            <?php if (!IS_USER_AUTH): ?>
                <div class="checkout-auth">
                    <i class="bi bi-person-circle"></i>
                    <div>Уже покупали у нас? <a href="<?= SiteRoot('user/login&back_url=' . urlencode(SiteRoot('shop/checkout'))) ?>">Войдите</a> — заказы будут в личном кабинете. Или оформите без регистрации.</div>
                </div>
            <?php endif ?>
            <?= $msg ?>
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
                    <label class="form-label">Комментарий к заказу</label>
                    <textarea name="comment" class="form-control" rows="3"><?= $form['comment'] ?></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-dark btn-lg w-100">Перейти к оплате</button>
                </div>
            </div>
        </form>
        <aside class="cart-summary">
            <div class="fw-semibold mb-3">Ваш заказ</div>
            <?php foreach ($items as $item): ?>
                <div class="cart-summary-row small">
                    <span class="text-truncate pe-3"><?= $item['product']->name ?> × <?= $item['quantity'] ?></span>
                    <span class="text-nowrap"><?= ShopPrice($item['amount']) ?></span>
                </div>
            <?php endforeach ?>
            <div class="cart-summary-row cart-summary-total"><span>Итого</span><span><?= ShopPrice($total) ?></span></div>
            <a href="<?= SiteRoot('shop/cart') ?>" class="btn btn-link link-secondary w-100">Изменить корзину</a>
        </aside>
    </div>
</div>
