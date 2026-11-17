<h1 class="my-4">Подсветка программного кода</h1>
<p>Код экранируется на сервере (<code>lib/CodeHighlight.php</code>), а раскрашивает его highlight.js - он загружается отдельным файлом и только на страницах, где есть код.</p>

<h3 class="mt-4">1. В шаблоне - функция DocCode()</h3>
<p>Код передаётся nowdoc-строкой, поэтому внутри можно писать что угодно - он не выполнится. Третий параметр - подпись над блоком.</p>
<?= DocCode(<<<'CODE'
    <?php

    class NewsModel extends SiteModel
    {
        public function __construct($id = null)
        {
            parent::__construct('news', $id);
        }

        // Последние опубликованные новости
        public function getPublished(int $limit = 10): array
        {
            $ids = $this->db->selectCol(
                "SELECT `id` FROM ?# WHERE `is_published` = 1 ORDER BY `id` DESC LIMIT ?d",
                $this->table,
                $limit
            );
            return array_map(fn($id) => new self($id), $ids);
        }
    }
    CODE, 'php', 'model/NewsModel.php') ?>

<p>Шаблон, где HTML смешан с PHP, подсвечивается и как HTML, и как PHP:</p>
<?= DocCode(<<<'CODE'
    <ul class="news-list">
        <?php foreach ($news as $item): ?>
            <li><a href="<?= SiteRoot('news&id=' . $item->id) ?>"><?= $item->title ?></a></li>
        <?php endforeach; ?>
    </ul>
    CODE, 'php', 'tpl/news.php') ?>

<h3 class="mt-4">2. В любом тексте - разметка [code=язык]</h3>
<p>Подходит для текстов из языковых файлов и страниц, которые редактируются в админке: блок заменяется при выводе страницы.</p>

[code=js]
// Добавление товара в корзину без перезагрузки страницы
document.querySelectorAll('[data-cart-add]').forEach(button => {
    button.addEventListener('click', async () => {
        const response = await fetch('/shop/cart', {method: 'POST', body: new FormData(button.form)});
        const data = await response.json();
        console.log(`В корзине ${data.count} товаров`);
    });
});
[/code]

[code=scss]
.news-list {
  li {
    padding: 0.5rem 0;
    color: var(--bs-secondary-color);
  }
}
[/code]

[code=sql]
SELECT `id`, `name`, `price` FROM `shop_products` WHERE `is_active` = 1 ORDER BY `sort`, `id` DESC LIMIT 24;
[/code]

[code=bash]
cd i/webpack && npm run build   # Собрать стили и скрипты
[/code]

<p class="mt-4">Языки: php (с HTML - как шаблон), html, js, ts, css/scss/less, bash, env/ini, sql, json, nginx, apache, c++, plain.</p>
