<div class="shop-page">
    <div class="shop-head">
        <h1 class="page-title">Избранное</h1>
        <?php if ($products): ?><span class="text-secondary"><?= count($products) ?> <?= Plural(count($products), 'товар', 'товара', 'товаров') ?></span><?php endif ?>
    </div>
    <?php if (empty($products)): ?>
        <div class="empty-state">
            <i class="bi bi-heart"></i>
            <p>Здесь будут товары, которые вы отметите сердечком</p>
            <a href="<?= SiteRoot('shop') ?>" class="btn btn-dark">В каталог</a>
        </div>
    <?php else: ?>
        <?php if (!IS_USER_AUTH): ?>
            <p class="text-secondary small"><a href="<?= SiteRoot('user/login&back_url=' . urlencode(SiteRoot('shop/favorites'))) ?>" class="link-dark">Войдите</a>, чтобы избранное сохранилось на всех устройствах</p>
        <?php endif ?>
        <div class="product-grid product-grid-wide">
            <?php foreach ($products as $product): ?>
                <?php IncludeCom('shop/_product_card', ['product' => $product]) ?>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</div>
