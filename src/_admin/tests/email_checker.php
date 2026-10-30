<?php

$msg = '';

if (Post('is_set')) {
    $isSend = (new EmailNotificator())->send(
        (object)['email' => SUPPORT_EMAIL],
        "Hello world"
    );
    $msg = sprintf("Is email send = %d", $isSend);
}
