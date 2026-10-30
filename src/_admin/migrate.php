<?php

$applied = [];
if (Post('is_run')) {
    $applied = Migrator::run();
}
$all     = Migrator::all();
$pending = Migrator::pending();
