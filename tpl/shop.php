<div class="shop-page">
    <div class="shop-head">
        <h1 class="page-title"><?= $filter['q'] !== '' ? 'Поиск: «' . $filter['q'] . '»' : 'Каталог' ?></h1>
        <span class="text-secondary"><?= $result['total'] ?> <?= Plural($result['total'], 'товар', 'товара', 'товаров') ?></span>
    </div>

    <div class="shop-layout">
        <!-- Форма только вокруг фильтров: внутри карточек свои формы «в корзину», а вложенные формы HTML не допускает -->
        <form action="<?= SiteRoot('shop') ?>" method="get" id="shopFilters" data-catalog-filter class="shop-filters offcanvas-lg offcanvas-start" tabindex="-1">
            <input type="hidden" name="q" value="<?= $filter['q'] ?>">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Фильтры</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#shopFilters" aria-label="Закрыть"></button>
            </div>
            <div class="offcanvas-body flex-column">
                <div class="filter-group">
                    <div class="filter-title">Цена, <?= Config('shop')['currency_sign'] ?></div>
                    <div class="d-flex gap-2">
                        <input type="number" name="price_min" class="form-control form-control-sm" placeholder="от <?= (int)$result['price_range'][0] ?>" value="<?= $filter['price_min'] ?>" min="0">
                        <input type="number" name="price_max" class="form-control form-control-sm" placeholder="до <?= (int)$result['price_range'][1] ?>" value="<?= $filter['price_max'] ?>" min="0">
                    </div>
                </div>
                <?php if ($hasStock): ?>
                    <div class="filter-group">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="in_stock" value="1" id="f-in-stock" <?= $filter['in_stock'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="f-in-stock">Только в наличии</label>
                        </div>
                    </div>
                <?php endif ?>
                <?php foreach ($facets as $name => $values): $key = ShopProductModel::facetKey($name); $checked = (array)($filter['params'][$key] ?? []) ?>
                    <div class="filter-group">
                        <div class="filter-title"><?= $name ?></div>
                        <div class="filter-options">
                            <?php foreach ($values as $value => $count): $id = 'f-' . md5($name . $value) ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="f[<?= $key ?>][]" value="<?= $value ?>" id="<?= $id ?>" <?= in_array((string)$value, array_map('strval', $checked), true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="<?= $id ?>"><?= $value ?> <span class="text-secondary"><?= $count ?></span></label>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                <?php endforeach ?>
                <div class="d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-dark btn-sm flex-grow-1">Показать</button>
                    <?php if ($hasFilters): ?>
                        <a href="<?= SiteRoot('shop' . ($filter['q'] !== '' ? '&q=' . urlencode(html_entity_decode($filter['q'])) : '')) ?>" class="btn btn-light btn-sm">Сбросить</a>
                    <?php endif ?>
                </div>
            </div>
        </form>

        <div class="shop-main">
            <div class="shop-toolbar">
                <button type="button" class="btn btn-light btn-sm d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#shopFilters">
                    <i class="bi bi-sliders2 me-1"></i>Фильтры<?= $hasFilters ? ' •' : '' ?>
                </button>
                <select name="sort" form="shopFilters" class="form-select form-select-sm ms-auto" aria-label="Сортировка" data-catalog-sort>
                    <?php foreach (ShopProductModel::SORTS as $key => $title): ?>
                        <option value="<?= $key ?>" <?= $key === $filter['sort'] ? 'selected' : '' ?>><?= $title ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <?php if (empty($products)): ?>
                <div class="empty-state">
                    <i class="bi bi-search"></i>
                    <p>Ничего не нашлось<?= $hasFilters || $filter['q'] !== '' ? '. Попробуйте изменить запрос или фильтры' : '' ?></p>
                    <?php if ($hasFilters || $filter['q'] !== ''): ?><a href="<?= SiteRoot('shop') ?>" class="btn btn-dark">Весь каталог</a><?php endif ?>
                </div>
            <?php else: ?>
                <div class="product-grid">
                    <?php foreach ($products as $product): ?>
                        <?php IncludeCom('shop/_product_card', ['product' => $product]) ?>
                    <?php endforeach ?>
                </div>
                <div class="mt-5">
                    <?php
                    IncludeCom('_dev/paginator', [
                        'css'          => 'justify-content-center',
                        'pageUrl'      => GetCurUrl('page=' . M_PAGINATOR_PAGE),
                        'firstPageUrl' => GetCurUrl('page=' . M_DELETE_PARAM),
                        'total'        => $result['total'],
                        'perPage'      => $perPage,
                        'curPage'      => $filter['page'],
                    ]);
                    ?>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
