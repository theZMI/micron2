<?php $images = $product->getImages(); $isFav = ShopFavorites::has($product->id); $isCompare = ShopCompare::has($product->id); $step = $product->step(); $uid = 'g' . $product->id . '-' . substr(md5(uniqid()), 0, 6) ?>
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
        <?php if ($product->reviews_count > 0): ?>
            <a href="<?= $product->url() ?>#reviews" class="product-rating-link"><?php IncludeCom('shop/_rating', ['rating' => $product->rating, 'count' => $product->reviews_count]) ?></a>
        <?php endif ?>
        <div class="product-price" data-base-price="<?= $product->price ?>" data-currency-sign="<?= Config('shop')['currency_sign'] ?>" data-addons-per-unit="<?= $product->isPieceUnit() ? 1 : 0 ?>">
            <?php IncludeCom('shop/_price', ['product' => $product]) ?>
            <?php if (!$product->isPieceUnit()): // У метражных/весовых доп. услуга - одна на позицию, к цене за метр её не прибавляем ?>
                <div class="product-addons-sum" data-addons-sum hidden></div>
            <?php endif ?>
        </div>
        <?php if ($availability = $product->availability()): ?>
            <div class="stock-label stock-<?= $availability['css'] ?>"><?= $availability['text'] ?></div>
        <?php endif ?>

        <?php foreach ($product->variantOptions() as $paramName => $variantParam): ?>
            <div class="variant-group">
                <div class="variant-title"><?= $paramName ?><?php foreach ($variantParam['options'] as $option) { if ($option['is_current']) { echo ': <span>' . $option['value'] . '</span>'; break; } } ?></div>
                <div class="variant-options<?= $variantParam['is_image'] ? ' variant-options-images' : '' ?>">
                    <?php foreach ($variantParam['options'] as $option): $variant = $option['product'] ?>
                        <a href="<?= $variant->url() ?>"
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

        <?php if ($product->isAvailable() && ($addons = $product->getAddons())): ?>
            <!-- Доп. товары/услуги: выбранные уходят в корзину вместе с товаром (поля привязаны к форме атрибутом form), цена выше пересчитывается сразу -->
            <div class="product-addons">
                <div class="variant-title">Дополнительно</div>
                <?php foreach ($addons as $addon): $addonPrice = $addon->priceFor($product->price) ?>
                    <label class="product-addon">
                        <input type="checkbox" name="addons[]" value="<?= $addon->id ?>" form="buy-<?= $uid ?>" data-addon-price="<?= $addonPrice ?>">
                        <?php if ($addon->imageUrl()): ?><img src="<?= $addon->imageUrl() ?>" alt="" width="200" height="200" loading="lazy"><?php else: ?><span class="product-addon-noimage"><i class="bi bi-gift"></i></span><?php endif ?>
                        <span class="product-addon-body">
                            <span class="product-addon-name"><?= $addon->name ?></span>
                            <?php if ($addon->description): ?><span class="product-addon-text"><?= nl2br($addon->description) ?></span><?php endif ?>
                        </span>
                        <span class="product-addon-price">+<?= ShopPrice($addonPrice) ?></span>
                        <i class="bi bi-check-circle-fill product-addon-check"></i>
                    </label>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <form action="<?= SiteRoot('shop/cart') ?>" method="post" class="product-buy" id="buy-<?= $uid ?>" data-cart-form>
            <input type="hidden" name="a" value="add">
            <input type="hidden" name="product_id" value="<?= $product->id ?>">
            <?php if ($product->isAvailable()): ?>
                <div class="qty-input<?= $product->isPieceUnit() ? '' : ' qty-input-unit' ?>">
                    <button type="button" data-qty="-1" aria-label="Меньше"><i class="bi bi-dash"></i></button>
                    <input type="number" name="quantity" value="<?= ShopQuantityValue($product->minQuantity()) ?>" min="<?= ShopQuantityValue($product->minQuantity()) ?>" max="<?= ShopQuantityValue($product->maxQuantity()) ?>" step="<?= ShopQuantityValue($step) ?>" inputmode="decimal" aria-label="Количество, <?= $product->unitName() ?>">
                    <?php if (!$product->isPieceUnit()): ?><span class="qty-unit"><?= $product->unitName() ?></span><?php endif ?>
                    <button type="button" data-qty="1" aria-label="Больше"><i class="bi bi-plus"></i></button>
                </div>
                <button type="submit" class="btn btn-dark btn-lg flex-grow-1 product-buy-main">В корзину</button>
            <?php else: ?>
                <button type="button" class="btn btn-light btn-lg flex-grow-1 product-buy-main" disabled>Нет в наличии</button>
            <?php endif ?>
            <button type="button" class="btn btn-outline-dark btn-lg btn-favorite-lg<?= $isFav ? ' is-active' : '' ?>" data-favorite="<?= $product->id ?>" aria-label="В избранное" title="В избранное">
                <i class="bi bi-heart"></i><i class="bi bi-heart-fill"></i>
            </button>
            <button type="button" class="btn btn-outline-dark btn-lg btn-compare-lg<?= $isCompare ? ' is-active' : '' ?>" data-compare="<?= $product->id ?>" aria-label="Сравнить" title="Сравнить">
                <i class="bi bi-bar-chart"></i><i class="bi bi-bar-chart-fill"></i>
            </button>
        </form>
        <?php if (!$product->isPieceUnit() && $product->isAvailable()): ?>
            <div class="product-unit-note">Цена за <?= $product->unitName() ?>, шаг <?= ShopQuantity($step, $product->unitKey()) ?><?= $product->minQuantity() > $step ? ', от ' . ShopQuantity($product->minQuantity(), $product->unitKey()) : '' ?></div>
        <?php endif ?>
        <?php if (!$product->isAvailable() && !Get('subscribed')): ?>
            <form action="<?= $product->url() ?>" method="post" class="stock-subscribe">
                <input type="hidden" name="a" value="stock_subscribe">
                <label class="form-label small text-secondary" for="stock-email-<?= $uid ?>">Сообщить о поступлении</label>
                <div class="input-group">
                    <input type="email" name="email" id="stock-email-<?= $uid ?>" class="form-control" placeholder="Ваш e-mail" required value="<?= IS_USER_AUTH ? $GLOBALS['g_user']->email : '' ?>">
                    <button type="submit" class="btn btn-outline-dark">Подписаться</button>
                </div>
            </form>
        <?php endif ?>
        <?= $msg ?? '' ?>

        <?php if ($params = $product->getParams()): ?>
            <dl class="product-params">
                <?php foreach ($params as $name => $value): ?>
                    <div><dt><?= $name ?></dt><dd><?= $value ?></dd></div>
                <?php endforeach ?>
                <?php if ($product->category_id && ($path = ShopCategoryModel::path($product->category_id))): $last = end($path) ?>
                    <div><dt>Раздел каталога</dt><dd><a href="<?= SiteRoot("shop/c/{$last['slug']}") ?>"><?= $last['name'] ?></a></dd></div>
                <?php endif ?>
            </dl>
        <?php endif ?>
        <?php if (!empty($full) && $product->description): ?>
            <div class="product-description"><?= nl2br($product->description) ?></div>
        <?php elseif ($product->description): ?>
            <p class="product-description-short"><?= mb_strimwidth(strip_tags(html_entity_decode($product->description)), 0, 220, '…') ?></p>
            <a href="<?= $product->url() ?>" class="link-arrow">Подробнее о товаре <i class="bi bi-arrow-right"></i></a>
        <?php endif ?>
    </div>
</div>
