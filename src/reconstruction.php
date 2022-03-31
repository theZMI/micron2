<?php

header(Php::Status(503), true);

ob_start();
    IncludeCom('_reconstruction');
$content = ob_get_clean();

ob_clean();
    IncludeCom('_dev/main_tpl_for_stop_info', ['content' => $content, 'title' => 'Обновление сайта']);
exit();
