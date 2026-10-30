<?php

$isSend = (new TelegramNotificator())->send(
    (object)['telegram_chat_id' => '499295775'],
    "Hello world"
);
die(printf("Is message send? = %d", $isSend));