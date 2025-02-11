<?php

$db = GetDefaultDb();
$qs = [
    'TABLE `shifts` DROP `_work_intervals`',
    'ALTER TABLE `shifts` DROP `work_time`'
];
array_walk($qs, fn($q) => $db->query($q));
die("END");