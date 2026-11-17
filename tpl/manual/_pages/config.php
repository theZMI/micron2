<h1>Конфигурация и .env</h1>
<p class="lead">Настройки делятся на два вида: то, что зависит от окружения (локальная копия, боевой сервер), лежит в <code>.env</code>, а настройки самого сайта - в <code>core/config/*</code>.</p>

<h2>.env - настройки окружения</h2>
<p>Файл <code>.env</code> в корне сайта читается при каждом запросе (библиотека phpdotenv), значения доступны через <code>Env('ИМЯ')</code>. Он не хранится в git: на локальной копии и на сервере он свой. Образец со всеми переменными - <code>.env.example</code>.</p>
<div class="manual-table-wrap">
<table>
    <tr><th>Переменная</th><th>Назначение</th></tr>
    <tr><td><code>ENV</code></td><td><code>local</code> - локальная разработка (SMS пишутся в <code>tmp/sms_mockup.log</code>, геолокация - заглушка, PWA не регистрируется), <code>production</code> - боевой сервер</td></tr>
    <tr><td><code>DEBUG_MODE</code></td><td><code>1</code> - режим разработки (ошибки на экране, debug-панель, письма в файл), <code>0</code> - боевой режим</td></tr>
    <tr><td><code>ERROR_LOG_FILE</code></td><td>Имя лог-файла ошибок PHP в <code>tmp/</code></td></tr>
    <tr><td><code>SITE_ROOT_URL</code></td><td>Адрес сайта со слешем в конце: <code>https://site.ru/</code>. От него строит ссылки <code>SiteRoot()</code></td></tr>
    <tr><td><code>DOMAIN_COOKIE</code></td><td>Домен для cookie (пусто - текущий)</td></tr>
    <tr><td><code>CORS_ALLOWED_ORIGINS</code></td><td>Домены, которым разрешены кросс-доменные запросы (через запятую)</td></tr>
    <tr><td><code>TRUSTED_PROXIES</code></td><td>IP прокси перед сайтом (nginx, балансировщик) - только от них принимается <code>X-Forwarded-For</code></td></tr>
    <tr><td><code>DATABASE_DSN</code>, <code>DATABASE_LOG_FILE</code></td><td>Подключение к БД и лог ошибочных запросов</td></tr>
    <tr><td><code>ADMIN_DEFAULT_LOGIN</code>, <code>ADMIN_DEFAULT_PASSWORD</code></td><td>Первый админ (создаётся, если в БД ещё нет админов)</td></tr>
    <tr><td><code>ADMIN_SALT</code></td><td>Нужна только для входа админов со старыми md5-хешами паролей (у пользователей паролей нет)</td></tr>
    <tr><td><code>CRON_SECRET</code></td><td>Ключ для запуска <code>/_cron/*</code> по расписанию</td></tr>
    <tr><td><code>EMAIL_*</code></td><td>Почтовый ящик для отправки писем (SMTP)</td></tr>
    <tr><td><code>TELEGRAM_*</code>, <code>PUSH_ONESIGNAL_*</code>, <code>SMSC_*</code>, <code>SMSRU_*</code></td><td>Уведомления: Telegram-бот, push, SMS</td></tr>
    <tr><td><code>PAY_SYSTEM_*</code></td><td>Ключи ЮKassa и Сбера</td></tr>
    <tr><td><code>SHOP_MANAGER_EMAILS</code>, <code>SHOP_IMPORT_GOOGLE_SHEET_URL</code></td><td>Кому писать об оплаченных заказах; Google-таблица для импорта товаров</td></tr>
    <tr><td><code>RECAPTCHA_*</code></td><td>Ключи Google reCAPTCHA</td></tr>
    <tr><td><code>API_CLIENT_*</code></td><td>Клиент внешнего API (<code>ApiClient</code>, <code>ApiModel</code>)</td></tr>
    <tr><td><code>APP_DOWNLOAD_KEY</code></td><td>Если задан - apk-приложение скачивается только по ссылке с ключом</td></tr>
</table>
</div>
<div class="manual-note is-warning">
    <p>Все пароли и ключи храните только в <code>.env</code>. Сам файл снаружи недоступен (это запрещено в <code>.htaccess</code>), но никогда не добавляйте его в git.</p>
</div>

<h2>core/config - настройки сайта</h2>
<p>Все настройки хранятся в глобальном массиве <code>$g_config</code>, работать с ним удобно функцией <code>Config()</code>. Первым грузится <code>main.php</code>, затем <code>languages.php</code>, затем остальные файлы папки (и подпапок). Любую настройку можно поменять «на лету» в контроллере - например, отключить главный шаблон для одной страницы.</p>

<h3>main.php - главные настройки движка</h3>
<dl class="manual-defs">
    <dt><code>charset</code></dt>
    <dd>Кодировка ответа: <code>utf-8</code>.</dd>
    <dt><code>default_component</code></dt>
    <dd>Компонент, загружаемый при заходе на сайт без указания страницы: <code>home</code>.</dd>
    <dt><code>main_tpl</code></dt>
    <dd>Компонент главного шаблона: <code>_main_tpl</code>. Чтобы показать страницу в другом шаблоне, поменяйте в её контроллере: <code>Config('main_tpl', '_landing_tpl')</code>.</dd>
    <dt><code>is_load_in_main_tpl</code></dt>
    <dd>Выводить ли страницу внутри главного шаблона. Для JSON, файлов и AJAX-ответов: <code>Config('is_load_in_main_tpl', false)</code>.</dd>
    <dt><code>prepare_functions</code></dt>
    <dd>Список функций пост-обработки HTML перед отправкой в браузер: склейка <code>&lt;head&gt;</code>, ExtraPacker, подсветка кода, debug-панель. Сюда можно добавить свою функцию.</dd>
    <dt><code>html_container_attrs</code></dt>
    <dd>Атрибуты тега <code>&lt;html&gt;</code> главного шаблона (например, css-класс). Удобно поменять в контроллере для страницы с отдельным оформлением.</dd>
</dl>

<h3>Другие файлы</h3>
<dl class="manual-defs">
    <dt><code>languages.php</code></dt>
    <dd>Список языков сайта и язык по умолчанию - смотрите <a href="<?= $url('langs') ?>">Мультиязычность</a>.</dd>
    <dt><code>php_ini.php</code></dt>
    <dd>Настройки php.ini, которые выставляются при старте: вывод и логирование ошибок, <code>memory_limit</code> (32M), <code>max_execution_time</code> (15 с), безопасные параметры сессии (HttpOnly, SameSite, strict mode). Там же часовой пояс <code>DEFAULT_TIME_ZONE</code>.</dd>
    <dt><code>db.php</code></dt>
    <dd>Подключения к БД, логирование и кеширование запросов - смотрите <a href="<?= $url('db') ?>">Работа с моделями и БД</a>.</dd>
    <dt><code>error_logger.php</code></dt>
    <dd><code>use_error_logger</code> - перехватывать ли ошибки своим обработчиком, <code>log_errors_into_db</code> - писать ли их в БД (раздел «Лог ошибок» в админке), <code>stop_on_warnings</code> - останавливать ли скрипт на warning/notice (по умолчанию - только в debug-режиме).</dd>
    <dt><code>extrapacker.php</code>, <code>webpack.php</code></dt>
    <dd>Склейка css/js и подключение сборки webpack - смотрите <a href="<?= $url('assets') ?>">CSS и JS</a>.</dd>
    <dt><code>security.php</code></dt>
    <dd>Лимиты защиты от перебора: паролей админов и кодов входа пользователей.</dd>
    <dt><code>user_model.php</code></dt>
    <dd>Коды из письма для входа и регистрации: время жизни, число попыток, как часто можно запросить новый.</dd>
    <dt><code>send_mail.php</code></dt>
    <dd>Настройки PHPMailer; <code>send_to_file</code> - писать письма в файл вместо отправки (по умолчанию в debug-режиме).</dd>
    <dt><code>shop.php</code>, <code>pay_systems/*</code></dt>
    <dd>Магазин и платёжные системы.</dd>
    <dt><code>uploader*.php</code>, <code>browser_data_cache.php</code>, <code>mime_types.php</code></dt>
    <dd>Загрузка файлов и отдача файлов браузеру.</dd>
    <dt><code>_admin/*</code></dt>
    <dd>Меню и настройки админки.</dd>
    <dt><code>notificators/*</code>, <code>recaptcha.php</code>, <code>www_redirector.php</code>, <code>paginator.php</code>, <code>apps.php</code>...</dt>
    <dd>Настройки отдельных возможностей. Каждый файл короткий и прокомментирован.</dd>
</dl>

<h2>Свои настройки</h2>
<p>Создайте файл <code>core/config/my_feature.php</code> - он подключится сам:</p>
<?= DocCode(<<<'CODE'
    <?php

    Config('my_feature', [
        'per_page'  => 20,
        'api_token' => Env('MY_FEATURE_TOKEN'), // Секреты - только из .env
    ]);
    CODE, 'php', 'core/config/my_feature.php') ?>
<p>И используйте в любом месте: <code>Config(['my_feature', 'per_page'])</code>.</p>
