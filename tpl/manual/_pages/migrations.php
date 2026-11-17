<h1>Миграции БД</h1>
<p class="lead">Модели создают свои таблицы сами (<code>CREATE TABLE IF NOT EXISTS</code>), но существующую таблицу это не меняет. Изменения структуры - новые колонки, индексы, переименования - делаются миграциями.</p>

<h2>Как это устроено</h2>
<ul>
    <li>Миграция - файл <code>core/migrations/NNNN_описание.php</code>, который возвращает функцию <code>function ($db) { ... }</code>. Номер задаёт порядок: <code>0006_news_add_slug.php</code>.</li>
    <li>Применённые миграции записываются в таблицу <code>migrations</code>, каждая выполняется один раз.</li>
    <li>Новые миграции применяются автоматически при первом запросе к сайту после обновления кода. Чтобы не обращаться к БД на каждый запрос, имя последней применённой миграции хранится в файле <code>tmp/migrations_applied.txt</code>.</li>
    <li>В админке («Для техников → Миграции БД», <code>/_admin/migrate</code>) можно посмотреть список и запустить их вручную.</li>
</ul>

<h2>Пример</h2>
<?= DocCode(<<<'CODE'
    <?php

    // Новости: адрес-слаг для красивых ссылок
    return function ($db) {
        if (!Migrator::tableExists($db, 'news')) {
            return; // Таблицы ещё нет - модель создаст её сразу с нужными полями
        }
        if (!Migrator::columnExists($db, 'news', 'slug')) {
            $db->query("ALTER TABLE `news` ADD `slug` VARCHAR(255) NOT NULL DEFAULT '' AFTER `title`");
            $db->query("ALTER TABLE `news` ADD KEY `slug` (`slug`)");
        }
    };
    CODE, 'php', 'core/migrations/0006_news_add_slug.php') ?>
<p>И не забудьте добавить поле в <code>createTable()</code> и <code>scheme()</code> модели - для новых установок таблица сразу создастся правильной.</p>

<h2>Правила</h2>
<ul>
    <li>Пишите миграции так, чтобы их можно было запустить повторно: проверяйте <code>Migrator::tableExists()</code> и <code>Migrator::columnExists()</code>.</li>
    <li>Не меняйте уже применённую миграцию - сделайте новую.</li>
    <li>Не удаляйте данные без бэкапа: перед большими изменениями сделайте его в админке («Сделать backup БД»).</li>
</ul>
