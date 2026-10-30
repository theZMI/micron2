<?php

// Локально (ENV=local) SMS не отправляем, а пишем в лог-файл
if (IsLocalEnv()) {
    class SmsNotificator extends \SmsNotificator\SmsMockupNotificator {}
} else {
    class SmsNotificator extends \SmsNotificator\SmscNotificator {}
}
