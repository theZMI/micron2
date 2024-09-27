<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">
                <span class="pull-start"><?= $model->isExists() ? "Отдел: " . $modelParam('department') : 'Новый отдел' ?></span>
            </h1>
            <form action="<?= GetCurUrl() ?>" method="post">
                <input type="hidden" name="is_set" value="1">
                <?= $msg ?>
                <div class="mb-4">
                    <label class="form-label">Название:</label>
                    <input type="text" name="department" class="form-control" value="<?= $modelParam('department') ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Работники:</label>
                    <?php if ($allUsers): ?>
                        <?php foreach ($allUsers as $user): ?>
                            <div class="form-check form-switch form-switch-md mb-2">
                                <input type="checkbox" role="switch" class="form-check-input" id="user_<?= $user->id ?>" name="user_ids[<?= $user->id ?>]" <?= in_array($user->id, array_keys($currentUsers)) ? ' checked' : '' ?>>
                                <label class="form-check-label ms-3 mt-1" for="user_<?= $user->id ?>">
                                    <?= $user->full_name ?>
                                    <span class="badge bg-secondary rounded-pill"><?= $user->login ?></span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">Нет пользователей без отдела</p>
                    <?php endif; ?>
                </div>
                <div class="text-center">
                    <a href="<?= SiteRoot('_admin/departments') ?>" class="btn btn-link btn-lg"><i class="bi bi-arrow-left pe-2"></i>К списку</a>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill"><i class="bi bi-check-lg pe-2"></i>Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>