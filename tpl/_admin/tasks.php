<div class="container-fluid mb-4" id="app-tasks">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">
                <span class="pull-start me-3">Задачи</span>
                <a href="<?= SiteRoot('_admin/tasks/add_or_edit') ?>" class="btn btn-primary rounded-pill pull-end">
                    <i class="bi bi-plus-lg me-2"></i>Добавить
                </a>
            </h1>
            <div class="table-responsive table-extra-condensed-wrapper mb-4">
                <table class="table table-condensed table-hover table-extra-condensed">
                    <tr>
                        <th>ID</th>
                        <th>Задача</th>
                        <th class="no-wrap">Закреплена за</th>
                        <th class="no-wrap">Выполнить до</th>
                        <th colspan="2">Действия</th>
                    </tr>
                    <tr class="table-extra-condensed-filters">
                        <th><input type="text" v-model="filters.id" value="" class="form-control"></th>
                        <th><input type="text" v-model="filters.task_or_description" value="" class="form-control"></th>
                        <th><input type="text" v-model="filters.user_full_name" value="" class="form-control"></th>
                        <th><input type="text" v-model="filters.deadline_inFormat" value="" class="form-control"></th>
                        <th colspan="2"></th>
                    </tr>
                    <tr v-if="tasks.length" v-for="task in tasks" :key="task.id">
                        <td>{{ task.id }}</td>
                        <td>
                            <strong class="mb-2">{{ task.task }}</strong><br>
                            <span class="text-muted">{{ task.description }}</span>
                        </td>
                        <td>{{ task.user.full_name ? task.user.full_name : "Без пользователя" }}</td>
                        <td>{{ task.deadline_inFormat }}</td>
                        <td width="1%" class="text-center">
                            <a :href="this._editLinkStart + task.id" class="btn btn-sm btn-primary" title="Изменить данные"><i class="bi bi-pencil-fill"></i></a>
                        </td>
                        <td width="1%" class="text-center">
                            <a :href="this._deleteLinkStart + task.id" class="btn btn-sm btn-danger" onclick="return confirm('Удалить?')" title="Удалить"><i class="bi bi-trash3-fill"></i></a>
                        </td>
                    </tr>
                    <tr v-else class="text-center">
                        <td colspan="99">Нет данных</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="module">
    Vue.createApp({
        data() {
            return {
                _editLinkStart: '<?= SiteRoot("_admin/tasks/add_or_edit&id=") ?>',
                _deleteLinkStart: '<?= GetCurUrl('a=delete&id=') ?>',
                _tasks: <?= json_encode($tasks) ?>,
                _users: <?= json_encode($users) ?>,
                filters: {},
            }
        },
        created() {
            if (!window.DB) {
                window.DB = {};
            }
            window.DB.users = this._users;
            window.DB.tasks = this._tasks;
        },
        mounted() {
        },
        methods: {},
        computed: {
            users() {
                return this._users.map(v => new UserModel(v.id));
            },
            tasks() {
                let tasks = this._tasks.map(v => new TaskModel(v.id));

                // Применяем фильтры
                for (const k in this.filters) {
                    const v = this.filters[k];
                    if (k === 'task_or_description') {
                        tasks = tasks.filter(task => {
                            return String(task.task).toLowerCase().indexOf(v) !== -1
                                || String(task.description).toLowerCase().indexOf(v) !== -1;
                        });
                        continue;
                    }
                    if (k === 'user_full_name') {
                        tasks = tasks.filter(task => {
                            const userName = task.user.full_name ? task.user.full_name : "Без пользователя";
                            return userName.toLowerCase().indexOf(v) !== -1;
                        });
                        continue;
                    }
                    tasks = tasks.filter(task => {
                        return String(task[k]).indexOf(v) !== -1;
                    });
                }

                return tasks;
            }
        },
        watch: {}
    }).mount('#app-tasks');
</script>