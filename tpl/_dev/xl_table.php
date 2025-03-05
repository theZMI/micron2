<head>
    <script type="text/javascript">
        window['g_searchers_<?= $divID ?>'] = [];

        $(() => {
            <?php $countSearchers = count($tableHeader); ?>
            <?php for ($i = 1; $i <= $countSearchers; $i++): ?>
            $('#table-with-filters-<?= $divID ?>').liveFilter(
                '#table-with-filters-<?= $divID ?> .live-filter-input-<?= $i ?>',
                '#table-with-filters-<?= $divID ?> .live-filter-data-block',
                {
                    filterChildSelector: '.live-filter-data-block-filterinfo-<?= $i ?>',
                    before: function (elem, contains, containsNot) {
                        let searchers = {};
                        let selector = '';
                        <?php for ($j = 1; $j <= $countSearchers; $j++): ?>
                        selector = '#table-with-filters-<?= $divID ?> .live-filter-input-<?= $j ?>';
                        if ($(selector).val()) {
                            searchers[<?= $j ?>] = $(selector).val();
                        }
                        <?php endfor ?>
                        console.log('searchers = ', searchers);
                        window['g_searchers_<?= $divID ?>'] = searchers;
                        console.log('set into g_searchers_<?= $divID ?>', searchers);
                    },
                    after: function (elem, contains, containsNot) {
                    }
                }
            );
            <?php endfor; ?>

            $('#table-with-filters-<?= $divID ?>').find('input:first, select:first').trigger('change');
        });
    </script>
</head>


<div id="table-with-filters-<?= $divID ?>" class="table-responsive mb-4 table-extra-condensed-wrapper">
    <table class="site-table">
        <?php if (count($tableData)): ?>
            <thead>
            <tr>
                <?php $i = 1; foreach ($tableHeader as $v): ?>
                    <th width="<?= $colWidths[$v] ?? '1%' ?>"><?= $v ?></th>
                    <?php $i++; endforeach; ?>
                <?= $tableHeaderEnd ?>
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
                        $filter  = $tableFilters[$v] ?? 'auto';
                        if ($filter === 'auto')
                        {
                            $isSelect = $uniq <= 10 && $uniq < $total // Показывать select если вариантов не больше 10-ти
                                ||
                                ($uniq <= 20 && $uniq <= 0.33*$total && $uniq < $total); // Если select сокращает кол-во пунктов до 1/3 от общего числа вариантов, но не больше 20
                        } else {
                            $isSelect = $filter === 'select';
                        }
                        ?>
                        <?php if ($isEmpty): ?>
                        <?php elseif ($isSelect): ?>
                            <select class="form-control input-for-live-search live-filter-input-<?= $i ?>" data-searchers-group="g_searchers_<?= $divID ?>">
                                <option value="">Неважно</option>
                                <?php foreach ($cells as $cell): ?>
                                    <option value="begin_<?= $smartStripTags($cell, true) ?>_end"<?= isset($defaultTableValues[$v]) && (strip_tags($defaultTableValues[$v]) == $cell) ? ' selected="selected"' : '' ?>><?= $cell ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input type="text" class="form-control input-for-live-search live-filter-input-<?= $i ?>" placeholder="" value="<?= $defaultTableValues[$v] ?? '' ?>"  data-searchers-group="g_searchers_<?= $divID ?>" />
                        <?php endif; ?>
                    </td>
                    <?php $i++; endforeach; ?>
                <?= $tableHeaderEndFilters ?>
            </tr>
            </thead>
            <tbody>
            <?php  foreach ($tableData as $id => $row): ?>
                <tr class="live-filter-data-block">
                    <?php $i = 1; foreach ($row as $cell): ?>
                        <?php $colName = $tableHeader[$i-1]; ?>
                        <td onclick="trClick(this)" class="<?= isset($colWidths[$colName]) ? Translit("col-class-{$colName}") : 'text-nowrap' ?> ps-2 pe-2">
                            <?= $cell ?>
                            <div class="hidden d-none live-filter-data-block-filterinfo-<?= $i ?>">begin_<?= $smartStripTags($cell, true) ?>_end</div>
                        </td>
                        <?php $i++; endforeach; ?>
                    <?= $tableDataEnd[$id] ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        <?php else: ?>
            <tbody>
            <tr>
                <td class="text-center">Нет данных</td>
            </tr>
            </tbody>
        <?php endif ?>
    </table>
</div>