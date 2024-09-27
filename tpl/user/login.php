<div class="login-form-wrapper">
    <div class="login-form">
        <form action="<?= GetCurUrl('back_url=' . M_DELETE_PARAM) ?>" method="post" class="login-form-form">
            <input type="hidden" name="is_login" value="1">
            <span class="login-form-icon mb-4">
                <a href="<?= SiteRoot() ?>" class="text-black"><i class="bi bi-lock-fill"></i></a>
            </span>
            <?= $msg ?>
            <div class="form-floating input-with-bottom-line-only mb-4">
                <input type="email" class="form-control" name="email" id="floatingEmail" placeholder="E-mail" value="<?= Post('email') ?>">
                <label for="floatingEmail">E-mail</label>
            </div>
            <div class="text-center pt-3 mb-5">
                <button type="submit" class="btn btn-primary btn-lg btn-min-width rounded-pill">
                    Войти<i class="ms-2 bi bi-arrow-right"></i>
                </button>
            </div>
            <div class="text-center">
                <a href="<?= SiteRoot('user/registration') ?>" class="btn btn-link text-dark">Регистрация</a><br>
                <a href="<?= SiteRoot('user/password_forgot') ?>" class="btn btn-link text-dark">Забыли пароль?</a>
            </div>
        </form>
    </div>
</div>
