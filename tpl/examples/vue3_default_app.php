<h1 class="my-4">Default template to vue3 application</h1>
<p>In this template we add special loader which shown while vue application don't applied to #page-app</p>
<?php ob_start() ?>
    <div style="padding: 30px; border-radius: 10px; background: #EEE; color: #000;">
        {{ content }}
    </div>
<?php IncludeCom('_dev/page_vue3_app_template', ['content' => ob_get_clean()]) ?>

<script type="module">
    import { formatTimer, formatDateTime, getNow } from "/i/ts/helpers/datetime.js"; // Хелперы дат (i/ts/helpers)

    const loadingStopper = () => {
        let sum = 0;
        for (let i = 0; i < 1e9; i++) {
            sum += i;
        }
    };
    loadingStopper();

    Vue.createApp({
        data() {
            return {
                helloString: 'Hello world!',
                content: 'Loading...',
            }
        },
        async mounted() {
            await this.emulateAjax();
            this.content = `${this.helloString} ${formatDateTime(getNow())}`; // getNow() - секунды, а не миллисекунды
        },
        methods: {
            formatTimer(seconds) {
                return formatTimer(seconds, false);
            },
            async emulateAjax() {
                const delay = ms => new Promise(
                    resolve => setTimeout(resolve, ms)
                ) ;
                await delay(3000);
            },
        },
    }).mount('#page-app');
</script>