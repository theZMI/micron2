<h1 class="my-3">Что это?</h1>
<p>Micron - это простой framework написанный на PHP и ориентированный на компонентную модель построения сайта (HMVC).</p>
<p>Почитайте подробности о нём в файле README.md, а так же посмотрите папки с примерами (src/examples и tpl/examples)</p>

<button type="button" class="btn btn-primary">Primary</button>
<button type="button" class="btn btn-secondary">Secondary</button>
<button type="button" class="btn btn-success">Success</button>
<button type="button" class="btn btn-danger">Danger</button>
<button type="button" class="btn btn-warning">Warning</button>
<button type="button" class="btn btn-info">Info</button>
<button type="button" class="btn btn-light">Light</button>
<button type="button" class="btn btn-dark">Dark</button>

<button type="button" class="btn btn-link">Link</button>

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