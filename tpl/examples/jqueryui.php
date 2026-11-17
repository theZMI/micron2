<head>
    <style>
        .jqui-sortable {
            list-style: none;
            padding: 0;
            max-width: 360px;
        }

        .jqui-sortable li {
            padding: 8px 12px;
            margin-bottom: 6px;
            border: 1px solid var(--bs-border-color);
            border-radius: var(--bs-border-radius);
            background: var(--bs-body-bg);
            cursor: move;
        }

        .jqui-sortable .ui-sortable-placeholder {
            visibility: visible !important;
            border-style: dashed;
            background: var(--bs-tertiary-bg);
        }

        .jqui-draggable {
            width: 110px;
            height: 110px;
            cursor: move;
            z-index: 2;
        }

        .jqui-droppable {
            width: 220px;
            height: 160px;
            border: 2px dashed var(--bs-border-color);
        }

        .jqui-droppable.is-hover {
            border-color: var(--bs-primary);
        }

        .jqui-droppable.is-dropped {
            border-color: var(--bs-success);
            background: var(--bs-success-bg-subtle);
        }

        .jqui-resizable {
            width: 260px;
            height: 120px;
        }
    </style>
    <script type="text/javascript">
        $(function () {
            // Слайдер: одно значение и диапазон
            $('#jqui-slider').slider({
                min: 0,
                max: 100,
                value: 30,
                slide: (e, ui) => $('#jqui-slider-value').text(ui.value)
            });
            $('#jqui-range').slider({
                range: true,
                min: 0,
                max: 50000,
                step: 500,
                values: [5000, 25000],
                slide: (e, ui) => $('#jqui-range-value').text(`${ui.values[0].toLocaleString('ru-RU')} - ${ui.values[1].toLocaleString('ru-RU')} ₽`)
            });

            // Сортировка списка перетаскиванием. Новый порядок можно отправить на сервер в update
            $('#jqui-sortable').sortable({
                placeholder: 'ui-sortable-placeholder',
                update: function () {
                    const order = $(this).sortable('toArray', {attribute: 'data-id'});
                    $('#jqui-sortable-order').text(order.join(', '));
                }
            });

            // Перетаскивание и «приёмник»
            $('#jqui-draggable').draggable({revert: 'invalid'});
            $('#jqui-droppable').droppable({
                accept: '#jqui-draggable',
                hoverClass: 'is-hover',
                drop: function () {
                    $(this).addClass('is-dropped').find('span').text('Попали!');
                }
            });

            // Изменение размера
            $('#jqui-resizable').resizable({minWidth: 150, minHeight: 80});

            // Автодополнение: из массива или с сервера (source: '/url', сервер отдаёт JSON-массив по ?term=...)
            $('#jqui-autocomplete').autocomplete({
                source: ['Москва', 'Санкт-Петербург', 'Новосибирск', 'Екатеринбург', 'Казань', 'Нижний Новгород', 'Челябинск', 'Самара', 'Омск', 'Ростов-на-Дону'],
                minLength: 1
            });

            // Аккордеон и вкладки
            $('#jqui-accordion').accordion({heightStyle: 'content', collapsible: true});
            $('#jqui-tabs').tabs();

            // Встроенный календарь (настройки по умолчанию - в tpl/_dev/jqueryui.php)
            $('#jqui-datepicker-inline').datepicker({
                dateFormat: 'dd-mm-yy',
                onSelect: date => $('#jqui-datepicker-value').text(date)
            });

            // Диалог с кнопками jQuery UI (опция buttons)
            const $dialog = $('#jqui-dialog').dialog({
                autoOpen: false,
                modal: true,
                width: 360,
                buttons: {
                    'Закрыть': function () {
                        $(this).dialog('close');
                    }
                }
            });
            $('#jqui-dialog-open').on('click', () => $dialog.dialog('open'));

            // Эффекты
            $('#jqui-effect-btn').on('click', () => $('#jqui-effect-box').effect($('#jqui-effect-name').val(), {}, 600));
        });
    </script>
</head>

<h1 class="my-4">jQuery UI</h1>
<p>
    Компонент <code>_dev/jqueryui</code> (jQuery UI 1.11.4 с русской локализацией календаря и темой <code>micron_theme.less</code>)
    подключён на всех страницах в <code>tpl/_common_head.php</code>, поэтому виджеты можно вызывать сразу.
    Поля с классами <code>.datepicker</code> и <code>.datepicker-with-min-date</code> становятся календарями автоматически.
    Документация: <a href="https://api.jqueryui.com/1.11/" target="_blank" rel="noopener">api.jqueryui.com</a>.
</p>

<div class="row g-4">
    <div class="col-lg-6">
        <h2 class="h4">Slider</h2>
        <div class="mb-2">Значение: <b id="jqui-slider-value">30</b></div>
        <div id="jqui-slider" class="mb-4"></div>
        <div class="mb-2">Цена: <b id="jqui-range-value">5 000 - 25 000 ₽</b></div>
        <div id="jqui-range"></div>
    </div>

    <div class="col-lg-6">
        <h2 class="h4">Datepicker</h2>
        <div class="row g-3">
            <div class="col-sm-6">
                <label class="form-label">Поле с классом <code>.datepicker</code></label>
                <input type="text" class="form-control datepicker" autocomplete="off" value="<?= OutputFormats::dateForDatePicker(time()) ?>">
                <label class="form-label mt-3">Только будущие даты <code>.datepicker-with-min-date</code></label>
                <input type="text" class="form-control datepicker-with-min-date" autocomplete="off">
            </div>
            <div class="col-sm-6">
                <div id="jqui-datepicker-inline"></div>
                <div class="small mt-1">Выбрано: <span id="jqui-datepicker-value">-</span></div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <h2 class="h4">Sortable</h2>
        <ul class="jqui-sortable" id="jqui-sortable">
            <?php foreach (['Первый', 'Второй', 'Третий', 'Четвёртый', 'Пятый'] as $i => $name): ?>
                <li data-id="<?= $i + 1 ?>"><i class="bi bi-grip-vertical text-muted"></i> <?= $name ?></li>
            <?php endforeach ?>
        </ul>
        <div class="small">Порядок id: <span id="jqui-sortable-order">1, 2, 3, 4, 5</span></div>
    </div>

    <div class="col-lg-6">
        <h2 class="h4">Draggable + Droppable</h2>
        <div class="d-flex gap-4 align-items-start">
            <div id="jqui-draggable" class="jqui-draggable bg-primary text-white rounded d-flex align-items-center justify-content-center text-center">
                Перетащи меня
            </div>
            <div id="jqui-droppable" class="jqui-droppable rounded d-flex align-items-center justify-content-center">
                <span>Сюда</span>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <h2 class="h4">Autocomplete</h2>
        <input type="text" class="form-control" id="jqui-autocomplete" placeholder="Начните вводить город">
    </div>

    <div class="col-lg-6">
        <h2 class="h4">Resizable</h2>
        <div id="jqui-resizable" class="jqui-resizable border rounded p-3 bg-body-tertiary">Потяните за правый нижний угол</div>
    </div>

    <div class="col-lg-6">
        <h2 class="h4">Accordion</h2>
        <div id="jqui-accordion">
            <h3>Раздел 1</h3>
            <div><p>Содержимое первого раздела.</p></div>
            <h3>Раздел 2</h3>
            <div><p>Содержимое второго раздела.</p></div>
            <h3>Раздел 3</h3>
            <div><p>Содержимое третьего раздела.</p></div>
        </div>
    </div>

    <div class="col-lg-6">
        <h2 class="h4">Tabs</h2>
        <div id="jqui-tabs">
            <ul>
                <li><a href="#jqui-tab-1">Описание</a></li>
                <li><a href="#jqui-tab-2">Характеристики</a></li>
                <li><a href="#jqui-tab-3">Отзывы</a></li>
            </ul>
            <div id="jqui-tab-1">Текст описания.</div>
            <div id="jqui-tab-2">Таблица характеристик.</div>
            <div id="jqui-tab-3">Список отзывов.</div>
        </div>
    </div>

    <div class="col-lg-6">
        <h2 class="h4">Dialog</h2>
        <button type="button" class="btn btn-primary" id="jqui-dialog-open">Открыть диалог</button>
        <div id="jqui-dialog" title="Диалог jQuery UI">
            <p>Модальное окно jQuery UI. Для обычных модальных окон в проекте лучше использовать Bootstrap modal.</p>
        </div>
    </div>

    <div class="col-lg-6">
        <h2 class="h4">Effects</h2>
        <div class="input-group mb-3" style="max-width: 320px">
            <select class="form-select" id="jqui-effect-name">
                <?php foreach (['shake', 'bounce', 'pulsate', 'highlight'] as $effect): ?>
                    <option value="<?= $effect ?>"><?= $effect ?></option>
                <?php endforeach ?>
            </select>
            <button type="button" class="btn btn-secondary" id="jqui-effect-btn">Запустить</button>
        </div>
        <div id="jqui-effect-box" class="border rounded p-3 d-inline-block">Блок для эффекта</div>
    </div>
</div>
