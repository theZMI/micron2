<h1>Ход выполнения приложения</h1>
<p class="lead">Micron работает по модели HMVC: каждая триада MVC - это независимый компонент, который может включать не только модель, контроллер и шаблон, но и всё остальное (config, init, стили, скрипты, картинки).</p>
<p>Не совсем корректно говорить, что модель входит в компонент. Модель - скорее библиотека, которую компонент использует. Модели строятся не по структуре компонентов, а по структуре БД. Примеры компонентов: контакты, постраничная навигация, поиск, меню, корзина. Примеры моделей: пользователи, заказы, товары.</p>

<h2>Путь одного запроса</h2>
<p>На примере страницы каталога <code>site.ru/shop</code>:</p>
<div class="manual-scheme">
    <div class="ms-node is-io">Браузер: <code>GET site.ru/shop</code></div>
    <div class="ms-arrow"></div>
    <div class="ms-node">
        <strong>index.php</strong>
        <div class="ms-parts"><span>vendor/autoload.php</span><i class="bi bi-plus"></i><span>.env</span></div>
    </div>
    <div class="ms-arrow"></div>
    <div class="ms-node">
        <strong>core/core.php</strong>
        <div class="ms-parts">
            <span>core/func/*</span><i class="bi bi-arrow-right"></i>
            <span>lang/LANG/_autoload/*</span><i class="bi bi-arrow-right"></i>
            <span>core/config/*</span><i class="bi bi-arrow-right"></i>
            <span>core/init/*</span>
        </div>
        <p>init: БД и миграции, перехват ошибок, сессия, редирект www, оплата, меню админки, роутер, затем ваши файлы</p>
    </div>
    <div class="ms-arrow"></div>
    <div class="ms-node">
        <strong>Роутер</strong>
        <div class="ms-parts"><span>core/init/router.php</span><i class="bi bi-arrow-right"></i><span>middleware группы</span></div>
        <p>/shop → компонент <code>shop</code>; для /_admin, /_cron, /api - свои маршруты и проверки доступа</p>
    </div>
    <div class="ms-arrow"></div>
    <div class="ms-node is-accent">
        <strong>IncludeCom('shop')</strong>
        <div class="ms-parts">
            <span>lang/DEF_LANG/shop.php</span><i class="bi bi-arrow-right"></i>
            <span>lang/LANG/shop.php</span><i class="bi bi-arrow-right"></i>
            <span>src/shop.php</span><i class="bi bi-arrow-right"></i>
            <span>tpl/shop.php</span>
        </div>
        <div class="ms-branches">
            <div>
                Контроллер берёт данные из модели:
                <div class="ms-parts"><span>ShopProductModel::search()</span></div>
            </div>
            <div>
                Шаблон подключает другие компоненты:
                <div class="ms-parts"><span>shop/_product_card</span><span>_dev/paginator</span></div>
            </div>
        </div>
    </div>
    <div class="ms-arrow"><span>$content</span></div>
    <div class="ms-node">
        <strong>_main_tpl</strong>
        <p>Главный шаблон: шапка, меню, футер, а в середине - <code>$content</code></p>
    </div>
    <div class="ms-arrow"></div>
    <div class="ms-node">
        <strong>Пост-обработка</strong>
        <div class="ms-parts">
            <span>подсветка кода</span><i class="bi bi-arrow-right"></i>
            <span>сборка &lt;head&gt; и &lt;bottom&gt;</span><i class="bi bi-arrow-right"></i>
            <span>ExtraPacker</span><i class="bi bi-arrow-right"></i>
            <span>debug-панель</span>
        </div>
    </div>
    <div class="ms-arrow"></div>
    <div class="ms-node is-io">Браузер получает HTML</div>
</div>
<ol>
    <li><strong><code>index.php</code></strong> засекает время старта, подключает composer (<code>vendor/autoload.php</code>) и загружает переменные окружения из <code>.env</code> в <code>$_ENV</code>. С этого момента известен режим <code>DEBUG_MODE</code>.</li>
    <li><strong><code>core/core.php</code></strong> запускает движок:
        <ul>
            <li>подключает все файлы функций из <code>core/func</code>;</li>
            <li>загружает <code>core/config/main.php</code> и <code>languages.php</code>, определяет язык запроса (константа <code>LANG</code>);</li>
            <li>подключает тексты из <code>lang/язык/_autoload</code>;</li>
            <li>подключает остальные файлы <code>core/config</code>;</li>
            <li>выполняет init-файлы в строгом порядке: БД (и <a href="<?= $url('migrations') ?>">миграции</a>), перехват ошибок, настройка PHP и сессии, редирект www, платёжная система, меню админки, роутер - а затем все остальные (ваши) файлы из <code>core/init</code>;</li>
            <li>выставляет заголовки CORS.</li>
        </ul>
    </li>
    <li><strong>Роутер</strong> (<code>core/init/router.php</code>) сопоставляет адрес с маршрутом, выполняет middleware этой группы (авторизация, подготовка API и пр.) и вызывает компонент. По умолчанию <code>site.ru/a/b</code> → компонент <code>a/b</code>.</li>
    <li><strong><code>IncludeCom()</code></strong> подключает файлы компонента по очереди: язык по умолчанию → текущий язык → <code>src</code> → <code>tpl</code>. Внутри шаблона можно подключать другие компоненты - так строится иерархия.</li>
    <li>Вывод страницы перехватывается, и если <code>Config('is_load_in_main_tpl')</code> включён, она подставляется в <strong>главный шаблон</strong> <code>_main_tpl</code> как переменная <code>$content</code>. Для JSON, файлов и AJAX-ответов главный шаблон отключают.</li>
    <li>Готовый HTML проходит через <strong>функции пост-обработки</strong> из <code>Config('prepare_functions')</code>: все <code>&lt;head&gt;</code> и <code>&lt;bottom&gt;</code> компонентов собираются в один, ExtraPacker склеивает css/js, подсвечивается программный код, в debug-режиме добавляется debug-панель.</li>
    <li>Результат отправляется в браузер.</li>
</ol>

<h2>Формула компонента</h2>
<p>Понятие компонента можно выразить простой формулой:</p>
<?= DocCode(<<<'CODE'
    Компонент = lang/DEF_LANG/имя.php + lang/LANG/имя.php + src/имя.php + tpl/имя.php
    CODE, 'plain') ?>
<p>Любой из файлов может отсутствовать. Компонент считается найденным, если есть хотя бы один, иначе роутер покажет страницу 404.</p>

<h2>Формула работы движка</h2>
<?= DocCode(<<<'CODE'
    Страница = пост-обработка( _main_tpl( роутер( middleware + Компонент( Компонент + Компонент ... ) ) ) )
    CODE, 'plain') ?>

<h2>Бенчмарк этапов</h2>
<p>Каждый этап отмечен вызовами <code>BM('Request / ...')</code>, поэтому в debug-панели видно, сколько времени и памяти ушло на запуск движка, роутинг, каждый компонент, каждый запрос к БД и пост-обработку. Подробнее - в разделе <a href="<?= $url('debug') ?>">Отладка</a>.</p>
