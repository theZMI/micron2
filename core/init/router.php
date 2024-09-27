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

// Админка
SimpleRouter::group(['middleware' => \Middlewares\AdminAuthMiddleware::class], function () {
    SimpleRouter::all('/_admin/{end_uri}', function ($end_uri) {
        $end_uri = FileSys::filenameSecurity($end_uri);
        TryIncludeCom("_admin/{$end_uri}");
    })->where(['end_uri' => '.*?']);
});

foreach ($getLangs() as $lang => $langUri) {
    // Кабинет пользователя
    SimpleRouter::group(['middleware' => \Middlewares\UserAuthMiddleware::class], function () use ($langUri) {
        SimpleRouter::all("/{$langUri}user/{end_uri}", function ($end_uri) use ($langUri) {
            $end_uri = FileSys::filenameSecurity($end_uri);
            TryIncludeCom("/{$langUri}user/{$end_uri}");
        })->where(['end_uri' => '.*?']);
    });

    // Все запросы сайта
    SimpleRouter::group(['middleware' => \Middlewares\CommonMiddleware::class], function () use ($langUri) {
        // *-*-* Уникальные запросы сайта необходимо вставлять вот сюда: *-*-*
        $apiUri = 'api/v1.0/';
        SimpleRouter::group(['middleware' => \Middlewares\ApiUserAuthMiddleware::class], function () use ($langUri, $apiUri) {
            // current_timer_value
            SimpleRouter::all("/{$langUri}{$apiUri}current_timer_value", function () use ($langUri, $apiUri) {
                TryIncludeCom("{$langUri}{$apiUri}current_timer_value", [], "{$apiUri}404");
            })->where(['end_uri' => '.*?']);

            // users
            SimpleRouter::all("/{$langUri}{$apiUri}users", function () use ($langUri, $apiUri) {
                TryIncludeCom("{$langUri}{$apiUri}users", [], "{$apiUri}404");
            })->where(['end_uri' => '.*?']);
            // users/{id}
            SimpleRouter::all("/{$langUri}{$apiUri}users/{id}", function ($id) use ($langUri, $apiUri) {
                TryIncludeCom("{$langUri}{$apiUri}users/user", ['id' => $id], "{$apiUri}404");
            })->where(['id' => '[0-9]+']);
            // users/{id}/timer
            SimpleRouter::all("/{$langUri}{$apiUri}users/{id}/timer", function ($id) use ($langUri, $apiUri) {
                TryIncludeCom("{$langUri}{$apiUri}users/user/timer", ['id' => $id], "{$apiUri}404");
            })->where(['id' => '[0-9]+']);
            // users/*
            SimpleRouter::all("/{$langUri}{$apiUri}users{end_uri}", function ($end_uri) use ($langUri, $apiUri) {
                $end_uri = FileSys::filenameSecurity($end_uri);
                $end_uri = empty($end_uri) ? '' : "/{$end_uri}";
                TryIncludeCom("{$langUri}{$apiUri}users{$end_uri}", [], "{$apiUri}404");
            })->where(['end_uri' => '.*?']);
        });
        SimpleRouter::all("/{$langUri}{$apiUri}{end_uri}", function ($end_uri) use ($langUri, $apiUri) {
            $end_uri = FileSys::filenameSecurity($end_uri);
            TryIncludeCom("{$langUri}{$apiUri}{$end_uri}", [], "{$apiUri}404");
        })->where(['end_uri' => '.*?']);

        SimpleRouter::get("/{$langUri}test_articles/{id}.html", function ($id) use ($langUri) {
            TryIncludeCom("{$langUri}test_articles", ['id' => $id]);
        })->where(['id' => '[0-9]+']);
        // *-*-* Конец уникальных запросов сайта *-*-*

        // Стандартный тип подключения микрона (т.е. contacts/about_us -> IncludeCom('contacts/about_us'))
        SimpleRouter::all("/{$langUri}{end_uri}", function ($end_uri) use ($langUri) {
            $end_uri = FileSys::filenameSecurity($end_uri);
            if (in_array($end_uri, ['index.php', 'index.html', 'index.htm'])) {
                IncludeCom($langUri . Config('defaultComponent'));
                return;
            }
            TryIncludeCom("{$langUri}{$end_uri}");
        })->where(['end_uri' => '.*?']);
        SimpleRouter::all("/{$langUri}", function () use ($langUri) {
            TryIncludeCom($langUri . Config('defaultComponent'));
        });
    });
}