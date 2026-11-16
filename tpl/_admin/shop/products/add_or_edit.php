<div class="container-fluid mb-4">
    <div class="row">
        <div class="col" style="max-width: 800px;">
            <h1 class="my-4"><?= $model->isExists() ? $model->name : 'Новый товар' ?></h1>
            <form action="<?= GetCurUrl() ?>" method="post">
                <input type="hidden" name="is_set" value="1">
                <?= $msg ?>
                <div class="mb-3">
                    <label class="form-label">Название *</label>
                    <input type="text" name="name" class="form-control" value="<?= $modelParam('name') ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Артикул</label>
                        <input type="text" name="vendor_code" class="form-control" value="<?= $modelParam('vendor_code') ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Цена, <?= Config('shop')['currency_sign'] ?> *</label>
                        <input type="text" name="price" class="form-control" value="<?= $model->isExists() ? number_format($model->price, 2, '.', '') : Post('price', '') ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Порядок</label>
                        <input type="number" name="sort" class="form-control" value="<?= $modelParam('sort', 0) ?>">
                        <div class="form-text">Меньше - выше в каталоге</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Описание</label>
                    <textarea name="description" class="form-control" rows="6"><?= $modelParam('description') ?></textarea>
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
        </div>
    </div>
</div>
