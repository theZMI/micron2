<div class="container-fluid mb-4" id="page-app">
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
                                        <?php for ($i = intval(5 * 3600); $i < intval(24 * 3600 - 1); $i += 1800): ?>
                                            <option value="<?= $i ?>">До <?= FormatTimeInterval($i) ?></option>
                                        <?php endfor ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="removeTask" class="btn btn-danger rounded-pill me-auto" v-if="isTaskEdit">Удалить</button>
                            <button type="submit" class="btn btn-primary rounded-pill"><i class="bi bi-check-lg pe-2"></i>Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>

            <h1 class="mt-4 mb-4">
                <span class="pull-start"><?= $model->isExists() ? "Смена №" . $modelParam('id') : 'Новая смена' ?></span>
            </h1>
            <form action="<?= GetCurUrl() ?>" method="post" @submit.prevent="saveShift">
                <input type="hidden" name="is_set" value="1">
                <div v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>
                <div class="row">
                    <div class="col">
                        <div class="mb-4">
                            <label class="form-label">Начать в:</label>
                            <div class="input-group mb-3">
                                <input type="text" name="shift_start_time" class="form-control datepicker datepicker-mask" autocomplete="off" v-model="start_time" placeholder="ДД-ММ-ГГГГ">
                                <span class="input-group-text">00:00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-4">
                            <label class="form-label">Закончить в:</label>
                            <div class="input-group mb-3">
                                <input type="text" name="shift_end_time" class="form-control datepicker datepicker-mask" autocomplete="off" v-model="end_time" placeholder="ДД-ММ-ГГГГ">
                                <span class="input-group-text">23:59</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Название:</label>
                    <input type="text" name="name" class="form-control" :value="dir_name_ready" @input="event => dir_name = event.target.value">
                </div>
                <div class="mb-4">
                    <label class="form-label">Работники:</label>
                    <div class="form-check form-switch form-switch-md mb-2" v-for="u in all_workers" :key="u.id">
                        <input type="checkbox" role="switch" class="form-check-input" :id="`worker_id__${u.id}`" :name="`user_ids[${u.id}]`" v-model="select_worker_ids" :value="u.id">
                        <label class="form-check-label ms-3 mt-1" :for="`worker_id__${u.id}`">
                            {{ u.full_name }}
                            <span class="badge bg-secondary rounded-pill">{{ u.login }}</span>
                        </label>
                    </div>
                </div>
                <div class="mb-4">
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
                            <div v-if="appendTasks.common.length" class="tasks-block">
                                <a v-for="task in appendTasks.common" class="tasks-block-task" @click="openTask(task)">
                                    <div class="tasks-block-task-task">
                                        {{ task.task }}
                                        <div class="tasks-block-task-deadline" v-if="task.deadline">До {{ formatInterval(+task.deadline) }}</div>
                                    </div>
                                    <div class="tasks-block-task-desc">{{ task.description }}</div>
                                </a>
                            </div>
                            <div v-else class="text-center pt-3 pb-3">
                                Нет задач
                            </div>
                            <div class="text-center">
                                <button class="btn btn-primary rounded-pill" type="button" data-bs-toggle="modal" data-bs-target="#taskFormModal"><i class="bi bi-plus-lg me-2"></i>Добавить задачу</button>
                            </div>
                            <hr style="color: #DDD;">
                            <p class="lead">Параметры по завершению смены:</p>
                            <div class="pt-3 pb-3">
                                <div class="form-check form-switch form-switch-md mb-2" v-for="param in possibleParams" :key="param.id">
                                    <input type="checkbox" role="switch" class="form-check-input" :id="`common_param_id__${param.id}`" v-model="shiftParams['common']" :value="param.id">
                                    <label class="form-check-label ms-3 mt-1" :for="`common_param_id__${param.id}`">
                                        {{ param.name }}
                                        <span class="badge bg-secondary rounded-pill">{{ param.type_name }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div v-for="worker in select_workers" :key="worker.id" :id="`worker-tasks-tab-${worker.id}-pane`" class="tab-pane fade" role="tabpanel" :aria-labelledby="`worker-tasks-tab-${worker.id}-tab`" tabindex="0">
                            <p class="lead">Задачи на смену:</p>
                            <div v-if="appendTasks[worker.id]?.length" class="tasks-block">
                                <a v-for="task in appendTasks[worker.id]" class="tasks-block-task" @click="openTask(task)">
                                    <div class="tasks-block-task-task">
                                        {{ task.task }}
                                        <div class="tasks-block-task-deadline" v-if="task.deadline">До {{ formatInterval(+task.deadline) }}</div>
                                    </div>
                                    <div class="tasks-block-task-desc">{{ task.description }}</div>
                                </a>
                            </div>
                            <div v-else class="text-center pt-3 pb-3">
                                Нет задач
                            </div>
                            <div class="text-center">
                                <button class="btn btn-primary rounded-pill" type="button" data-bs-toggle="modal" data-bs-target="#taskFormModal"><i class="bi bi-plus-lg me-2"></i>Добавить задачу</button>
                            </div>
                            <hr style="color: #DDD;">
                            <p class="lead">Параметры по завершению смены:</p>
                            <div class="pt-3 pb-3">
                                <div class="form-check form-switch form-switch-md mb-2" v-for="param in possibleParams" :key="param.id">
                                    <input type="checkbox" role="switch" class="form-check-input" :id="`user_param_id__${worker.id}__${param.id}`" v-model="shiftParams[worker.id]" :value="param.id">
                                    <label class="form-check-label ms-3 mt-1" :for="`user_param_id__${worker.id}__${param.id}`">
                                        {{ param.name }}
                                        <span class="badge bg-secondary rounded-pill">{{ param.type_name }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <a href="<?= SiteRoot('_admin/dir_shifts') ?>" class="btn btn-link btn-lg"><i class="bi bi-arrow-left pe-2"></i>К списку</a>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill"><i class="bi bi-check-lg pe-2"></i>Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="module">
    $(() => {
        Vue.createApp({
            data() {
                return {
                    dir_name: '<?= $modelParam('name') ?>',
                    start_time: '<?= Post('start_time', OutputFormats::dateForDatePicker(+$modelParam('start_time', strtotime("+1 day")))) ?>',
                    end_time: '<?= Post('end_time', OutputFormats::dateForDatePicker(+$modelParam('end_time', strtotime("+1 day")))) ?>',
                    all_worker_ids: <?= json_encode($allWorkersIds) ?>,
                    select_worker_ids: <?= json_encode($currentWorkersIds) ?>,
                    _users: <?= json_encode(array_values(array_map(fn($u) => $u->getData(), $users))) ?>,
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
                                deadline: <?= intval($task->deadline_time) ?>
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
                    isTaskEdit: false,
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
                    shiftParams: {
                        'common': [],
                        <?php $wasUserIds = []; ?>
                        <?php foreach ($model->shifts as $shift): ?>
                        <?php $wasUserIds[] = $shift->user_id; ?>
                        <?= intval($shift->user_id) ?>: [
                            <?php foreach ($shift->shift_params as $shift_param): ?>
                            <?= $shift_param->param->id ?>,
                            <?php endforeach; ?>
                        ],
                        <?php endforeach ?>
                        <?php foreach (array_diff($allWorkersIds, $wasUserIds) as $user_id): ?>
                        <?= intval($user_id) ?>: [],
                        <?php endforeach; ?>
                    }
                }
            },
            created() {
                if (!window.DB) {
                    window.DB = {};
                }
                window.DB.users = this._users;
            },
            mounted() {
                // Hook close modal window (add or edit task)
                $("#taskFormModal").on("hidden.bs.modal", () => {
                    this.currentTask = {...this.emptyTask};
                    this.isTaskEdit = false;
                });
            },
            methods: {
                formatInterval(seconds) {
                    const dt = new Date(seconds * 1000);
                    const h = dt.getUTCHours().toString().padStart(2, '');
                    const m = dt.getUTCMinutes().toString().padStart(2, '0');
                    return (+h > 0 ? `${h}:` : '') + [m].join(':');
                },
                currentTaskSubmit() {
                    if (!this.currentTask.id) {
                        this.currentTask.id = -Date.now();
                    }
                    if (!this.appendTasks[this.selectedTab]) {
                        this.appendTasks[this.selectedTab] = [];
                    }

                    let foundTask = null;
                    for (const v of this.appendTasks[this.selectedTab]) {
                        if (this.currentTask.id === v.id) {
                            foundTask = v;
                            break;
                        }
                    }

                    if (foundTask) {
                        ['task', 'description', 'deadline'].forEach(field => {
                            foundTask[field] = this.currentTask[field];
                        })
                    } else {
                        this.appendTasks[this.selectedTab].push(
                            {...this.currentTask}
                        );
                    }

                    $('#taskFormModal').modal('hide');
                },
                openTask(task) {
                    this.isTaskEdit = true;

                    this.currentTask.id = task.id;
                    this.currentTask.task = task.task;
                    this.currentTask.description = task.description;
                    this.currentTask.deadline = task.deadline;

                    $('#taskFormModal').modal('show');
                    // new bootstrap.Modal(document.getElementById('taskFormModal'), {}).show();
                },
                removeTask() {
                    if (!confirm('Удалить данную задачу?')) {
                        return;
                    }
                    for (const k in this.appendTasks[this.selectedTab]) {
                        const v = this.appendTasks[this.selectedTab][k];
                        if (v.id === this.currentTask.id) {
                            this.appendTasks[this.selectedTab].splice(k, 1);
                        }
                    }
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
                            'params': this.shiftParams,
                        },
                        'json'
                    );
                    if (response['data'] === 'OK') {
                        window.location.replace('<?= SiteRoot('_admin/dir_shifts') ?>');
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
                    return `Смена с ${this.start_time} по ${this.end_time}`;
                },
                all_workers() {
                    return this.all_worker_ids.map(id => new UserModel(id));
                },
                select_workers() {
                    return this.select_worker_ids.map(id => new UserModel(id));
                },
            },
        }).mount('#page-app');
    });
</script>