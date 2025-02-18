<?php

$page   = intval(Get('p', 1));
$list   = (new DirShiftsModel())->getList();