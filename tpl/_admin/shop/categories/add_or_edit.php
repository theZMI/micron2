<div class="container-fluid">
    <div class="row">
        <div class="col" style="max-width: 800px;">
            <h1 class="my-4"><?= $model->isExists() ? $model->name : 'Новая категория' ?></h1>
            <form action="<?= GetCurUrl() ?>" method="post">
                <input type="hidden" name="is_set" value="1">
                <?= $msg ?>
                <div class="mb-3">
                    <label class="form-label">Название *</label>
                    <input type="text" name="name" class="form-control" value="<?= $modelParam('name') ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Родительская категория</label>
                        <select name="parent_id" class="form-select">
                            <option value="0">— верхний уровень —</option>
                            <?php foreach (ShopCategoryModel::selectOptions((int)$model->id) as $id => $name): ?>
                                <option value="<?= $id ?>" <?= (int)$modelParam('parent_id', 0) === $id ? 'selected' : '' ?>><?= $name ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Адрес</label>
                        <div class="input-group">
                            <span class="input-group-text">/shop/c/</span>
                            <input type="text" name="slug" class="form-control" value="<?= $modelParam('slug') ?>" placeholder="из названия">
                        </div>
                        <div class="form-text">Латиница, цифры и дефис</div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Порядок</label>
                        <input type="number" name="sort" class="form-control" value="<?= $modelParam('sort', 0) ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Описание</label>
                    <textarea name="description" class="form-control" rows="4"><?= $modelParam('description') ?></textarea>
                    <div class="form-text">Показывается над товарами и в описании страницы для поисковиков</div>
                </div>
                <div class="form-check mb-4">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="i-is-active" <?= $model->isExists() ? ($model->is_active ? 'checked' : '') : 'checked' ?>>
                    <label class="form-check-label" for="i-is-active">Показывать на сайте (вместе с подкатегориями)</label>
                </div>
                <div class="text-center">
                    <a href="<?= SiteRoot('_admin/shop/categories') ?>" class="btn btn-link btn-lg"><i class="bi bi-arrow-left pe-2"></i>К списку</a>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill"><i class="bi bi-check-lg pe-2"></i>Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
