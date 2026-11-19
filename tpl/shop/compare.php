<head><meta name="robots" content="noindex, follow"></head>
<div class="shop-page">
    <div class="shop-head">
        <h1 class="page-title">Сравнение</h1>
        <?php if ($products): ?>
            <span class="text-secondary"><?= count($products) ?> <?= Plural(count($products), 'товар', 'товара', 'товаров') ?></span>
        <?php endif ?>
    </div>
    <?php if (empty($products)): ?>
        <div class="empty-state">
            <i class="bi bi-bar-chart"></i>
            <p>Добавьте товары в сравнение кнопкой <i class="bi bi-bar-chart"></i> в каталоге</p>
            <a href="<?= SiteRoot('shop') ?>" class="btn btn-dark">В каталог</a>
        </div>
    <?php else: ?>
        <div class="shop-toolbar">
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="compare-diff" <?= $onlyDiff ? 'checked' : '' ?> onchange="location.href=this.checked ? '<?= GetCurUrl('diff=1') ?>' : '<?= GetCurUrl('diff=' . M_DELETE_PARAM) ?>'">
                <label class="form-check-label" for="compare-diff">Только различия</label>
            </div>
            <form action="<?= SiteRoot('shop/compare') ?>" method="post" class="ms-auto">
                <input type="hidden" name="a" value="clear">
                <button type="submit" class="btn btn-light btn-sm">Очистить</button>
            </form>
        </div>
        <div class="compare-table-wrap">
            <table class="compare-table">
                <thead>
                <tr>
                    <th></th>
                    <?php foreach ($products as $product): ?>
                        <td class="compare-product">
                            <button type="button" class="btn btn-icon btn-icon-muted compare-remove" data-compare="<?= $product->id ?>" data-compare-reload title="Убрать из сравнения" aria-label="Убрать из сравнения"><i class="bi bi-x-lg"></i></button>
                            <a href="<?= $product->url() ?>" class="compare-product-image">
                                <?php if ($image = $product->mainImage()): ?><img src="<?= $image ?>" alt="" loading="lazy"><?php else: ?><i class="bi bi-image"></i><?php endif ?>
                            </a>
                            <a href="<?= $product->url() ?>" class="compare-product-title"><?= $product->name ?></a>
                            <?php IncludeCom('shop/_price', ['product' => $product]) ?>
                            <?php if ($product->isAvailable()): ?>
                                <form action="<?= SiteRoot('shop/cart') ?>" method="post" data-cart-form class="mt-2">
                                    <input type="hidden" name="a" value="add">
                                    <input type="hidden" name="product_id" value="<?= $product->id ?>">
                                    <button type="submit" class="btn btn-dark btn-sm">В корзину</button>
                                </form>
                            <?php else: ?>
                                <div class="text-secondary small mt-2">Нет в наличии</div>
                            <?php endif ?>
                        </td>
                    <?php endforeach ?>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th>Рейтинг</th>
                    <?php foreach ($products as $product): ?>
                        <td><?= $product->reviews_count ? '<i class="bi bi-star-fill text-warning"></i> ' . number_format($product->rating, 1, ',', '') . ' <span class="text-secondary">(' . $product->reviews_count . ')</span>' : '—' ?></td>
                    <?php endforeach ?>
                </tr>
                <?php foreach ($rows as $name => $row): if ($onlyDiff && $row['is_same']) continue ?>
                    <tr class="<?= $row['is_same'] ? '' : 'is-diff' ?>">
                        <th><?= $name ?></th>
                        <?php foreach ($row['values'] as $value): ?>
                            <td><?= $value !== '' ? $value : '—' ?></td>
                        <?php endforeach ?>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</div>
