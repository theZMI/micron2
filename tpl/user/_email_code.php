<div class="email-code-form">
    <h1 class="text-center mb-4">Введите код</h1>
    <form action="<?= SiteRoot('user/login') ?>" method="post">
        <input type="hidden" name="is_check_email_code" value="1">
        <?= $msg ?>
        <div class="mb-5">
            <input type="text" name="user_code" class="form-control form-control-lg is-code-input text-center" inputmode="numeric" autocomplete="one-time-code" autofocus
                   placeholder="<?= str_repeat('✱', UserAuthCodeModel::CODE_LENGTH) ?>" maxlength="<?= UserAuthCodeModel::CODE_LENGTH ?>" aria-label="Код из письма">
            <p class="text-muted text-center mt-2">Мы отправили код на e-mail: <strong><?= htmlspecialchars($sentTo) ?></strong></p>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg btn-min-width rounded-pill">
                Войти<i class="ms-2 bi bi-arrow-right"></i>
            </button>
        </div>
    </form>
    <form action="<?= SiteRoot('user/login') ?>" method="post" class="text-center mt-4">
        <input type="hidden" name="is_resend_email_code" value="1">
        <button type="submit" class="btn btn-link text-dark">Отправить код ещё раз</button><br>
        <a href="<?= SiteRoot('user/login') ?>" class="btn btn-link text-dark">Другой логин</a>
    </form>
</div>
