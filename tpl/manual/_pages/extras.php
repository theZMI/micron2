<h1>Уведомления, cron и прочее</h1>

<h2>Уведомления пользователям</h2>
<?= DocCode(<<<'CODE'
    // Отправит по всем каналам, которые у пользователя подтверждены и включены. Вернёт число каналов, куда ушло
    SendNotice($user, 'Ваш заказ отправлен', ['telegram', 'email', 'sms']);
    CODE) ?>
<dl class="manual-defs">
    <dt>Почта</dt>
    <dd><code>SendMail()</code> через PHPMailer, ящик - <code>EMAIL_*</code> в <code>.env</code>. В debug-режиме письма пишутся в <code>tmp/sent_emails.txt</code>.</dd>
    <dt>SMS</dt>
    <dd>Через SMSC или SMS.ru (<code>SMSC_*</code>, <code>SMSRU_*</code>). При <code>ENV=local</code> не отправляются, а пишутся в <code>tmp/sms_mockup.log</code>.</dd>
    <dt>Telegram</dt>
    <dd>Бот с токеном <code>TELEGRAM_BOT_TOKEN</code>. Пользователь указывает свой Telegram в профиле и пишет боту <code>/start</code> - бот привязывает чат. Webhook - <code>/_telegram_bot_hook</code>, он принимает запросы только с заголовком <code>X-Telegram-Bot-Api-Secret-Token</code>, поэтому при подключении передайте <code>secret_token=TELEGRAM_WEBHOOK_SECRET</code> в <code>setWebhook</code>.</dd>
    <dt>Push</dt>
    <dd>Через OneSignal (<code>PUSH_ONESIGNAL_*</code>).</dd>
</dl>
<p>Проверить каждый канал можно в админке: «Для техников → Отправить тестовое письмо» и соседние пункты (<code>/_admin/tests/*</code>).</p>

<h2>Задачи по расписанию (cron)</h2>
<p>Задачи - обычные компоненты в <code>src/_cron</code>. Открыть их может админ или сервер по ключу <code>CRON_SECRET</code> (параметр <code>key</code> или заголовок <code>X-Cron-Key</code>):</p>
<?= DocCode(<<<'CODE'
    # crontab -e
    0 3 * * *   wget -qO- "https://site.ru/_cron/db_backup?key=CRON_SECRET"            # Бэкап БД в db_backups/
    */10 * * * * wget -qO- "https://site.ru/_cron/pay_system_check?key=CRON_SECRET"    # Зависшие платежи
    0 * * * *   wget -qO- "https://site.ru/_cron/shop_cancel_unpaid?key=CRON_SECRET"  # Брошенные заказы
    0 */6 * * * wget -qO- "https://site.ru/_cron/shop_import?key=CRON_SECRET"         # Импорт товаров
    0 9 * * *   wget -qO- "https://site.ru/_cron/free_space_checker?key=CRON_SECRET"  # Место на диске
    CODE, 'bash') ?>
<p>Своя задача - файл <code>src/_cron/имя.php</code>. Все задачи можно запустить вручную из админки («Для техников»).</p>

<h2>Капча и коды подтверждения</h2>
<ul>
    <li>Капча-картинка: <code>GenNewCaptchaCode()</code> и <code>CheckCaptchaCode(Post('captcha_code'))</code>, компонент <code>_dev/simple_captcha_code</code>. Код одноразовый - после проверки больше не подходит.</li>
    <li>Google reCAPTCHA: <code>CheckRecaptchaCode(Post('g-recaptcha-response'))</code>, ключи <code>RECAPTCHA_*</code>.</li>
    <li>Одноразовые коды (почта/SMS): <code>GeneratePinCode($user)</code>, <code>CheckPinCode($user, GetCodeFromForm())</code>, поля ввода - <code>_dev/pin_code_block</code>.</li>
</ul>

<h2>Мобильные приложения и PWA</h2>
<p>Сборки apk кладутся в папку <code>apps/</code> с номером версии в имени: <code>apps/1.0.9.apk</code>, <code>apps/1.0.10.apk</code>. Если приложений несколько - каждое в своей подпапке (<code>apps/courier/1.0.2.apk</code>) и дальше передаётся <code>app=courier</code>.</p>
<ul>
    <li><code>/get_app</code> - скачать последнюю версию. Папка <code>apps/</code> снаружи недоступна, а если задан <code>APP_DOWNLOAD_KEY</code>, скачать можно только по ссылке <code>/get_app?key=...</code>.</li>
    <li><code>api/v1.0/get_last_app_version</code> - номер последней версии, чтобы приложение само проверяло обновления. В коде - <code>GetLastAppVersion('courier')</code> (сравнение через <code>version_compare</code>, поэтому 1.0.10 новее 1.0.9).</li>
    <li>PWA: компонент <code>_dev/pwa</code> в <code>&lt;head&gt;</code>; <code>manifest.json</code> и <code>service_worker.js</code> кладутся в корень сайта и подключаются, только если они есть. При <code>ENV=local</code> service worker не регистрируется.</li>
</ul>

<h2>Excel, QR-коды, геолокация</h2>
<ul>
    <li><code>ReadXL($file)</code> - прочитать Excel, <code>DownloadAsXL($rows, $name)</code> - отдать таблицу как Excel, компонент <code>_dev/xl_table</code> - показать таблицу.</li>
    <li><code>CreateQrCode($text [, $pathToSave])</code> - картинка QR-кода: вернёт PNG-данные или сохранит в файл (для отдачи снаружи предусмотрена папка <code>tmp/qrcodes</code>).</li>
    <li><code>GetLocationByIP()</code>, <code>GetCountries()</code>, <code>GetCoordinatesByAddress()</code> - геолокация (при <code>ENV=local</code> - тестовые данные).</li>
</ul>

<h2>Обслуживание сайта</h2>
<ul>
    <li>«Закрыть/открыть сайт» в админке - посетители видят страницу технических работ, админы продолжают работать.</li>
    <li>Редирект с www или на www - <code>core/config/www_redirector.php</code>.</li>
    <li>Бэкапы БД - <code>/_cron/db_backup</code> и кнопка в админке, файлы в <code>db_backups/</code> (снаружи недоступна).</li>
</ul>
