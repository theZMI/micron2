<h1>CSS и JS: ExtraPacker и webpack</h1>
<p class="lead">В Micron два сборщика, и они дополняют друг друга: webpack собирает основной бандл сайта (Bootstrap, SCSS, TypeScript, Vue), а ExtraPacker на лету склеивает css/js, которые подключены прямо в компонентах.</p>

<h2>webpack</h2>
<p>Папка <code>i/webpack</code>. Точка входа - <code>index.ts</code>: в ней подключаются стили (<code>@scss/_all</code>), Bootstrap, jQuery, Vue 3 и скрипты сайта из <code>i/webpack/src</code> и <code>i/ts</code>.</p>
<?= DocCode(<<<'CODE'
    cd i/webpack
    npm install        # Один раз: установить зависимости
    npm run dev        # Сборка для разработки (быстрее, без сжатия)
    npm run build      # Сборка для сервера (сжатие, autoprefixer)
    npm run analyze    # Отчёт о размере бандла
    CODE, 'bash') ?>
<p>Результат - <code>i/webpack/dist/app.css</code> и <code>app.js</code> - подключается в <code>&lt;head&gt;</code> главного шаблона автоматически (<code>core/config/webpack.php</code>) и проходит мимо ExtraPacker. Папка <code>dist</code> не хранится в git, поэтому после обновления стилей или скриптов на сервере нужно выполнить <code>npm run build</code>.</p>
<p>Короткие пути для импорта: <code>@scss</code>, <code>@ts</code>, <code>@js</code>, <code>@css</code>, <code>@less</code>, <code>@image</code>, <code>@data</code>, <code>@webpack</code>, <code>@root</code>.</p>

<h3>Добавить стили страницы</h3>
<ol>
    <li>Создайте <code>i/scss/news.scss</code> (в нём доступны переменные и миксины Bootstrap и сайта).</li>
    <li>Подключите его в <code>i/scss/_all.scss</code>: <code>@import "@scss/news";</code></li>
    <li>Выполните <code>npm run build</code>.</li>
</ol>

<h3>Добавить скрипт</h3>
<ol>
    <li>Создайте <code>i/webpack/src/News.js</code> (или модуль на TypeScript в <code>i/ts</code>).</li>
    <li>Подключите в <code>i/webpack/index.ts</code>: <code>import "@webpack/src/News";</code></li>
    <li>Если функция нужна в обычных <code>&lt;script&gt;</code> на страницах, положите её в <code>window</code> (так сделано для <code>bootstrap</code>, <code>Vue</code>, <code>UserModel</code>).</li>
</ol>

<h2>ExtraPacker</h2>
<p>ExtraPacker - очень важная часть движка. Он находит в HTML страницы все подключённые css/less/js-файлы (кроме исключений), объединяет их в один css и один js-файл, сжимает и вставляет ссылки на них в <code>&lt;head&gt;</code>. Ничего прописывать в шаблонах не нужно - всё происходит автоматически.</p>
<p>Вместе с этим Micron собирает все <code>&lt;head&gt;</code>-секции компонентов в одну, поэтому компонент может сам подключить нужные ему файлы, не боясь дублирования.</p>
<?= DocCode(<<<'CODE'
    <head>
        <link rel="stylesheet" type="text/css" href="/i/css/my_css.css"/>
        <link rel="stylesheet" type="text/css" href="/i/less/my_less.less"/>
        <script src="/i/js/my_js.js"></script>
        <script>call_function_from_my_js();</script>
    </head>
    <div>
        Html-код компонента.
        <button onclick="call_function_from_my_js();">Вызов функции из /i/js/my_js.js</button>
    </div>
    CODE) ?>
<p>В этом случае Micron:</p>
<ul>
    <li>скопирует код <code>my_js.js</code> в общий js-файл и сожмёт его;</li>
    <li>скопирует <code>my_css.css</code> и скомпилированный <code>my_less.less</code> в общий css-файл и сожмёт его;</li>
    <li>подключит общие файлы в главном шаблоне;</li>
    <li>перенесёт <code>&lt;head&gt;</code> компонента в <code>&lt;head&gt;</code> главного шаблона - на странице будет только один <code>&lt;head&gt;</code>.</li>
</ul>

<h3>Как работает</h3>
<p>ExtraPacker не перепаковывает файлы при каждом запросе:</p>
<ol>
    <li>составляет список css и js-файлов страницы;</li>
    <li>проверяет, не изменились ли они и не появились ли новые с момента последней упаковки;</li>
    <li>при необходимости упаковывает заново (в имя файла добавляется время упаковки - так браузер не возьмёт старую версию из кеша);</li>
    <li>заменяет ссылки на файлы ссылкой на упакованный файл.</li>
</ol>
<p>Упаковка: объединение файлов в один → для css - проход компилятора Less (поэтому в css-файлах можно использовать вложенность, переменные и миксины Less, объявленные в любом из склеиваемых файлов, если порядок подключения правильный) → удаление лишних пробелов и комментариев (js - через JSMin) → сохранение в <code>tmp/_auto_merge_css_js</code>. Пути в <code>url()</code> внутри css пересчитываются автоматически.</p>
<p>В админке есть отдельная папка (<code>tmp/_admin_auto_merge_css_js</code>) и раздел «Информация по склеенным extraPacker-ом файлам».</p>

<h3>Настройки</h3>
<?= DocCode(<<<'CODE'
    Config('extra_packer', [
        'dir'      => '_auto_merge_css_js', // Подпапка в tmp для упакованных файлов
        'packHtml' => false,                // Сжимать ли сам HTML (код страницы станет нечитаемым)
        'packCss'  => true,
        'packJs'   => true,
        'arrExceptions_js'        => [],    // Добавить в пак, но не сжимать (в конец пака)
        'arrExceptionsNotAdd_js'  => Config(['webpack', 'jsFiles']), // Не добавлять в пак вообще
        'arrExceptions_css'       => [],    // Добавить в пак, но не сжимать (в начало пака)
        'arrExceptionsNotAdd_css' => Config(['webpack', 'cssFiles']),
        'buffering'    => false,            // Дополнительно сохранять gzip-версию
        'statFilePath' => BASEPATH . 'tmp/_auto_merge_css_js/time_stat.json', // Статистика времени упаковки
    ]);
    CODE, 'php', 'core/config/extrapacker.php') ?>

<h3>Исключение файлов из упаковки</h3>
<p>Иногда файл не нужно добавлять в пак: внешняя библиотека с CDN или большой файл, нужный только на одной странице. Тогда:</p>
<ul>
    <li>подключите его вне <code>&lt;head&gt;</code>, например в <code>&lt;bottom&gt;</code> или прямо в теле страницы;</li>
    <li>или добавьте адрес в <code>arrExceptionsNotAdd_js</code> / <code>arrExceptionsNotAdd_css</code> - так сделано для сборки webpack.</li>
</ul>

<h3>Ошибки в упакованных файлах</h3>
<p>Изредка внешняя js-библиотека сжимается некорректно, и появляются ошибки. Добавьте такой файл в <code>arrExceptions_js</code> (css - в <code>arrExceptions_css</code>): его содержимое попадёт в общий пак, но без сжатия. Для css в исключениях не работает и компилятор Less.</p>
<p>Если что-то пошло не так, просто очистите папку <code>tmp/_auto_merge_css_js</code> - файлы пересоберутся.</p>

<h2>Что куда писать</h2>
<div class="manual-table-wrap">
<table>
    <tr><th>Что</th><th>Куда</th></tr>
    <tr><td>Общие стили сайта, стили страниц</td><td><code>i/scss/*.scss</code> → webpack</td></tr>
    <tr><td>Скрипты сайта, модули, Vue-компоненты</td><td><code>i/webpack/src</code>, <code>i/ts</code> → webpack</td></tr>
    <tr><td>Стили и скрипты переносимого компонента (админка, <code>_dev</code>)</td><td><code>i/css</code>, <code>i/js</code> в <code>&lt;head&gt;</code> компонента → ExtraPacker</td></tr>
    <tr><td>Внешние библиотеки с CDN</td><td>Исключения ExtraPacker</td></tr>
</table>
</div>
