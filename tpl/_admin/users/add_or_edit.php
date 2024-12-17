<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">
                <span class="pull-start"><?= $model->isExists() ? $modelParam('full_name') : 'Новый пользователь' ?></span>
            </h1>
            <form action="<?= GetCurUrl() ?>" method="post">
                <input type="hidden" name="is_set" value="1">
                <?= $msg ?>
                <div class="mb-4 d-none">
                    <label class="form-label">Логин:</label>
                    <input type="text" name="login" readonly class="form-control" value="<?= $modelParam('login') ?>">
                </div>
                <div class="mb-4 d-none">
                    <label class="form-label">Email:</label>
                    <input type="email" name="email" class="form-control" value="<?= $modelParam('email') ?>">
                </div>
                <div class="row">
                    <div class="col">
                        <div class="mb-3">
                            <label class="form-label">Фамилия:</label>
                            <input type="text" name="surname" class="form-control" value="<?= $modelParam('surname') ?>">
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <label class="form-label">Имя:</label>
                            <input type="text" name="first_name" class="form-control" value="<?= $modelParam('first_name') ?>">
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <label class="form-label">Отчество:</label>
                            <input type="text" name="patronymic" class="form-control" value="<?= $modelParam('patronymic') ?>">
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Телефон:</label>
                    <input type="text" name="phone" class="form-control phone-mask" value="<?= $modelParam('phone') ?>">
                </div>
                <div class="mb-4">
                    <label class="form-label">Отдел:</label>
                    <select name="department_id" class="form-control">
                        <option value="0">Выберите...</option>
                        <?php foreach ($departments as $department): ?>
                            <option value="<?= $department->id ?>"<?= +$modelParam('department_id') === +$department->id ? ' selected' : '' ?>><?= $department->department ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label">Роль сотрудника:</label>
                    <select name="role" class="form-control">
                        <option value="0">Выберите...</option>
                        <?php foreach ($model->roles() as $role => $roleName): ?>
                            <option value="<?= $role ?>"<?= intval($modelParam('role')) === $role ? ' selected' : '' ?>><?= $roleName ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Пароль:</label>
                    <?php if ($model->isExists()): ?>
                        <div>
                            <button type="button" class="btn btn-secondary" onclick="$(this).hide(); $('[name=password]').show();"><i class="bi bi-lock-fill pe-2"></i>Изменить</button>
                            <input type="text" name="password" class="form-control" style="display: none;" placeholder="Введите новый пароль">
                        </div>
                    <?php else: ?>
                        <input type="text" name="password" class="form-control" placeholder="Введите новый пароль">
                    <?php endif; ?>
                </div>
                <div class="text-center">
                    <a href="<?= SiteRoot('_admin/users') ?>" class="btn btn-link btn-lg"><i class="bi bi-arrow-left pe-2"></i>К списку</a>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill"><i class="bi bi-check-lg pe-2"></i>Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>