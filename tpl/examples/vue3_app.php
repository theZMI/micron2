<div id="app">
    <div class="container">
        <div class="row">
            <div class="col-12 py-3">
                <h1 class="border-bottom pb-3">Приложение на Vue3</h1>
                <p>
                    Здесь продемонстрировано:<br>
                    1) Запуск приложения на vue3 в micron<br>
                    2) Работа с моделями данных (UserModel)<br>
                    3) Работа с виртуальными полями в моделях (UserModel.full_name)<br>
                </p>
                <ol v-if="users.length" class="m-0 mb-3 p-0 site-ol-list">
                    <li v-for="u in users" :key="u.id">
                        #{{ u.id }} -> {{ u.full_name }}
                    </li>
                </ol>
                <div v-else-if="loading" class="pt-3 pb-3">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div v-else>
                    <p class="text-muted pt-3 pb-3">Нет данных по пользователям</p>
                </div>
                <button class="btn btn-secondary" @click="updateUsers">Обновить список пользователей</button>
            </div>
        </div>
    </div>

</div>
<script>
    const delay = ms => new Promise(resolve => setTimeout( resolve, ms ));

    Vue.createApp({
        data() {
            return {
                loading: false,
                users: []
            }
        },
        methods: {
            async updateUsers() {
                const emulateAjax = async () => {
                    await delay(1500);
                    const first_names = ['Michail', 'Roman', 'Uriy', 'Ivan', 'Konstantin', 'Sergey'];
                    const surnames = ['Zaytsev', 'Zatcepin', 'Guzenko', 'Ivanov', 'Petrov', 'Sidorov'];
                    const patronymics = ['Ivanovich', 'Nikolaevich', 'Leonidovich', 'Timoveevich', 'Nikitich', 'Olegovich'];
                    const random = arr => arr[Math.floor((Math.random() * arr.length))];

                    const DB = [];
                    for (let i = 1, max = Math.floor(Math.random() * 10 + 5); i <= max; i++) {
                        DB.push({
                            id: i,
                            first_name: random(first_names),
                            surname: random(surnames),
                            patronymic: random(patronymics)
                        });
                    }
                    return DB;
                }

                try {
                    this.loading = true;
                    new UserModel().setTableData( await emulateAjax() );
                    this.users = new UserModel().getList();
                } finally {
                    this.loading = false;
                }
            }
        },
        created() {
            window.DB = [];
            new UserModel().setTableData([]);
        }
    }).mount('#app');
</script>