<?php

$steps = BreadCrumb::getInstance()->steps();

if (count($steps) === 1) {
    ExitCom();
}