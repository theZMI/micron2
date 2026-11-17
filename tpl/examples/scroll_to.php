<?php IncludeCom('_dev/scroll_to') ?>

<head>
    <style>
        .scroll-to-box {
            height: 220px;
            overflow: auto;
            border: 1px solid var(--bs-border-color);
            border-radius: var(--bs-border-radius);
        }

        .scroll-to-box .item {
            padding: 12px 16px;
            border-bottom: 1px solid var(--bs-border-color);
        }

        .scroll-to-box .item.active {
            background: var(--bs-warning-bg-subtle);
        }

        .scroll-to-section {
            min-height: 60vh;
        }
    </style>
    <script type="text/javascript">
        $(function () {
            // Прокрутка окна из JS: цель, длительность, настройки
            $('#btn-to-section-2').on('click', function () {
                $.scrollTo('#scroll-section-2', 800, {
                    offset: -80, // отступ сверху, чтобы заголовок не прятался под шапкой
                    onAfter: function () {
                        $('#scroll-section-2 h2').addClass('text-danger');
                    }
                });
            });

            // Прокрутка внутри блока с overflow: auto
            $('#btn-box-item').on('click', function () {
                const n = parseInt($('#box-item-number').val()) || 1;
                const $item = $('#scroll-to-box .item').removeClass('active').eq(n - 1).addClass('active');
                $('#scroll-to-box').scrollTo($item, 400);
            });
            $('#btn-box-end').on('click', () => $('#scroll-to-box').scrollTo('max', 400));
            $('#btn-box-start').on('click', () => $('#scroll-to-box').scrollTo(0, 400));

            // Относительные значения и проценты
            $('#btn-down-300').on('click', () => $.scrollTo('+=300px', 400));
            $('#btn-bottom').on('click', () => $.scrollTo('100%', 800));
        });
    </script>
</head>

<h1 class="my-4">Плавная прокрутка (jQuery.ScrollTo)</h1>
<p>
    Компонент <code>_dev/scroll_to</code> подключается только на нужных страницах: <code>&lt;?php IncludeCom('_dev/scroll_to') ?&gt;</code>.
    Он добавляет плагин <code>$.scrollTo()</code> / <code>$(...).scrollTo()</code> и обработчик для элементов с атрибутом <code>data-scroll-to</code>.
</p>

<h2 class="h4 mt-4">Без JS - через data-атрибуты</h2>
<div class="d-flex flex-wrap gap-2">
    <a href="#scroll-section-1" class="btn btn-outline-primary" data-scroll-to>К разделу 1 (href)</a>
    <button type="button" class="btn btn-outline-primary" data-scroll-to="#scroll-section-3" data-scroll-offset="-80" data-scroll-duration="1200">
        К разделу 3 (медленно, с отступом)
    </button>
</div>

<h2 class="h4 mt-4">Из JS</h2>
<div class="d-flex flex-wrap gap-2">
    <button type="button" class="btn btn-primary" id="btn-to-section-2">К разделу 2 + onAfter</button>
    <button type="button" class="btn btn-primary" id="btn-down-300">Ниже на 300px</button>
    <button type="button" class="btn btn-primary" id="btn-bottom">В самый низ (100%)</button>
</div>

<h2 class="h4 mt-4">Внутри блока с прокруткой</h2>
<div class="d-flex flex-wrap gap-2 mb-2">
    <div class="input-group" style="max-width: 260px">
        <input type="number" class="form-control" id="box-item-number" value="25" min="1" max="50">
        <button type="button" class="btn btn-secondary" id="btn-box-item">К строке</button>
    </div>
    <button type="button" class="btn btn-secondary" id="btn-box-start">В начало</button>
    <button type="button" class="btn btn-secondary" id="btn-box-end">В конец</button>
</div>
<div class="scroll-to-box" id="scroll-to-box">
    <?php for ($i = 1; $i <= 50; $i++): ?>
        <div class="item">Строка <?= $i ?></div>
    <?php endfor ?>
</div>

<?php for ($i = 1; $i <= 3; $i++): ?>
    <div class="scroll-to-section pt-5" id="scroll-section-<?= $i ?>">
        <h2>Раздел <?= $i ?></h2>
        <p class="text-muted">Длинный блок, чтобы было куда прокручивать.</p>
        <a href="#" class="btn btn-sm btn-link px-0" onclick="$.scrollTo(0, 500); return false;"><i class="bi bi-arrow-up"></i> Наверх</a>
    </div>
<?php endfor ?>
<div style="height: 50vh"></div><?php // Запас снизу, чтобы последний раздел тоже доезжал до верха экрана ?>
