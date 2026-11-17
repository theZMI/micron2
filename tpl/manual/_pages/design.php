<h1>Оформление и темы</h1>
<p class="lead">Сайт оформлен на Bootstrap 5.3 с собственными переменными, светлой и тёмной темой, шрифтом PT Sans и иконками Bootstrap Icons. Все стили - в <code>i/scss</code>, собираются webpack-ом.</p>

<h2>Главные файлы</h2>
<dl class="manual-defs">
    <dt><code>i/scss/_variables.scss</code></dt>
    <dd>Цвета, шрифты, скругления и прочие переменные Bootstrap - для светлой (<code>$body-bg</code>, <code>$body-color</code>) и тёмной темы (<code>$body-bg-dark</code>, <code>$body-color-dark</code>). Поменяйте <code>$primary</code> или шрифт - и изменится весь сайт.</dd>
    <dt><code>i/scss/components/theme.scss</code></dt>
    <dd>CSS-переменные сайта для обеих тем (<code>--site-panel</code>, <code>--site-line</code>, <code>--site-fill</code>...), «стеклянные» панели, кнопки-пилюли, анимация ссылок, переключатель темы.</dd>
    <dt><code>i/scss/_all.scss</code></dt>
    <dd>Список всех подключаемых стилей.</dd>
    <dt><code>i/scss/components/*</code>, <code>i/scss/*.scss</code></dt>
    <dd>Стили меню, футера, форм, страниц (магазин, кабинет, документация...).</dd>
</dl>

<h2>Светлая и тёмная тема</h2>
<p>Тема задаётся атрибутом <code>data-bs-theme="light|dark"</code> у <code>&lt;html&gt;</code> - это стандартный механизм Bootstrap 5.3. Скрипт <code>tpl/_main_tpl/theme_init.php</code> выполняется в самом начале <code>&lt;head&gt;</code>, поэтому страница не «мигает» при загрузке. Тема выбирается так:</p>
<ol>
    <li>выбор посетителя, сохранённый переключателем (<code>localStorage</code>, ключ <code>site-theme</code>);</li>
    <li>иначе - тема операционной системы;</li>
    <li>иначе - тёмная.</li>
</ol>
<p>Переключатель (солнце / луна) выводится компонентом <code>_main_tpl/theme_switch</code> в шапке, а смена темы анимируется кругом от кнопки (View Transitions API, где браузер его поддерживает).</p>

<h3>Как писать стили под обе темы</h3>
<p>Не задавайте цвета жёстко - используйте переменные, которые сами меняются вместе с темой:</p>
<?= DocCode(<<<'CODE'
    .news-card {
        @include site-glass;                     // Полупрозрачная панель с размытием и тенью
        border-radius: $border-radius-lg;
        color: var(--bs-body-color);             // Основной цвет текста
        border-bottom: 1px solid var(--site-line); // Тонкая линия

        .news-date {
            color: var(--bs-secondary-color);    // Второстепенный текст
        }

        &:hover {
            background: var(--site-fill);        // Лёгкая заливка
        }
    }

    // Если всё же нужно что-то только для тёмной темы:
    [data-bs-theme="dark"] .news-card {
        box-shadow: none;
    }
    CODE, 'css', 'i/scss/news.scss') ?>
<p>Классы Bootstrap (<code>text-body</code>, <code>text-secondary</code>, <code>bg-body-tertiary</code>, <code>border</code>...) тоже работают в обеих темах. На публичных страницах (класс <code>site-sky</code> у <code>&lt;html&gt;</code>) старые классы для светлого фона - <code>text-dark</code>, <code>bg-white</code>, <code>btn-primary</code> - автоматически переопределены, чтобы готовые шаблоны (вход, регистрация, оплата) читались и в тёмной теме.</p>

<h2>Размеры текста</h2>
<p>Размеры текста - только из шкалы Bootstrap 5 (переменные в <code>i/scss/_variables.scss</code>), своих промежуточных размеров не заводим:</p>
<div class="manual-table-wrap">
<table>
    <tr><th>Переменная</th><th>Размер</th><th>Для чего</th></tr>
    <tr><td><code>$site-font-xs</code></td><td>0.75rem</td><td>Счётчики, бейджи, метки, код в блоках</td></tr>
    <tr><td><code>$site-font-sm</code></td><td>0.875rem</td><td>Мелкий текст: футер, хлебные крошки, подписи, заметки, таблицы (то же, что <code>.small</code>)</td></tr>
    <tr><td><code>$site-font-md</code></td><td>1rem</td><td>Основной текст</td></tr>
    <tr><td><code>$site-font-lg</code></td><td>1.25rem</td><td><code>.lead</code>, акценты, итоговые суммы</td></tr>
    <tr><td><code>$site-h1</code> ... <code>$site-h5</code></td><td>2.5 / 2 / 1.75 / 1.5 / 1.25rem</td><td>Заголовки (как h1-h5 в Bootstrap)</td></tr>
    <tr><td><code>$site-page-title</code></td><td>от 2.5 до 3.5rem</td><td>Крупный заголовок страницы (<code>.page-title</code>)</td></tr>
</table>
</div>
<p>Размеры иконок к шкале не относятся - они подбираются под соседний текст.</p>

<h2>Кнопки и заголовки</h2>
<?= DocCode(<<<'CODE'
    <h1 class="page-title">Каталог</h1>  <!-- Крупный узкий заголовок страницы -->

    <a href="#" class="btn btn-dark">Главная кнопка<i class="bi bi-chevron-right"></i></a> <!-- Инверсия темы -->
    <a href="#" class="btn btn-light">Второстепенная</a>  <!-- Полупрозрачная -->
    <a href="#" class="btn btn-outline-dark">С рамкой</a>
    CODE) ?>
<p>Кнопкам в ряд удобно задавать одинаковую минимальную ширину: <code>.btn-min-width</code> (11.5rem) или <code>.btn-min-width-lg</code> (15rem, для крупных кнопок). У таких кнопок стрелка при наведении выезжает в свободное место справа и не меняет ширину кнопки.</p>
<p>Все кнопки - «пилюли» (полностью скруглённые). Главная кнопка тёмная в светлой теме и белая в тёмной. Иконка <code>bi-chevron-right</code> в кнопке выезжает при наведении.</p>

<h2>Главный шаблон</h2>
<dl class="manual-defs">
    <dt><code>tpl/_main_tpl.php</code></dt>
    <dd>Каркас страницы: <code>&lt;head&gt;</code> (общие мета-теги - <code>tpl/_common_head.php</code>), меню, хлебные крошки, контент, футер.</dd>
    <dt><code>src/_main_tpl/menu.php</code></dt>
    <dd>Пункты меню (массив: <code>link</code>, <code>name</code>, вложенный <code>list</code> для выпадающих списков), правое меню с иконками избранного, корзины и входа. Шапка прозрачная и становится «стеклом» при прокрутке.</dd>
    <dt><code>tpl/_main_tpl/footer.php</code></dt>
    <dd>Футер.</dd>
</dl>
<p>У админки свой главный шаблон (<code>_admin/_main_tpl</code>), она всегда светлая. У кабинета пользователя - свой внутренний шаблон (<code>user/_main_tpl</code>) внутри общего.</p>

<h2>Страница с отдельным оформлением</h2>
<?= DocCode(<<<'CODE'
    // В контроллере лендинга
    Config('main_tpl', '_landing_tpl');                        // Свой главный шаблон
    Config(['html_container_attrs', 'class'], 'landing-page'); // Свой класс у <html>
    CODE) ?>
