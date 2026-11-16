<div class="shop-page cart-page">
    <h1 class="page-title">Корзина</h1>
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
                <?php foreach ($items as $item): $product = $item['product'] ?>
                    <div class="cart-item">
                        <a href="<?= SiteRoot("shop/product&id={$product->id}") ?>" class="cart-item-image">
                            <?php if ($image = $product->mainImage()): ?><img src="<?= $image ?>" alt="" loading="lazy"><?php else: ?><i class="bi bi-image"></i><?php endif ?>
                        </a>
                        <div class="cart-item-info">
                            <a href="<?= SiteRoot("shop/product&id={$product->id}") ?>" class="cart-item-title"><?= $product->name ?></a>
                            <div class="text-secondary small"><?= $product->vendor_code ? "Арт. {$product->vendor_code} · " : '' ?><?= ShopPrice($product->price) ?> / шт.</div>
                        </div>
                        <div class="qty-input qty-input-sm">
                            <button type="button" data-qty="-1" aria-label="Меньше"><i class="bi bi-dash"></i></button>
                            <input type="number" name="quantity[<?= $product->id ?>]" value="<?= $item['quantity'] ?>" min="0" max="<?= Config('shop')['max_quantity'] ?>" aria-label="Количество" data-autosubmit>
                            <button type="button" data-qty="1" aria-label="Больше"><i class="bi bi-plus"></i></button>
                        </div>
                        <div class="cart-item-sum"><?= ShopPrice($item['amount']) ?></div>
                        <button type="submit" form="cart-remove-<?= $product->id ?>" class="btn btn-icon btn-icon-muted" title="Удалить" aria-label="Удалить"><i class="bi bi-x-lg"></i></button>
                    </div>
                <?php endforeach ?>
                <noscript><button type="submit" class="btn btn-light mt-3">Пересчитать</button></noscript>
            </form>
            <aside class="cart-summary">
                <div class="cart-summary-row"><span>Товаров</span><span><?= array_sum(array_column($items, 'quantity')) ?></span></div>
                <div class="cart-summary-row cart-summary-total"><span>Итого</span><span><?= ShopPrice($total) ?></span></div>
                <a href="<?= SiteRoot('shop/checkout') ?>" class="btn btn-dark btn-lg w-100">Оформить заказ</a>
                <a href="<?= SiteRoot('shop') ?>" class="btn btn-link link-secondary w-100 mt-2">Продолжить покупки</a>
            </aside>
        </div>
        <?php foreach ($items as $item): ?>
            <form action="<?= SiteRoot('shop/cart') ?>" method="post" id="cart-remove-<?= $item['product']->id ?>" class="d-none">
                <input type="hidden" name="a" value="remove">
                <input type="hidden" name="product_id" value="<?= $item['product']->id ?>">
            </form>
        <?php endforeach ?>
    <?php endif ?>
</div>
