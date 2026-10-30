<?php

// PWA: подключается в <head> главного шаблона. manifest.json и service_worker.js кладутся в корень сайта и подключаются, только если они есть
$hasManifest      = is_readable(BASEPATH . 'manifest.json');
$hasServiceWorker = is_readable(BASEPATH . 'service_worker.js');
