<div class="password-forgot-form">
    <?php if ($isCheckCodeBlock): ?>
        <h1 class="text-center mb-4">Введите код</h1>
        <form action="<?= SiteRoot('user/password_forgot') ?>" method="post" role="form">
            <input type="hidden" name="is_check_pwd_recover_code" value="1">
            <input type="hidden" name="is_code_check_block" value="1">
            <input type="hidden" name="email" value="<?= Post('email') ?>">
            <?= $msg ?>
            <div class="mb-5">
                <label class="form-label"></label>
                <input type="text" name="user_code" class="form-control form-control-lg is-code-input text-center" placeholder="• • • • •" maxlength="5" value="<?= Post('user_code') ?>">
                <p class="text-muted text-center mt-2">Введите код полученный вами на e-mail: <strong><?= Post('email') ?></strong></p>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg btn-min-width rounded-pill">
                    Отправить<i class="ms-2 bi bi-arrow-right"></i>
                </button>
            </div>
        </form>
    <?php else: ?>
        <h1 class="text-center mb-4">Забыли пароль?</h1>
        <form action="<?= SiteRoot('user/password_forgot') ?>" method="post" role="form">
            <input type="hidden" name="is_pwd_recover_order" value="1">
            <?= $msg ?>
            <div class="mb-5">
                <label class="form-label">Ведите ваш e-mail:</label>
                <input type="email" name="email" class="form-control form-control-lg" value="<?= Post('email') ?>"/>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg btn-min-width ps-4 pe-4 rounded-pill">
                    Отправьте мне код<i class="ms-2 bi bi-arrow-right"></i>
                </button>
            </div>
        </form>
    <?php endif ?>
</div>