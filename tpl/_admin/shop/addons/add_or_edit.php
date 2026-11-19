<?php $size = ShopAddonModel::IMAGE_SIZE ?>
<div class="container-fluid">
    <div class="row">
        <div class="col" style="max-width: 800px;">
            <h1 class="my-4"><?= $model->isExists() ? $model->name : 'Новая доп. услуга' ?></h1>
            <form action="<?= GetCurUrl() ?>" method="post" id="addon-form">
                <input type="hidden" name="is_set" value="1">
                <?= $msg ?>
                <div class="mb-3">
                    <label class="form-label">Название *</label>
                    <input type="text" name="name" class="form-control" value="<?= $modelParam('name') ?>" required placeholder="Подарочная упаковка">
                </div>
                <div class="row">
                    <div class="col-md-5 mb-3">
                        <label class="form-label">Цена</label>
                        <div class="input-group">
                            <input type="text" name="price_value" class="form-control" inputmode="decimal" value="<?= $model->isExists() ? ShopRubles((int)$model->price_value) : Post('price_value', '') ?>">
                            <select name="price_type" class="form-select" style="max-width: 13rem;">
                                <?php foreach (ShopAddonModel::priceTypes() as $key => $title): ?>
                                    <option value="<?= $key ?>" <?= $modelParam('price_type', ShopAddonModel::PRICE_FIXED) === $key ? 'selected' : '' ?>><?= $title ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-text">Процент считается от цены товара и округляется вверх до <?= ShopPrice((int)ShopKopecks((string)Config('shop')['addons']['percent_round'])) ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">В чеке</label>
                        <select name="is_service" class="form-select">
                            <option value="1" <?= $model->isExists() ? ($model->is_service ? 'selected' : '') : 'selected' ?>>Услуга (гарантия, упаковка)</option>
                            <option value="0" <?= $model->isExists() && !$model->is_service ? 'selected' : '' ?>>Товар (ремешок, чехол)</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Порядок</label>
                        <input type="number" name="sort" class="form-control" value="<?= $modelParam('sort', 0) ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Описание</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Коротко - показывается под названием"><?= $modelParam('description') ?></textarea>
                </div>
                <div class="mb-2">
                    <label class="form-label">Картинка</label>
                    <input type="hidden" name="addon_image_rdy_name" value="">
                    <div class="d-flex align-items-center gap-3">
                        <img src="<?= $model->imageUrl() ?: Root('i/image/def_img_bg.png') ?>" width="100" height="100" alt="" class="rounded is-addon_image-<?= $size ?>x<?= $size ?>-preview" style="object-fit: cover;">
                        <div class="form-text m-0">Квадрат <?= $size ?>×<?= $size ?>: после загрузки откроется окно обрезки.
                            <?php if ($model->image): ?><label class="d-block mt-1"><input type="checkbox" name="image_delete" value="1" class="form-check-input me-1">Удалить картинку</label><?php endif ?>
                        </div>
                    </div>
                </div>
            </form>
            <!-- Форма загрузки - отдельная форма, поэтому вне основной -->
            <div class="mb-3"><?= $uploadForm ?></div>
            <div class="form-check mb-4">
                <input type="checkbox" name="is_active" value="1" form="addon-form" class="form-check-input" id="i-is-active" <?= $model->isExists() ? ($model->is_active ? 'checked' : '') : 'checked' ?>>
                <label class="form-check-label" for="i-is-active">Показывать покупателям</label>
            </div>
            <div class="text-center">
                <a href="<?= SiteRoot('_admin/shop/addons') ?>" class="btn btn-link btn-lg"><i class="bi bi-arrow-left pe-2"></i>К списку</a>
                <button type="submit" form="addon-form" class="btn btn-primary btn-lg rounded-pill"><i class="bi bi-check-lg pe-2"></i>Сохранить</button>
            </div>
        </div>
    </div>
</div>
