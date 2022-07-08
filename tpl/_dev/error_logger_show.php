<div class="text-center">
    <span class="glyphicon glyphicon-remove header-icon text-danger"></span>
    <p class="lead text-danger"><?= $logger->errno ?></p>
    <br>
    <table class="table text-left">
        <tr>
            <th>Error:</th>
            <td><?= nl2br($logger->errstr) ?></td>
        </tr>
        <tr>
            <th>Place:</th>
            <td><?= $logger->errfile ?> (<?= $logger->errline ?>)</td>
        </tr>
    </table>
    <br>
    <div>
        <button type="button" onclick="$('#i-backtrace-wrapper').slideToggle();" class="btn btn-default"><span
                    class="glyphicon glyphicon-chevron-down"></span> &nbsp; Backtrace
        </button>
        <div id="i-backtrace-wrapper" class="backtrace-wrapper">
            <pre class="text-left"><?= htmlspecialchars($logger->backtrace) ?></pre>
        </div>

        <div class="debug-panel-wrapper">
            <?php IncludeCom('_dev/debug_panel/main') ?>
        </div>
    </div>
</div>
