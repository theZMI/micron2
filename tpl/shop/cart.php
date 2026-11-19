<div class="shop-page cart-page">
    <h1 class="page-title">Корзина</h1>
    <?php if ($isAdjusted): ?>
        <?= MsgWarning('Некоторых товаров не хватает на складе - мы уменьшили количество или убрали их из корзины') ?>
    <?php endif ?>
    <?php if (empty($items)): ?>
        <div class="empty-state">
            <i class="bi bi-bag"></i>
            <p>В корзине пока пусто</p>
            <a href="<?= SiteRoot('shop') ?>" class="btn btn-dark">В каталог</a>
        </div>
    <?php else: ?>
        <div class="cart-layout">
            <form action="<?= SiteRoot('shop/cart') ?>" method="post" id="cart-form" class="cart-items">
                <input type="hidden" name="a" value="set">
                <?php foreach ($items as $item): $product = $item['product']; $rowId = substr(md5($item['key']), 0, 10) ?>
                    <div class="cart-item">
                        <a href="<?= $product->url() ?>" class="cart-item-image">
                            <?php if ($image = $product->mainImage()): ?><img src="<?= $image ?>" alt="" loading="lazy"><?php else: ?><i class="bi bi-image"></i><?php endif ?>
                        </a>
                        <div class="cart-item-info">
                            <a href="<?= $product->url() ?>" class="cart-item-title"><?= $product->name ?></a>
                            <div class="text-secondary small"><?= $product->vendor_code ? "Арт. {$product->vendor_code} · " : '' ?><?= ShopPrice($product->price) ?> / <?= $product->unitName() ?></div>
                            <?php foreach ($item['addons'] as $addonId => $addon): ?>
                                <div class="cart-item-addon"><i class="bi bi-plus"></i><?= $addon->name ?> <span class="text-secondary">· <?= ShopPrice($item['addon_prices'][$addonId]) ?><?= $product->isPieceUnit() && $item['quantity'] > 1 ? ' × ' . ShopQuantity($item['quantity']) : '' ?></span></div>
                            <?php endforeach ?>
                        </div>
                        <div class="qty-input qty-input-sm<?= $product->isPieceUnit() ? '' : ' qty-input-unit' ?>">
                            <button type="button" data-qty="-1" aria-label="Меньше"><i class="bi bi-dash"></i></button>
                            <input type="number" name="quantity[<?= $item['key'] ?>]" value="<?= ShopQuantityValue($item['quantity']) ?>" min="0" data-min="<?= ShopQuantityValue($product->minQuantity()) ?>" max="<?= ShopQuantityValue($product->maxQuantity()) ?>" step="<?= ShopQuantityValue($product->step()) ?>" inputmode="decimal" aria-label="Количество, <?= $product->unitName() ?>" data-autosubmit>
                            <?php if (!$product->isPieceUnit()): ?><span class="qty-unit"><?= $product->unitName() ?></span><?php endif ?>
                            <button type="button" data-qty="1" aria-label="Больше"><i class="bi bi-plus"></i></button>
                        </div>
                        <div class="cart-item-sum"><?= ShopPrice($item['amount']) ?></div>
                        <button type="submit" form="cart-remove-<?= $rowId ?>" class="btn btn-icon btn-icon-muted" title="Удалить" aria-label="Удалить"><i class="bi bi-x-lg"></i></button>
                    </div>
                <?php endforeach ?>
                <noscript><button type="submit" class="btn btn-light mt-3">Пересчитать</button></noscript>
            </form>
            <aside class="cart-summary">
                <div class="cart-summary-row"><span>Товары (<?= ShopCart::itemsCount($items) ?>)</span><span><?= ShopPrice($totals['items']) ?></span></div>
                <?php if ($totals['promo']): ?>
                    <div class="cart-summary-row cart-summary-discount">
                        <span>Промокод <b><?= $totals['promo']->code ?></b>
                            <button type="submit" form="cart-promo-remove" class="btn btn-link btn-sm p-0 ms-1 align-baseline link-secondary" title="Убрать промокод" aria-label="Убрать промокод"><i class="bi bi-x-circle"></i></button>
                        </span>
                        <span>−<?= ShopPrice($totals['discount']) ?></span>
                    </div>
                <?php endif ?>
                <div class="cart-summary-row cart-summary-total"><span>Итого</span><span><?= ShopPrice($totals['total']) ?></span></div>
                <div class="text-secondary small mb-3">Без учёта доставки - её выберете при оформлении</div>
                <?php if (!$totals['promo']): ?>
                    <form action="<?= SiteRoot('shop/cart') ?>" method="post" class="cart-promo" id="promo">
                        <input type="hidden" name="a" value="promo">
                        <div class="input-group input-group-sm">
                            <input type="text" name="promo_code" class="form-control<?= $promoError || $promoWarning ? ' is-invalid' : '' ?>" placeholder="Промокод" value="<?= Get('promo_code', '') ?: ShopCart::promoCode() ?>" aria-label="Промокод" autocomplete="off" required>
                            <button type="submit" class="btn btn-outline-dark">Применить</button>
                        </div>
                        <?php if ($promoError || $promoWarning): ?><div class="small text-danger mt-1"><?= $promoError ?: $promoWarning ?></div><?php endif ?>
                    </form>
                <?php endif ?>
                <a href="<?= SiteRoot('shop/checkout') ?>" class="btn btn-dark btn-lg w-100">Оформить заказ</a>
                <a href="<?= SiteRoot('shop') ?>" class="btn btn-link link-secondary w-100 mt-2">Продолжить покупки</a>
            </aside>
        </div>
        <?php if ($suggested): ?>
            <section class="home-section">
                <div class="section-head"><h2 class="section-title">Добавьте к заказу</h2></div>
                <div class="product-grid product-grid-wide">
                    <?php foreach ($suggested as $product): ?>
                        <?php IncludeCom('shop/_product_card', ['product' => $product]) ?>
                    <?php endforeach ?>
                </div>
            </section>
        <?php endif ?>
        <form action="<?= SiteRoot('shop/cart') ?>" method="post" id="cart-promo-remove" class="d-none"><input type="hidden" name="a" value="promo_remove"></form>
        <?php foreach ($items as $item): ?>
            <form action="<?= SiteRoot('shop/cart') ?>" method="post" id="cart-remove-<?= substr(md5($item['key']), 0, 10) ?>" class="d-none">
                <input type="hidden" name="a" value="remove">
                <input type="hidden" name="key" value="<?= $item['key'] ?>">
            </form>
        <?php endforeach ?>
    <?php endif ?>
</div>
