<div class="container-fluid">
    <div class="row">
        <div class="col">
            <h1 class="my-4">Заказы</h1>
            <?php
            IncludeCom('_dev/xl_table', [
                'tableHeader'           => $tableHeader,
                'tableHeaderEnd'        => $tableHeaderEnd,
                'tableHeaderEndFilters' => $tableHeaderEndFilters,
                'colWidths'             => $colWidths,
                'tableData'             => $tableData,
                'tableDataEnd'          => $tableDataEnd,
                'defaultTableValues'    => $defaultTableValues,
                'tableFilters'          => $tableFilters,
            ]);
            ?>
        </div>
    </div>
</div>
