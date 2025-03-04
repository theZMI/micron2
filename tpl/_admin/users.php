<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">
                <span class="pull-start me-3">Пользователи</span>
                <a href="<?= SiteRoot('_admin/users/add_or_edit') ?>" class="btn btn-primary rounded-pill pull-end">
                    <i class="bi bi-plus-lg me-2"></i>Добавить
                </a>
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