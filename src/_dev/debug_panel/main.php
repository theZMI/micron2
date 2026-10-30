<?php

$totalTime = floatval(microtime(true) - $GLOBALS['micron_start_time']);
$totalMem = memory_get_usage();
$debug = new DebugPanel();
