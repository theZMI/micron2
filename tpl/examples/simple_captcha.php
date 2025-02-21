<h1 class="mt-4 mb-4">Простая captcha:</h1>
<form action="<?= GetCurUrl() ?>" method="post">
    <input type="hidden" name="is_set" value="1">
    <?= $msg ?>
    <div class="mb-3">
        <label for="username" class="form-label">Username:</label>
        <input type="text" class="form-control" id="username" name="username" value="<?= Post('username') ?>">
    </div>
    <div class="mb-3">
        <label for="captcha-code" class="form-label">Проверочный код:</label>
        <br>
        <img src="<?= $captcha->getImage(); ?>" alt="Captcha Code" />
        <input type="text" class="form-control text-uppercase" id="captcha-code" name="captcha_code" value="<?= Post('captcha_code') ?>">
    </div>
    <button type="submit" class="btn btn-primary btn-lg">Отправить</button>
</form>