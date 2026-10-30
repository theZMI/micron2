<?php

$isSend = (new SmsNotificator())->send(
    (object)['phone' => SUPPORT_PHONE],
    "Hello world"
);
die(printf("Is sms send? = %d", $isSend));