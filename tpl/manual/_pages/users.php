<h1>Пользователи, кабинет и API</h1>

<h2>Пользователи на сайте</h2>
<ul>
    <li>Регистрация - <code>/user/registration</code>, вход - <code>/user/login</code>, выход - <code>/user/logout</code>.</li>
    <li>Паролей у пользователей нет. Вход и на сайте, и в приложении - по логину и 6-значному одноразовому коду, который приходит на e-mail. Логин по умолчанию равен e-mail и меняется вместе с ним; другой логин может задать админ.</li>
    <li>На сайте: человек вводит логин, получает код, вводит его. Регистрация устроена так же: после заполнения формы приходит код подтверждения. Форма ввода кода - общий служебный компонент <code>user/_email_code</code>, а какой пользователь ждёт код, хранится в сессии (в форме нет ни логина, ни e-mail; адрес показывается частично: <code>a***@example.com</code>).</li>
    <li>Если при регистрации указан уже зарегистрированный e-mail, ошибки нет: на него отправляется код для входа (имя и телефон существующего пользователя не меняются).</li>
    <li>Кабинет - <code>/user/dashboard</code>, «Мои заказы» - <code>/user/orders</code>, настройки - <code>/user/settings</code>. Страницы <code>/user/*</code> открываются только авторизованным (<code>CheckUserAuthMiddleware</code>), у кабинета свой внутренний шаблон <code>user/_main_tpl</code>.</li>
    <li>Если вход начался из магазина (или другой страницы), после него покупатель возвращается обратно: <code>user/login?back_url=...</code>, <code>RememberAuthBackUrl()</code>. Возврат возможен только на страницы своего сайта.</li>
</ul>
<p>В любом компоненте текущий пользователь доступен как <code>$g_user</code> (<code>UserModel</code>):</p>
<?= DocCode(<<<'CODE'
    <?php if (IS_USER_AUTH): ?>
        Здравствуйте, <?= $g_user->first_name ?>!
    <?php else: ?>
        <a href="<?= SiteRoot('user/login') ?>">Войти</a>
    <?php endif; ?>
    CODE) ?>

<h2>Коды и сессии</h2>
<ul>
    <li>Коды - <code>UserAuthCodeModel</code>: 6 цифр, действуют 15 минут, 5 попыток на код, одноразовые, новый можно запросить не чаще раза в минуту (новый код отменяет прежний). Настройки - <code>core/config/user_model.php</code>.</li>
    <li>После входа выдаётся случайный токен (таблица <code>auth_tokens</code>, в БД хранится только его sha256): на сайте - в HttpOnly-cookie с <code>SameSite=Lax</code>, в API - в ответе <code>api/v1.0/login</code>.</li>
    <li>Токен живёт год. Когда человек впервые подтверждает e-mail кодом, все его прежние сессии закрываются (на случай, если кто-то вписал чужой e-mail в свой аккаунт).</li>
    <li>Защита от перебора: после 10 неверных кодов (или неизвестных логинов) на один логин или 30 с одного IP за 15 минут вход в приложение блокируется (<code>login_attempts</code>, лимиты - <code>core/config/security.php</code>).</li>
    <li>Удалённые пользователи (<code>STATUS_DELETE</code>) войти не могут.</li>
    <li>Смена e-mail, телефона или Telegram сбрасывает их подтверждение. Пустой или неверный e-mail через API задать нельзя - иначе человек не сможет войти.</li>
</ul>

<h2>REST API</h2>
<p>Компоненты API лежат в <code>src/api/v1.0</code> и открываются по адресу <code>/api/v1.0/...</code>. Ответ - JSON вида <code>{"is_success": true, "is_error": false, "data": ...}</code>.</p>
<?= DocCode(<<<'CODE'
    <?php

    $helloMessage = "I'm alive! Right now: " . gmdate('d-m-Y H:i:s') . ' UTC';
    (new ApiResponse())->normal($helloMessage);          // 200 и is_success = true
    // (new ApiResponse())->error('Неверные данные', 400); // Ошибка
    CODE, 'php', 'src/api/v1.0/ping.php') ?>

<h3>Готовые методы</h3>
<div class="manual-table-wrap">
<table>
    <tr><th>Метод</th><th>Что делает</th></tr>
    <tr><td><code>api/v1.0/ping</code></td><td>Проверка связи</td></tr>
    <tr><td><code>api/v1.0/send_login_code</code></td><td>Вход, шаг 1: <code>login</code> → код уходит на e-mail. Ответ: <code>sent_to</code> (адрес частично), <code>code_length</code>, <code>resend_after</code> (через сколько секунд можно запросить снова), <code>ttl</code>, <code>is_sent</code> (false - код уже отправлен недавно)</td></tr>
    <tr><td><code>api/v1.0/login</code></td><td>Вход, шаг 2: <code>login</code> и <code>code</code> → данные пользователя и <code>token</code></td></tr>
    <tr><td><code>api/v1.0/logout</code></td><td>Выход (токен перестаёт действовать)</td></tr>
    <tr><td><code>api/v1.0/users/{id}</code></td><td>Данные пользователя; пользователь может читать и менять только свои</td></tr>
    <tr><td><code>api/v1.0/users/count</code></td><td>Количество активных пользователей</td></tr>
    <tr><td><code>api/v1.0/get_last_app_version</code></td><td>Последняя версия мобильного приложения</td></tr>
</table>
</div>
<p>Методы в <code>api/v1.0/users/*</code> требуют авторизации: токен передаётся в заголовке <code>API-User-Auth-Token</code>, и пользователь загружается в <code>$g_user</code>. Остальные методы доступны без токена. Примеры запросов - на странице «REST API» в разделе «Компоненты» (при <code>DEBUG_MODE=1</code>).</p>

<h3>Свой метод</h3>
<p>Метод без авторизации - файл <code>src/api/v1.0/имя.php</code>. С авторизацией - в <code>src/api/v1.0/users/</code> или в своей группе маршрутов с <code>CheckApiUserAuthMiddleware</code> (<code>core/init/router.php</code>).</p>
<p>Кросс-доменные запросы к API (с другого сайта или из веб-приложения) разрешены только доменам из <code>CORS_ALLOWED_ORIGINS</code>.</p>

<h3>Клиент на JS</h3>
<p>В <code>i/ts</code> есть клиент API (<code>libraries/Api.js</code>, очередь запросов <code>ApiQueue.js</code>) и модели на стороне браузера (<code>models/UserModel.ts</code>) - удобно для приложений на Vue и мобильных приложений.</p>

<h2>Внешний API</h2>
<p>Если сайт сам ходит в чужой API, используйте <code>ApiClient</code> и <code>ApiModel</code> (адрес, токен и лог - <code>API_CLIENT_*</code> в <code>.env</code>):</p>
<?= DocCode(<<<'CODE'
    $response = ApiClient::getInstance()->get('users/5');
    $name     = ApiClient::prepareResponse($response, 'first_name', 'Без имени');
    CODE) ?>
