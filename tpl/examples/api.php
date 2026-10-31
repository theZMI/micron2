<h1 class="my-4">API endpoints</h1>
<a href="<?= SiteRoot('api/v1.0/ping') ?>">ping</a><br>
<a href="<?= SiteRoot('api/v1.0/ping_error') ?>">ping_error</a><br>
<a href="<?= SiteRoot('api/v1.0/get_last_app_version') ?>">get_last_app_version</a><br>
<p class="mt-3">
    Авторизация: <code>POST api/v1.0/login</code> (поля <code>login_or_name</code>, <code>password</code>) возвращает <code>token</code>.<br>
    Дальше передавайте его в заголовке <code>API-User-Auth-Token</code>, например: <code>GET api/v1.0/users/{id}</code>.<br>
    Выход (отзыв токена): <code>POST api/v1.0/logout</code>.
</p>
