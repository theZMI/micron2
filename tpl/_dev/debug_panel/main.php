<link rel="stylesheet" type="text/css" href="<?= Root('i/css/_dev/debug_panel.css') ?>" />
<script type="text/javascript" src="<?= Root('i/js/_dev/debug_panel.js') ?>"></script>


<div style="clear: both"></div>

<div id="i-debug-panel">
    <span class="icon show-profile" id="i-show-profile" onclick="g_debug.togglePanel()"></span>

    <ul id="i-debug-panel-list" style="display: none;">
        <li class="show-hide-elem">
            <span class="icon hide-profile" onclick="g_debug.togglePanel()"></span>
        </li>
        <li>
            <span class="icon time"></span>
            <?= number_format(floatval(microtime(true) - Config('start_exec_time')), 3) ?> s
        </li>
        <li>
            <span class="icon mem"></span>
            <?= $debug->memoryUsage() ?>
        </li>
        <li>
            <span class="icon db"></span>
            <a href="javascript:g_debug.toggle('i-databasa-log')">SQL</a>
        </li>
        <li>
            <span class="icon vars"></span>
            <a href="javascript:g_debug.toggle('i-vars-log')">Vars <span class="small">(G: <?= count($_GET) ?> / P: <?= count($_POST) ?> / C: <?= count($_COOKIE) ?> / F: <?= count($_FILES) ?>)</span></a>
        </li>
        <li>
            <span class="icon files"></span>
            <a href="javascript:g_debug.toggle('i-files')">Files <span class="small">(<?= count($debug->files()) ?>)</span></a>
        </li>
        <li>
            <span class="icon engine"></span>
            <a href="javascript:g_debug.toggle('i-engine')">Engine</a>
        </li>
        <li>
            <span class="icon ini-values"></span>
            <a href="javascript:g_debug.toggle('i-ini-values')">Ini + Exts</a>
        </li>
    </ul>

    <div id="i-debug-panel-all-panels" style="display: none;">
        <div id="i-databasa-log" class="debug-panel" style="display: none;">
            <?= $debug->db() ?>
        </div>

        <div id="i-vars-log" class="debug-panel" style="display: none;">
            <ul>
                <li>
                    <?php IncludeCom('_dev/debug_panel/var_panel', ['head' => '$_GET', 'link' => 'i-get-log', 'arr' => $_GET]) ?>
                </li>
                <li>
                    <?php IncludeCom('_dev/debug_panel/var_panel', ['head' => '$_POST', 'link' => 'i-post-log', 'arr' => $_POST]) ?>
                </li>
                <li>
                    <?php IncludeCom('_dev/debug_panel/var_panel', ['head' => '$_COOKIE', 'link' => 'i-cookie-log', 'arr' => $_COOKIE]) ?>
                </li>
                <?php if (session_id()): ?>
                    <li>
                        <?php IncludeCom('_dev/debug_panel/var_panel', ['head' => '$_SESSION', 'link' => 'i-session-log', 'arr' => $_SESSION]) ?>
                    </li>
                <?php endif ?>
                <li>
                    <?php IncludeCom('_dev/debug_panel/var_panel', ['head' => '$_SERVER', 'link' => 'i-server-log', 'arr' => $_SERVER]) ?>
                </li>
                <li>
                    <a href="javascript:g_debug.toggle('i-files-log')">$_FILES <span>(<?= count($_FILES) ?>)</span></a>
                    <div id="i-files-log" style="display: none;">
                        <table>
                            <tr>
                                <th>№</th>
                                <th>Field name</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Tmp_name</th>
                                <th>Error</th>
                                <th>Size</th>
                            </tr>
                            <?php if (count($_FILES)): ?>
                                <?php $i = 0;
                                foreach ($_FILES as $k => $v): ?>
                                    <tr>
                                        <td class="num"><?= ++$i ?></td>
                                        <td><?= $k ?></td>
                                        <td><?= $v['name'] ?></td>
                                        <td><?= $v['type'] ?></td>
                                        <td><?= $v['tmp_name'] ?></td>
                                        <td><?= $v['error'] ?></td>
                                        <td><?= $v['size'] ?></td>
                                    </tr>
                                <?php endforeach ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="center">Empty</td>
                                </tr>
                            <?php endif ?>
                        </table>
                    </div>
                </li>
            </ul>
        </div>

        <div id="i-files" class="debug-panel" style="display: none;">
            <table>
                <tr>
                    <th>№</th>
                    <th>File</th>
                    <th>Size</th>
                    <th>Lines</th>
                </tr>
                <?php $i = 0;
                foreach ($debug->files() as $f): ?>
                    <tr>
                        <td class="num"><?= ++$i ?></td>
                        <td><?= str_replace(BASEPATH, '<span>' . BASEPATH . '</span>', $f['file']) ?></td>
                        <td><?= $f['size'] ?></td>
                        <td><?= $f['lines'] ?></td>
                    </tr>
                <?php endforeach ?>
                <tr class="total">
                    <td></td>
                    <td class="center"><span>Total</span> <?= count($debug->files()) ?> <span>files</span></td>
                    <td><?= $debug->totalFileSize() ?></td>
                    <td><?= $debug->totalFileLines() ?></td>
                </tr>
            </table>
        </div>

        <div id="i-engine" class="debug-panel" style="display: none;">
            <table>
                <tr>
                    <td>Language</td>
                    <td><?= Config('langs')[LANG]['name'] ?> <strong>(<?= LANG ?>)</strong></td>
                </tr>
                <tr>
                    <td>Current url</td>
                    <td><?= GetCurUrl() ?></td>
                </tr>
                <tr>
                    <td>Query</td>
                    <td><?= GetQuery() ?></td>
                </tr>
                <?php foreach (Config('php_ini') as $k => $v): ?>
                    <tr>
                        <td><?= $k ?></td>
                        <td><?= VarDump($v) ?></td>
                    </tr>
                <?php endforeach ?>
            </table>
        </div>

        <div id="i-ini-values" class="debug-panel" style="display: none;">
            <h3>Load extensions</h3>
            <ul class="exts">
                <?php foreach (get_loaded_extensions() as $v): ?>
                    <li>
                        <a href="javascript:g_debug.showExtensionFuncs('i-ext-<?= md5($v) ?>')">
                            <?= $v ?>
                        </a>
                        <div style="display:none" id="i-ext-<?= md5($v) ?>">
                            <h3>Function in extension: <?= $v ?></h3>
                            <?php
                            $funcs = get_extension_funcs($v);
                            $funcs = empty($funcs) ? [] : $funcs;

                            if ($funcs):
                                ?>
                                <table>
                                    <?php foreach (get_extension_funcs($v) as $k => $func): ?>
                                        <tr>
                                            <td><?= $k++ ?></td>
                                            <td><?= $func ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                </table>
                            <?php endif ?>
                        </div>
                    </li>
                <?php endforeach ?>
            </ul>
            <div style="clear: both"></div>
            <div id="i-ext-show"></div>

            <h3>Php.ini</h3>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Global val</th>
                    <th>Local val</th>
                    <th>Access</th>
                </tr>
                <?php foreach (ini_get_all() as $k => $v): ?>
                    <tr>
                        <td><?= $k ?></td>
                        <td><?= VarDump($v['global_value']) ?></td>
                        <td><?= VarDump($v['local_value']) ?></td>
                        <td><?= DebugPanel::showPhpIniAccess($v['access']) ?></td>
                    </tr>
                <?php endforeach ?>
            </table>
        </div>
    </div>
</div>

<div style="clear: both"></div>
