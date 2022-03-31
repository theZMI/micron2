<?php

// Должен быть передан $logger
ob_start();
    IncludeCom('_dev/debug_panel/main');
$debugPanel = ob_get_clean();
