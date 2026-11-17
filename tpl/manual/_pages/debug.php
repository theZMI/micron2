<h1>Отладка и обработка ошибок</h1>
<p class="lead">В Micron встроены перехват ошибок с полным backtrace, лог ошибок в файле и в админке и debug-панель с запросами к БД и бенчмарком. Режим работы задаётся в <code>.env</code>.</p>

<h2>Режимы</h2>
<div class="manual-table-wrap">
<table>
    <tr><th></th><th><code>DEBUG_MODE=1</code> (разработка)</th><th><code>DEBUG_MODE=0</code> (сервер)</th></tr>
    <tr><td>Ошибки</td><td>На экране с backtrace</td><td>Посетитель видит страницу 500, ошибка - в логе</td></tr>
    <tr><td>Warning, notice, deprecated</td><td>Останавливают скрипт - их сразу видно</td><td>Только пишутся в лог</td></tr>
    <tr><td>Debug-панель</td><td>Внизу страницы</td><td>Нет</td></tr>
    <tr><td>Письма</td><td>В файл <code>tmp/sent_emails.txt</code></td><td>Отправляются</td></tr>
    <tr><td>Примеры <code>/examples/*</code></td><td>Доступны</td><td>404</td></tr>
</table>
</div>
<p>Настройки - в <code>core/config/php_ini.php</code> (<code>error_reporting</code>, <code>display_errors</code>, файл лога <code>ERROR_LOG_FILE</code>) и <code>core/config/error_logger.php</code>:</p>
<dl class="manual-defs">
    <dt><code>use_error_logger</code></dt>
    <dd>Перехватывать ли ошибки своим обработчиком (<code>ErrorLogger</code>) - он добавляет к сообщению backtrace, адрес, данные запроса.</dd>
    <dt><code>log_errors_into_db</code></dt>
    <dd>Писать ли ошибки в БД - тогда они видны в админке («Для техников → Лог ошибок»).</dd>
    <dt><code>stop_on_warnings</code></dt>
    <dd>Останавливать ли скрипт на warning/notice/deprecated (по умолчанию - в debug-режиме).</dd>
</dl>
<p>В лог не попадают пароли, токены, ключи и значения cookie - они заменяются на <code>***</code>.</p>

<h2>Debug-панель</h2>
<p>При <code>DEBUG_MODE=1</code> внизу страницы справа выводится свёрнутая панель. В развёрнутом виде в ней видно:</p>
<ul>
    <li>время выполнения и затраченную память;</li>
    <li>все SQL-запросы с временем выполнения и ошибками;</li>
    <li>переменные <code>$_GET</code>, <code>$_POST</code>, <code>$_COOKIE</code>, <code>$_SESSION</code>, <code>$_SERVER</code>;</li>
    <li>подключённые файлы в порядке подключения;</li>
    <li>основные параметры движка: язык, режим, настройки php.ini;</li>
    <li>информацию о php.ini и модулях PHP.</li>
</ul>
<p>В ответы без главного шаблона (JSON, AJAX, файлы) панель не добавляется.</p>

<h2>Бенчмарк</h2>
<p>Клик по времени/памяти в панели открывает бенчмарк: время и память по этапам запроса (запуск движка, роутинг, главный шаблон, пост-обработка), по каждому компоненту и каждому запросу к БД, а также итог отдельно для PHP, БД и внешних сервисов.</p>
<p>Свои замеры - <code>BM('Название')</code> в начале и ещё раз в конце участка кода:</p>
<?= DocCode(<<<'CODE'
    BM('Services / Курс валют'); // Метки "Services / ..." попадают в итог по внешним сервисам
    $rate = ApiClient::getInstance()->get('rates/usd');
    BM('Services / Курс валют');
    CODE) ?>

<h2>Распечатка и логи</h2>
<?= DocCode(<<<'CODE'
    Xmp($order->getData());        // Распечатать массив на странице
    echo VarDump($value);          // С типами
    ToLog('Пришёл webhook: ' . json_encode($data)); // В лог-файл
    CODE) ?>
<p>Ошибочные запросы к БД дополнительно пишутся в <code>tmp/</code> + <code>DATABASE_LOG_FILE</code>, а в админке есть раздел с SQL-ошибками.</p>
<p>Проверить, как сайт показывает ошибки, можно на страницах раздела «Компоненты»: «Ошибка в PHP-коде» и «Ошибка в запросе к БД».</p>

<h2>XDebug</h2>
<p>В docker-окружении XDebug включается переменной <code>XDEBUG_MODE=debug</code> - смотрите <a href="<?= $url('setup') ?>">Установка</a>.</p>
