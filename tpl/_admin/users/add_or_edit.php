<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="my-4">
                <span class="pull-start"><?= $model->isExists() ? $modelParam('full_name') : 'Новый пользователь' ?></span>
            </h1>
            <form action="<?= GetCurUrl() ?>" method="post">
                <input type="hidden" name="is_set" value="1">
                <?= $msg ?>
                <div class="mb-3">
                    <label class="form-label">ФИО:</label>
                    <input type="text" name="full_name" class="form-control" value="<?= $modelParam('full_name') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">E-mail:</label>
                    <input type="email" name="email" class="form-control" value="<?= $modelParam('email') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Телефон:</label>
                    <input type="text" name="phone" class="form-control phone-mask" value="<?= $modelParam('phone') ?>">
                </div>
                <div class="mb-3">
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