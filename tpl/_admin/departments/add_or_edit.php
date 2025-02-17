<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">
                <span class="pull-start"><?= $model->isExists() ? "Отдел: ".$modelParam('department') : 'Новый отдел' ?></span>
            </h1>
            <form action="<?= GetCurUrl() ?>" method="post">
                <input type="hidden" name="is_set" value="1">
                <?= $msg ?>
                <div class="mb-4">
                    <label class="form-label">Название:</label>
                    <input type="text" name="department" class="form-control" value="<?= $modelParam('department') ?>" required>
                </div>
                <div class="form-check form-switch form-switch-lg mb-5">
                    <input class="form-check-input" type="checkbox" name="use_timer" id="flexCheckUseTimer" role="switch">
                    <label class="form-check-label ms-3 mt-2" for="flexCheckUseTimer">
                        Отдел использует таймер
                    </label>
                </div>
                <div class="text-center">
                    <a href="<?= SiteRoot('_admin/departments') ?>" class="btn btn-link btn-lg"><i class="bi bi-arrow-left pe-2"></i>К списку</a>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill"><i class="bi bi-check-lg pe-2"></i>Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>