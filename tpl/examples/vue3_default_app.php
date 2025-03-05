<h1 class="my-4">Default template to vue3 application</h1>
<p>In this template we add special loader which shown while vue application don't applied to #page-app</p>
<?php ob_start() ?>
    <div style="padding: 30px; border-radius: 10px; background: #EEE;">
        {{ content }}
    </div>
<?php IncludeCom('_dev/page_vue3_app_template', ['content' => ob_get_clean()]) ?>

<script type="module">
    import { formatTimer } from "/i/js/_dev/datetime.js";

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
            this.content = this.helloString + formatTimer(+Date.now());
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