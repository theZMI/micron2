<h1 class="my-3">Что это за движок:</h1>
<p>Micron - это простой framework написанный на PHP и ориентированный на компонентную модель построения сайта (HMVC).</p>

<h2 class="my-3">Структура движка и как с ним работать</h2>
[code=Bash]
📁 core - ядро движка
    📁 сonfig
    📁 func - ф-ии хелперы (для отправки email-ов, для отображения сообщений, для дебага, форматирвоания, работы с текстом и пр.)
    📁 init - тут происходит инициализия базы, роутинга, логгера ошибок и пр.
📁 i - js/css-файлы, картинки, файлы для загрузки (doc, xl, apk и что угодно другое что можно считать asset-ами)
📁 lang - переводы старниц/компонентов
📁 lib - библиотеки установленные не через composer
📁 model - классы для работы с БД (по технологии ActiveRecord т.е. формат вида $user1001 = new UserModel(1001); // Запись из таблицы users с id=1001)
📁 src - php-код страниц/компонентов (controllers)
📁 tmp - скомпилированные js/css-ки и всякие временные файлы (в public должно быть доступны только папки вида _auto_mege_css_js, но не сама папка tmp так как в ней текстовые логи ошибок и их отбражение - это небезопасно)
📁 tpl - шаблоны страниц/компонентов (views)
📁 vendor - библиотеки установленные composer-ом
📄 .env - файл с переменными зависящими от окружения (должно быть для production-а одни, для local-ки другие). Например подключение к БД, или домен сайта
📄 .gitignore - список тех файлов которые должны игнорироваться при коммитах проекта в git
📄 composer.json - библиотеки которые нужно установить в vendor для работы сайта (главный список)
📄 composer.lock - весь список с конкретными версиями и зависимостями установленными в vendor
📄 favicon.ico
📄 index.php - входная точка в движок. Все запросы на сайт (кроме запрос картинок и файлов) идут сюда, а этот файл уже подключает движок из core и нужную страницу из src+tpl
📄 README.md - хелп который вы сейчас читаете
📄 robots.txt - для google/yandex поисковик
[/code]

<h2 class="my-3">Что есть из коробки?</h2>
<ul>
    <li>Главный шаблон для сайта (шапка, футер)</li>
    <li>Админка</li>
    <li>Заготовка для пользовательского кабинета</li>
    <li>Пакеровщик (webpack) для работы с sass, less, typescript</li>
    <li>Мультиязычность (каталоговая модель)</li>
    <li>Работа с БД (DbSimple и простые модели)</li>
    <li>Инструменты для debug-а (debug-panel, простая ф-я распечатки -> Xmp</li>
    <li>Подключён Bootstrap 5 для дизайн</li>
    <li>Подключён шрифт PTSans</li>
    <li>Подключён Vue3</li>
    <li>Роутинг</li>
</ul>

<form action="<?= SiteRoot('test&a=123') ?>" method="post" class="mb-4">
    <input type="hidden" name="par_1" value="123">
    <input type="hidden" name="par_2" value="abc">
    <input type="hidden" name="par_3" value="<b>bold text</b><script>alert(hi)</script>('">
    <button class="btn btn-primary">Отправить POST на тестовый скрипт</button>
</form>
<?= L('Hello world') ?>
<div class="text-end text-black-50">
    Обновлено: <?= FormatDate(time()) ?>
</div>
        </div>
    </div>
</div>