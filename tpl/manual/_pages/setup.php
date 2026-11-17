<h1>Установка</h1>
<p class="lead">Сайт на Micron - это папка с движком, файл настроек <code>.env</code> и зависимости composer. Удобнее всего запускать его в готовом docker-окружении (Apache, PHP 8.2, MySQL 8, phpMyAdmin).</p>

<h2>Вариант 1: docker-окружение (рекомендуется)</h2>
<p>Рядом с сайтом лежит инфраструктура: <code>docker-compose.yml</code>, <code>Makefile</code>, образ <code>php-apache</code> и папка <code>databases</code>, в которой MySQL хранит данные. Сам сайт кладётся в папку <code>site/</code>.</p>
<ol>
    <li>Положите движок в папку <code>site/имя_проекта</code>.</li>
    <li>В <code>.env</code> рядом с <code>docker-compose.yml</code> задайте <code>APP_DIR</code> (папка сайта, например <code>./site/micron</code>), <code>APP_PORT</code> (порт сайта) и при желании <code>DB_PORT</code> (порт MySQL для программ на вашем компьютере).</li>
    <li>Соберите и запустите контейнеры:</li>
</ol>
<?= DocCode(<<<'CODE'
    make build   # Один раз: собрать образ
    make start   # Запустить (сайт - localhost:APP_PORT)
    make stop    # Остановить
    make logs    # Логи контейнеров
    make bash    # Консоль внутри php-контейнера
    make mysql   # Консоль MySQL
    CODE, 'bash') ?>
<p>Подключение к БД из сайта: <code>mysqli://user:pass@mysqldb/app_db?charset=utf8mb4</code>. Из программ на компьютере (Sequel Ace, DataGrip): хост <code>127.0.0.1</code>, порт <code>DB_PORT</code>.</p>
<p>XDebug по умолчанию выключен (он замедляет каждый запрос). Чтобы включить, добавьте <code>XDEBUG_MODE=debug</code> в <code>.env</code> окружения и выполните <code>make start</code>. Отладчик (PhpStorm) должен слушать порт 9003, маппинг путей: <code>site/&lt;проект&gt;</code> → <code>/var/www/html</code>.</p>
<div class="manual-note is-warning">
    <p>Для разных проектов задавайте разные порты. Браузеры не открывают сайты на некоторых «служебных» портах (21, 22, 25, 465, 587, 6000, 6665-6669 и др.) - список есть в README окружения.</p>
</div>

<h2>Вариант 2: обычный хостинг</h2>
<ol>
    <li>Загрузите файлы движка в корень сайта (файл <code>index.php</code> должен оказаться в корне).</li>
    <li>Нужны PHP 8.2+, MySQL 8 (или MariaDB) и Apache с <code>mod_rewrite</code> (для NGINX смотрите раздел <a href="<?= $url('servers') ?>">Apache2 и NGINX</a>).</li>
    <li>Выставьте права на запись для папок <code>tmp</code> (временные файлы, склеенные css/js, логи) и <code>upl</code> (загружаемые файлы).</li>
</ol>

<h2>Настройка сайта</h2>
<ol>
    <li>Скопируйте <code>.env.example</code> в <code>.env</code> и задайте свои значения: адрес сайта <code>SITE_ROOT_URL</code>, подключение к БД <code>DATABASE_DSN</code>, режим <code>DEBUG_MODE</code>, домен для cookie и пр. Подробно - в разделе <a href="<?= $url('config') ?>">Конфигурация и .env</a>.</li>
    <li>Обязательно задайте <code>ADMIN_DEFAULT_PASSWORD</code> (без него первый админ не создастся) и <code>CRON_SECRET</code> (сгенерировать: <code>openssl rand -hex 32</code>).</li>
    <li>Установите зависимости: <code>composer install</code> (в docker: <code>make bash</code>, затем <code>composer install</code>).</li>
    <li>Соберите css/js: в папке <code>i/webpack</code> выполните <code>npm install</code> и <code>npm run build</code> (или <code>npm run dev</code> для разработки). Готовые файлы в <code>i/webpack/dist</code> не хранятся в git, поэтому на сервере сборку нужно делать после каждого обновления стилей и скриптов.</li>
</ol>
<p>Таблицы в БД создаются сами при первом обращении к моделям, а изменения структуры применяются <a href="<?= $url('migrations') ?>">миграциями</a> при первом запросе после обновления кода.</p>
<p>Все ключи и пароли (почта, SMS, push, Telegram, платёжные системы) хранятся только в <code>.env</code> - в коде их быть не должно.</p>

<h2>Красивые адреса</h2>
<p>Все запросы (кроме разрешённых файлов) направляются в <code>/index.php</code> правилом из <code>.htaccess</code>, имя страницы передаётся в параметре <code>micron_query</code>. Пользователь видит адрес <code>site.ru/about</code>, а движок получает <code>/index.php?micron_query=about</code>. Файл <code>.htaccess</code> уже лежит в корне - ничего распаковывать не нужно.</p>

<h2>Устранение неполадок</h2>

<h3>Запускаю сайт, а у меня белый экран</h3>
<p>Проверьте права на запись в <code>tmp</code> и что выполнен <code>composer install</code> (нужна папка <code>vendor</code>). Включите <code>DEBUG_MODE=1</code> в <code>.env</code>, чтобы увидеть ошибку на экране, а на боевом сервере смотрите лог: <code>tmp/</code> + имя из <code>ERROR_LOG_FILE</code> и раздел админки «Лог ошибок».</p>

<h3>Нет стилей или скриптов</h3>
<p>Скорее всего, не собран webpack: выполните <code>npm run build</code> в <code>i/webpack</code>. Если проблема в файлах, которые склеивает ExtraPacker, очистите папку <code>tmp/_auto_merge_css_js</code> - она пересоздастся.</p>

<h3>Не получается войти в админку</h3>
<p>Убедитесь, что в <code>.env</code> задан <code>ADMIN_DEFAULT_PASSWORD</code> (первый админ создаётся только если в БД ещё нет админов) и что браузер сохраняет cookie. После 10 неудачных попыток вход по этому логину блокируется на 15 минут.</p>
