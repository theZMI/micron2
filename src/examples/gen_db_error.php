<?php

$db = GetDefaultDb();
$db->query("SELECT * FROM `no_exists_table`");