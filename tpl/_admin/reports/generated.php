<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">
                Отчёт с <?= OutputFormats::dateTime($time_from, false) ?> по <?= OutputFormats::dateTime($time_to, false) ?>
            </h1>
            <div class="mb-3">
                <h3>Повторяющиеся задачи:</h3>
                <?php
                    $tableHeader           = ['Задача', 'Исполнитель', 'Статус', 'Повторилась',];
                    $tableHeaderEnd        = '';
                    $tableHeaderEndFilters = '';
                    $colWidths             = ['Задача' => 'auto'];
                    $tableData             = [];
                    $tableDataEnd          = [];
                    foreach ($tasksByGroups['repeats'] as $taskName => $users) {
                        foreach ($users as $user => $statuses) {
                            foreach ($statuses as $status => $tasks) {
                                $tableData[] = [
                                    $taskName,
                                    (new UserModel($user))->full_name,
                                    (new TaskModel())->statuses($status)['label'],
                                    +count($tasks)
                                ];
                                $tableDataEnd[] = '';
                            }
                        }
                    }
                    $defaultTableValues = [];
                    $tableFilters = [
                        'Задача'      => 'input',
                        'Повторилась' => 'input',
                    ];

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
            <div class="mb-3">
                <h3>Уникальные задачи:</h3>
                <?php
                    $tableHeader           = ['Задача', 'Исполнитель', 'Статус', 'Поставлена в', 'Исполнена в', 'Комментарий',];
                    $tableHeaderEnd        = '';
                    $tableHeaderEndFilters = '';
                    $colWidths             = ['Задача' => 'auto'];
                    $tableData             = [];
                    $tableDataEnd          = [];
                    foreach ($tasksByGroups['once'] as $taskName => $users) {
                        foreach ($users as $user => $statuses) {
                            foreach ($statuses as $status => $tasks) {
                                $tableData[] = [
                                    $taskName,
                                    (new UserModel($user))->full_name,
                                    (new TaskModel())->statuses($status)['label'],
                                    OutputFormats::dateTime($tasks[0]->create_time, false),
                                    OutputFormats::dateTime($tasks[0]->done_time, false),
                                    nl2br($tasks[0]->user_comment)
                                ];
                                $tableDataEnd[] = '';
                            }
                        }
                    }
                    $defaultTableValues = [];
                    $tableFilters = [
                        'Задача'      => 'input',
                        'Повторилась' => 'input',
                    ];

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
            <div class="mb-3 text-center">
                <a href="#" class="btn btn-primary btn-lg rounded-pill"><i class="bi bi-check-lg pe-2"></i>Скачать</a>
            </div>
            <div class="mb-3 text-center">
                <a href="<?= SiteRoot('_admin/reports') ?>" class="btn btn-link btn-lg"><i class="bi bi-arrow-left pe-2"></i>К списку</a>
            </div>
        </div>
    </div>
</div>