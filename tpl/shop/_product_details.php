<?php $images = $product->getImages(); $isFav = ShopFavorites::has($product->id); $uid = 'g' . $product->id . '-' . substr(md5(uniqid()), 0, 6) ?>
<div class="product-details">
    <div class="product-gallery" data-gallery>
        <div class="product-gallery-main">
            <?php if ($images): ?>
                <img src="<?= $images[0]['url'] ?>" alt="<?= $product->name ?>" data-gallery-main>
            <?php else: ?>
                <span class="product-card-noimage"><i class="bi bi-image"></i></span>
            <?php endif ?>
        </div>
        <?php if (count($images) > 1): ?>
            <div class="product-gallery-thumbs">
                <?php foreach ($images as $i => $image): ?>
                    <button type="button" class="<?= $i === 0 ? 'is-active' : '' ?>" data-gallery-thumb="<?= $image['url'] ?>" aria-label="Фото <?= $i + 1 ?>">
                        <img src="<?= $image['thumb'] ?>" alt="" loading="lazy">
                    </button>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </div>
    <div class="product-info">
        <?php if ($product->vendor_code): ?>
            <div class="product-sku">Артикул <?= $product->vendor_code ?></div>
        <?php endif ?>
        <h1 class="product-title"><?= $product->name ?></h1>
        <div class="product-price"><?php IncludeCom('shop/_price', ['product' => $product]) ?></div>
        <?php if ($availability = $product->availability()): ?>
            <div class="stock-label stock-<?= $availability['css'] ?>"><?= $availability['text'] ?></div>
        <?php endif ?>

        <?php foreach ($product->variantOptions() as $paramName => $variantParam): ?>
            <div class="variant-group">
                <div class="variant-title"><?= $paramName ?><?php foreach ($variantParam['options'] as $option) { if ($option['is_current']) { echo ': <span>' . $option['value'] . '</span>'; break; } } ?></div>
                <div class="variant-options<?= $variantParam['is_image'] ? ' variant-options-images' : '' ?>">
                    <?php foreach ($variantParam['options'] as $option): $variant = $option['product'] ?>
                        <a href="<?= SiteRoot("shop/product&id={$variant->id}") ?>"
                           class="variant-option<?= $option['is_current'] ? ' is-current' : '' ?><?= $variant->isAvailable() ? '' : ' is-unavailable' ?>"
                           title="<?= $option['value'] ?><?= $variant->isAvailable() ? '' : ' — нет в наличии' ?>"
                           <?= empty($full) ? 'data-quick-view="' . $variant->id . '"' : '' ?>>
                            <?php if ($variantParam['is_image'] && ($thumb = $variant->mainImage())): ?>
                                <img src="<?= $thumb ?>" alt="<?= $option['value'] ?>" loading="lazy">
                            <?php else: ?>
                                <?= $option['value'] ?>
                            <?php endif ?>
                        </a>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endforeach ?>

        <form action="<?= SiteRoot('shop/cart') ?>" method="post" class="product-buy" data-cart-form>
            <input type="hidden" name="a" value="add">
            <input type="hidden" name="product_id" value="<?= $product->id ?>">
            <?php if ($product->isAvailable()): ?>
                <div class="qty-input">
                    <button type="button" data-qty="-1" aria-label="Меньше"><i class="bi bi-dash"></i></button>
                    <input type="number" name="quantity" value="1" min="1" max="<?= $product->maxQuantity() ?>" aria-label="Количество">
                    <button type="button" data-qty="1" aria-label="Больше"><i class="bi bi-plus"></i></button>
                </div>
                <button type="submit" class="btn btn-dark btn-lg flex-grow-1">В корзину</button>
            <?php else: ?>
                <button type="button" class="btn btn-light btn-lg flex-grow-1" disabled>Нет в наличии</button>
            <?php endif ?>
            <button type="button" class="btn btn-outline-dark btn-lg btn-favorite-lg<?= $isFav ? ' is-active' : '' ?>" data-favorite="<?= $product->id ?>" aria-label="В избранное" title="В избранное">
                <i class="bi bi-heart"></i><i class="bi bi-heart-fill"></i>
            </button>
        </form>

        <?php if ($params = $product->getParams()): ?>
            <dl class="product-params">
                <?php foreach ($params as $name => $value): ?>
                    <div><dt><?= $name ?></dt><dd><?= $value ?></dd></div>
                <?php endforeach ?>
            </dl>
        <?php endif ?>
        <?php if (!empty($full) && $product->description): ?>
            <div class="product-description"><?= nl2br($product->description) ?></div>
        <?php elseif ($product->description): ?>
            <p class="product-description-short"><?= mb_strimwidth(strip_tags(html_entity_decode($product->description)), 0, 220, '…') ?></p>
            <a href="<?= SiteRoot("shop/product&id={$product->id}") ?>" class="link-arrow">Подробнее о товаре <i class="bi bi-arrow-right"></i></a>
        <?php endif ?>
    </div>
</div>
