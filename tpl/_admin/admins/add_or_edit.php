<div class="container-fluid">
    <div class="row">
        <div class="col">

            <h1 class="mt-4 mb-4">
                <span class="pull-start"><h1><?= $admin_id ? "Редактирование администратора" : "Новый администратор" ?></h1></span>
            </h1>
            <form action="<?= GetCurUrl(); ?>" method="post">
                <input type="hidden" name="is_apply" value="1"/>
                <?= $msg ?>
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="form-label" for="inputLogin">Логин <span class="required">*</span></label>
                            <input type="text" class="form-control" id="inputLogin" autocomplete="on" name="login" value="<?= $login ?>" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="form-label" for="inputName">Имя администратора</label>
                            <input type="text" class="form-control" id="inputName" autocomplete="on" name="name" value="<?= $name ?>" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="form-label" for="inputPassword">Пароль <span class="required">*</span></label>
                            <input type="password" class="form-control" id="inputPassword" autocomplete="on" name="pwd" autocomplete="off" placeholder="<?= $admin_id ? 'Не вводите пароль если не собираетесь его менять' : '' ?>">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="form-label" for="inputPassword2">Повторите пароль <span class="required">*</span></label>
                            <input type="password" class="form-control" id="inputPassword2" autocomplete="on" name="pwd2" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="form-label" for="inputEmail">E-mail</label>
                            <input type="text" class="form-control" id="inputEmail" autocomplete="on" name="email" value="<?= $email ?>" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="form-label" for="inputPhone">Телефон</label>
                            <input type="text" class="form-control" id="inputPhone" autocomplete="on" name="phone" value="<?= $phone ?>" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="inputDesc">Описание учётной записи</label>
                    <textarea name="desc" id="inputDesc" class="form-control" rows="3"><?= $desc ?></textarea>
                </div>

                <div class="text-center">
                    <a href="<?= SiteRoot('_admin/admins') ?>" class="btn btn-link btn-lg"><i class="bi bi-arrow-left pe-2"></i>К списку</a>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill"><i class="bi bi-check-lg pe-2"></i>Сохранить</button>
                </div>
            </form>

        </div>
    </div>
</div>