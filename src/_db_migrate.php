<?php

$db = GetDefaultDb();
$qs = [
    'TABLE `shifts` DROP `_work_intervals`',
    'ALTER TABLE `shifts` DROP `work_time`',
    'ALTER TABLE `shift_params` ADD `status` INT NULL DEFAULT NULL AFTER `value_as_number`'
];
array_walk($qs, fn($q) => $db->query($q));
die("END");