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

// Можно ли открыть компонент по url-у напрямую. Компоненты, у которых часть пути начинается с "_" (_main_tpl, _dev/..., _cron/...), - служебные
// и подключаются только из кода или через явные маршруты ниже. Примеры (examples/*) доступны только в DEBUG_MODE
$isPublicComponent = function (string $uri): bool {
    foreach (explode('/', $uri) as $part) {
        if (str_starts_with($part, '_')) {
            return false;
        }
    }
    return Env('DEBUG_MODE') || !str_starts_with($uri, 'examples/');
};
$includePublicCom  = function (string $langUri, string $uri, string $component404 = '404') use ($isPublicComponent) {
    $uri = FileSys::filenameSecurity($uri);
    $isPublicComponent($uri) ? TryIncludeCom("{$langUri}{$uri}", [], $component404) : IncludeCom($component404);
};

// Админка
SimpleRouter::group(['middleware' => [
    AdminAuthMiddleware::class
]], function () {
    SimpleRouter::all('/_admin/{end_uri}', function ($end_uri) {
        $end_uri = FileSys::filenameSecurity($end_uri);
        TryIncludeCom("_admin/{$end_uri}");
    })->where(['end_uri' => '.*?']);
});
SimpleRouter::all('/_admin', fn() => UrlRedirect::go(SiteRoot('_admin/login')));

// Задачи по расписанию: доступ по CRON_SECRET или для админа
SimpleRouter::group(['middleware' => [
    CronAuthMiddleware::class
]], function () {
    SimpleRouter::all('/_cron/{end_uri}', function ($end_uri) {
        $end_uri = FileSys::filenameSecurity($end_uri);
        TryIncludeCom("_cron/{$end_uri}");
    })->where(['end_uri' => '.*?']);
});

// Webhook телеграм-бота
SimpleRouter::group(['middleware' => [
    TelegramWebhookMiddleware::class
]], function () {
    SimpleRouter::post('/_telegram_bot_hook', fn() => IncludeCom('_telegram_bot_hook'));
});

// Обрезка загруженных картинок (только для авторизованных)
SimpleRouter::group(['middleware' => [
    CommonMiddleware::class
]], function () {
    SimpleRouter::all('/_crop_image', fn() => (IS_ADMIN_AUTH || IS_USER_AUTH) ? IncludeCom('_crop_image') : IncludeCom('403'));
});

foreach ($getLangs() as $lang => $langUri) {
    // Вызовы API с авторизацией (по заголовку API-User-Auth-Token, пользователь будет занесён в g_user)
    SimpleRouter::group(['middleware' => [
        PrepareApiMiddleware::class,
        CommonMiddleware::class,
        CheckApiUserAuthMiddleware::class]
    ], function () use ($langUri, $apiUri) {
        // users/{id}
        SimpleRouter::all("/{$langUri}{$apiUri}users/{id}", function ($id) use ($langUri, $apiUri) {
            TryIncludeCom("{$langUri}{$apiUri}users/one", ['id' => $id], "{$apiUri}404");
        })->where(['id' => '-?[0-9]+?']);
        // users/*
        SimpleRouter::all("/{$langUri}{$apiUri}users{end_uri}", function ($end_uri) use ($langUri, $apiUri) {
            $end_uri = FileSys::filenameSecurity($end_uri);
            $end_uri = empty($end_uri) ? '' : "/{$end_uri}";
            TryIncludeCom("{$langUri}{$apiUri}users{$end_uri}", [], "{$apiUri}404");
        })->where(['end_uri' => '.*?']);
    });
    // Вызовы API без авторизации
    SimpleRouter::group([
        'middleware' => [
            PrepareApiMiddleware::class,
            CommonMiddleware::class
        ]], function () use ($langUri, $apiUri, $includePublicCom) {
        SimpleRouter::all("/{$langUri}{$apiUri}{end_uri}", function ($end_uri) use ($langUri, $apiUri, $includePublicCom) {
            $includePublicCom($langUri, "{$apiUri}{$end_uri}", "{$apiUri}404");
        })->where(['end_uri' => '.*?']);
    });

    // Кабинет пользователя
    SimpleRouter::group(['middleware' => [
        PrepareUserMiddleware::class,
        CommonMiddleware::class,
        CheckUserAuthMiddleware::class
    ]], function () use ($langUri, $includePublicCom) {
        SimpleRouter::all("/{$langUri}user/{end_uri}", function ($end_uri) use ($langUri, $includePublicCom) {
            $includePublicCom($langUri, "user/{$end_uri}");
        })->where(['end_uri' => '.*?']);
    });

    // Стандартный тип подключения микрона (т.е. contacts/about_us -> IncludeCom('contacts/about_us'))
    SimpleRouter::group(['middleware' => [
        CommonMiddleware::class
    ]], function () use ($langUri, $includePublicCom) {
        SimpleRouter::all("/{$langUri}{end_uri}", function ($end_uri) use ($langUri, $includePublicCom) {
            $end_uri = FileSys::filenameSecurity($end_uri);
            if (in_array($end_uri, ['index.php', 'index.html', 'index.htm'])) {
                IncludeCom($langUri . Config('default_component'));
                return;
            }
            $includePublicCom($langUri, $end_uri);
        })->where(['end_uri' => '.*?']);

        // Запросы вида /ru, /en... должны вызывать главную
        SimpleRouter::all("/{$langUri}", fn() => TryIncludeCom($langUri . Config('default_component')));
    });
}
