<h1>Роутинг и middleware</h1>
<p class="lead">Адрес запроса сопоставляется с компонентом в <code>core/init/router.php</code> (библиотека pecee/simple-router). Для обычных страниц ничего настраивать не нужно: <code>site.ru/a/b</code> подключает компонент <code>a/b</code>.</p>

<h2>Стандартное правило</h2>
<?= DocCode(<<<'CODE'
    site.ru/                 → home (Config('default_component'))
    site.ru/contacts         → src/contacts.php + tpl/contacts.php
    site.ru/shop/cart        → src/shop/cart.php + tpl/shop/cart.php
    site.ru/en/contacts      → то же, но LANG = 'en'
    site.ru/нет_такого       → 404
    CODE, 'plain') ?>
<p>Имя компонента из адреса очищается (<code>FileSys::filenameSecurity</code>), поэтому адреса вида <code>../../.env</code> не сработают. Компонент можно открыть по адресу, только если ни одна часть пути не начинается с <code>_</code>. Примеры (<code>examples/*</code>) открываются только при <code>DEBUG_MODE=1</code>.</p>

<h2>Особые маршруты</h2>
<div class="manual-table-wrap">
<table>
    <tr><th>Адрес</th><th>Middleware</th><th>Что делает</th></tr>
    <tr><td><code>/_admin/*</code></td><td><code>AdminAuthMiddleware</code></td><td>Админка: свой главный шаблон, вход только для админа</td></tr>
    <tr><td><code>/_cron/*</code></td><td><code>CronAuthMiddleware</code></td><td>Задачи по расписанию: админ или ключ <code>CRON_SECRET</code></td></tr>
    <tr><td><code>/_telegram_bot_hook</code></td><td><code>TelegramWebhookMiddleware</code></td><td>Webhook бота, только с секретным заголовком</td></tr>
    <tr><td><code>/_crop_image</code></td><td><code>CommonMiddleware</code></td><td>Обрезка картинок, только для авторизованных</td></tr>
    <tr><td><code>/api/v1.0/users/*</code></td><td><code>PrepareApiMiddleware</code>, <code>CheckApiUserAuthMiddleware</code></td><td>API, нужен токен пользователя</td></tr>
    <tr><td><code>/api/v1.0/*</code></td><td><code>PrepareApiMiddleware</code></td><td>API без авторизации</td></tr>
    <tr><td><code>/user/*</code></td><td><code>PrepareUserMiddleware</code>, <code>CheckUserAuthMiddleware</code></td><td>Кабинет пользователя</td></tr>
    <tr><td>всё остальное</td><td><code>CommonMiddleware</code></td><td>Стандартное правило</td></tr>
</table>
</div>
<p><code>CommonMiddleware</code> выполняется для всех страниц: загружает текущего пользователя и админа в <code>$g_user</code> / <code>$g_admin</code>, задаёт константы <code>IS_USER_AUTH</code>, <code>IS_ADMIN_AUTH</code> и др., отклоняет изменяющие запросы с чужих сайтов (CSRF).</p>

<h2>Свой маршрут</h2>
<p>Например, красивый адрес товара <code>site.ru/product/15</code>. Добавьте маршрут в <code>core/init/router.php</code> перед стандартным правилом:</p>
<?= DocCode(<<<'CODE'
    SimpleRouter::group(['middleware' => [CommonMiddleware::class]], function () {
        SimpleRouter::get('/product/{id}', function ($id) {
            IncludeCom('shop/product&id=' . (int)$id); // Контроллер товара берёт id через Get('id')
        })->where(['id' => '[0-9]+']);
    });
    CODE) ?>

<h2>Свой middleware</h2>
<p>Middleware - класс в папке <code>middleware</code> с методом <code>handle()</code>. Он выполняется до компонента. Пример: пускать в раздел только пользователей с подтверждённой почтой.</p>
<?= DocCode(<<<'CODE'
    <?php

    use Pecee\Http\Middleware\IMiddleware;
    use Pecee\Http\Request;

    class CheckEmailVerifiedMiddleware implements IMiddleware
    {
        public function handle(Request $request): void
        {
            global $g_user;
            if (!$g_user->is_email_verified) {
                UrlRedirect::go(SiteRoot('user/settings'));
            }
        }
    }
    CODE, 'php', 'middleware/CheckEmailVerifiedMiddleware.php') ?>
<p>И подключите его к нужной группе маршрутов в <code>core/init/router.php</code> (после <code>CommonMiddleware</code>, чтобы пользователь уже был загружен).</p>

<h2>Второй путь: init-файл</h2>
<p>Если код нужно выполнять на каждый запрос, создайте файл в <code>core/init/</code> - он подключится сам после стандартных init-файлов. Если же нужен только для части адресов, то либо оформите его как middleware, либо проверьте адрес прямо в файле (<code>GetQuery()</code>).</p>
