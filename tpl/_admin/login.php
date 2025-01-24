<div class="login-form-wrapper">
    <div class="login-form">
        <form action="<?= GetCurUrl() ?>" method="post" class="login-form-form">
            <input type="hidden" name="is_login" value="1">
            <span class="login-form-icon mb-4">
                <i class="bi bi-lock-fill"></i>
            </span>
            <?= $msg ?>
            <div class="form-floating mb-4">
                <input type="text" class="form-control" name="login_or_name" value="<?= Post('login_or_name') ?>" id="floatingLogin" placeholder="Логин или ФИО">
                <label for="floatingLogin">Логин или ФИО</label>
            </div>
            <div class="form-floating mb-4 position-relative">
                <input type="password" class="form-control" name="password" id="floatingPassword" placeholder="Пароль">
                <label for="floatingPassword">Пароль</label>
                <button
                        class="pwd-shower btn btn-link text-dark position-absolute top-50 end-0 translate-middle-y"
                        type="button"
                        data-field="#floatingPassword"
                        data-class-show="bi-eye-fill"
                        data-class-hide="bi-eye-slash-fill"
                >
                    <span class="bi bi-eye-slash-fill"></span>
                </button>
            </div>
            <div class="text-center pt-3 mb-5">
                <button type="submit" class="btn btn-primary btn-lg btn-min-width rounded-pill">
                    Войти<i class="ms-2 bi bi-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</div>