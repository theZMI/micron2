<?php

$data = ReadXL(BASEPATH . 'tmp/example.xls', [12, 13]);
Xmp($data);
die;

echo "This is test script";
Xmp($_GET);
Xmp($_POST);
echo "Filtered par_3: " . Post('par_3');

echo "Independence include EN-version:";
IncludeCom('en/home');