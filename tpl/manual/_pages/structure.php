<h1>Структура папок и файлов</h1>

<h2>Файлы в корне</h2>
<dl class="manual-defs">
    <dt><code>index.php</code></dt>
    <dd>Точка входа: все запросы к сайту (кроме разрешённых файлов) идут сюда, а он подключает движок из <code>core</code> и нужную страницу из <code>src</code> + <code>tpl</code>.</dd>
    <dt><code>.htaccess</code></dt>
    <dd>Правила Apache: переадресация запросов на <code>/index.php?micron_query=адрес</code>, запрет доступа к служебным файлам (<code>.env</code>, <code>composer.json</code>...), заголовки безопасности и кеширование статики. Apache напрямую отдаёт склеенные css/js из <code>tmp/</code>, сборку webpack, файлы CKEditor, картинки/шрифты/css/js из <code>i/</code> и картинки из <code>upl/</code> (так каталог не запускает PHP на каждую картинку). Прочие файлы из <code>i/</code> и <code>upl/</code> отдаёт сам движок (<code>BrowserDataCache</code>): только из разрешённых папок (<code>core/config/browser_data_cache.php</code>), только разрешённых типов и с кешированием в браузере.</dd>
    <dt><code>.env</code> и <code>.env.example</code></dt>
    <dd>Настройки, зависящие от окружения: режим отладки, адрес сайта, подключение к БД, пароли и ключи сервисов. <code>.env</code> не хранится в git, <code>.env.example</code> - образец со всеми переменными.</dd>
    <dt><code>composer.json</code>, <code>composer.lock</code>, <code>vendor/</code></dt>
    <dd>Библиотеки, установленные composer-ом: DbSimple и модели, роутер, PHPMailer, клиенты платёжных систем, PhpSpreadsheet и др.</dd>
    <dt><code>Makefile</code></dt>
    <dd>Команды для сборки фронтенда (<code>make build-assets</code>) и анализа бандла.</dd>
    <dt><code>favicon.ico</code></dt>
    <dd>Иконка сайта (замените на свою). <code>robots.txt</code> файлом не лежит - его отдаёт движок (<code>src/robots.php</code>, правила - <code>core/config/robots.php</code>).</dd>
    <dt><code>README.md</code></dt>
    <dd>Краткая справка по движку.</dd>
</dl>

<h2><code>core</code> - ядро движка</h2>
<dl class="manual-defs">
    <dt><code>core/core.php</code></dt>
    <dd>Запуск движка: подключает функции, настройки, языки и init-файлы в правильном порядке.</dd>
    <dt><code>core/func</code></dt>
    <dd>Функции движка и помощники - подключаются все файлы папки. Главные: <code>main.php</code> (<code>IncludeCom</code>, <code>SiteRoot</code>, <code>Get</code>, <code>Post</code>, <code>L</code>...), <code>config.php</code>, <code>env.php</code>, <code>db.php</code>, <code>messages.php</code> (<code>MsgOk</code>, <code>MsgErr</code>), <code>debug.php</code> (<code>Xmp</code>, <code>ToLog</code>), <code>validation.php</code>, <code>security.php</code>, <code>send_mail.php</code>, <code>benchmark.php</code>.</dd>
    <dt><code>core/config</code></dt>
    <dd>Настройки: первым грузится <code>main.php</code>, затем <code>languages.php</code> и все остальные (<code>db.php</code>, <code>php_ini.php</code>, <code>extrapacker.php</code>, <code>shop.php</code>, <code>security.php</code>...). В подпапках - настройки админки (<code>_admin</code>), платёжных систем (<code>pay_systems</code>) и уведомлений (<code>notificators</code>).</dd>
    <dt><code>core/init</code></dt>
    <dd>Инициализация: <code>db.php</code> (БД и миграции), <code>error_logger.php</code>, <code>main.php</code> (PHP, сессия, пользователь), <code>www_redirecter.php</code>, <code>pay_system.php</code>, <code>router.php</code> (все маршруты). Ваши init-файлы подключаются после них.</dd>
    <dt><code>core/migrations</code></dt>
    <dd>Миграции БД: <code>NNNN_описание.php</code>.</dd>
</dl>

<h2><code>src</code> и <code>tpl</code> - компоненты</h2>
<p>В <code>src</code> лежат контроллеры, в <code>tpl</code> - шаблоны, с одинаковыми путями: <code>src/shop/cart.php</code> + <code>tpl/shop/cart.php</code> = компонент <code>shop/cart</code> по адресу <code>site.ru/shop/cart</code>.</p>
<dl class="manual-defs">
    <dt><code>home</code>, <code>404</code>, <code>403</code>, <code>500</code></dt>
    <dd>Главная страница (компонент по умолчанию) и страницы ошибок.</dd>
    <dt><code>_main_tpl</code>, <code>_main_tpl/*</code></dt>
    <dd>Главный шаблон сайта: <code>&lt;head&gt;</code>, меню, хлебные крошки, футер, переключатель темы.</dd>
    <dt><code>_dev/*</code></dt>
    <dd>Готовые служебные компоненты: меню, постраничная навигация, капча, поля одноразового кода, звёзды рейтинга, PWA, debug-панель и др.</dd>
    <dt><code>_admin/*</code></dt>
    <dd>Админка (адрес <code>/_admin</code>).</dd>
    <dt><code>_cron/*</code></dt>
    <dd>Задачи по расписанию: бэкап БД, проверка платежей, импорт товаров, отмена брошенных заказов, проверка места на диске.</dd>
    <dt><code>user/*</code></dt>
    <dd>Вход и регистрация по коду из письма (<code>user/_email_code</code>), кабинет и настройки пользователя.</dd>
    <dt><code>api/v1.0/*</code></dt>
    <dd>REST API (ответы в JSON).</dd>
    <dt><code>shop</code>, <code>shop/*</code>, <code>pay_system/*</code></dt>
    <dd>Магазин и страницы оплаты.</dd>
    <dt><code>manual</code></dt>
    <dd>Эта документация.</dd>
    <dt><code>examples</code>, <code>examples/*</code></dt>
    <dd>Раздел «Компоненты»: список готовых компонентов (<code>core/config/examples.php</code>) и примеры работы с ними. Сами примеры открываются только при <code>DEBUG_MODE=1</code>.</dd>
</dl>

<h2><code>model</code>, <code>lib</code>, <code>trait</code>, <code>middleware</code></h2>
<p>Классы из этих папок подключаются автоматически (автозагрузка composer): если класс называется <code>ShopCart</code>, то он ищется в <code>lib/ShopCart.php</code>, <code>model/ShopCart.php</code> и т. д.</p>
<dl class="manual-defs">
    <dt><code>model</code></dt>
    <dd>Модели для работы с БД: <code>SiteModel</code> (базовая), <code>UserModel</code>, <code>AdminModel</code>, <code>PaymentModel</code>, <code>ShopProductModel</code>, <code>ShopOrderModel</code>, <code>MicronCache</code>, <code>ApiModel</code> и др.</dd>
    <dt><code>lib</code></dt>
    <dd>Библиотеки, установленные не через composer: ExtraPacker, DebugPanel, Uploader, ErrorLogger, PaySystem, Migrator, корзина и картинки магазина и пр.</dd>
    <dt><code>trait</code></dt>
    <dd>Общие куски классов: <code>SingletonTrait</code>, <code>AuthTrait</code>, <code>JsonFieldTrait</code> (поле-JSON в модели).</dd>
    <dt><code>middleware</code></dt>
    <dd>Код, выполняемый перед группами маршрутов: авторизация админа и пользователя, подготовка API, ключ для cron, webhook Telegram.</dd>
</dl>

<h2><code>i</code> - ресурсы сайта</h2>
<dl class="manual-defs">
    <dt><code>i/scss</code></dt>
    <dd>Стили сайта на SCSS: <code>_variables.scss</code> (цвета, шрифты, переопределение Bootstrap), <code>_all.scss</code> (что подключается), <code>components/*</code> (тема, меню, футер...), стили страниц (<code>shop.scss</code>, <code>manual.scss</code>...).</dd>
    <dt><code>i/webpack</code></dt>
    <dd>Сборщик: <code>index.ts</code> (точка входа), <code>src/*</code> (скрипты сайта: меню, магазин, маски ввода), <code>webpack.config.js</code>, <code>package.json</code>. Результат - в <code>i/webpack/dist</code> (не хранится в git).</dd>
    <dt><code>i/ts</code></dt>
    <dd>TypeScript/JS-модули: хелперы, клиент API, модели на стороне браузера, компоненты.</dd>
    <dt><code>i/css</code>, <code>i/js</code>, <code>i/less</code></dt>
    <dd>Файлы, которые подключаются прямо в компонентах и склеиваются ExtraPacker-ом (в том числе стили и скрипты админки и <code>_dev</code>-компонентов).</dd>
    <dt><code>i/image</code>, <code>i/fonts</code>, <code>i/data</code></dt>
    <dd>Картинки, шрифты (PT Sans) и прочие файлы (json, xls и т. п.).</dd>
</dl>

<h2><code>lang</code> - тексты сайта</h2>
<dl class="manual-defs">
    <dt><code>lang/ru</code>, <code>lang/en</code>...</dt>
    <dd>Папка на каждый язык. Файл <code>lang/ru/имя_компонента.php</code> подключается вместе с компонентом.</dd>
    <dt><code>lang/ru/_autoload</code></dt>
    <dd>Тексты, которые загружаются всегда: <code>main.php</code> (title, description, keywords по умолчанию), <code>contacts.php</code> (телефон и почта поддержки) и пр.</dd>
</dl>

<h2>Папки с данными</h2>
<dl class="manual-defs">
    <dt><code>tmp</code></dt>
    <dd>Временные файлы: склеенные css/js, логи, кеш. Подразумевается, что после очистки папки сайт работает так же. Снаружи доступны только склеенные css/js и qr-коды, логи и прочие файлы - нет.</dd>
    <dt><code>upl</code></dt>
    <dd>Загруженные файлы (аватарки, картинки товаров и пр.). Запуск скриптов в ней запрещён.</dd>
    <dt><code>db_backups</code></dt>
    <dd>Бэкапы БД (снаружи недоступны).</dd>
    <dt><code>apps</code></dt>
    <dd>Сборки мобильных приложений (<code>apps/1.0.10.apk</code>), отдаются через <code>/get_app</code>.</dd>
</dl>
