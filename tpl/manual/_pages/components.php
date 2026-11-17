<h1>Работа с компонентами</h1>

<h2>Вставка компонента</h2>
<p>Вставить компонент можно в любом месте кода, но чаще всего это происходит в tpl-файлах. Например, подключим меню в шапке сайта:</p>
<?= DocCode(<<<'CODE'
    <header class="site-header">
        <a href="<?= SiteRoot() ?>" title="На главную">
            <img src="<?= Root('i/image/logo.png') ?>" alt="Логотип">
        </a>
        <?php IncludeCom('menu') ?>
    </header>
    CODE) ?>

<h2>Передача параметров</h2>
<p>В компонент можно передать параметры - в нём они станут обычными переменными (ключ массива - имя переменной):</p>
<?= DocCode(<<<'CODE'
    <?php IncludeCom('menu', ['active' => GetQuery(), 'color' => 'dark']) ?>
    <!-- В src/menu.php и tpl/menu.php доступны $active и $color -->
    CODE) ?>
<p>Доступен и способ передачи как в адресе:</p>
<?= DocCode(<<<'CODE'
    <?php IncludeCom('menu&color=black', ['active' => GetQuery()]) ?>
    CODE) ?>
<p>Но помните, что при такой передаче переменная <code>color</code> записывается в массив <code>$_GET</code> и останется там после работы компонента - как при обычном вызове через адрес в браузере.</p>
<p>Переменные из src-файла компонента видны в его tpl-файле, но не «вытекают» наружу: каждый компонент работает в своей области видимости. Глобально доступны только <code>$g_lang</code>, <code>$g_user</code>, <code>$g_admin</code> и настройки через <code>Config()</code>.</p>

<h2>Прерывание компонента</h2>
<p>Пусть компонент <code>news</code> состоит из <code>src/news.php</code> и <code>tpl/news.php</code>. Если новостей нет, с помощью <code>ExitCom()</code> мы запрещаем подключение шаблона:</p>
<?= DocCode(<<<'CODE'
    <?php

    $page = max(1, (int)Get('page', 1));
    $news = (new NewsModel())->getList($page);

    if (empty($news)) {
        ExitCom(); // Нет новостей - блок не выводим
        return;    // и дальше код src-файла не выполняем
    }
    CODE, 'php', 'src/news.php') ?>

<h2>Свой &lt;head&gt; и &lt;bottom&gt; в компоненте</h2>
<p>Любой компонент может объявить секцию <code>&lt;head&gt;</code> - Micron соберёт все такие секции страницы в одну и перенесёт в <code>&lt;head&gt;</code> главного шаблона. Так компонент сам подключает нужные ему стили, скрипты и мета-теги. Если компонент подключён на странице несколько раз, файлы подключатся один раз.</p>
<p>Аналогично <code>&lt;bottom&gt;...&lt;/bottom&gt;</code> собирается и вставляется перед <code>&lt;/body&gt;</code> - удобно для скриптов, которым нужна вся разметка страницы.</p>
<?= DocCode(<<<'CODE'
    <head>
        <link rel="stylesheet" type="text/css" href="<?= Root('i/css/news.css') ?>"/>
        <meta name="robots" content="noindex">
    </head>

    <div class="news-list">
        <?php foreach ($news as $item): ?>
            <article><?= $item->title ?></article>
        <?php endforeach; ?>
    </div>

    <bottom>
        <script src="<?= Root('i/js/news.js') ?>"></script>
        <script>initNews();</script>
    </bottom>
    CODE, 'php', 'tpl/news.php') ?>

<h2>Заголовок и SEO страницы</h2>
<p>Заголовок, описание и ключевые слова берутся из <code>$g_lang['m_title']</code>, <code>m_description</code>, <code>m_keyWords</code>. Их можно задать в языковом файле страницы или прямо в контроллере:</p>
<?= DocCode(<<<'CODE'
    $g_lang['m_title']       = $product->name . L('m_titlePostfix');
    $g_lang['m_description'] = TextWithMaxLen(strip_tags($product->description), 160);
    CODE) ?>
<p>Если заголовок не задан, движок возьмёт текст первого <code>&lt;h1&gt;</code> страницы (функция <code>AutoTitle</code>).</p>

<h2>Хлебные крошки</h2>
<?= DocCode(<<<'CODE'
    BreadCrumb::getInstance()->add(['text' => 'Каталог', 'link' => SiteRoot('shop')]);
    BreadCrumb::getInstance()->add(['text' => $product->name]);
    CODE) ?>
<p>Крошки выводятся в главном шаблоне, если в них больше одного шага (первый шаг - главная - добавляется сам).</p>

<h2>Ответ без главного шаблона (AJAX, JSON, файлы)</h2>
<?= DocCode(<<<'CODE'
    <?php

    Config('is_load_in_main_tpl', false);
    header('Content-Type: application/json');
    echo json_encode(['count' => ShopCart::count()]);
    CODE) ?>
<p>Для API удобнее <code>(new ApiResponse())-&gt;normal($data)</code> - смотрите <a href="<?= $url('users') ?>">Пользователи и API</a>.</p>

<h2>Служебные компоненты</h2>
<p>Компоненты, у которых любая часть пути начинается с <code>_</code> (<code>_main_tpl</code>, <code>_seo</code>, <code>_dev/paginator</code>), по адресу не открываются - их подключают только из кода. Это защищает от вызова «внутренностей» сайта напрямую. Исключения заданы явными маршрутами (админка, cron) - смотрите <a href="<?= $url('routing') ?>">Роутинг</a>.</p>

<h2>Готовые компоненты (<code>_dev</code>)</h2>
<dl class="manual-defs">
    <dt><code>_dev/menu</code></dt>
    <dd>Меню на Bootstrap с выпадающими списками и бейджами (используется в шапке сайта).</dd>
    <dt><code>_dev/paginator</code></dt>
    <dd>Постраничная навигация.</dd>
    <dt><code>_dev/simple_captcha_code</code>, <code>_dev/simple_captcha_code_in_modal_wnd</code></dt>
    <dd>Капча-картинка: <code>IncludeCom('_dev/simple_captcha_code', ['captcha' =&gt; GetCaptchaInstance()])</code>.</dd>
    <dt><code>_dev/simple_captcha_code_2</code>, <code>_dev/simple_captcha_code_2_in_modal_wnd</code></dt>
    <dd>Google reCAPTCHA.</dd>
    <dt><code>_dev/pin_code_block</code></dt>
    <dd>Поля ввода одноразового кода: автопереход, вставка из буфера, автосабмит, таймер повторной отправки. На странице нужно Vue-приложение с <code>mixins: [window.pinCodeForm]</code>.</dd>
    <dt><code>_dev/rating_stars</code></dt>
    <dd>Звёзды рейтинга: <code>['rating' =&gt; 3, 'jsCallback' =&gt; 'onRate']</code>.</dd>
    <dt><code>_dev/bootstrap_validator</code></dt>
    <dd>Проверка форм на стороне браузера.</dd>
    <dt><code>_dev/pwa</code>, <code>_dev/network_status</code></dt>
    <dd>PWA (подключается в <code>&lt;head&gt;</code>; <code>manifest.json</code> и <code>service_worker.js</code> кладутся в корень сайта) и индикатор сети.</dd>
    <dt><code>_dev/xl_table</code>, <code>_dev/live_filter</code>, <code>_dev/ekko_lightbox</code>, <code>_dev/jcrop</code>, <code>_dev/autosize_inputs</code></dt>
    <dd>Таблица из Excel, фильтр списка на лету, галерея картинок, обрезка картинки, растущие поля ввода.</dd>
    <dt><code>_dev/page_vue3_app_template</code></dt>
    <dd>Заготовка страницы с приложением на Vue 3.</dd>
</dl>
<p>Примеры использования многих из них - в разделе <a href="<?= SiteRoot('examples') ?>">«Компоненты»</a> (сами примеры открываются при <code>DEBUG_MODE=1</code>). Список задаётся в <code>core/config/examples.php</code>.</p>
