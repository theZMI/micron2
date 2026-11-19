<div class="container-fluid">
    <div class="row">
        <div class="col" style="max-width: 900px;">
            <h1 class="my-4">Импорт товаров</h1>
            <?= $msg ?>
            <?php if ($report): ?>
                <?= MsgOk("Готово: создано {$report['created']}, обновлено {$report['updated']}, снято с продажи {$report['hidden']}") ?>
                <?php if ($report['errors']): ?>
                    <?= MsgWarning('Замечания (' . count($report['errors']) . '):<br>' . implode('<br>', array_map('htmlspecialchars', $report['errors']))) ?>
                <?php endif ?>
            <?php endif ?>

            <?php $st = $imagesStatus; $editUrl = SiteRoot('_admin/shop/products/add_or_edit&id=') ?>
            <?php if ($st['products'] > 0 || $imagesLog['lines']): ?>
                <!-- Картинки из таблицы: состояние из БД, скачивание порциями и журнал (обновляются сами, см. скрипт ниже) -->
                <div class="card card-body mb-4" id="shop-images"
                     data-run-url="<?= SiteRoot('_admin/shop/import_images') ?>" data-log-url="<?= SiteRoot('_admin/shop/import_log') ?>"
                     data-edit-url="<?= $editUrl ?>" data-offset="<?= $imagesLog['offset'] ?>" data-autostart="<?= $autoStart ? 1 : 0 ?>">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                        <h5 class="mb-0 me-auto">Картинки из таблицы</h5>
                        <span class="badge rounded-pill" data-img-state></span>
                    </div>
                    <div class="small mb-2" data-img-stats>
                        Товаров с картинками: <b><?= $st['with_images'] ?></b> из <?= $st['products'] ?> ·
                        файлов скачано: <b><?= $st['files'] ?></b> из <?= $st['files_total'] ?> ·
                        в очереди товаров: <b><?= $st['pending'] ?></b>
                    </div>
                    <div class="progress mb-3" role="progressbar" style="height: 6px;">
                        <div class="progress-bar" data-img-progress style="width: <?= $st['files_total'] ? round($st['files'] / $st['files_total'] * 100) : 100 ?>%"></div>
                    </div>
                    <form action="<?= GetCurUrl() ?>" method="post" class="d-flex flex-wrap gap-2 mb-3">
                        <input type="hidden" name="a" value="images">
                        <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill" data-img-run><i class="bi bi-images pe-1"></i>Скачать картинки</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill d-none" data-img-stop><i class="bi bi-pause-fill pe-1"></i>Остановить</button>
                    </form>
                    <details class="mb-3 small" data-img-failed-box<?= $st['failed'] ? '' : ' hidden' ?>>
                        <summary class="text-danger">Не скачались: <span data-img-failed-count><?= count($st['failed']) ?></span></summary>
                        <div class="table-responsive mt-2">
                            <table class="table table-sm mb-0">
                                <thead><tr><th>Артикул</th><th>Файл</th><th>Причина</th></tr></thead>
                                <tbody data-img-failed>
                                <?php foreach ($st['failed'] as $f): ?>
                                    <tr><td><a href="<?= $editUrl . $f['product_id'] ?>"><?= $f['vendor_code'] ?></a></td><td><?= htmlspecialchars($f['source']) ?></td><td><?= htmlspecialchars($f['error']) ?></td></tr>
                                <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-secondary mt-1">Загрузите файл в источник или исправьте имя в таблице - при следующем импорте он скачается.</div>
                    </details>
                    <div class="small text-secondary mb-1">Журнал (<code><?= ShopProductImporter::IMAGES_LOG ?></code>), обновляется сам - в том числе когда качает крон:</div>
                    <pre class="mb-0 p-2 rounded small" data-img-log style="max-height: 320px; overflow: auto; background: #0B1220; color: #D7DEE8; white-space: pre-wrap;"><?php foreach ($imagesLog['lines'] as $line): ?><div class="<?= str_contains($line, 'НЕ СКАЧАЛАСЬ') ? 'text-danger' : (str_contains($line, '──') ? 'text-info' : '') ?>"><?= htmlspecialchars($line) ?></div><?php endforeach ?></pre>
                </div>
                <script>
                (() => {
                    const box = document.getElementById('shop-images');
                    const $ = sel => box.querySelector(sel);
                    const logEl = $('[data-img-log]');
                    const esc = s => String(s).replace(/[&<>"']/g, c => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c]));
                    const sameOrigin = url => { const u = new URL(url, location.href); return u.pathname + u.search; };
                    let offset = parseInt(box.dataset.offset, 10) || 0;
                    let running = false; // Качает эта страница
                    let stopped = false;
                    let status = null;

                    function render(st) {
                        status = st;
                        $('[data-img-stats]').innerHTML = `Товаров с картинками: <b>${st.with_images}</b> из ${st.products} · файлов скачано: <b>${st.files}</b> из ${st.files_total} · в очереди товаров: <b>${st.pending}</b>`;
                        $('[data-img-progress]').style.width = (st.files_total ? Math.round(st.files / st.files_total * 100) : 100) + '%';
                        $('[data-img-failed-count]').textContent = st.failed.length;
                        $('[data-img-failed-box]').hidden = !st.failed.length;
                        $('[data-img-failed]').innerHTML = st.failed.map(f => `<tr><td><a href="${box.dataset.editUrl}${f.product_id}">${esc(f.vendor_code)}</a></td><td>${esc(f.source)}</td><td>${esc(f.error)}</td></tr>`).join('');
                        const busy = running || st.is_running;
                        const state = $('[data-img-state]');
                        state.className = 'badge rounded-pill ' + (busy ? 'text-bg-primary' : (st.pending ? 'text-bg-warning' : 'text-bg-success'));
                        state.textContent = busy ? (running ? 'Качаем…' : 'Качает другой процесс (крон)…') : (st.pending ? 'Ждут скачивания' : 'Всё скачано');
                        $('[data-img-progress]').classList.toggle('progress-bar-striped', busy);
                        $('[data-img-progress]').classList.toggle('progress-bar-animated', busy);
                        $('[data-img-run]').disabled = busy || !st.pending;
                        $('[data-img-stop]').classList.toggle('d-none', !running);
                    }

                    function addLines(lines) {
                        if (!lines.length) return;
                        const atBottom = logEl.scrollTop + logEl.clientHeight >= logEl.scrollHeight - 20;
                        logEl.insertAdjacentHTML('beforeend', lines.map(l => `<div class="${l.includes('НЕ СКАЧАЛАСЬ') ? 'text-danger' : (l.includes('──') ? 'text-info' : '')}">${esc(l)}</div>`).join(''));
                        while (logEl.children.length > 1000) logEl.firstChild.remove();
                        if (atBottom) logEl.scrollTop = logEl.scrollHeight;
                    }

                    // Журнал: раз в секунду, пока качаем (или качает крон), иначе раз в 5 секунд; состояние - через раз
                    let tick = 0;
                    async function poll() {
                        const busy = running || (status && status.is_running);
                        const withStatus = !running && (tick++ % 3 === 0 || busy);
                        try {
                            const r = await fetch(sameOrigin(box.dataset.logUrl) + (box.dataset.logUrl.includes('?') ? '&' : '?') + 'offset=' + offset + (withStatus ? '&status=1' : ''), {headers: {'X-Requested-With': 'XMLHttpRequest'}, credentials: 'same-origin'});
                            const json = await r.json();
                            if (json.is_success) {
                                offset = json.data.offset;
                                addLines(json.data.lines);
                                if (json.data.status) render(json.data.status);
                            }
                        } catch (e) {}
                        setTimeout(poll, busy ? 1000 : 5000);
                    }

                    // Скачивание: порции по ~25 секунд одна за другой, пока очередь не опустеет или не нажали «Остановить»
                    async function run() {
                        running = true;
                        stopped = false;
                        $('[data-img-stop]').disabled = false;
                        render(status || {products: 0, with_images: 0, files: 0, files_total: 0, pending: 1, failed: [], is_running: false});
                        while (!stopped) {
                            try {
                                const r = await fetch(sameOrigin(box.dataset.runUrl), {method: 'POST', headers: {'X-Requested-With': 'XMLHttpRequest'}, credentials: 'same-origin'});
                                const json = await r.json();
                                if (!json.is_success) break;
                                render(json.data.status);
                                if (json.data.report.busy) { // Уже качает крон - просто смотрим журнал
                                    break;
                                }
                                if (!json.data.status.pending) break;
                            } catch (e) {
                                addLines(['Ошибка запроса - повторим через 5 секунд']);
                                await new Promise(ok => setTimeout(ok, 5000));
                            }
                        }
                        running = false;
                        render(status);
                    }

                    $('[data-img-run]').closest('form').addEventListener('submit', e => { e.preventDefault(); run(); });
                    $('[data-img-stop]').addEventListener('click', () => { stopped = true; $('[data-img-stop]').disabled = true; addLines(['Остановим после текущей порции…']); });
                    logEl.scrollTop = logEl.scrollHeight;
                    render(<?= json_encode($st, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?>);
                    if (box.dataset.autostart === '1') run();
                    setTimeout(poll, 1000);
                })();
                </script>
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

            <div class="card card-body mt-4">
                <h5>Выгрузить каталог</h5>
                <p class="small text-secondary mb-3">Все товары в том же формате (с характеристиками, остатками, вариантами). Удобно, чтобы начать вести каталог в Google-таблице:
                    выгрузите файл, в Google-таблице выберите «Файл → Импорт → Загрузить», дальше правьте там и импортируйте сюда.</p>
                <a href="<?= SiteRoot('_admin/shop/export') ?>" class="btn btn-outline-dark rounded-pill align-self-start"><i class="bi bi-download pe-2"></i>Выгрузить каталог (CSV)</a>
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
                <tr><td><code>variant_group</code></td><td>Группа вариантов</td><td>один код у одного товара в разных цветах/размерах</td></tr>
                <tr><td><code>related</code></td><td>Покупают вместе</td><td>артикулы через запятую</td></tr>
                <tr><td><code>stock</code></td><td>Остаток</td><td>число в единицах товара (можно 12,5); пусто - не учитывать</td></tr>
                <tr><td><code>category</code></td><td>Категория</td><td>«Одежда / Футболки» - с подкатегорией; новые категории создаются сами</td></tr>
                <tr><td><code>unit</code></td><td>Единица</td><td><?= implode(', ', array_keys(Config('shop')['units'])) ?> (пусто - <?= Config('shop')['default_unit'] ?>); цена - за единицу</td></tr>
                <tr><td><code>qty_step</code></td><td>Шаг</td><td>шаг количества в корзине (пусто - минимальный шаг единицы из настроек, по умолчанию 1); не меньше него</td></tr>
                <tr><td><code>qty_min</code></td><td>Минимум</td><td>минимальный заказ в единицах товара (пусто - один шаг)</td></tr>
                <tr><td><code>similar</code></td><td>Похожие</td><td>«Похожие товары»: артикулы через запятую (дальше - сами, из той же категории)</td></tr>
                <tr><td><code>images</code></td><td>Картинки</td><td>ссылки или имена файлов через запятую, первая - главная. Имена файлов берутся из <code>SHOP_IMPORT_IMAGES_SOURCE</code> в .env (публичная папка Яндекс Диска, начало ссылки или папка на сервере). Качаются после импорта порциями, уже скачанные повторно не качаются</td></tr>
                <tr><td><code># ...</code></td><td></td><td>служебная колонка (заметки), не импортируется</td></tr>
                <tr><td><code>P: Название</code></td><td></td><td>характеристика: «P: Состав», «P: Цвет», «P: Размер»...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
