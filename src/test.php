<?php

echo "This is test script";
__($_GET);
__($_POST);
echo "Filtered par_3: " . Post('par_3');

echo "Independence include EN-version:";
IncludeCom('en/home');