<link rel="stylesheet" type="text/css" href="<?= Root("i/css/_dev/debug_panel.css") ?>"/>

<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">Лог ошибок</h1>

            <div class="table-responsive mb-4 table-extra-condensed-wrapper">
                <table class="table table-condensed table-extra-condensed">
                    <?php if (empty($all)): ?>
                        <tr>
                            <td class="text-center">Нет записей об ошибках</td>
                        </tr>
                    <?php else: ?>
                        <thead>
                        <tr>
                            <th>LogID</th>
                            <th>Error (No) &nbsp; / &nbsp; File (Line)</th>
                            <th>Backtrace</th>
                            <th>SQL</th>
                            <th>IP + Browser + Platform</th>
                            <th>Time</th>
                            <th>$_GET</th>
                            <th>$_POST</th>
                            <th>$_COOKIE</th>
                            <th>$_SESSION</th>
                            <th>$_SERVER</th>
                            <th>$_FILES</th>
                            <th>G_CONFIG</th>
                            <th>G_LANG</th>
                            <th>G_USER</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($all as $log): ?>
                            <tr>
                                <td><?= $log->id ?></td>
                                <td>
                                    <strong><?= $log->errstr ?> (<?= $log->errno ?>)</strong>
                                    <br>
                                    <?= $log->errfile ?> (<?= $log->errline ?>)
                                </td>
                                <td>
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="$('#i-backtrace-<?= $log->id ?>').slideToggle()">Backtrace</button>
                                    <div id="i-backtrace-<?= $log->id ?>" style="display: none;">
                                        <textarea class="form-control" style="font-size: 11px; min-height: 450px; min-width: 1200px;"><?= htmlspecialchars($log->backtrace) ?></textarea>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="const c = $('#i-sql-<?= $log->id ?>'); c.load('<?= Root("_admin/error_log_sql&id={$log->id}") ?>').slideToggle()">SQL</button>
                                    <div id="i-sql-<?= $log->id ?>" style="display: none;"></div>
                                </td>
                                <td>
                                    <strong>
                                        <nobr><?= $log->platform ?> (<?= $log->browser ?> <sup><?= $log->browser_version ?></sup>)</nobr>
                                    </strong>
                                    <br>
                                    IP: <?= $log->ip ?>
                                </td>
                                <td><?= date("d-m-Y H:i:s", $log->create_time) ?></td>
                                <td>
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="$('#i-get-<?= $log->id ?>').slideToggle()">GET</button>
                                    <div id="i-get-<?= $log->id ?>" style="display: none; font-size: 11px; min-width: 600px; padding: 15px; border: 1px solid #DDD; border-radius: 7px;">
                                        <?php @Xmp(json_decode($log->_get)) ?>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="$('#i-post-<?= $log->id ?>').slideToggle()">POST</button>
                                    <div id="i-post-<?= $log->id ?>" style="display: none; font-size: 11px; min-width: 600px; padding: 15px; border: 1px solid #DDD; border-radius: 7px;">
                                        <?php @Xmp(json_decode($log->_post)) ?>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="$('#i-cookie-<?= $log->id ?>').slideToggle()">COOKIE</button>
                                    <div id="i-cookie-<?= $log->id ?>" style="display: none; font-size: 11px; min-width: 600px; padding: 15px; border: 1px solid #DDD; border-radius: 7px;">
                                        <?php @Xmp(json_decode($log->_cookie)) ?>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="$('#i-session-<?= $log->id ?>').slideToggle()">SESSION</button>
                                    <div id="i-session-<?= $log->id ?>" style="display: none; font-size: 11px; min-width: 600px; padding: 15px; border: 1px solid #DDD; border-radius: 7px;">
                                        <?php @Xmp(json_decode($log->_session)) ?>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="$('#i-server-<?= $log->id ?>').slideToggle()">SERVER</button>
                                    <div id="i-server-<?= $log->id ?>" style="display: none; font-size: 11px; min-width: 600px; padding: 15px; border: 1px solid #DDD; border-radius: 7px;">
                                        <?php @Xmp(json_decode($log->_server)) ?>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="$('#i-files-<?= $log->id ?>').slideToggle()">FILES</button>
                                    <div id="i-files-<?= $log->id ?>" style="display: none; font-size: 11px; min-width: 600px; padding: 15px; border: 1px solid #DDD; border-radius: 7px;">
                                        <?php @Xmp(json_decode($log->_files)) ?>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="$('#i-config-<?= $log->id ?>').slideToggle()">CONFIG</button>
                                    <div id="i-config-<?= $log->id ?>" style="display: none; font-size: 11px; min-width: 600px; padding: 15px; border: 1px solid #DDD; border-radius: 7px;">
                                        <?php @Xmp(json_decode($log->g_config)) ?>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="$('#i-user-<?= $log->id ?>').slideToggle()">USER</button>
                                    <div id="i-user-<?= $log->id ?>" style="display: none; font-size: 11px; min-width: 600px; padding: 15px; border: 1px solid #DDD; border-radius: 7px;">
                                        <?php @Xmp(json_decode($log->g_user)) ?>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="$('#i-lang-<?= $log->id ?>').slideToggle()">LANG</button>
                                    <div id="i-lang-<?= $log->id ?>" style="display: none; font-size: 11px; min-width: 600px; padding: 15px; border: 1px solid #DDD; border-radius: 7px;">
                                        <?php @Xmp(json_decode($log->g_lang)) ?>
                                    </div>
                                </td>
                                <td width="1%" class="text-center">
                                    <a href="<?= GetCurUrl('a=delete&id=' . $log->id) ?>" onclick="return confirm('Удалить эту запись?')" class="btn btn-sm btn-danger"><i class="bi bi-trash3-fill"></i></a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    <?php endif ?>
                </table>
            </div>
            <div class="text-center mb-4">
                <?php IncludeCom(
                    "_dev/paginator",
                    [
                        "css"          => "justify-content-center",
                        "pageUrl"      => GetCurUrl("page=" . M_PAGINATOR_PAGE),
                        "firstPageUrl" => GetCurUrl("page=" . M_DELETE_PARAM),
                        "total"        => $count,
                        "perPage"      => ErrorLoggerModel::PAGE_LIMIT,
                        "curPage"      => Get("page", 1)
                    ]
                ) ?>
            </div>
            <div class="text-center">
                <a href="<?= GetCurUrl('a=clear') ?>" class="btn btn-danger btn-lg rounded-pill" onclick="return confirm('Очистить весь лог ошибок?')">
                    <i class="bi bi-trash3-fill me-2"></i>Удалить всё
                </a>
            </div>

        </div>
    </div>
</div>