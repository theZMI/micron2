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
            </div>
        </div>
    </div>

</div>
<script>
    Vue.createApp({
        data() {
            return {firstname:'Tom', lastname:'Smith'}
        },
        computed:{
            fullname: {
                get: function () {
                    return this.firstname + ' ' + this.lastname;
                },
                set: function (newValue) {
                    const names = newValue.split(' ')
                    this.firstname = names[0]
                    this.lastname = names[names.length - 1]
                }
            }
        }
    }).mount('#app');
</script>