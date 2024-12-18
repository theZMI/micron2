<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">
                <span class="pull-start"><?= $model->isExists() ? "Параметр №".$modelParam('id') : 'Новый параметр' ?></span>
            </h1>
            <form action="<?= GetCurUrl() ?>" method="post">
                <input type="hidden" name="is_set" value="1">
                <?= $msg ?>
                <div class="mb-4">
                    <label class="form-label">Название:</label>
                    <input type="text" name="name" class="form-control" value="<?= $modelParam('name') ?>">
                </div>
                <div class="mb-4">
                    <label class="form-label">Тип параметра:</label>
                    <select name="type" class="form-control">
                        <?php foreach (ParamModel::getTypes() as $k => $v): ?>
                            <option value="<?= $k ?>"<?= intval($modelParam('type')) === +$k ? ' selected' : '' ?>><?= $v ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="text-center">
                    <a href="<?= SiteRoot('_admin/params') ?>" class="btn btn-link btn-lg"><i class="bi bi-arrow-left pe-2"></i>К списку</a>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill"><i class="bi bi-check-lg pe-2"></i>Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>