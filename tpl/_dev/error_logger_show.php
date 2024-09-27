<div class="page">
    <div class="text-center">
        <i class="bi bi-exclamation-circle-fill text-danger fs-1"></i><br>
        <p class="lead text-danger">Error #<?= $logger->id ?></p>
    </div>
    <table class="table text-start">
        <tr>
            <th>Error:</th>
            <td><?= nl2br($logger->errstr) ?> (<?= $logger->errno ?>)</td>
        </tr>
        <tr>
            <th>Place:</th>
            <td><?= $logger->errfile ?> (<?= $logger->errline ?>)</td>
        </tr>
    </table>
    <br>
    <div class="text-center">
        <button type="button" onclick="$('#i-backtrace-wrapper').slideToggle();" class="btn btn-primary rounded-pill ps-4 pe-4">Backtrace<i class="ms-1 bi bi-arrow-down-short"></i></button>
    </div>
    <br>
    <div id="i-backtrace-wrapper" class="backtrace-wrapper">
        <code class="text-start d-block mt-1">
            <pre><?= htmlspecialchars($logger->backtrace) ?></pre>
        </code>
    </div>
</div>
<div class="debug-panel-wrapper">
    <?php IncludeCom('_dev/debug_panel/main') ?>
</div>