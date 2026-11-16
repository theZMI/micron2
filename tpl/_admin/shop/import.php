<div class="container-fluid mb-4">
    <div class="row">
        <div class="col" style="max-width: 900px;">
            <h1 class="my-4">Импорт товаров</h1>
            <?= $msg ?>
            <?php if ($report): ?>
                <?= MsgOk("Готово: создано {$report['created']}, обновлено {$report['updated']}, снято с продажи {$report['hidden']}") ?>
                <?php if ($report['errors']): ?>
                    <?= MsgWarning('Пропущено строк: ' . count($report['errors']) . '<br>' . implode('<br>', array_map('htmlspecialchars', $report['errors']))) ?>
                <?php endif ?>
            <?php endif ?>

            <div class="row g-4">
                <div class="col-md-6">
                    <form action="<?= GetCurUrl() ?>" method="post" class="card card-body h-100">
                        <input type="hidden" name="a" value="google">
                        <h5>Из Google-таблицы</h5>
                        <?php if ($sheetUrl): ?>
                            <p class="small"><a href="<?= htmlspecialchars($sheetUrl) ?>" target="_blank">Открыть таблицу</a></p>
                        <?php else: ?>
                            <p class="small text-danger">Укажите ссылку в .env: <code>SHOP_IMPORT_GOOGLE_SHEET_URL</code></p>
                        <?php endif ?>
                        <button type="submit" class="btn btn-primary rounded-pill mt-auto" <?= $sheetUrl ? '' : 'disabled' ?> onclick="return confirm('Обновить каталог из таблицы?')">
                            <i class="bi bi-cloud-download pe-2"></i>Импортировать
                        </button>
                    </form>
                </div>
                <div class="col-md-6">
                    <form action="<?= GetCurUrl() ?>" method="post" enctype="multipart/form-data" class="card card-body h-100">
                        <input type="hidden" name="a" value="file">
                        <h5>Из CSV-файла</h5>
                        <input type="file" name="csv" accept=".csv,text/csv" class="form-control mb-3" required>
                        <button type="submit" class="btn btn-primary rounded-pill mt-auto" onclick="return confirm('Обновить каталог из файла?')">
                            <i class="bi bi-upload pe-2"></i>Загрузить
                        </button>
                    </form>
                </div>
            </div>

            <h2 class="h5 mt-5">Формат таблицы</h2>
            <p>Первая строка - заголовки, дальше по товару в строке. Товар ищется по артикулу: есть - обновляется, нет - создаётся.
                <?= Config('shop')['import']['hide_missing'] ? 'Товары, которых нет в таблице, снимаются с продажи (не удаляются).' : '' ?>
                Если заголовки неправильные, каталог не меняется.</p>
            <table class="table table-sm small">
                <thead><tr><th>Колонка</th><th>Можно назвать</th><th></th></tr></thead>
                <tbody>
                <tr><td><code>vendor_code</code></td><td>Артикул</td><td>обязательно, уникальный</td></tr>
                <tr><td><code>name</code></td><td>Название</td><td>обязательно</td></tr>
                <tr><td><code>price</code></td><td>Цена</td><td>обязательно, больше нуля. Можно «1 620,50»</td></tr>
                <tr><td><code>price_old</code></td><td>Старая цена</td><td>цена до скидки</td></tr>
                <tr><td><code>description</code></td><td>Описание</td><td></td></tr>
                <tr><td><code>sort</code></td><td>Порядок</td><td>меньше - выше в каталоге</td></tr>
                <tr><td><code>is_active</code></td><td>В продаже</td><td>да / нет (нет колонки - в продаже)</td></tr>
                <tr><td><code>P: Название</code></td><td></td><td>характеристика: «P: Состав», «P: Цвет», «P: Размер»...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
