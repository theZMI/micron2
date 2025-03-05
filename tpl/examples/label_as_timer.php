<h1 class="my-4">Лейблы-таймеры</h1>
<p>Просто лейбы которые тикают +1 секунду после загрузки страницы</p>

<script>
    function InitSimpleTimers() {
        $('.simple-timer').each(
            () => {
                setInterval(() => $(this).html( $(this).data('start-time') ), 1000);
            }
        );
    }

    $(() => {
        InitSimpleTimers();
    });
</script>
<span data-start-time="<?= time() ?>" class="simple-timer">
    <span class="bi bi-hourglass-split"></span>
</span>