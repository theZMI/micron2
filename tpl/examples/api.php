<h1 class="my-4">API endpoints</h1>
<a href="<?= SiteRoot('api/v1.0/ping') ?>">ping</a><br>
<a href="<?= SiteRoot('api/v1.0/ping_error') ?>">ping_error</a><br>
<a href="<?= SiteRoot('api/v1.0/get_last_app_version') ?>">get_last_app_version</a><br>
<p class="mt-3">
    Авторизация без пароля, в два шага:<br>
    1) <code>POST api/v1.0/send_login_code</code> (поле <code>login</code>, по умолчанию это e-mail) - на почту уходит код;<br>
    2) <code>POST api/v1.0/login</code> (поля <code>login</code> и <code>code</code>) возвращает <code>token</code>.<br>
    Дальше передавайте его в заголовке <code>API-User-Auth-Token</code>, например: <code>GET api/v1.0/users/{id}</code>.<br>
    Выход (отзыв токена): <code>POST api/v1.0/logout</code>.
</p>
