<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">
                <span class="pull-start"><?= $model->isExists() ? "Задача №" . $modelParam('id') : 'Новая задача' ?></span>
            </h1>
            <form action="<?= GetCurUrl() ?>" method="post">
                <input type="hidden" name="is_set" value="1">
                <?= $msg ?>
                <div class="mb-3">
                    <label class="form-label">Задача</label>
                    <input type="text" name="task" class="form-control" value="<?= $modelParam('task') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Подробное описание задачи</label>
                    <textarea name="description" class="form-control" cols="30" rows="10"><?= $modelParam('description') ?></textarea>
                </div>
                <div>
                    <a href="<?= SiteRoot('_admin/tasks') ?>" class="btn btn-link btn-lg"><i class="bi bi-arrow-left pe-2"></i>К списку</a>
                    <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-lg pe-2"></i>Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>