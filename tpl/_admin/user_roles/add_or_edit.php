<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">
                <span class="pull-start"><?= $model->isExists() ? "Роль: " . $modelParam('name') : 'Новый тип пользователей' ?></span>
            </h1>
            <form action="<?= GetCurUrl() ?>" method="post">
                <input type="hidden" name="is_set" value="1">
                <?= $msg ?>
                <div class="mb-4">
                    <label class="form-label">Название:</label>
                    <input type="text" name="name" class="form-control" value="<?= $modelParam('name') ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Описание:</label>
                    <textarea name="description" rows="4" class="form-control"><?= $modelParam('description') ?></textarea>
                </div>
                <div class="mb-4">
                    <label class="form-label">Права:</label>
                    <?php foreach ($allAccessRules as $rule): ?>
                        <div class="form-check form-switch form-switch-md mb-2">
                            <input type="checkbox" role="switch" class="form-check-input" id="access_rule_<?= $rule->id ?>" name="access_rules[<?= $rule->name ?>]" <?= $model->getAccessRule($rule->name) ? ' checked' : '' ?>>
                            <label class="form-check-label ms-3 mt-1" for="access_rule_<?= $rule->id ?>">
                                <?= $rule->description ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="text-center">
                    <a href="<?= SiteRoot('_admin/user_roles') ?>" class="btn btn-link btn-lg"><i class="bi bi-arrow-left pe-2"></i>К списку</a>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill"><i class="bi bi-check-lg pe-2"></i>Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>