<h1 class="my-4">Test script</h1>
<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title">Полученные параметры</h5>
        <div class="card-text">
            $_GET: <?php __($_GET) ?>
        </div>
        <div class="card-text">
            $_POST: <?php __($_POST) ?>
        </div>
        <div class="card-text">
            Post('par_3'): <strong><?= Post('par_3') ?></strong>
            <p class="text-muted">Отфильтрованное значение переменной par_3</p>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Пример подключения компонента с заданным en-языком (по умолчанию автовыбор)</h5>
        <div class="card-text">
            <?php IncludeCom('en/home') ?>
        </div>
    </div>
</div>