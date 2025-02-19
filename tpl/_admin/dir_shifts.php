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
            <?php
                IncludeCom('_dev/xl_table', [
                    'tableHeader'           => $tableHeader,
                    'tableHeaderEnd'        => $tableHeaderEnd,
                    'tableHeaderEndFilters' => $tableHeaderEndFilters,
                    'colWidths'             => $colWidths,
                    'tableData'             => $tableData,
                    'tableDataEnd'          => $tableDataEnd,
                    'defaultTableValues'    => $defaultTableValues,
                    'tableFilters'          => $tableFilters
                ]);
            ?>
        </div>
    </div>
</div>