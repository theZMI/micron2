<div class="container-fluid">
    <div class="row">
        <div class="col" style="max-width: 800px;">
            <h1 class="my-4"><?= $model->isExists() ? $model->name : 'Новая группа доп. услуг' ?></h1>
            <form action="<?= GetCurUrl() ?>" method="post">
                <input type="hidden" name="is_set" value="1">
                <?= $msg ?>
                <div class="row">
                    <div class="col-md-9 mb-3">
                        <label class="form-label">Название *</label>
                        <input type="text" name="name" class="form-control" value="<?= $modelParam('name') ?>" required placeholder="Доп. услуги для телефонов">
                        <div class="form-text">По нему группа подключается к товарам из таблицы импорта (колонка «Доп. услуги»)</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Порядок</label>
                        <input type="number" name="sort" class="form-control" value="<?= $modelParam('sort', 0) ?>">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Доп. услуги в группе</label>
                    <?php if (!$addons): ?>
                        <p class="text-muted small">Сначала <a href="<?= SiteRoot('_admin/shop/addons/add_or_edit') ?>">добавьте доп. услуги</a></p>
                    <?php endif ?>
                    <?php foreach ($addons as $addon): ?>
                        <div class="form-check d-flex align-items-center gap-2 mb-2">
                            <input type="checkbox" name="addon_ids[]" value="<?= $addon->id ?>" class="form-check-input" id="a-<?= $addon->id ?>" <?= in_array((int)$addon->id, $selected, true) ? 'checked' : '' ?>>
                            <label class="form-check-label d-flex align-items-center gap-2" for="a-<?= $addon->id ?>">
                                <?php if ($addon->imageUrl()): ?><img src="<?= $addon->imageUrl() ?>" alt="" width="32" height="32" class="rounded"><?php endif ?>
                                <?= $addon->name ?> <span class="text-secondary small"><?= $addon->priceText() ?><?= $addon->is_active ? '' : ' · скрыта' ?></span>
                            </label>
                        </div>
                    <?php endforeach ?>
                </div>
                <div class="text-center">
                    <a href="<?= SiteRoot('_admin/shop/addon_groups') ?>" class="btn btn-link btn-lg"><i class="bi bi-arrow-left pe-2"></i>К списку</a>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill"><i class="bi bi-check-lg pe-2"></i>Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
