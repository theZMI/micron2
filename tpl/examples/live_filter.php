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
                    $allCells = [];
                    foreach ($tableData as $row) {
                        $i2 = 1;
                        foreach ($row as $cell) {
                            if ($i2 == $i) {
                                $allCells[] = strip_tags($cell);
                            }
                            $i2++;
                        }
                    }
                    $allCells = array_unique($allCells);
                    natsort($allCells);

                    $isEmpty = !count(array_filter($allCells));
                    $isSelect = count($allCells) < 10;
                    ?>
                    <?php if ($isEmpty): ?>
                    <?php elseif ($isSelect): ?>
                        <select class="form-control input-for-live-search live-filter-input-<?= $i ?>">
                            <option value="">Неважно</option>
                            <?php foreach ($allCells as $cell): ?>
                                <option value="begin_<?= $cell ?>_end"<?= isset($defaultTableValues[$v]) && (strip_tags($defaultTableValues[$v]) == $cell) ? ' selected="selected"' : '' ?>><?= $cell ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input type="text" class="form-control input-for-live-search live-filter-input-<?= $i ?>" placeholder="" value="<?= isset($defaultTableValues[$v]) && (strip_tags($defaultTableValues[$v]) == $cell) ? ' selected="selected"' : '' ?>" />
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