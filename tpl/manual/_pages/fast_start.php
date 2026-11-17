<h1>Быстрый старт</h1>
<p class="lead">Чтобы начать писать на Micron как можно скорее, достаточно усвоить несколько простых вещей.</p>

<dl class="manual-defs">
    <dt>Папка <code>src</code></dt>
    <dd>PHP-код страниц и компонентов (контроллеры).</dd>
    <dt>Папка <code>tpl</code></dt>
    <dd>HTML-шаблоны страниц и компонентов.</dd>
    <dt>Папка <code>i</code></dt>
    <dd>Ресурсы сайта: <code>i/scss</code> - стили (собираются webpack-ом), <code>i/css</code> и <code>i/js</code> - файлы, которые подключаются прямо в компонентах, <code>i/image</code> - картинки, <code>i/webpack</code> - сборщик.</dd>
    <dt>Компонент <code>_main_tpl</code></dt>
    <dd>Главный шаблон сайта (<code>tpl/_main_tpl.php</code>): шапка, меню, футер. В его середину выводится запрошенная страница - переменная <code>$content</code>.</dd>
    <dt><code>index.php</code></dt>
    <dd>Точка входа. Запускается первым при каждом запросе, подключает движок, через роутер находит нужные файлы страницы (<code>src</code> + <code>tpl</code>) и выводит результат внутри главного шаблона.</dd>
</dl>

<h2>В упрощённом виде схема работы Micron</h2>
<div class="manual-flow">
    <span>site.ru/about</span><i class="bi bi-arrow-right"></i>
    <span>index.php</span><i class="bi bi-arrow-right"></i>
    <span>роутер</span><i class="bi bi-arrow-right"></i>
    <span>src/about.php</span><i class="bi bi-arrow-right"></i>
    <span>tpl/about.php</span><i class="bi bi-arrow-right"></i>
    <span>_main_tpl</span><i class="bi bi-arrow-right"></i>
    <span>браузер</span>
</div>
<p>Подробнее - в разделе <a href="<?= $url('scheme') ?>">Ход выполнения приложения</a>.</p>

<h2>Начало работы</h2>
<p>Если у вас простой сайт с текстами и картинками, то всё, что нужно:</p>
<ol>
    <li><a href="<?= $url('setup') ?>">Установить движок</a> и открыть сайт в браузере.</li>
    <li>Поправить главный шаблон <code>tpl/_main_tpl.php</code> (меню - в <code>src/_main_tpl/menu.php</code>, футер - в <code>tpl/_main_tpl/footer.php</code>). В месте, где выводится середина страницы, уже стоит <code>&lt;?= $content ?&gt;</code>.</li>
    <li>Создавать страницы в <code>tpl/имя_страницы.php</code>, а код к ним (если он нужен) - в <code>src/имя_страницы.php</code>. Страница сразу доступна по адресу <code>site.ru/имя_страницы</code>.</li>
</ol>

<h2>Пример страницы на Micron</h2>
<p>Пусть нам нужна страница «Написать письмо» по адресу <code>site.ru/send_mail</code>, а письма должны приходить на <code>any-mail@example.com</code>.</p>
<p>Создадим файл-обработчик <code>src/send_mail.php</code>:</p>
<?= DocCode(<<<'CODE'
    <?php

    // Здесь будет сообщение над формой об ошибках или об успешной отправке
    $msg = '';

    // Нажали «Отправить»
    if (Post('is_send_mail')) {
        // Получаем данные через безопасные функции Post()
        $email  = trim(Post('email'));
        $letter = trim(Post('letter'));

        // Проверяем правильность введённых данных
        $errs = [];
        if (!IsValidEmail($email)) {
            $errs[] = 'Неправильный email адрес';
        }
        if ($letter === '') {
            $errs[] = 'Письмо не может быть пустым';
        }

        if ($errs) {
            $msg = MsgErr(implode('<br>', $errs));
        } else {
            $text   = "Письмо от: {$email}<br>Текст письма: {$letter}";
            $isSent = SendMail('any-mail@example.com', 'Письмо с сайта', $text);
            $msg    = $isSent ? MsgOk('Письмо успешно отправлено') : MsgErr('Произошла внутренняя ошибка, извините');
        }
    }
    CODE, 'php', 'src/send_mail.php') ?>
<p>Далее создадим HTML страницы <code>tpl/send_mail.php</code>:</p>
<?= DocCode(<<<'CODE'
    <head>
        <link rel="stylesheet" type="text/css" href="<?= Root('i/css/send_mail.css') ?>"/>
    </head>

    <h1 class="page-title">Отправка письма с сайта</h1>
    <form action="<?= GetCurUrl() ?>" method="post" class="send-mail">
        <?= $msg ?>
        <input type="hidden" name="is_send_mail" value="1">
        <div class="mb-3">
            <label class="form-label" for="i-email">Ваш email для ответа</label>
            <input type="email" class="form-control" id="i-email" name="email" value="<?= Post('email') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label" for="i-letter">Текст сообщения</label>
            <textarea class="form-control" id="i-letter" name="letter" rows="5"><?= Post('letter') ?></textarea>
        </div>
        <button type="submit" class="btn btn-dark">Отправить письмо</button>
    </form>
    CODE, 'php', 'tpl/send_mail.php') ?>
<p>Обратите внимание на <code>action</code> формы: он задан функцией <code>GetCurUrl()</code>, которая возвращает текущий адрес. Так мы, во-первых, не привязываемся к имени страницы, а во-вторых, если встроить этот компонент в другую страницу (например, <code>contacts</code>), то после отправки мы останемся на <code>contacts</code>, она снова подключит <code>send_mail</code> и его код отработает как надо.</p>
<p>Значения полей мы выводим через <code>Post()</code> - она уже экранирует HTML, поэтому вставлять их в шаблон безопасно.</p>
<p>Стили страницы лежат в собственном файле и подключены в <code>&lt;head&gt;</code> прямо внутри компонента. Micron перенесёт этот <code>&lt;head&gt;</code> в общий, а ExtraPacker склеит и сожмёт css-файл вместе с остальными:</p>
<?= DocCode(<<<'CODE'
    .send-mail {
        max-width: 560px;

        textarea {
            resize: vertical;
        }
    }
    CODE, 'css', 'i/css/send_mail.css') ?>
<p>Css-файлы, подключённые в компонентах, можно писать с вложенностью и переменными Less - ExtraPacker прогоняет их через компилятор. Общие стили сайта удобнее писать в SCSS (<code>i/scss</code>), их собирает webpack - смотрите раздел <a href="<?= $url('assets') ?>">CSS и JS</a>.</p>
<div class="manual-note">
    <p>На локальной копии (<code>DEBUG_MODE=1</code>) письма не отправляются, а пишутся в файл <code>tmp/sent_emails.txt</code> - так удобно проверять формы без настройки почты.</p>
</div>
<p>Всё: страница <code>site.ru/send_mail</code> с оформлением готова. Добавить её в меню можно в <code>src/_main_tpl/menu.php</code>.</p>

<h2>Что ещё полезно знать сразу</h2>
<ul>
    <li><code>SiteRoot('contacts')</code> - адрес страницы сайта (с учётом языка), <code>Root('i/image/logo.png')</code> - адрес файла.</li>
    <li><code>IncludeCom('имя')</code> - подключить компонент в любом месте, <code>ExitCom()</code> - не подключать дальше файлы текущего компонента.</li>
    <li><code>Get()</code> и <code>Post()</code> - безопасное получение данных от пользователя.</li>
    <li><code>Xmp($var)</code> - распечатать переменную при отладке.</li>
    <li>Компоненты, у которых имя (или часть пути) начинается с <code>_</code>, по адресу не открываются - это служебные блоки.</li>
</ul>
