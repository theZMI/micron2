<h1>Основные понятия (глоссарий)</h1>

<h2>Точка входа</h2>
<p>Все запросы к сайту обрабатываются через файл <code>/index.php</code>. Он подключает composer-библиотеки, читает <code>.env</code>, запускает движок (<code>core/core.php</code>), передаёт запрос роутеру, а тот - контроллеру нужного компонента. Получившаяся страница выводится внутри главного шаблона и проходит пост-обработку (склейка <code>&lt;head&gt;</code>, сборка css/js, debug-панель).</p>

<h2>Контроллер</h2>
<p>Контроллеры - это обычные PHP-файлы с кодом. Важно их идеологическое предназначение: в контроллере определяется, как реагировать на запрос, то есть в нём находится код конкретной страницы (конкретного компонента). Хранятся в папке <code>/src</code>.</p>

<h2>Шаблон</h2>
<p>Шаблоны - это PHP-файлы с HTML-кодом страницы или её части. Их задача - оформить вывод данных, подготовленных контроллером (или просто вывести HTML, если страница статическая). Хранятся в папке <code>/tpl</code>. Переменные, созданные в контроллере, доступны в шаблоне напрямую.</p>

<h2>Языковой файл</h2>
<p>PHP-файл с текстами, которые зависят от языка сайта. Тексты задаются в массиве <code>$g_lang</code>, а выводятся функцией <code>L('ключ')</code>. Подключив файл другого языка, получаем страницу на новом языке. Хранятся в <code>/lang/язык/</code>. Через раздел админки «Страницы» эти тексты можно править без программиста.</p>

<h2>Модель</h2>
<p>Класс, в котором собрана вся работа с некоторой сущностью БД. Например, <code>UserModel</code> работает с таблицей <code>users</code>. Модели позволяют реже обращаться к БД напрямую, а значит дают свободу менять и дополнять архитектуру хранения данных.</p>
<?= DocCode(<<<'CODE'
    // Так мы обновляем поля в БД у пользователя с id 1001
    $user        = new UserModel(1001);
    $user->name  = 'New user name';
    $user->email = 'example@example.com';
    $user->flush(); // Сохранить сейчас (иначе сохранится само в конце работы скрипта)

    // Создание новой записи
    $user        = new UserModel();
    $user->login = 'new_login';
    $id          = $user->flush(); // Вернёт id новой записи
    CODE) ?>
<p>Преимущества такого подхода очевидны: спрятав реализацию получения данных, мы сможем менять её в будущем. Несколько примеров:</p>
<ol>
    <li>Если у пользователя может быть несколько email, то сначала можно сделать поля <code>email1</code>, <code>email2</code> в таблице <code>users</code>, а позже вынести адреса в отдельную таблицу <code>user_emails</code>. Скрипты менять не придётся: достаточно перехватить чтение поля (метод <code>__get</code>) и запись в него (<code>__set</code>).</li>
    <li>Сначала можно хранить время последнего визита в поле <code>last_activity</code>, а позже завести таблицу <code>user_activity</code> с историей входов и при чтении поля просто брать последнюю запись.</li>
    <li>Таблицу можно разбить на две: для неизменяемых и для изменяемых данных - и запретить MySQL-пользователю менять первую. Так появляется защита от ошибок на уровне ниже, чем наш код.</li>
</ol>
<p>Модели помогают не только продумать архитектуру приложения, но и открывают большие возможности для оптимизации и масштабирования. Подробнее - в разделе <a href="<?= $url('db') ?>">Работа с моделями и БД</a>.</p>

<h2>Компонент</h2>
<p>Независимая часть сайта: блок авторизации, страница контактов, меню, корзина и т. д. Обычно состоит из трёх частей - языкового файла, контроллера и шаблона, но обязательной нет ни одной из них. Компонент подключается в нужное место функцией <code>IncludeCom()</code>:</p>
<?= DocCode(<<<'CODE'
    <header class="site-header">
        <a href="<?= SiteRoot() ?>">Logo</a>
        <?php
        // Компонент покажет либо кнопку «Войти», если пользователь не авторизован,
        // либо его имя и кнопку «Выход», если авторизован
        IncludeCom('block_user_login');
        ?>
    </header>
    CODE) ?>
<p>Здесь <code>block_user_login</code> может состоять из языкового файла с текстами, контроллера, который определяет, авторизован ли человек, и шаблона, который выводит нужный вариант. В реальности компонент может включать и больше:</p>
<ol>
    <li>файл функций: <code>core/func/любое_имя.php</code>;</li>
    <li>файл настроек: <code>core/config/любое_имя.php</code>;</li>
    <li>файл инициализации: <code>core/init/любое_имя.php</code>;</li>
    <li>глобальные тексты, загружаемые всегда: <code>lang/язык/_autoload/любое_имя.php</code>;</li>
    <li>тексты компонента: <code>lang/язык/имя_компонента.php</code>;</li>
    <li>контроллер: <code>src/имя_компонента.php</code>;</li>
    <li>шаблон: <code>tpl/имя_компонента.php</code>;</li>
    <li>стили: <code>i/scss/...</code> (через webpack) или <code>i/css/...</code> (подключается в <code>&lt;head&gt;</code> шаблона);</li>
    <li>скрипты: <code>i/webpack/src/...</code>, <code>i/ts/...</code> или <code>i/js/...</code>;</li>
    <li>модели, библиотеки, middleware и миграции - если нужны.</li>
</ol>

<h2>Служебный компонент</h2>
<p>Компонент, у которого имя или любая часть пути начинается с <code>_</code>: <code>_main_tpl</code>, <code>_dev/paginator</code>, <code>_admin/...</code>. По адресу из браузера такие компоненты не открываются - их подключают только из кода или через явные маршруты роутера.</p>

<h2>HMVC</h2>
<p>HMVC - развитие паттерна MVC с возможностью иерархического вызова триад MVC друг из друга (в нашем случае - вызова одного компонента из другого). Страница - это компонент, внутри которого подключены другие компоненты, внутри которых - третьи, и так далее.</p>
<div class="manual-figures">
    <figure class="manual-figure">
        <svg class="manual-diagram" viewBox="0 0 360 280" role="img" aria-label="Схема MVC: браузер, контроллер, модель, шаблон">
            <defs>
                <marker id="md-arrow-mvc" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse">
                    <path class="md-arrow" d="M0,0 L10,5 L0,10 z"/>
                </marker>
            </defs>
            <path class="md-edge" d="M140,42 L96,101" marker-end="url(#md-arrow-mvc)"/>
            <path class="md-edge" d="M160,129 L197,129" marker-end="url(#md-arrow-mvc)"/>
            <path class="md-edge" d="M264,102 L221,45" marker-end="url(#md-arrow-mvc)"/>
            <path class="md-edge" d="M85,157 L85,217" marker-start="url(#md-arrow-mvc)" marker-end="url(#md-arrow-mvc)"/>
            <path class="md-edge" d="M163,245 L197,245" marker-start="url(#md-arrow-mvc)" marker-end="url(#md-arrow-mvc)"/>
            <text class="md-label" x="62" y="70">запрос</text>
            <text class="md-label" x="252" y="70">HTML</text>
            <text class="md-label" x="93" y="191">данные</text>

            <rect class="md-node" x="120" y="8" width="120" height="34" rx="17"/>
            <text class="md-title" x="180" y="25">Браузер</text>
            <rect class="md-node" x="10" y="104" width="150" height="50" rx="10"/>
            <text class="md-title" x="85" y="121">Контроллер</text>
            <text class="md-sub" x="85" y="140">src/имя.php</text>
            <rect class="md-node" x="200" y="104" width="150" height="50" rx="10"/>
            <text class="md-title" x="275" y="121">Шаблон</text>
            <text class="md-sub" x="275" y="140">tpl/имя.php</text>
            <rect class="md-node" x="10" y="220" width="150" height="50" rx="10"/>
            <text class="md-title" x="85" y="237">Модель</text>
            <text class="md-sub" x="85" y="256">model/XModel.php</text>
            <rect class="md-node" x="200" y="220" width="150" height="50" rx="10"/>
            <text class="md-title" x="275" y="237">База данных</text>
            <text class="md-sub" x="275" y="256">MySQL</text>
        </svg>
        <figcaption>MVC: контроллер принимает запрос, берёт данные через модель и передаёт их в шаблон, а шаблон отдаёт HTML.</figcaption>
    </figure>
    <figure class="manual-figure">
        <svg class="manual-diagram" viewBox="0 0 470 320" role="img" aria-label="Схема HMVC: дерево компонентов страницы каталога">
            <defs>
                <marker id="md-arrow-hmvc" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse">
                    <path class="md-arrow" d="M0,0 L10,5 L0,10 z"/>
                </marker>
            </defs>
            <path class="md-edge" d="M235,44 V64 H79 V93" marker-end="url(#md-arrow-hmvc)"/>
            <path class="md-edge" d="M235,44 V64 H391 V93" marker-end="url(#md-arrow-hmvc)"/>
            <path class="md-edge" d="M235,64 V93" marker-end="url(#md-arrow-hmvc)"/>
            <path class="md-edge" d="M79,132 V181" marker-end="url(#md-arrow-hmvc)"/>
            <path class="md-edge" d="M235,132 V181" marker-end="url(#md-arrow-hmvc)"/>
            <path class="md-edge" d="M235,154 H391 V181" marker-end="url(#md-arrow-hmvc)"/>
            <path class="md-edge" d="M79,220 V269" marker-end="url(#md-arrow-hmvc)"/>
            <text class="md-label" x="242" y="84">$content</text>
            <text class="md-label" x="239" y="242" text-anchor="middle">× на каждый товар</text>

            <rect class="md-node" x="160" y="8" width="150" height="36" rx="10"/>
            <text class="md-code" x="235" y="26">_main_tpl</text>
            <rect class="md-node" x="4" y="96" width="150" height="36" rx="10"/>
            <text class="md-code" x="79" y="114">_main_tpl/menu</text>
            <rect class="md-node is-accent" x="160" y="96" width="150" height="36" rx="10"/>
            <text class="md-code" x="235" y="114">shop</text>
            <rect class="md-node" x="316" y="96" width="150" height="36" rx="10"/>
            <text class="md-code" x="391" y="114">_main_tpl/footer</text>
            <rect class="md-node" x="4" y="184" width="150" height="36" rx="10"/>
            <text class="md-code" x="79" y="202">_dev/menu</text>
            <rect class="md-node" x="164" y="188" width="150" height="36" rx="10"/>
            <rect class="md-node" x="160" y="184" width="150" height="36" rx="10"/>
            <text class="md-code" x="235" y="202">shop/_product_card</text>
            <rect class="md-node" x="316" y="184" width="150" height="36" rx="10"/>
            <text class="md-code" x="391" y="202">_dev/paginator</text>
            <rect class="md-node" x="4" y="272" width="150" height="36" rx="10"/>
            <text class="md-code" x="79" y="290">_dev/menu/sub_menu</text>
        </svg>
        <figcaption>HMVC в Micron: страница каталога. Роутер вызывает компонент <code>shop</code> (выделен), его вывод попадает в главный шаблон как <code>$content</code>, а каждый компонент сам подключает вложенные через <code>IncludeCom()</code>.</figcaption>
    </figure>
</div>

<h2>Middleware</h2>
<p>Код, который выполняется до компонента для определённой группы адресов: проверка авторизации админа, подготовка ответа API, загрузка пользователя. Подключается в роутере <code>core/init/router.php</code>.</p>

<h2>Миграция</h2>
<p>Файл в <code>core/migrations</code>, который меняет структуру БД (добавляет колонку, индекс и т. п.). Каждая миграция выполняется один раз, автоматически при первом запросе после обновления кода.</p>

<h2>Production-режим</h2>
<p>Работа сайта на боевом сервере: ошибки пишутся в лог, а не выводятся в браузер, письма отправляются по-настоящему, подключение к настоящей БД. Задаётся в <code>.env</code>: <code>DEBUG_MODE=0</code>, <code>ENV=production</code>.</p>

<h2>Debug-режим</h2>
<p>Режим для разработки, противоположный production: ошибки на экране, debug-панель внизу страницы, письма пишутся в файл. Задаётся <code>DEBUG_MODE=1</code>. Дополнительно <code>ENV=local</code> включает заглушки для SMS и геолокации.</p>
