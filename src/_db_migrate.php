<?php

$db = GetDefaultDb();
$qs = [

];
array_walk($qs, fn($q) => $db->query($q));
die("END");