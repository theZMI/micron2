<div class="login-form-wrapper">
    <div class="login-form">
        <form action="<?= GetCurUrl('back_url=' . M_DELETE_PARAM) ?>" method="post" class="login-form-form">
            <input type="hidden" name="is_login" value="1">
            <span class="login-form-icon mb-4">
                <a href="<?= SiteRoot() ?>" class="text-black"><i class="bi bi-lock-fill"></i></a>
            </span>
            <?= $msg ?>
            <div class="form-floating input-with-bottom-line-only mb-4">
                <input type="text" class="form-control" name="login" id="floatingLogin" placeholder="Логин" value="<?= Post('login') ?>" autocomplete="username" autocapitalize="off">
                <label for="floatingLogin">Логин (обычно это ваш e-mail)</label>
            </div>
            <p class="text-secondary small text-center mt-n2 mb-4">Пароль не нужен - мы отправим на почту код для входа</p>
            <div class="text-center pt-3 mb-5">
                <button type="submit" class="btn btn-primary btn-lg btn-min-width rounded-pill">
                    Войти<i class="ms-2 bi bi-arrow-right"></i>
                </button>
            </div>
            <div class="text-center">
                <a href="<?= SiteRoot('user/registration') ?>" class="btn btn-link text-dark">Регистрация</a>
            </div>
        </form>
    </div>
</div>
