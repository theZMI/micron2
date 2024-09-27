<?php

$isSend = (new EmailNotificator())->send(
    (object)['email' => SUPPORT_EMAIL],
    "Hello world"
);
die(sprintf("Is email send = %d", $isSend));