<?php

const MIN_GBS = 3;
$bytes       = disk_free_space(BASEPATH);
$gbs         = intval($bytes / 1024 / 1024 / 1024);
$spaceAsText = function (int $bytes): string {
    $si_prefix = array('B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB');
    $base      = 1024;
    $class     = min((int)log($bytes, $base), count($si_prefix) - 1);
    return sprintf('%1.2f', $bytes / pow($base, $class)) . $si_prefix[$class];
};

if ($gbs <= MIN_GBS) {
    $isSend = SendMail(
        SUPPORT_EMAIL,
        "На сайте заканчивается место, осталось меньше " . +MIN_GBS . " Гб",
        "Внимание!<br>На HDD сайта осталось " . $spaceAsText($bytes) . " свободного места!"
    );
    echo $isSend ? "Emails about free space sent" : "Error to send email about free space!";
}

die("Free space: " . $spaceAsText($bytes));