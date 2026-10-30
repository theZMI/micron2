<?php

// Ключи Google reCAPTCHA v2 ("Я не робот") из https://www.google.com/recaptcha/admin
define('RECAPTCHA_SITE_KEY', (string)Env('RECAPTCHA_SITE_KEY', ''));
define('RECAPTCHA_SECRET_KEY', (string)Env('RECAPTCHA_SECRET_KEY', ''));
