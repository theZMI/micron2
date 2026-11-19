<div class="container-fluid">
    <div class="row">
        <div class="col" style="max-width: 800px;">
            <h1 class="my-4"><?= $model->isExists() ? $model->name : 'Новый товар' ?></h1>
            <!-- Картинки - отдельные формы (загрузчик сам является формой), поэтому над основной формой -->
            <div class="card card-body mb-4">
                <label class="form-label fw-bold">Картинки</label>
                <?php if (!$model->isExists()): ?>
                    <p class="small text-secondary mb-0">Сохраните товар - сразу откроется его карточка, и можно будет добавить картинки.</p>
                <?php else: ?>
                    <?php if (Get('is_new')): ?><?= MsgOk('Товар создан. Добавьте картинки') ?><?php endif ?>
                    <?php if ($images = $model->getImages()): ?>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <?php foreach ($images as $i => $image): ?>
                                <div class="position-relative" style="width: 120px;">
                                    <img src="<?= $image['thumb'] ?>" alt="" class="rounded w-100" style="aspect-ratio: <?= $ratioW ?>/<?= $ratioH ?>; object-fit: cover; <?= $i === 0 ? 'outline: 2px solid #111; outline-offset: 2px;' : '' ?>">
                                    <div class="d-flex justify-content-center gap-1 mt-1">
                                        <?php if ($i > 0): ?>
                                            <button type="submit" form="img-main-<?= $image['name'] ?>" class="btn btn-sm btn-light py-0 px-2" title="Сделать главной"><i class="bi bi-star"></i></button>
                                        <?php endif ?>
                                        <button type="submit" form="img-del-<?= $image['name'] ?>" class="btn btn-sm btn-light py-0 px-2" title="Удалить" onclick="return confirm('Удалить картинку?')"><i class="bi bi-trash3"></i></button>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php endif ?>
                    <!-- После загрузки откроется обрезка; после обрезки форма отправится сама (см. скрипт ниже) -->
                    <form action="<?= GetCurUrl() ?>" method="post" id="image-add-form" class="d-flex align-items-center gap-3 mb-2">
                        <input type="hidden" name="a" value="image_add">
                        <input type="hidden" name="product_image_rdy_name" value="">
                        <img src="" alt="" width="120" class="rounded is-product_image-<?= $previewW ?>x<?= $previewH ?>-preview d-none" style="aspect-ratio: <?= $ratioW ?>/<?= $ratioH ?>; object-fit: cover;">
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill d-none" data-image-add><i class="bi bi-plus-lg pe-1"></i>Добавить к товару</button>
                    </form>
                    <?= $uploadForm ?>
                    <div class="form-text">JPEG, PNG или GIF. После загрузки откроется обрезка в пропорции <?= $ratioW ?>:<?= $ratioH ?>; картинка добавится к товару сама. Первая картинка - главная<?= $model->image_sources && $model->image_sources !== '[]' ? '. <b>Картинки этого товара ведутся в таблице импорта</b> - при следующей синхронизации останутся только они' : '' ?></div>
                    <script>
                        (() => {
                            const form = document.getElementById('image-add-form');
                            const input = form.querySelector('input[name=product_image_rdy_name]');
                            const preview = form.querySelector('img');
                            const button = form.querySelector('[data-image-add]');
                            // Файл загружен - показываем кнопку (если обрезку закрыли без изменений)
                            setInterval(() => button.classList.toggle('d-none', !input.value), 400);
                            // Обрезка сохранена - превью обновилось: добавляем картинку к товару
                            new MutationObserver(() => {
                                if (input.value && preview.getAttribute('src')) {
                                    preview.classList.remove('d-none');
                                    setTimeout(() => form.submit(), 300);
                                }
                            }).observe(preview, {attributes: true, attributeFilter: ['src']});
                        })();
                    </script>
                <?php endif ?>
            </div>
            <form action="<?= GetCurUrl() ?>" method="post">
                <input type="hidden" name="is_set" value="1">
                <?= $msg ?>
                <div class="mb-3">
                    <label class="form-label">Название *</label>
                    <input type="text" name="name" class="form-control" value="<?= $modelParam('name') ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Артикул</label>
                        <input type="text" name="vendor_code" class="form-control" value="<?= $modelParam('vendor_code') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Цена, <?= Config('shop')['currency_sign'] ?> *</label>
                        <input type="text" name="price" class="form-control" value="<?= $model->isExists() ? ShopRubles($model->price) : Post('price', '') ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Старая цена, <?= Config('shop')['currency_sign'] ?></label>
                        <input type="text" name="price_old" class="form-control" value="<?= $model->isExists() ? ($model->price_old > 0 ? ShopRubles($model->price_old) : '') : Post('price_old', '') ?>">
                        <div class="form-text">Цена до скидки, зачёркнута в каталоге</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Порядок</label>
                        <input type="number" name="sort" class="form-control" value="<?= $modelParam('sort', 0) ?>">
                        <div class="form-text">Меньше - выше в каталоге</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Категория</label>
                        <select name="category_id" class="form-select">
                            <option value="0">— без категории —</option>
                            <?php foreach (ShopCategoryModel::selectOptions() as $id => $name): ?>
                                <option value="<?= $id ?>" <?= (int)$modelParam('category_id', 0) === $id ? 'selected' : '' ?>><?= $name ?></option>
                            <?php endforeach ?>
                        </select>
                        <div class="form-text"><a href="<?= SiteRoot('_admin/shop/categories') ?>">Категории</a></div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Единица</label>
                        <select name="unit" class="form-select">
                            <?php foreach (Config('shop')['units'] as $key => $unit): ?>
                                <option value="<?= $key ?>" <?= ($model->isExists() ? $model->unitKey() : Post('unit', Config('shop')['default_unit'])) === $key ? 'selected' : '' ?>><?= $unit['name'] ?></option>
                            <?php endforeach ?>
                        </select>
                        <div class="form-text">Цена - за неё</div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Шаг</label>
                        <input type="text" name="qty_step" class="form-control" inputmode="decimal" value="<?= ShopQuantityValue($model->isExists() ? $model->step() : (float)Post('qty_step', 1)) ?>">
                        <div class="form-text">Не меньше: <?= implode(', ', array_map(fn($k, $u) => ShopQuantityValue(ShopProductModel::unitMinStep($k)) . ' ' . $u['name'], array_keys(Config('shop')['units']), Config('shop')['units'])) ?></div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Минимум</label>
                        <input type="text" name="qty_min" class="form-control" inputmode="decimal" value="<?= ShopQuantityValue($model->isExists() ? $model->minQuantity() : (float)Post('qty_min', 1)) ?>">
                        <div class="form-text">В заказе</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Остаток</label>
                        <input type="text" name="stock" inputmode="decimal" class="form-control" value="<?= $model->isExists() ? ($model->track_stock ? ShopQuantityValue($model->stock) : '') : Post('stock', '') ?>">
                        <div class="form-text">В единицах товара. Пусто - не учитывать<?= $model->isExists() && ($waiting = ShopStockSubscriptionModel::countWaiting((int)$model->id)) ? ". <b>Ждут поступления: {$waiting}</b>" : '' ?></div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Группа вариантов</label>
                        <input type="text" name="variant_group" class="form-control" value="<?= $modelParam('variant_group') ?>" placeholder="WATCH-01">
                        <div class="form-text">Один код у одного товара в разных цветах/размерах</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Покупают вместе</label>
                        <input type="text" name="related" class="form-control" value="<?= $modelParam('related') ?>" placeholder="Артикулы через запятую">
                        <div class="form-text">Покажем первыми, дальше - что покупали вместе в заказах</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Похожие товары</label>
                    <input type="text" name="similar" class="form-control" value="<?= $modelParam('similar') ?>" placeholder="Артикулы через запятую">
                    <div class="form-text">Покажем первыми, дальше - из той же категории с близкой ценой</div>
                </div>
                <?php if ($model->isExists() && ($variants = $model->getVariants())): ?>
                    <p class="small text-secondary">Варианты в группе: <?= implode(', ', array_map(fn($v) => '<a href="' . SiteRoot("_admin/shop/products/add_or_edit&id={$v->id}") . '">' . ($v->vendor_code ?: '#' . $v->id) . '</a>', $variants)) ?>.
                        Переключатель строится по характеристикам, которые у них различаются (например, «Цвет» или «Размер»).</p>
                <?php endif ?>
                <div class="mb-3">
                    <label class="form-label">Описание</label>
                    <textarea name="description" class="form-control" rows="6"><?= $modelParam('description') ?></textarea>
                </div>
                <?php if ($addonGroups = (new ShopAddonGroupModel())->getAll()): $checkedGroups = Post('is_set') ? array_map('intval', (array)Post('addon_group_ids', [])) : $model->addonGroupIds() ?>
                    <div class="mb-3">
                        <label class="form-label">Доп. товары и услуги</label>
                        <div class="d-flex flex-wrap gap-3">
                            <?php foreach ($addonGroups as $group): ?>
                                <div class="form-check">
                                    <input type="checkbox" name="addon_group_ids[]" value="<?= $group->id ?>" class="form-check-input" id="ag-<?= $group->id ?>" <?= in_array((int)$group->id, $checkedGroups, true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="ag-<?= $group->id ?>"><?= $group->name ?></label>
                                </div>
                            <?php endforeach ?>
                        </div>
                        <div class="form-text">Группы <a href="<?= SiteRoot('_admin/shop/addon_groups') ?>">доп. услуг</a>; повторяющиеся услуги покажутся один раз<?= $model->isExists() && ($addons = $model->getAddons()) ? '. Сейчас у товара: ' . implode(', ', array_map(fn($a) => $a->name, $addons)) : '' ?></div>
                    </div>
                <?php endif ?>
                <div class="mb-3">
                    <label class="form-label">Характеристики</label>
                    <textarea name="params_text" class="form-control font-monospace" rows="5" placeholder="Состав: 100% хлопок&#10;Цвет: синий"><?= $paramsText ?></textarea>
                    <div class="form-text">По строке на характеристику: «Название: значение»</div>
                </div>
                <div class="form-check mb-4">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="i-is-active" <?= $model->isExists() ? ($model->is_active ? 'checked' : '') : 'checked' ?>>
                    <label class="form-check-label" for="i-is-active">В продаже (показывать в каталоге)</label>
                </div>
                <div class="text-center">
                    <a href="<?= SiteRoot('_admin/shop/products') ?>" class="btn btn-link btn-lg"><i class="bi bi-arrow-left pe-2"></i>К списку</a>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill"><i class="bi bi-check-lg pe-2"></i>Сохранить</button>
                </div>
            </form>
            <?php foreach ($model->getImages() as $image): ?>
                <form action="<?= GetCurUrl() ?>" method="post" id="img-main-<?= $image['name'] ?>" class="d-none"><input type="hidden" name="a" value="image_main"><input type="hidden" name="image" value="<?= $image['name'] ?>"></form>
                <form action="<?= GetCurUrl() ?>" method="post" id="img-del-<?= $image['name'] ?>" class="d-none"><input type="hidden" name="a" value="image_delete"><input type="hidden" name="image" value="<?= $image['name'] ?>"></form>
            <?php endforeach ?>
        </div>
    </div>
</div>
