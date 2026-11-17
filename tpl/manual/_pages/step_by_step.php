<h1>Создание сайта по шагам</h1>
<p class="lead">Сделаем простой сайт компании: главная, «О компании», «Продукция» и «Контакты» с формой обратной связи. Потом вынесем тексты в языковые файлы, чтобы их можно было править в админке.</p>

<h2>Шаг 1. Запускаем движок</h2>
<p>Положите движок в папку <code>site/super-site</code> рядом с docker-окружением, в <code>.env</code> окружения задайте <code>APP_DIR=./site/super-site</code> и свободный <code>APP_PORT</code>, затем:</p>
<?= DocCode(<<<'CODE'
    make build && make start
    make bash                 # Дальше - внутри контейнера
    cp .env.example .env      # И впишите в .env свои значения (смотрите ниже)
    composer install
    exit
    cd site/super-site/i/webpack && npm install && npm run build
    CODE, 'bash') ?>
<p>Подробнее - в разделе <a href="<?= $url('setup') ?>">Установка</a>. Откройте <code>localhost:APP_PORT</code> - сайт работает.</p>

<h2>Шаг 2. Настройка</h2>
<p>В <code>.env</code> сайта задайте:</p>
<?= DocCode(<<<'CODE'
    ENV=local
    DEBUG_MODE=1
    SITE_ROOT_URL=http://localhost:99/
    DATABASE_DSN=mysqli://user:pass@mysqldb/app_db?charset=utf8mb4
    ERROR_LOG_FILE=errors_k93hd.log        # Своё имя - так безопаснее
    DATABASE_LOG_FILE=db_errors_k93hd.log
    ADMIN_DEFAULT_LOGIN="admin"
    ADMIN_DEFAULT_PASSWORD="придумайте-надёжный-пароль"
    CRON_SECRET="результат openssl rand -hex 32"
    CODE, 'env', '.env') ?>
<p>Язык по умолчанию - русский (<code>DEF_LANG</code> в <code>core/config/languages.php</code>). Если нужен другой язык, добавьте его в <code>Config('langs')</code> там же.</p>

<h2>Шаг 3. Главный шаблон</h2>
<p>Главный шаблон уже свёрстан на Bootstrap 5: шапка с меню, хлебные крошки, футер, светлая и тёмная тема. Поправим под себя.</p>
<p>Меню - в <code>src/_main_tpl/menu.php</code>, массив <code>$menu</code>:</p>
<?= DocCode(<<<'CODE'
    $logo = [
        'logo'  => '<span class="site-logo-text">Company</span>',
        'link'  => SiteRoot(),
        'title' => 'Company',
        'css'   => 'site-logo me-5',
    ];

    $menu = [
        ['link' => SiteRoot(), 'name' => 'Главная'],
        ['link' => SiteRoot('about_company'), 'name' => 'О компании'],
        ['link' => SiteRoot('products'), 'name' => 'Продукция'],
        ['link' => SiteRoot('contacts'), 'name' => 'Контакты'],
    ];
    CODE, 'php', 'src/_main_tpl/menu.php') ?>
<p>Вызов <code>SiteRoot()</code> без параметров - это главная страница (<code>home</code>). Пункты справа (избранное, корзина, вход) - в массиве <code>$rightMenu</code>; если магазин не нужен, просто уберите их.</p>
<p>Футер - <code>tpl/_main_tpl/footer.php</code>, телефон и почта компании - <code>lang/ru/_autoload/contacts.php</code> (выводятся через <code>SUPPORT_PHONE</code> и <code>SUPPORT_EMAIL</code>). Название сайта в заголовке окна - <code>lang/ru/_autoload/main.php</code>.</p>
<p>Цвета и шрифты - <code>i/scss/_variables.scss</code>. После правок стилей выполните <code>npm run build</code> (или держите запущенным <code>npm run dev</code>).</p>

<h2>Шаг 4. Страницы</h2>
<p>Сейчас по пунктам меню открывается страница 404 - создадим страницы. Это обычные статичные страницы, поэтому нужны только шаблоны: <code>tpl/home.php</code> (уже есть - замените содержимое), <code>tpl/about_company.php</code>, <code>tpl/products.php</code>, <code>tpl/contacts.php</code>.</p>
<?= DocCode(<<<'CODE'
    <h1 class="page-title">О нашей компании</h1>
    <p>Мы делаем лучшие в мире табуретки с 1998 года.</p>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit...</p>
    CODE, 'php', 'tpl/about_company.php') ?>
<p>Заголовок окна возьмётся из <code>&lt;h1&gt;</code> автоматически. Страницы сразу доступны по адресам <code>/about_company</code>, <code>/products</code>, <code>/contacts</code>.</p>

<h2>Шаг 5. Форма обратной связи</h2>
<p>Добавим форму на страницу контактов:</p>
<?= DocCode(<<<'CODE'
    <h1 class="page-title">Наши контакты</h1>
    <address>
        <strong>ООО «Компания»</strong><br>
        Москва, ул. Пушкина, 1<br>
        Телефон: <a href="tel:<?= PhoneFilter(SUPPORT_PHONE) ?>"><?= SUPPORT_PHONE ?></a>
    </address>

    <h3 class="mt-5">Отправить сообщение / Задать вопрос</h3>
    <form action="<?= GetCurUrl() ?>#contact-form" method="post" id="contact-form" style="max-width: 560px">
        <?= $msg ?>
        <input type="hidden" name="is_send" value="1">
        <div class="mb-3">
            <label class="form-label" for="i-email">Ваш e-mail</label>
            <input type="email" class="form-control" id="i-email" name="email" value="<?= Post('email') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label" for="i-text">Текст вопроса/сообщения</label>
            <textarea class="form-control" id="i-text" name="text" rows="4"><?= Post('text') ?></textarea>
        </div>
        <button type="submit" class="btn btn-dark"><i class="bi bi-envelope pe-2"></i>Отправить сообщение</button>
    </form>
    CODE, 'php', 'tpl/contacts.php') ?>
<p>И обработчик:</p>
<?= DocCode(<<<'CODE'
    <?php

    $msg = '';

    if (Post('is_send')) {
        $errs  = [];
        $email = trim(Post('email'));
        $text  = trim(Post('text'));

        if (!IsValidEmail($email)) {
            $errs[] = 'Некорректный e-mail';
        }
        if ($text === '') {
            $errs[] = 'Пустой текст сообщения';
        }

        if (!$errs) {
            $lines = [
                "Email пользователя: {$email}",
                "Текст сообщения: {$text}",
            ];
            if (!SendMail(SUPPORT_EMAIL, 'Сообщение с сайта компании', implode('<br>', $lines))) {
                $errs[] = 'Не удалось отправить сообщение, попробуйте позже';
            }
        }

        if ($errs) {
            $msg = MsgErr(implode('<br>', $errs));
        } else {
            $msg   = MsgOk('Сообщение успешно отправлено');
            $_POST = []; // Чтобы форма очистилась
        }
    }
    CODE, 'php', 'src/contacts.php') ?>
<p>Код не требует пояснений. Замечу лишь, что <code>IsValidEmail</code> - стандартная функция движка (как <code>PhoneFilter</code>, <code>IsValidPhone</code>, <code>IsValidUrl</code>) из <code>core/func/validation.php</code>, а вместо прямого обращения к <code>$_POST</code> мы везде используем <code>Post()</code> - она очищает ввод от HTML и XSS.</p>
<p>На локальной копии письма не уходят на почту, а пишутся в <code>tmp/sent_emails.txt</code>. На сервере задайте почтовый ящик в <code>EMAIL_*</code> в <code>.env</code>.</p>
<p>Поздравляю! Минимальный сайт готов. Дальше сделаем его удобнее для владельца.</p>

<h2>Шаг 6. Тексты в языковые файлы</h2>
<p>Если править тексты через админку или делать многоязычность не нужно, можно оставить всё как есть. Иначе вынесем тексты в языковой файл. Сделаем это для контактов:</p>
<?= DocCode(<<<'CODE'
    <?php

    $g_lang['title__noTags'] = 'Наши контакты';
    $g_lang['address']       = '<strong>ООО «Компания»</strong><br>Москва, ул. Пушкина, 1';
    $g_lang['form_title__noTags'] = 'Отправить сообщение / Задать вопрос';
    $g_lang['email__noTags']      = 'Ваш e-mail';
    $g_lang['text__noTags']       = 'Текст вопроса/сообщения';
    $g_lang['send__noTags']       = 'Отправить сообщение';

    $g_lang['err_email__noTags']  = 'Некорректный e-mail';
    $g_lang['err_text__noTags']   = 'Пустой текст сообщения';
    $g_lang['err_send__noTags']   = 'Не удалось отправить сообщение, попробуйте позже';
    $g_lang['success__noTags']    = 'Сообщение успешно отправлено';
    CODE, 'php', 'lang/ru/contacts.php') ?>
<p>В шаблоне и контроллере заменяем тексты на <code>L()</code>:</p>
<?= DocCode(<<<'CODE'
    <h1 class="page-title"><?= L('title__noTags') ?></h1>
    <address><?= L('address') ?></address>
    <h3 class="mt-5"><?= L('form_title__noTags') ?></h3>
    ...
    <label class="form-label" for="i-email"><?= L('email__noTags') ?></label>
    ...

    // и в src/contacts.php:
    $errs[] = L('err_email__noTags');
    $msg    = MsgOk(L('success__noTags'));
    CODE) ?>
<p>Постфикс <code>__noTags</code> говорит админке, что для этого текста не нужен визуальный редактор - хватит простого поля. Так заголовок <code>&lt;h1&gt;Наши контакты&lt;/h1&gt;</code> не превратится в <code>&lt;h1&gt;&lt;p&gt;Наши контакты&lt;/p&gt;&lt;/h1&gt;</code>.</p>
<p>Почему бы не поместить всю страницу в одну переменную? Причины две: не все теги корректно редактируются в визуальном редакторе, а человек, далёкий от вёрстки, легко превратит <code>&lt;h1&gt;</code> в <code>&lt;p&gt;</code> с большим шрифтом. Поэтому заголовки и сложные блоки (как <code>&lt;address&gt;</code>) лучше держать отдельно. Подробнее - <a href="<?= $url('langs') ?>">Мультиязычность</a>.</p>

<h2>Шаг 7. Правка текстов в админке</h2>
<p>Откройте <code>/_admin</code>, войдите под логином и паролем из <code>.env</code> и зайдите в «Страницы» - там видны все языковые файлы. Тексты с HTML редактируются в CKEditor, тексты с <code>__noTags</code> - в обычном поле. Там же задаются заголовок и описание страницы для поисковиков.</p>

<h2>Что дальше</h2>
<ul>
    <li>Нужен магазин? Он уже есть - <a href="<?= $url('shop') ?>">Магазин</a> и <a href="<?= $url('payments') ?>">Приём оплаты</a>.</li>
    <li>Нужны свои данные в БД? <a href="<?= $url('db') ?>">Модели</a> и <a href="<?= $url('migrations') ?>">миграции</a>.</li>
    <li>Выкладываем на сервер - <a href="<?= $url('servers') ?>">Apache2 и NGINX</a> и <a href="<?= $url('security') ?>">Безопасность</a>.</li>
</ul>
