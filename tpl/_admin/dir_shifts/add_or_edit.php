<?php ob_start() ?>
<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <div class="modal fade" id="taskFormModal" tabindex="-1" aria-labelledby="taskFormModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form class="d-block modal-content" action="<?= GetCurUrl() ?>" method="post" @submit.prevent="currentTaskSubmit">
                        <div class="modal-header">
                            <h3 class="modal-title" id="taskFormModalLabel">Новая задача</h3>
                            <button type="button" class="btn-close" id="taskFormModalCloseButton" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div>
                                <input type="hidden" name="is_add_task" value="1">
                                <div class="mb-3">
                                    <label class="form-label">Задача:</label>
                                    <input type="text" class="form-control" v-model="currentTask.task">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Описание:</label>
                                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" v-model="currentTask.description"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Решить до:</label>
                                    <select class="form-control" v-model="currentTask.deadline">
                                        <option value="0">В течение смены</option>
                                        <?php for ($i = intval(5*3600); $i < intval(24*3600 - 1); $i+=1800): ?>
                                            <option value="<?= $i ?>">До <?= FormatTimeInterval($i) ?></option>
                                        <?php endfor ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="removeTask" class="btn btn-min-width btn-outline-danger rounded-pill me-auto" v-if="isTaskEdit">Удалить</button>
                            <button type="submit" class="btn btn-min-width btn-primary rounded-pill"><i class="bi bi-check-lg pe-2"></i>Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>

            <h1 class="mt-4 mb-4">
                <span class="pull-start"><?= $model->isExists() ? "Смена №".$modelParam('id') : 'Новая смена' ?></span>
            </h1>
            <form action="<?= GetCurUrl() ?>" method="post" @submit.prevent="saveShift">
                <input type="hidden" name="is_set" value="1">
                <div v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>
                <?php if (!$noDates): ?>
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label">Начать в:</label>
                                <div class="input-group mb-3">
                                    <input type="text" name="shift_start_time" class="form-control datepicker datepicker-mask" autocomplete="off" v-model="start_time" placeholder="ДД-ММ-ГГГГ">
                                    <span class="input-group-text">00:00</span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label">Закончить в:</label>
                                <div class="input-group mb-3">
                                    <input type="text" name="shift_end_time" class="form-control datepicker datepicker-mask" autocomplete="off" v-model="end_time" placeholder="ДД-ММ-ГГГГ">
                                    <span class="input-group-text">23:59</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="mb-3">
                    <label class="form-label">Название:</label>
                    <input type="text" name="name" class="form-control" :value="dir_name_ready" @input="event => dir_name = event.target.value">
                </div>
                <br>
                <div class="mb-3">
                    <label class="form-label">Работники:</label>
                    <div class="form-check form-switch form-switch-md mb-2" v-for="u in all_workers" :key="u.id">
                        <input type="checkbox" role="switch" class="form-check-input" :id="`worker_id__${u.id}`" :name="`user_ids[${u.id}]`" v-model="select_worker_ids" :value="u.id">
                        <label class="form-check-label ms-3 mt-1" :for="`worker_id__${u.id}`">{{ u.full_name }}</label>
                    </div>
                </div>
                <br>
                <div class="mb-3">
                    <label class="form-label">Задачи работникам:</label>
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" @click="selectedTab = 'common'" id="common-tasks-tab" data-bs-toggle="tab" data-bs-target="#common-tasks-tab-pane" type="button" role="tab" aria-controls="common-tasks-tab-pane" aria-selected="true">Для всех</button>
                        </li>
                        <li class="nav-item" role="presentation" v-for="worker in select_workers" :key="worker.id">
                            <button class="nav-link" @click="selectedTab = worker.id" :id="`worker-tasks-tab-${worker.id}`" data-bs-toggle="tab" :data-bs-target="`#worker-tasks-tab-${worker.id}-pane`" type="button" role="tab" :aria-controls="`worker-tasks-tab-${worker.id}-pane`" aria-selected="false">{{ worker.full_name }}</button>
                        </li>
                    </ul>
                    <div class="tab-content tab-bordered" id="myTabContent">
                        <div class="tab-pane fade show active" id="common-tasks-tab-pane" role="tabpanel" aria-labelledby="common-tasks-tab" tabindex="0">
                            <p class="lead">Задачи на смену:</p>
                            <div class="d-flex flex-row flex-wrap">
                                <div class="tasks">
                                    <a v-if="appendTasks.common.length" v-for="task in appendTasks.common" @click="openTask(task)" class="task">
                                        <div class="task-name">{{ task.task }}<div class="task-deadline badge text-white text-bg-secondary" v-if="task.deadline">До {{ formatTimer(+task.deadline) }}</div></div>
                                        <div class="task-description">{{ task.description }}</div>
                                    </a>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <button class="btn btn-outline-primary rounded-pill btn-add-task" type="button" data-bs-toggle="modal" data-bs-target="#taskFormModal"><i class="bi bi-plus-lg me-2"></i>Добавить задачу</button>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <p class="lead">Параметры по завершению смены:</p>
                            <div class="form-check form-switch form-switch-md mb-2" v-for="param in possibleParams" :key="param.id">
                                <input type="checkbox" role="switch" class="form-check-input" :id="`common_param_id__${param.id}`" v-model="params['common']" :value="param.id">
                                <label class="form-check-label ms-3 mt-1" :for="`common_param_id__${param.id}`">{{ param.name }}</label>
                            </div>
                        </div>
                        <div v-for="worker in select_workers" :key="worker.id" :id="`worker-tasks-tab-${worker.id}-pane`" class="tab-pane fade" role="tabpanel" :aria-labelledby="`worker-tasks-tab-${worker.id}-tab`" tabindex="0">
                            <p class="lead">Задачи на смену:</p>
                            <div class="d-flex flex-row flex-wrap">
                                <div class="tasks">
                                    <a v-if="appendTasks[worker.id]?.length" v-for="task in appendTasks[worker.id]" @click="openTask(task)" class="task">
                                        <div class="task-name">{{ task.task }}<div class="task-deadline badge text-white text-bg-secondary" v-if="task.deadline">До {{ formatTimer(+task.deadline) }}</div></div>
                                        <div class="task-description">{{ task.description }}</div>
                                    </a>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <button class="btn btn-outline-primary rounded-pill btn-add-task" type="button" data-bs-toggle="modal" data-bs-target="#taskFormModal"><i class="bi bi-plus-lg me-2"></i>Добавить задачу</button>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <p class="lead">Параметры по завершению смены:</p>
                            <div class="pt-3 pb-3">
                                <div class="form-check form-switch form-switch-md mb-2" v-for="param in possibleParams" :key="param.id">
                                    <input type="checkbox" role="switch" class="form-check-input" :id="`user_param_id__${worker.id}__${param.id}`" v-model="params[worker.id]" :value="param.id">
                                    <label class="form-check-label ms-3 mt-1" :for="`user_param_id__${worker.id}__${param.id}`">{{ param.name }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <a href="<?= SiteRoot("_admin/{$componentURI}") ?>" class="btn btn-link btn-lg"><i class="bi bi-arrow-left pe-2"></i>К списку</a>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill"><i class="bi bi-check-lg pe-2"></i>Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php IncludeCom('_dev/page_vue3_app_template', ['content' => ob_get_clean()]) ?>

<script type="module">
import { formatTimer } from "/i/js/_dev/format_date.js";

const vueInit = () => {
    Vue.createApp({
        data() {
            return {
                dir_name: '<?= $modelParam('name') ?>',
                start_time: '<?= Post( 'start_time', OutputFormats::dateForDatePicker(+$modelParam('start_time', strtotime("+1 day"))) ) ?>',
                end_time: '<?= Post( 'end_time', OutputFormats::dateForDatePicker(+$modelParam('end_time', strtotime("+1 day"))) ) ?>',
                all_worker_ids: <?= json_encode( $allWorkersIds ) ?>,
                select_worker_ids: <?= json_encode( $currentWorkersIds ) ?>,
                users: <?= json_encode( array_values(array_map(fn($u) => $u->getData(), $users)) ) ?>,
                errorMessage: '',
                selectedTab: 'common',
                appendTasks: {
                    'common': [],
                    <?php foreach ($model->shifts as $shift): ?>
                        <?= intval($shift->user_id) ?>: [
                            <?php foreach ($shift->tasks as $task): ?>
                                {
                                    id: <?= intval($task->id) ?>,
                                    task: '<?= $task->task ?>',
                                    description: <?= json_encode($task->description) ?>,
                                    deadline: <?= intval($task->deadline_time_only) ?>
                                },
                            <?php endforeach; ?>
                        ],
                    <?php endforeach ?>
                },
                emptyTask: {
                    id: 0,
                    task: '',
                    description: '',
                    deadline: 0
                },
                currentTask: {
                    id: 0,
                    task: '',
                    description: '',
                    deadline: 0
                },
                possibleParams: [
                    <?php foreach ($possibleParams as $param): ?>
                    {
                        id: <?= $param->id ?>,
                        name: '<?= $param->name ?>',
                        type: <?= $param->type ?>,
                        type_name: '<?= $param->type_name ?>',
                    },
                    <?php endforeach; ?>
                ],
                params: {
                    'common': [],
                    <?php
                        // Создаём объект с перечислением параметров для каждого юзера в виде: user_id: [param_id, param_id...]
                        $wasUserIds = [];
                        foreach ($model->shifts as $shift) {
                            $wasUserIds[] = $shift->user_id;
                            ?>
                            <?= intval($shift->user_id) ?>: [
                                <?php
                                foreach ($shift->params as $shift_param) {
                                    echo "{$shift_param->param->id}, ";
                                }
                                ?>
                            ],
                            <?php
                        }
                        foreach (array_diff($allWorkersIds, $wasUserIds) as $user_id) {
                            echo intval($user_id) . ": [],";
                        }
                    ?>
                },
                isTaskEdit: false
            }
        },
        created() {
            if (!window.DB) {
                window.DB = {};
            }
            (new UserModel()).setTableData(this.users);
        },
        mounted() {
            // Hook на закрытие модалки в которой редактируется (или создаётся) задача
            $("#taskFormModal").on("hidden.bs.modal", () => {
                this.currentTask = {...this.emptyTask};
                this.isTaskEdit = false;
            });
        },
        methods: {
            formatTimer(seconds) {
                return formatTimer(seconds, false);
            },
            currentTaskSubmit() {
                if (!this.currentTask.id) {
                    this.currentTask.id = -Date.now();
                }
                if (!this.appendTasks[this.selectedTab]) {
                    this.appendTasks[this.selectedTab] = [];
                }

                let foundTask = this.appendTasks[this.selectedTab].find(v => this.currentTask.id === v.id);
                foundTask
                    ? ['task', 'description', 'deadline'].forEach(field => foundTask[field] = this.currentTask[field]) // Редактируем добавленный task
                    : this.appendTasks[this.selectedTab].push({...this.currentTask});  // Добавляем новый task в массив appendTasks
                $('#taskFormModal').modal('hide');
            },
            openTask(task) {
                this.isTaskEdit = true;
                ['id', 'task', 'description', 'deadline'].forEach(field => this.currentTask[field] = task[field]);
                $('#taskFormModal').modal('show');
                // new bootstrap.Modal(document.getElementById('taskFormModal'), {}).show();
            },
            removeTask() {
                if (!confirm('Удалить данную задачу?')) {
                    return;
                }

                this.appendTasks[this.selectedTab] = this.appendTasks[this.selectedTab].filter(v => v.id !== this.currentTask.id);
                $('#taskFormModal').modal('hide');
            },
            async saveShift() {
                const response = await $.post(
                    '<?= GetCurUrl() ?>',
                    {
                        'is_set': true,
                        'worker_ids': this.select_worker_ids,
                        'tasks': this.appendTasks,
                        'start_time': this.start_time,
                        'end_time': this.end_time,
                        'dir_name': this.dir_name_ready,
                        'params': this.params,
                    },
                    'json'
                );
                if (response['data'] === 'OK') {
                    window.location.replace('<?= SiteRoot("_admin/{$componentURI}") ?>');
                    return;
                }
                this.errorMessage = response;
            }
        },
        computed: {
            dir_name_ready() {
                if (this.dir_name) {
                    return this.dir_name;
                }
                if (this.start_time === this.end_time) {
                    return `Смена на ${this.start_time}`;
                }
                return `Смена с ${this.start_time} по ${this.end_time}` ;
            },
            all_workers() {
                return this.all_worker_ids.map(id => new UserModel(id));
            },
            select_workers() {
                return this.select_worker_ids.map(id => new UserModel(id));
            },
        },
    }).mount('#page-app');
}

const reInitDatePickers = () => {
    $('.datepicker-mask').each(
        function () {
            return new IMask($(this).get(0), {
                mask: Date,  // enable date mask

                // other options are optional
                pattern: 'd-`m-`Y',  // Pattern mask with defined blocks, default is 'd{.}`m{.}`Y'
                // you can provide your own blocks definitions, default blocks for date mask are:
                blocks: {
                    d: {
                        mask: IMask.MaskedRange,
                        from: 1,
                        to: 31,
                        maxLength: 2,
                    },
                    m: {
                        mask: IMask.MaskedRange,
                        from: 1,
                        to: 12,
                        maxLength: 2,
                    },
                    Y: {
                        mask: IMask.MaskedRange,
                        from: 1900,
                        to: 9999,
                    }
                },

                // define date -> str convertion
                format: date => {
                    let day = date.getDate();
                    let month = date.getMonth() + 1;
                    const year = date.getFullYear();

                    if (day < 10) day = "0" + day;
                    if (month < 10) month = "0" + month;

                    return [day, month, year].join('-');
                },

                // define str -> date convertion
                parse: str => {
                    const date = str.split('-');
                    return new Date(date[2], date[1] - 1, date[0]);
                },

                // optional interval options
                // min: new Date(2000, 0, 1),  // defaults to `1900-01-01`
                // max: new Date(2030, 0, 1),  // defaults to `9999-01-01`

                autofix: true,  // defaults to `false`

                // pattern options can be set as well
                lazy: false,

                // and other common options
                overwrite: true  // defaults to `false`
            });
        }
    );

    $(".datepicker").datepicker({
        dateFormat: "dd-mm-yy",
        regional: "ru"
    });
};

vueInit();

$(() => {
    // vueInit();
    // reInitDatePickers();
});
</script>