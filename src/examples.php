<?php

// Список готовых компонентов и примеров (core/config/examples.php)
$groups      = Config('examples');
$isAvailable = (bool)Env('DEBUG_MODE'); // Примеры открываются только в режиме разработки
