<h1>Библиотеки</h1>
<p class="lead">Свои библиотеки движка лежат в папке <code>/lib</code>, сторонние ставятся через composer в <code>/vendor</code>. Классы подключаются автоматически: класс <code>FileLogger</code> ищется в файле <code>FileLogger.php</code> в папках <code>lib</code>, <code>model</code>, <code>trait</code>, <code>middleware</code> (настройка <code>autoload</code> в <code>composer.json</code>). После добавления нового класса автозагрузка работает сразу; если вы меняли <code>composer.json</code>, выполните <code>composer dump-autoload</code>.</p>

<h2>FileSys - работа с файлами</h2>
<p>Пакет <code>thezmi/filesys</code>. Все методы статические.</p>
<dl class="manual-defs">
    <dt><code>string FileSys::filenameSecurity(string $str)</code></dt>
    <dd>Удаляет из строки все символы, которые нельзя использовать в имени файла (включая <code>../</code> и <code>./</code>).</dd>
    <dt><code>bool FileSys::deleteDir(string $dir)</code></dt>
    <dd>Удаляет каталог с подкаталогами.</dd>
    <dt><code>bool FileSys::makeDir(string $path)</code></dt>
    <dd>Создаёт каталог и все каталоги, в которые он вложен.</dd>
    <dt><code>int FileSys::writeFile(string $file, string $data [, bool $append = false])</code></dt>
    <dd>Записывает данные в файл, создав при необходимости сам файл и каталоги. При <code>$append = true</code> дописывает в конец. Возвращает число записанных байт.</dd>
    <dt><code>string FileSys::readFile(string $file)</code></dt>
    <dd>Содержимое файла, если его можно прочитать.</dd>
    <dt><code>array FileSys::readList(string $dir)</code></dt>
    <dd>Список файлов каталога, включая подкаталоги.</dd>
    <dt><code>string FileSys::size(string $file)</code></dt>
    <dd>Размер файла текстом (Kb, Mb, Gb).</dd>
</dl>

<h2>FileLogger - текстовые логи</h2>
<p>Пакет <code>thezmi/filelogger</code>. Корректно работает при повторном открытии одного файла, следит за размером лога и при переполнении архивирует старый лог рядом.</p>
<?= DocCode(<<<'CODE'
    $log = FileLogger::create(BASEPATH . 'tmp/my.log');
    $log->message('Test 1'); // Обычная строка
    $log->error('Test 2');   // Строка с пометкой, что это ошибка
    CODE) ?>
<p>Для быстрой записи есть функция <code>ToLog($msg [, $path])</code>.</p>

<h2>Php - настройка PHP</h2>
<p>Внутренняя библиотека: выставляет локаль, часовой пояс и настройки из <code>Config('php_ini')</code>. Снаружи полезен метод <code>Php::status(int $code)</code> - правильная строка статуса ответа (<code>HTTP/1.1 404 Not Found</code>): <code>header(Php::status(404));</code></p>

<h2>Полезные классы сайта</h2>
<dl class="manual-defs">
    <dt><code>UrlRedirect::go($url)</code></dt>
    <dd>Редирект. Можно и через <code>header()</code>, но класс-надстройка позволяет централизованно что-то поменять или логировать.</dd>
    <dt><code>SessionManager::get/set/has/delete($key)</code></dt>
    <dd>Работа с сессией: сессия открывается только на время чтения или записи и сразу закрывается, поэтому параллельные AJAX-запросы не ждут друг друга.</dd>
    <dt><code>FlashParam</code></dt>
    <dd>Значение, которое переживёт редирект: <code>(new FlashParam())-&gt;set('msg', 'Сохранено')</code>, на следующей странице - <code>-&gt;get('msg')</code>.</dd>
    <dt><code>BreadCrumb</code></dt>
    <dd>Хлебные крошки: <code>BreadCrumb::getInstance()-&gt;add(['link' =&gt; SiteRoot('shop'), 'text' =&gt; 'Каталог'])</code>.</dd>
    <dt><code>Uploader</code></dt>
    <dd>Загрузка файлов с проверкой типа и размера и созданием миниатюр: <code>(new Uploader())-&gt;upload('file', $config)</code>, затем <code>getInf()</code> или <code>errors()</code>. Готовые настройки - <code>core/config/uploader*.php</code>, готовый компонент - <code>_ajax_upload</code>.</dd>
    <dt><code>Downloader</code>, <code>ForceDownload()</code></dt>
    <dd>Отдача файла на скачивание.</dd>
    <dt><code>BrowserDataCache</code></dt>
    <dd>Отдаёт файлы из разрешённых папок с кешированием в браузере (ETag).</dd>
    <dt><code>ApiClient</code></dt>
    <dd>Клиент внешнего REST API: <code>ApiClient::getInstance()-&gt;get('users/5')</code>, <code>post()</code>, <code>put()</code>, <code>patch()</code>, <code>delete()</code>. Адрес, токен и лог задаются в <code>.env</code> (<code>API_CLIENT_*</code>).</dd>
    <dt><code>ApiResponse</code></dt>
    <dd>JSON-ответ API: <code>-&gt;normal($data)</code> и <code>-&gt;error($data, $code)</code>.</dd>
    <dt><code>Migrator</code></dt>
    <dd>Миграции БД.</dd>
    <dt><code>CodeHighlight</code>, <code>DocCode()</code></dt>
    <dd>Блоки программного кода с подсветкой: <code>DocCode($code, 'php', 'src/news.php')</code> в шаблоне или <code>&#91;code=php]...&#91;/code]</code> в любом тексте (в том числе в страницах из админки). На сервере код только экранируется, а раскрашивает его в браузере highlight.js - он загружается отдельным файлом и только на страницах, где есть код. Языки: php (с HTML - как шаблон), html, js, ts, css/scss/less, bash, env/ini, sql, json, nginx, apache, c++, plain.</dd>
    <dt><code>SimpleCaptcha</code>, <code>Benchmark</code></dt>
    <dd>Капча, замеры для бенчмарка.</dd>
    <dt><code>EmailNotificator</code>, <code>SmsNotificator</code>, <code>TelegramNotificator</code>, <code>PushNotificator</code></dt>
    <dd>Каналы уведомлений (используются в <code>SendNotice()</code>).</dd>
    <dt><code>PaySystem</code></dt>
    <dd>Приём оплаты через ЮKassa и Сбер.</dd>
    <dt><code>ShopCart</code>, <code>ShopFavorites</code>, <code>ShopImage</code>, <code>ShopProductImporter</code>, <code>ShopRecentlyViewed</code></dt>
    <dd>Магазин - смотрите раздел <a href="<?= $url('shop') ?>">Магазин</a>.</dd>
</dl>

<h2>Библиотеки, на которых работает движок</h2>
<p>Эти классы мало применимы вне самого движка, поэтому опишем только их назначение:</p>
<ul>
    <li><code>ExtraPacker</code> - объединение и сжатие css/js;</li>
    <li><code>HtmlValidate</code> - объединение <code>&lt;head&gt;</code> и <code>&lt;bottom&gt;</code> всех компонентов;</li>
    <li><code>Output</code> - пост-обработка HTML перед отправкой;</li>
    <li><code>Request</code>, <code>Input</code>, <code>InputClean</code> - разбор адреса и очистка входных данных от XSS (используются в <code>Get()</code> и <code>Post()</code>);</li>
    <li><code>ErrorLogger</code> - перехват и логирование ошибок;</li>
    <li><code>DebugPanel</code> - панель внизу страницы в debug-режиме;</li>
    <li><code>EngineQueryManager</code> - отдача статических файлов, если запрос пришёл не на страницу.</li>
</ul>

<h2>Composer-пакеты</h2>
<p>DbSimple и модели (<code>thezmi/*</code>), роутер <code>pecee/simple-router</code>, <code>vlucas/phpdotenv</code>, PHPMailer, <code>wikimedia/less.php</code>, WideImage (обработка картинок), PhpSpreadsheet (Excel), <code>endroid/qr-code</code>, SDK ЮKassa и клиент Сбербанка, Mobile Detect и др. - полный список в <code>composer.json</code>.</p>
