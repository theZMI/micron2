<div class="container">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-3">Добро пожаловать</h1>
            <p>Это главная страница сайта.</p>
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