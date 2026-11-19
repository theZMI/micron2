<?php $isFav = ShopFavorites::has($product->id); $isCompare = ShopCompare::has($product->id); $variantsCount = $product->variant_group !== '' ? (ShopProductModel::variantCounts()[$product->variant_group] ?? 0) : 0 ?>
<article class="product-card<?= $product->isAvailable() ? '' : ' is-unavailable' ?>">
    <a href="<?= $product->url() ?>" class="product-card-media">
        <?php if ($image = $product->mainImage()): ?>
            <img src="<?= $image ?>" alt="<?= $product->name ?>" loading="lazy">
        <?php else: ?>
            <span class="product-card-noimage"><i class="bi bi-image"></i></span>
        <?php endif ?>
        <?php if (!$product->isAvailable()): ?>
            <span class="product-card-badge product-card-badge-muted">Нет в наличии</span>
        <?php elseif ($product->hasDiscount()): ?>
            <span class="product-card-badge">−<?= (int)round(100 - $product->price * 100 / $product->price_old) ?>%</span>
        <?php endif ?>
    </a>
    <button type="button" class="product-card-quick" data-quick-view="<?= $product->id ?>">Быстрый просмотр</button>
    <button type="button" class="btn-favorite<?= $isFav ? ' is-active' : '' ?>" data-favorite="<?= $product->id ?>" aria-label="В избранное" title="В избранное">
        <i class="bi bi-heart"></i><i class="bi bi-heart-fill"></i>
    </button>
    <button type="button" class="btn-compare<?= $isCompare ? ' is-active' : '' ?>" data-compare="<?= $product->id ?>" aria-label="Сравнить" title="Сравнить">
        <i class="bi bi-bar-chart"></i><i class="bi bi-bar-chart-fill"></i>
    </button>
    <div class="product-card-body">
        <a href="<?= $product->url() ?>" class="product-card-title"><?= $product->name ?></a>
        <?php if ($product->reviews_count > 0): ?>
            <div class="product-card-rating"><i class="bi bi-star-fill"></i> <?= number_format($product->rating, 1, ',', '') ?> <span>· <?= $product->reviews_count ?> <?= Plural($product->reviews_count, 'отзыв', 'отзыва', 'отзывов') ?></span></div>
        <?php endif ?>
        <?php if ($variantsCount > 1): ?>
            <div class="product-card-variants"><?= $variantsCount ?> <?= Plural($variantsCount, 'вариант', 'варианта', 'вариантов') ?></div>
        <?php endif ?>
        <div class="product-card-footer">
            <?php IncludeCom('shop/_price', ['product' => $product]) ?>
            <?php if ($product->isAvailable()): ?>
                <form action="<?= SiteRoot('shop/cart') ?>" method="post" data-cart-form>
                    <input type="hidden" name="a" value="add">
                    <input type="hidden" name="product_id" value="<?= $product->id ?>">
                    <button type="submit" class="btn btn-icon" aria-label="В корзину" title="В корзину"><i class="bi bi-bag-plus"></i></button>
                </form>
            <?php endif ?>
        </div>
    </div>
</article>
