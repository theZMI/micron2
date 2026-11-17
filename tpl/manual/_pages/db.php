<h1>Работа с моделями и БД</h1>
<ul>
    <li>Работа с БД идёт через класс DbSimple (пакет <code>thezmi/dbsimple</code>, развитие DbSimple от DkLab) - с плейсхолдерами, которые защищают от SQL-инъекций.</li>
    <li>Можно работать с несколькими БД сразу.</li>
    <li>Подключение к БД происходит только при первом запросе - страницы без запросов к БД не тратят время на подключение.</li>
    <li>Ошибочные запросы пишутся в лог-файл, а все запросы в debug-режиме видны в debug-панели.</li>
</ul>

<h2>Настройка подключения</h2>
<p>Подключение задаётся в <code>.env</code> строкой DSN (Data Source Name) - это обычная строка, в которой через разделители указаны все параметры:</p>
<?= DocCode(<<<'CODE'
    # Формат: mysqli://ПОЛЬЗОВАТЕЛЬ:ПАРОЛЬ@ХОСТ/ИМЯ_БД?charset=КОДИРОВКА
    DATABASE_DSN=mysqli://user:pass@mysqldb/app_db?charset=utf8mb4
    DATABASE_LOG_FILE=db_errors.log
    CODE, 'env', '.env') ?>
<p>У локальной копии и боевого сервера просто разные файлы <code>.env</code> - в коде ничего менять не нужно.</p>
<p>Сами подключения описаны в <code>core/config/db.php</code>:</p>
<?= DocCode(<<<'CODE'
    Config('db_simple', [
        'logDbInfo'  => Env('DEBUG_MODE'), // Собирать все запросы (для debug-панели)
        'logDbError' => true,              // Писать ошибочные запросы в dbLogFile
        'dbLogFile'  => BASEPATH . 'tmp/' . Env('DATABASE_LOG_FILE'),
        'databases'  => [
            'db'         => ['dsn' => Env('DATABASE_DSN'), 'pCacheFunc' => ['MyDataBaseCache', 'staticCache']],
            // Ещё БД - просто перечислите их:
            'dbPayments' => ['dsn' => Env('DATABASE_PAYMENTS_DSN')],
        ],
    ]);
    CODE, 'php', 'core/config/db.php') ?>
<p>Основная БД доступна через <code>GetDefaultDb()</code>, остальные - через <code>Dbs::getInstance()-&gt;getDatabases()-&gt;dbPayments</code>.</p>
<p>Имя лог-файла ошибок задавайте своё, а не стандартное, - это повышает безопасность. Отключать логирование ошибок не нужно даже на боевом сервере.</p>

<h2>Запросы и плейсхолдеры</h2>
<p>Никогда не вставляйте данные в запрос склейкой строк - только через плейсхолдеры:</p>
<?= DocCode(<<<'CODE'
    $db = GetDefaultDb();

    $user  = $db->selectRow("SELECT * FROM `users` WHERE `id` = ?d", $id);          // ?d - целое число
    $users = $db->select("SELECT * FROM ?# WHERE `login` = ?", 'users', $login);   // ?# - имя таблицы/поля, ? - строка
    $ids   = $db->selectCol("SELECT `id` FROM `users` WHERE `id` IN (?a)", [1, 2]); // ?a - список
    $count = $db->selectCell("SELECT COUNT(*) FROM `users`");                      // одно значение
    $db->query("UPDATE `users` SET ?a WHERE `id` = ?d", ['first_name' => 'Иван'], $id); // ?a с ключами - SET

    // Необязательная часть запроса: если передать DBSIMPLE_SKIP, блок {...} выбросится
    $list = $db->select(
        "SELECT * FROM `shop_products` WHERE 1 { AND `price` >= ?d } ORDER BY `id`",
        $minPrice ?: DBSIMPLE_SKIP
    );
    CODE) ?>

<h2>Транзакции</h2>
<?= DocCode(<<<'CODE'
    // Модели создаём ДО транзакции (см. предупреждение ниже)
    new ShopOrderModel();
    new ShopProductModel();

    $orderId = DbTransaction(function ($db) use ($items) {
        // ... несколько связанных изменений
        // Любое исключение внутри - и всё откатится
        return $order->flush();
    });
    CODE) ?>
<div class="manual-note is-warning">
    <p>Модель при первом создании в процессе выполняет <code>CREATE TABLE IF NOT EXISTS</code>, а любой DDL-запрос в MySQL неявно завершает транзакцию. Поэтому создайте нужные модели до <code>DbTransaction()</code> - иначе rollback не сработает.</p>
</div>

<h2>Кеширование запросов</h2>
<p>Чтобы закешировать запрос, оберните его в <code>AddDbCache()</code> (или напишите перед ним строку <code>-- CACHE: 1h 5m</code>):</p>
<?= DocCode(<<<'CODE'
    $countries = $db->select(AddDbCache("SELECT * FROM `countries`"));
    CODE) ?>
<p>По умолчанию кеш живёт в пределах одного запроса к сайту (убирает повторы одинаковых запросов). Для кеша с временем жизни на диске укажите <code>'pCacheFunc' =&gt; ['MyDataBaseCache', 'fileCache']</code>.</p>

<h2>Модели</h2>
<p>Модель - класс для работы с данными объектов системы: пользователей, заказов, товаров. Модели хранятся в папке <code>/model</code> и подключаются автоматически: если в коде встречается <code>new UserModel()</code>, то будет подключён файл <code>model/UserModel.php</code>.</p>
<p>В 99 случаях из 100 модель работает с данными в БД. Код</p>
<?= DocCode(<<<'CODE'
    $user = new UserModel(1001);
    $user->first_name = 'Иван';
    CODE) ?>
<p>по результату равен запросу <code>UPDATE `users` SET `first_name` = 'Иван' WHERE `id` = 1001</code>. Но в первом случае мы работаем с логической сущностью «пользователь», а не с данными напрямую. Это приятнее идеологически, делает код менее зависимым и более читаемым, а кроме того, открывает много возможностей: вычисляемые поля, прозрачное логирование, кеширование и пр.</p>

<h2>Иерархия моделей</h2>
<dl class="manual-defs">
    <dt><code>Models\ModelOptimized</code></dt>
    <dd>Основа: чтение по ключу, запись изменённых полей, удаление. Данные загружаются лениво и пачкой: если вы создали 50 моделей по id, то при первом обращении к полю будет один запрос <code>WHERE id IN (...)</code>, а не 50.</dd>
    <dt><code>Models\ModelExtends</code></dt>
    <dd>Ключ <code>id</code>, основная БД, автоматическое <code>create_time</code>, метод <code>getList($page)</code>.</dd>
    <dt><code>ModelWithScheme</code></dt>
    <dd>Схема типов полей: значения приводятся к <code>int</code>/<code>float</code>/<code>bool</code>/<code>string</code>, поэтому <code>$user-&gt;id</code> - число, а не строка из MySQL.</dd>
    <dt><code>SiteModel</code></dt>
    <dd>Базовая модель сайта: дополнительно обновляет <code>last_update_time</code> при сохранении. Наследуйте свои модели от неё.</dd>
</dl>

<h2>Своя модель</h2>
<p>Пусть нам нужна модель новостей. Создадим файл <code>model/NewsModel.php</code>:</p>
<?= DocCode(<<<'CODE'
    <?php

    class NewsModel extends SiteModel
    {
        public function __construct($id = null)
        {
            parent::__construct('news', $id); // Таблица news, ключ - id
        }

        public function scheme()
        {
            return [
                'id'               => 'int',
                'title'            => 'string',
                'text'             => 'string',
                'is_published'     => 'bool',
                'create_time'      => 'int',
                'last_update_time' => 'int',
            ];
        }

        // Выполняется при первом создании модели в процессе: таблица появится сама
        protected function createTable()
        {
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS ?# (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `title` VARCHAR(255) NOT NULL DEFAULT '',
                    `text` TEXT,
                    `is_published` TINYINT(1) NOT NULL DEFAULT 0,
                    `create_time` INT NOT NULL DEFAULT 0,
                    `last_update_time` INT NOT NULL DEFAULT 0,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
                $this->table
            );
        }

        // Свои методы: выборки, вычисляемые значения
        public function getPublished(int $limit = 10): array
        {
            $ids = $this->db->selectCol(
                "SELECT `id` FROM ?# WHERE `is_published` = 1 ORDER BY `create_time` DESC LIMIT ?d",
                $this->table,
                $limit
            );
            return array_map(fn($id) => new self($id), $ids);
        }
    }
    CODE, 'php', 'model/NewsModel.php') ?>
<p>Теперь мы можем:</p>
<?= DocCode(<<<'CODE'
    // Читать
    $news = new NewsModel(123);
    if ($news->isExists()) {
        echo $news->title;
    }

    // Изменять
    $news->title = 'Новый заголовок';
    $news->flush(); // Если не вызвать, сохранится само в деструкторе

    // Добавлять
    $news               = new NewsModel();
    $news->title        = 'Первая новость';
    $news->is_published = true;
    $id = $news->flush(); // id новой записи; create_time выставится сам

    // Удалять
    (new NewsModel(123))->delete();

    // Получать списки
    $all   = (new NewsModel())->getList();   // Все записи (новые сначала)
    $page2 = (new NewsModel())->getList(2);  // Вторая страница по PAGE_LIMIT (100) записей
    $last  = (new NewsModel())->getPublished(5);

    // Все данные записи массивом (с приведением типов)
    $data = $news->getData();
    CODE) ?>
<p>Новые поля добавляйте <a href="<?= $url('migrations') ?>">миграцией</a>: <code>CREATE TABLE IF NOT EXISTS</code> не меняет уже существующую таблицу.</p>
<div class="manual-note">
    <p>Будьте осторожны с <code>$model-&gt;id</code> у новой модели, в которую уже что-то записано: обращение к <code>id</code> сохраняет запись (чтобы вернуть настоящий id). Если вы ещё не решили, сохранять ли запись, проверяйте <code>isExists()</code>.</p>
</div>

<h2>Виртуальные поля</h2>
<p>Предположим, нужно знать время последнего визита пользователя. Быстрое решение - поле <code>last_activity</code>, которое обновляется при входе. Позже, когда понадобится история входов, можно завести таблицу <code>user_activities</code>, а чтобы не менять код, где уже написано <code>$user-&gt;last_activity</code>, перехватить чтение поля магическим методом <code>__get</code>:</p>
<?= DocCode(<<<'CODE'
    class UserModel extends CommonUserModel
    {
        public function __get($key)
        {
            if ($key === 'last_activity') {
                return (int)$this->db->selectCell(
                    "SELECT MAX(`time`) FROM `user_activities` WHERE `user_id` = ?d",
                    $this->id
                );
            }
            return parent::__get($key);
        }
    }
    CODE) ?>
<p>Аналогично можно перехватить запись (<code>__set($key, $value)</code>) и, например, при записи <code>$user-&gt;last_activity</code> добавлять строку в <code>user_activities</code>. Имена полей - обычные строки, поэтому можно сделать и «умные» поля: <code>$user-&gt;avatar32x32</code> находит или создаёт на лету миниатюру нужного размера.</p>

<h2>Поле-JSON</h2>
<p>Для произвольных настроек удобно хранить значения в одной JSON-колонке - трейт <code>JsonFieldTrait</code>:</p>
<?= DocCode(<<<'CODE'
    class UserModel extends CommonUserModel
    {
        use JsonFieldTrait;

        public function jsonFieldName(): string
        {
            return 'settings'; // TEXT-колонка в таблице
        }
    }

    $user->setJsonValue('theme', 'dark');
    $user->flush();
    echo $user->getJsonValue('theme', 'light'); // 'light' - значение по умолчанию
    CODE) ?>

<h2>Другие готовые модели</h2>
<dl class="manual-defs">
    <dt><code>MockupModel</code></dt>
    <dd>Модель с данными из массива вместо БД - удобно для прототипов и тестов. Наследник реализует <code>_pseudoDB()</code>.</dd>
    <dt><code>ApiModel</code></dt>
    <dd>Модель, данные которой приходят из внешнего API (через <code>ApiClient</code>).</dd>
    <dt><code>MicronCache</code></dt>
    <dd>Кеш значений в БД (функции <code>MemoryGet</code>/<code>MemorySet</code>).</dd>
    <dt><code>Models\ModelDoubleIndex</code></dt>
    <dd>Модель таблицы с составным ключом из двух полей.</dd>
</dl>
