<?php

// /sitemap.xml (src/sitemap.php): страницы сайта + категории и товары магазина. Ссылку на него стоит добавить в robots.txt
Config('sitemap', [
    'pages'         => ['', 'shop', 'contacts', 'manual'], // Обычные страницы: uri для SiteRoot()
    'include_shop'  => true, // Категории и товары
]);
