<div id="app">
    <div class="container">
        <div class="row">
            <div class="col-12 py-3">
                <h1 class="border-bottom pb-3">Приложение на Vue3</h1>
                <p class="fs-3 mb-3">Имя: {{fullname}}</p>
                <div class="row">
                    <div class="col-4"><input type="text" class="form-control" v-model="firstname" /></div>
                    <div class="col-4"><input type="text" class="form-control" v-model="lastname" /></div>
                    <div class="col-4"><input type="text" class="form-control" v-model="fullname" /></div>
                </div>
                <hr>
                <ul class="site-ol-list">
                    <li v-for="u in users" :key="u.id">
                        #{{ u.id }} -> {{ u }} -> {{ u.full_name }}
                    </li>
                </ul>
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
                firstname:'Tom',
                lastname:'Smith',
                users: []
            }
        },
        computed: {
            fullname: {
                get: function () {
                    return this.firstname + ' ' + this.lastname;
                },
                set: function (newValue) {
                    const names = newValue.split(' ')
                    this.firstname = names[0]
                    this.lastname = names[names.length - 1]
                }
            },
            users() {
                return (new UserModel()).getList();
            }
        },
        methods: {
            async updateUsers() {
                const emulateGetFromAjax = async () => {
                    // await delay(1500);
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

                const DB = await emulateGetFromAjax();
                window.DB = [];
                window.DB['users'] = DB;
                // const model = new UserModel();
                // model.setTableData(DB);
                console.log(window);
            }
        }
    }).mount('#app');
</script>