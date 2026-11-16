<?php $isFav = ShopFavorites::has($product->id) ?>
<article class="product-card">
    <a href="<?= SiteRoot("shop/product&id={$product->id}") ?>" class="product-card-media">
        <?php if ($image = $product->mainImage()): ?>
            <img src="<?= $image ?>" alt="<?= $product->name ?>" loading="lazy">
        <?php else: ?>
            <span class="product-card-noimage"><i class="bi bi-image"></i></span>
        <?php endif ?>
        <?php if ($product->hasDiscount()): ?>
            <span class="product-card-badge">−<?= (int)round(100 - $product->price / $product->price_old * 100) ?>%</span>
        <?php endif ?>
    </a>
    <button type="button" class="product-card-quick" data-quick-view="<?= $product->id ?>">Быстрый просмотр</button>
    <button type="button" class="btn-favorite<?= $isFav ? ' is-active' : '' ?>" data-favorite="<?= $product->id ?>" aria-label="В избранное" title="В избранное">
        <i class="bi bi-heart"></i><i class="bi bi-heart-fill"></i>
    </button>
    <div class="product-card-body">
        <a href="<?= SiteRoot("shop/product&id={$product->id}") ?>" class="product-card-title"><?= $product->name ?></a>
        <div class="product-card-footer">
            <?php IncludeCom('shop/_price', ['product' => $product]) ?>
            <form action="<?= SiteRoot('shop/cart') ?>" method="post" data-cart-form>
                <input type="hidden" name="a" value="add">
                <input type="hidden" name="product_id" value="<?= $product->id ?>">
                <button type="submit" class="btn btn-icon" aria-label="В корзину" title="В корзину"><i class="bi bi-bag-plus"></i></button>
            </form>
        </div>
    </div>
</article>
