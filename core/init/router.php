<?php

use \Pecee\SimpleRouter\SimpleRouter;

// Получаем список языков вида название_языка=>uri, но так, чтобы дефолтный язык был последним, иначе он перебьёт пути с языком в url-е
$getLangs = function () {
    $langs = Config('langs');
    unset($langs[DEF_LANG]);
    foreach ($langs as $k => $v) {
        $langs[$k] = "{$k}/";
    }
    $langs[DEF_LANG] = '';
    return $langs;
};
$apiUri   = 'api/v1.0/';

// Админка
SimpleRouter::group(['middleware' => \Middlewares\AdminAuthMiddleware::class], function () {
    SimpleRouter::all('/_admin/{end_uri}', function ($end_uri) {
        $end_uri = FileSys::filenameSecurity($end_uri);
        TryIncludeCom("_admin/{$end_uri}");
    })->where(['end_uri' => '.*?']);
});

foreach ($getLangs() as $lang => $langUri) {
    // Вызовы API с авторизацией
    SimpleRouter::group(['middleware' => [\Middlewares\PrepareApiMiddleware::class, \Middlewares\CommonMiddleware::class, \Middlewares\CheckApiUserAuthMiddleware::class]], function () use ($langUri, $apiUri) {
        // users
        SimpleRouter::all("/{$langUri}{$apiUri}users", function () use ($langUri, $apiUri) {
            TryIncludeCom("{$langUri}{$apiUri}users", [], "{$apiUri}404");
        })->where(['end_uri' => '.*?']);

        // users/{id}
        SimpleRouter::all("/{$langUri}{$apiUri}users/{id}", function ($id) use ($langUri, $apiUri) {
            TryIncludeCom("{$langUri}{$apiUri}users/user", ['id' => $id], "{$apiUri}404");
        })->where(['id' => '[0-9]+']);

        // users/*
        SimpleRouter::all("/{$langUri}{$apiUri}users{end_uri}", function ($end_uri) use ($langUri, $apiUri) {
            $end_uri = FileSys::filenameSecurity($end_uri);
            $end_uri = empty($end_uri) ? '' : "/{$end_uri}";
            TryIncludeCom("{$langUri}{$apiUri}users{$end_uri}", [], "{$apiUri}404");
        })->where(['end_uri' => '.*?']);
    });

    // Вызовы API без авторизации
    SimpleRouter::group(['middleware' => [\Middlewares\PrepareApiMiddleware::class, \Middlewares\CommonMiddleware::class]], function () use ($langUri, $apiUri) {
        SimpleRouter::all("/{$langUri}{$apiUri}{end_uri}", function ($end_uri) use ($langUri, $apiUri) {
            $end_uri = FileSys::filenameSecurity($end_uri);
            TryIncludeCom("{$langUri}{$apiUri}{$end_uri}", [], "{$apiUri}404");
        })->where(['end_uri' => '.*?']);
    });

    // Кабинет пользователя
    SimpleRouter::group(['middleware' => [\Middlewares\PrepareUserMiddleware::class, \Middlewares\CommonMiddleware::class, \Middlewares\CheckUserAuthMiddleware::class]], function () use ($langUri) {
        SimpleRouter::all("/{$langUri}user/{end_uri}", function ($end_uri) use ($langUri) {
            $end_uri = FileSys::filenameSecurity($end_uri);
            TryIncludeCom("/{$langUri}user/{$end_uri}");
        })->where(['end_uri' => '.*?']);
    });

    // Стандартный тип подключения микрона (т.е. contacts/about_us -> IncludeCom('contacts/about_us'))
    SimpleRouter::group(['middleware' => \Middlewares\CommonMiddleware::class], function () use ($langUri, $apiUri) {
        SimpleRouter::all("/{$langUri}{end_uri}", function ($end_uri) use ($langUri) {
            $end_uri = FileSys::filenameSecurity($end_uri);
            if (in_array($end_uri, ['index.php', 'index.html', 'index.htm'])) {
                IncludeCom($langUri . Config('defaultComponent'));
                return;
            }
            TryIncludeCom("{$langUri}{$end_uri}");
        })->where(['end_uri' => '.*?']);

        // Запросы вида /ru, /en... должны вызывать главную
        SimpleRouter::all("/{$langUri}", fn() => TryIncludeCom($langUri . Config('defaultComponent')));
    });
}
