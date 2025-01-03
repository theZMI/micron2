<?php

use Pecee\SimpleRouter\SimpleRouter;

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
SimpleRouter::group(['middleware' => AdminAuthMiddleware::class], function () {
    SimpleRouter::all('/_admin/{end_uri}', function ($end_uri) {
        $end_uri = FileSys::filenameSecurity($end_uri);
        TryIncludeCom("_admin/{$end_uri}");
    })->where(['end_uri' => '.*?']);
});

foreach ($getLangs() as $lang => $langUri) {
    // Вызовы API с авторизацией
    SimpleRouter::group(['middleware' => [PrepareApiMiddleware::class, CommonMiddleware::class, CheckApiUserAuthMiddleware::class]], function () use ($langUri, $apiUri) {
        // current_timer_value
        SimpleRouter::all("/{$langUri}{$apiUri}current_timer_value", function () use ($langUri, $apiUri) {
            TryIncludeCom("{$langUri}{$apiUri}current_timer_value", [], "{$apiUri}404");
        })->where(['end_uri' => '.*?']);

        // users
        SimpleRouter::all("/{$langUri}{$apiUri}users", function () use ($langUri, $apiUri) {
            TryIncludeCom("{$langUri}{$apiUri}users", [], "{$apiUri}404");
        });
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

        // shifts (юзер определиться по x-auth и будет занесён в g_user)
        SimpleRouter::all("/{$langUri}{$apiUri}shifts", function () use ($langUri, $apiUri) {
            TryIncludeCom("{$langUri}{$apiUri}shifts", [], "{$apiUri}404");
        });

        // tasks/{id}
        SimpleRouter::all("/{$langUri}{$apiUri}tasks/{id}", function ($id) use ($langUri, $apiUri) {
            TryIncludeCom("{$langUri}{$apiUri}tasks/task", ['id' => $id], "{$apiUri}404");
        })->where(['id' => '[0-9]+']);
    });

    // Вызовы API без авторизации
    SimpleRouter::group(['middleware' => [PrepareApiMiddleware::class, CommonMiddleware::class]], function () use ($langUri, $apiUri) {
        SimpleRouter::all("/{$langUri}{$apiUri}{end_uri}", function ($end_uri) use ($langUri, $apiUri) {
            $end_uri = FileSys::filenameSecurity($end_uri);
            TryIncludeCom("{$langUri}{$apiUri}{$end_uri}", [], "{$apiUri}404");
        })->where(['end_uri' => '.*?']);
    });

    // Кабинет пользователя
    SimpleRouter::group(['middleware' => [PrepareUserMiddleware::class, CommonMiddleware::class, CheckUserAuthMiddleware::class]], function () use ($langUri) {
        SimpleRouter::all("/{$langUri}user/{end_uri}", function ($end_uri) use ($langUri) {
            $end_uri = FileSys::filenameSecurity($end_uri);
            TryIncludeCom("/{$langUri}user/{$end_uri}");
        })->where(['end_uri' => '.*?']);
    });

    // Кабинет менеджера
    SimpleRouter::group(['middleware' => [PrepareManagerMiddleware::class, CommonMiddleware::class, CheckManagerAuthMiddleware::class]], function () use ($langUri) {
        SimpleRouter::all("/{$langUri}manager/{end_uri}", function ($end_uri) use ($langUri) {
            $end_uri = FileSys::filenameSecurity($end_uri);
            TryIncludeCom("/{$langUri}manager/{$end_uri}");
        })->where(['end_uri' => '.*?']);
    });

    // Стандартный тип подключения микрона (т.е. contacts/about_us -> IncludeCom('contacts/about_us'))
    SimpleRouter::group(['middleware' => CommonMiddleware::class], function () use ($langUri, $apiUri) {
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
