<h1 class="my-4">Что это?</h1>
<p>Micron - это простой framework написанный на PHP и ориентированный на компонентную модель построения сайта (HMVC).</p>
<p>Почитайте подробности о нём в файле README.md, а так же посмотрите папки с примерами (src/examples и tpl/examples)</p>

<form action="<?= SiteRoot('home_test&a=123') ?>" method="post" class="mb-4">
    <input type="hidden" name="par_1" value="123">
    <input type="hidden" name="par_2" value="abc">
    <input type="hidden" name="par_3" value="<b>bold text</b><script>alert(hi)</script>('">
    <button class="btn btn-primary">Отправить POST на тестовый скрипт</button>
</form>
<div>Языкозависимый текст: <strong><?= L('Hello world') ?></strong></div>
<div class="text-end text-black-50">
    Обновлено: <?= FormatDate(time()) ?>
</div>