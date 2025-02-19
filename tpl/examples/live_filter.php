<head>
    <script type="text/javascript">
        let g_searchers;

        $(() => {
            <?php $countSearchers = count($tableHeader); ?>
            <?php for ($i = 1; $i <= $countSearchers; $i++): ?>
            $('#table-with-filters').liveFilter(
                '.live-filter-input-<?= $i ?>',
                '.live-filter-data-block',
                {
                    filterChildSelector: '.live-filter-data-block-filterinfo-<?= $i ?>',
                    before: function (elem, contains, containsNot) {
                        let searchers = {};
                        let selector = '';
                        <?php for ($j = 1; $j <= $countSearchers; $j++): ?>
                            selector = '#table-with-filters .live-filter-input-<?= $j ?>';
                            if ($(selector).val()) {
                                searchers[<?= $j ?>] = $(selector).val();
                            }
                        <?php endfor ?>
                        g_searchers = searchers;
                    },
                    after: function (elem, contains, containsNot) {
                    }
                }
            );
            <?php endfor; ?>

            $('#table-with-filters').find('input:first, select:first').trigger('change');
        });
    </script>
</head>

<h1 class="mt-4 mb-4">Таблицы с мультисортировкой</h1>

<div id="table-with-filters" class="table-responsive mb-4 table-extra-condensed-wrapper">
    <table class="site-table">
        <thead>
        <tr>
            <?php foreach ($tableHeader as $v): ?>
                <th><?= $v ?></th>
            <?php endforeach; ?>
        </tr>
        <tr>
            <?php $i = 1; foreach ($tableHeader as $v): ?>
                <td>
                    <?php
                        // Получаем все возможные значения в этом столбце
                        $cells = array_map(
                            fn($row) => strip_tags(array_values($row)[$i-1]),
                            array_values($tableData)
                        );
                        $cells = array_unique($cells);
                        $uniq  = count($cells);
                        $total = count(array_column($tableData, $i-1));
                        natsort($cells);

                        $isEmpty = !count(array_filter($cells));
                        $isSelect = $uniq <= 10 && $uniq < $total // Показывать select если вариантов не больше 10-ти
                                    ||
                                    ($uniq <= 20 && $uniq <= 0.33*$total && $uniq < $total); // Если select сокращает кол-во пунктов до 1/3 от общего числа вариантов, но не больше 20
                    ?>
                    <?php if ($isEmpty): ?>
                    <?php elseif ($isSelect): ?>
                        <select class="form-control input-for-live-search live-filter-input-<?= $i ?>">
                            <option value="">Неважно</option>
                            <?php foreach ($cells as $cell): ?>
                                <option value="begin_<?= $cell ?>_end"<?= isset($defaultTableValues[$v]) && (strip_tags($defaultTableValues[$v]) == $cell) ? ' selected="selected"' : '' ?>><?= $cell ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input type="text" class="form-control input-for-live-search live-filter-input-<?= $i ?>" placeholder="" value="<?= $defaultTableValues[$v] ?? '' ?>" />
                    <?php endif; ?>
                </td>
                <?php $i++; endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($tableData as $row): ?>
            <tr class="live-filter-data-block">
                <?php $i = 1; foreach ($row as $v):
                    $cell = is_scalar($v) ? str_replace("\r\n", "<br>", $v) : implode("<br>", empty($v) ? [] : $v);;
                    ?>
                    <td>
                        <div class="hidden d-none live-filter-data-block-filterinfo-<?= $i ?>">begin_<?= strip_tags($cell) ?>_end</div>
                        <?= $cell ?>
                    </td>
                <?php $i++; endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>