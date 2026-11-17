<h1>Функции, переменные и константы</h1>
<p class="lead">Знание основных элементов движка заметно упрощает работу с Micron. Все функции лежат в <code>core/func</code> - каждый файл маленький, и его легко прочитать целиком.</p>

<h2>Константы</h2>
<dl class="manual-defs">
    <dt><code>BASEPATH</code></dt>
    <dd>Путь к папке сайта на диске, например <code>/var/www/html/</code>. Создаётся автоматически в <code>index.php</code>.</dd>
    <dt><code>DEF_LANG</code></dt>
    <dd>Язык сайта по умолчанию (<code>'ru'</code>). Задаётся в <code>core/config/languages.php</code>.</dd>
    <dt><code>LANG</code></dt>
    <dd>Язык текущего запроса. Если открыта страница <code>site.ru/en/about</code>, то <code>LANG</code> равен <code>'en'</code>, а для <code>site.ru/about</code> - <code>DEF_LANG</code>.</dd>
    <dt><code>M_HTML_FILTER_OFF</code></dt>
    <dd>Флаг для <code>Get()</code> и <code>Post()</code>: не экранировать HTML. Пример: <code>$text = Post('text', '', M_HTML_FILTER_OFF);</code></dd>
    <dt><code>M_XSS_FILTER_OFF</code></dt>
    <dd>Флаг для <code>Get()</code> и <code>Post()</code>: не чистить от XSS. Получить данные совсем без обработки: <code>Post('html', '', M_XSS_FILTER_OFF | M_HTML_FILTER_OFF)</code>. Используйте только когда точно понимаете, зачем.</dd>
    <dt><code>M_DELETE_PARAM</code></dt>
    <dd>Передаётся в <code>GetCurUrl()</code>, чтобы убрать параметр из текущего адреса: <code>GetCurUrl('page=' . M_DELETE_PARAM)</code>.</dd>
    <dt><code>IS_ADMIN_AUTH</code>, <code>IS_USER_AUTH</code></dt>
    <dd>Авторизован ли админ / пользователь (задаёт <code>CommonMiddleware</code>).</dd>
    <dt><code>IS_ADMIN_AREA</code>, <code>IS_USER_CABINET</code>, <code>IS_API</code></dt>
    <dd>Где мы находимся: в админке, в кабинете пользователя или в API.</dd>
    <dt><code>SUPPORT_PHONE</code>, <code>SUPPORT_EMAIL</code></dt>
    <dd>Контакты поддержки из <code>lang/язык/_autoload/contacts.php</code>.</dd>
    <dt><code>DEFAULT_TIME_ZONE</code></dt>
    <dd>Часовой пояс сайта (<code>core/config/php_ini.php</code>).</dd>
</dl>
<h2>Глобальные переменные</h2>
<dl class="manual-defs">
    <dt><code>$g_config</code></dt>
    <dd>Массив настроек сайта. Удобнее работать через функцию <code>Config()</code>.</dd>
    <dt><code>$g_lang</code></dt>
    <dd>Массив текстов текущего языка. Читать - через <code>L()</code>. Заголовок страницы - <code>$g_lang['m_title']</code>, описание - <code>m_description</code>, ключевые слова - <code>m_keyWords</code>.</dd>
    <dt><code>$g_user</code>, <code>$g_admin</code></dt>
    <dd>Текущий пользователь (<code>UserModel</code>) и админ (<code>AdminModel</code>). Доступны в любом компоненте: <code>$g_user-&gt;isAuth()</code>, <code>$g_user-&gt;id</code>.</dd>
</dl>

<h2>Главные функции</h2>
<dl class="manual-defs">
    <dt><code>int IncludeCom(string $name [, array $params])</code></dt>
    <dd>
        <p>Самая важная функция движка: подключает компонент по имени, то есть последовательно делает <code>require</code> для его lang/src/tpl-файлов. Переменные из <code>$params</code> создаются до подключения: при <code>['a' =&gt; 1]</code> в компоненте будет <code>$a = 1</code>. Имя может содержать параметры: <code>IncludeCom('login&amp;x=1')</code> создаст <code>$_GET['x']</code>. Возвращает число подключённых файлов (0 - компонента нет).</p>
        <?= DocCode(<<<'CODE'
            // В упрощённом виде IncludeCom('example') делает:
            require BASEPATH . 'lang/ru/example.php'; // язык по умолчанию
            require BASEPATH . 'lang/en/example.php'; // текущий язык (если отличается)
            require BASEPATH . 'src/example.php';     // контроллер
            require BASEPATH . 'tpl/example.php';     // шаблон
            CODE) ?>
    </dd>
    <dt><code>void TryIncludeCom(string $name [, array $params [, string $component404 = '404']])</code></dt>
    <dd>Подключает компонент, а если его нет - страницу 404 (или указанный компонент).</dd>
    <dt><code>void ExitCom()</code></dt>
    <dd>Останавливает дальнейшую загрузку файлов текущего компонента. Функция не прерывает работу немедленно: если вызвать её в src-файле, он выполнится до конца (или до <code>return</code>), а tpl-файл уже не подключится.</dd>
    <dt><code>bool IsComExists(string $name)</code>, <code>bool IsComIncluded(string $name)</code></dt>
    <dd>Есть ли такой компонент и был ли он уже подключён.</dd>
    <dt><code>string GetQuery()</code></dt>
    <dd>Имя запрошенного компонента без языка. Для <code>site.ru/en/news?page=1</code> вернёт <code>news</code>.</dd>
    <dt><code>string SiteRoot([string $uri])</code></dt>
    <dd>Полный адрес страницы сайта с учётом языка. Используйте для ссылок на страницы. Адрес строится от <code>SITE_ROOT_URL</code> из <code>.env</code>, поэтому подходит и для писем. Пример: <code>SiteRoot('contacts')</code> → <code>https://site.ru/contacts</code> (для английской версии - <code>https://site.ru/en/contacts</code>), <code>SiteRoot('contacts&amp;a=1&amp;b=2')</code> → <code>https://site.ru/contacts?a=1&amp;b=2</code>, <code>SiteRoot()</code> - главная.</dd>
    <dt><code>string Root([string $uri])</code></dt>
    <dd>Адрес файла от корня сайта без учёта языка. Используйте для картинок, css, js: <code>Root('i/image/logo.png')</code> → <code>/i/image/logo.png</code>.</dd>
    <dt><code>string GetCurUrl([string $params])</code></dt>
    <dd>
        <p>Текущий адрес, а также новый адрес на его основе. Пусть текущий адрес <code>site.ru/news?id=1001&amp;type=full</code>, тогда:</p>
        <?= DocCode(<<<'CODE'
            GetCurUrl('id=1002');                                 // Замена:      /news?id=1002&type=full
            GetCurUrl('style=dark');                              // Добавление:  /news?id=1001&type=full&style=dark
            GetCurUrl('type=' . M_DELETE_PARAM);                  // Удаление:    /news?id=1001
            GetCurUrl('style=dark&type=' . M_DELETE_PARAM);       // Всё вместе:  /news?id=1001&style=dark
            CODE) ?>
    </dd>
    <dt><code>mixed Get(string $name [, mixed $default = false [, int $flags]])</code></dt>
    <dd>Значение из <code>$_GET</code>, очищенное от XSS и с экранированным HTML (для массивов - каждое значение). Если параметра нет - вернёт <code>$default</code>. Флаги: <code>M_HTML_FILTER_OFF</code>, <code>M_XSS_FILTER_OFF</code>.</dd>
    <dt><code>mixed Post(string $name [, mixed $default = false [, int $flags]])</code></dt>
    <dd>То же для <code>$_POST</code>.</dd>
    <dt><code>string L(string $key)</code></dt>
    <dd>Текст из языковых файлов. Если перевода нет - вернёт сам ключ, поэтому можно писать <code>L('Hello world')</code> и добавить перевод позже.</dd>
    <dt><code>mixed Config([string|array $key [, mixed $value]])</code></dt>
    <dd>
        <p>Чтение и запись настроек <code>$g_config</code>:</p>
        <?= DocCode(<<<'CODE'
            Config();                              // Весь $g_config
            Config('main_tpl');                    // $g_config['main_tpl']
            Config('main_tpl', 'other_tpl');       // $g_config['main_tpl'] = 'other_tpl'
            Config(['shop', 'currency']);          // $g_config['shop']['currency']
            Config(['shop', 'currency'], 'RUB');   // $g_config['shop']['currency'] = 'RUB'
            Config(['list', 'PUSH'], 123);         // $g_config['list'][] = 123
            CODE) ?>
    </dd>
    <dt><code>mixed Env(string $key [, mixed $default])</code></dt>
    <dd>Переменная из <code>.env</code>: <code>Env('DEBUG_MODE')</code>, <code>Env('SITE_ROOT_URL')</code>.</dd>
    <dt><code>bool IsLocalEnv()</code></dt>
    <dd>Работаем ли на локальной копии (<code>ENV=local</code>).</dd>
</dl>

<h2>База данных</h2>
<dl class="manual-defs">
    <dt><code>GetDefaultDb()</code></dt>
    <dd>Объект DbSimple для основной БД - удобно использовать в моделях и скриптах.</dd>
    <dt><code>DbTransaction(callable $fn)</code></dt>
    <dd>Выполнить <code>$fn($db)</code> в транзакции: commit, а при исключении - rollback.</dd>
    <dt><code>AddDbCache(string $query [, int $ttl])</code></dt>
    <dd>Пометить запрос для кеширования.</dd>
</dl>

<h2>Отладка</h2>
<dl class="manual-defs">
    <dt><code>Xmp($a)</code></dt>
    <dd>Распечатать переменную, массив или объект (HTML-теги выводятся как текст).</dd>
    <dt><code>VarDump($a)</code></dt>
    <dd>Похожа на <code>Xmp</code>, но через <code>var_dump</code> - печатает и типы. Возвращает строку.</dd>
    <dt><code>ToLog($msg [, $path])</code></dt>
    <dd>Записать сообщение в лог-файл (по умолчанию - в <code>tmp/</code>).</dd>
    <dt><code>BM('Название')</code></dt>
    <dd>Замер времени и памяти участка кода: вызовите в начале и в конце участка, результат будет в бенчмарке debug-панели.</dd>
</dl>

<h2>Сообщения</h2>
<dl class="manual-defs">
    <dt><code>Msg($text [, $css])</code>, <code>MsgOk($text)</code>, <code>MsgErr($text)</code>, <code>MsgWarning($text)</code></dt>
    <dd>Возвращают HTML сообщения в рамке (Bootstrap alert): обычного, успешного (зелёное), об ошибке (красное) и предупреждения.</dd>
</dl>

<h2>Проверка данных</h2>
<dl class="manual-defs">
    <dt><code>IsValidEmail($email)</code>, <code>IsValidPhone($phone)</code>, <code>IsValidUrl($url)</code></dt>
    <dd>Проверка email, телефона и адреса.</dd>
    <dt><code>PhoneFilter($phone)</code></dt>
    <dd>Оставляет в телефоне только цифры и <code>+</code>. Удобно для ссылок: <code>&lt;a href="tel:&lt;?= PhoneFilter($phone) ?&gt;"&gt;</code></dd>
    <dt><code>SafeRedirectUrl($url, $default)</code></dt>
    <dd>Вернёт <code>$url</code>, только если это адрес нашего сайта, иначе <code>$default</code>. Для редиректов «обратно».</dd>
    <dt><code>IsAjaxRequest()</code>, <code>GetClientIP()</code></dt>
    <dd>AJAX ли это запрос; IP посетителя (с учётом доверенных прокси).</dd>
</dl>

<h2>Тексты, даты, числа</h2>
<dl class="manual-defs">
    <dt><code>FormatDate($timestamp)</code>, <code>FormatTimeInterval($seconds)</code></dt>
    <dd>Дата в виде «10 января 2026 г.» и интервал в виде <code>02:15</code> (часы:минуты, с секундами - <code>02:15:30</code>).</dd>
    <dt><code>Plural($n, 'товар', 'товара', 'товаров')</code></dt>
    <dd>Правильное окончание после числа.</dd>
    <dt><code>Translit($str)</code>, <code>TextToUrl($str)</code>, <code>TextWithMaxLen($str, $n)</code>, <code>HasHtml($str)</code></dt>
    <dd>Транслит, текст для адреса (<code>moy-tovar</code>), обрезка текста, есть ли в строке HTML.</dd>
    <dt><code>RoundToN()</code>, <code>ArrayOrderBy()</code>, <code>ShopPrice($amount)</code></dt>
    <dd>Округление, сортировка массива по нескольким полям, цена в формате магазина.</dd>
</dl>

<h2>Прочее</h2>
<dl class="manual-defs">
    <dt><code>SendMail($to, $subject, $html [, $config [, $headers [, $files]]])</code></dt>
    <dd>Отправка письма через PHPMailer (настройки в <code>.env</code> и <code>core/config/send_mail.php</code>). В debug-режиме письма пишутся в <code>tmp/sent_emails.txt</code>.</dd>
    <dt><code>SendNotice($user, $text, ['telegram', 'email', 'sms'])</code></dt>
    <dd>Уведомление пользователю по всем включённым и подтверждённым каналам.</dd>
    <dt><code>ForceDownload($file [, $name])</code>, <code>DownloadAsXL()</code>, <code>ReadXL()</code></dt>
    <dd>Отдать файл на скачивание; выгрузить и прочитать Excel.</dd>
    <dt><code>CreateQrCode($text)</code></dt>
    <dd>Картинка с QR-кодом.</dd>
    <dt><code>GetLocationByIP()</code>, <code>GetCountries()</code>, <code>GetCoordinatesByAddress()</code></dt>
    <dd>Геолокация (на локальной копии - тестовые данные).</dd>
    <dt><code>MemoryGet($key)</code>, <code>MemorySet($key, $value)</code>, <code>MemoryHas($key)</code></dt>
    <dd>Простой кеш значений в БД (<code>MicronCache</code>).</dd>
    <dt><code>GenNewCaptchaCode()</code>, <code>CheckCaptchaCode()</code>, <code>CheckRecaptchaCode()</code></dt>
    <dd>Капча-картинка и Google reCAPTCHA.</dd>
    <dt><code>GeneratePinCode($user)</code>, <code>CheckPinCode($user, $code)</code></dt>
    <dd>Одноразовые коды подтверждения.</dd>
</dl>

