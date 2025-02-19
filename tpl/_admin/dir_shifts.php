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

<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">
                <span class="pull-start me-3">Смены</span>
                <div class="dropdown pull-end d-inline-block">
                    <button class="btn btn-primary rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-plus-lg me-2"></i>Добавить
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= SiteRoot('_admin/dir_shifts/create_by_template') ?>">На основе шаблона</a></li>
                        <li><a class="dropdown-item" href="<?= SiteRoot('_admin/dir_shifts/add_or_edit') ?>">Уникальная</a></li>
                    </ul>
                </div>
            </h1>
            <div id="table-with-filters" class="table-responsive mb-4 table-extra-condensed-wrapper">
                <table class="site-table">
                    <?php if (count($list)): ?>
                    <thead>
                        <tr>
                            <?php $i = 1; foreach ($tableHeader as $v): ?>
                                <th width="<?= $i === 2 ? '' : '1%'?>"><?= $v ?></th>
                            <?php $i++; endforeach; ?>
                            <th width="1%">Прогресс по задачам</th>
                            <th colspan="2">Действия</th>
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
                            <td></td>
                            <td colspan="2"></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tableData as $id => $row): ?>
                            <tr class="live-filter-data-block">
                                <?php $i = 1; foreach ($row as $cell): ?>
                                    <td onclick="trClick(this)" class="<?= $i === 2 ? '' : 'text-nowrap' ?> ps-2 pe-2">
                                        <?= $cell ?>
                                        <div class="hidden d-none live-filter-data-block-filterinfo-<?= $i ?>">begin_<?= strip_tags($cell) ?>_end</div>
                                    </td>
                                <?php $i++; endforeach; ?>
                                <td class="text-nowrap ps-2 pe-2">
                                    <?php IncludeCom('_admin/_dir_shift_progresses', ['model' => $list[$id]]) ?>
                                </td>
                                <td width="1%" class="text-center" style="display: none;">
                                    <a href="<?= SiteRoot("_admin/dir_shifts/add_or_edit&id={$id}") ?>" class="btn btn-sm btn-primary rounded-pill default-click" title="Изменить данные"><i class="bi bi-pencil-fill"></i></a>
                                </td>
                                <td width="1%" class="text-center text-nowrap">
                                    <a href="<?= GetCurUrl('a=delete&id=' . $id) ?>" class="btn btn-sm btn-danger rounded-pill" onclick="return confirm('Удалить?')" title="Удалить"><i class="bi bi-trash3-fill"></i></a>
                                </td>
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
        </div>
    </div>
</div>