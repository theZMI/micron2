<?php

$isSend = (new PushNotificator())->send(
    (object)['device_id' => '5f289f07-af08-41a4-8a32-7bfccd5671a7'],
    "Hello world"
);
die(printf("Is message send? = %d", $isSend));