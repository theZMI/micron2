<div class="container-fluid mb-4">
    <div class="row">
        <div class="col" style="max-width: 800px;">
            <h1 class="my-4"><?= $model->isExists() ? $model->name : 'Новый товар' ?></h1>
            <form action="<?= GetCurUrl() ?>" method="post" enctype="multipart/form-data">
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
                        <input type="text" name="price" class="form-control" value="<?= $model->isExists() ? number_format($model->price, 2, '.', '') : Post('price', '') ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Старая цена, <?= Config('shop')['currency_sign'] ?></label>
                        <input type="text" name="price_old" class="form-control" value="<?= $model->isExists() ? ($model->price_old > 0 ? number_format($model->price_old, 2, '.', '') : '') : Post('price_old', '') ?>">
                        <div class="form-text">Цена до скидки, зачёркнута в каталоге</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Порядок</label>
                        <input type="number" name="sort" class="form-control" value="<?= $modelParam('sort', 0) ?>">
                        <div class="form-text">Меньше - выше в каталоге</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Описание</label>
                    <textarea name="description" class="form-control" rows="6"><?= $modelParam('description') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Картинки</label>
                    <?php if ($images = $model->getImages()): ?>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <?php foreach ($images as $i => $image): ?>
                                <div class="position-relative" style="width: 96px;">
                                    <img src="<?= $image['thumb'] ?>" alt="" class="rounded w-100" style="aspect-ratio: 4/5; object-fit: cover; <?= $i === 0 ? 'outline: 2px solid #111; outline-offset: 2px;' : '' ?>">
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
                    <input type="file" name="images[]" class="form-control" accept="image/jpeg,image/png,image/gif" multiple>
                    <div class="form-text">JPEG, PNG или GIF. Первая картинка - главная. Большие фото уменьшаются автоматически</div>
                </div>
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
