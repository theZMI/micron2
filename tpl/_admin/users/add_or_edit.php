<div class="container-fluid">
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
                    <label class="form-label">Логин:</label>
                    <input type="text" name="login" class="form-control" value="<?= $modelParam('login') ?>" placeholder="Пусто - такой же, как e-mail">
                    <div class="form-text">Паролей нет: человек входит на сайт и в приложение по логину и одноразовому коду, который приходит на e-mail</div>
                </div>
                <div class="text-center">
                    <a href="<?= SiteRoot('_admin/users') ?>" class="btn btn-link btn-lg"><i class="bi bi-arrow-left pe-2"></i>К списку</a>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill"><i class="bi bi-check-lg pe-2"></i>Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>