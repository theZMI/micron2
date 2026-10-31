<?php

$apiResponse           = new ApiResponse();
$tryGetLoginByManyWays = function() {
    // В поле loginOrName может содержаться фамилия, фамилия имя, фамилия имя отчество или сразу логин
    $loginOrName   = explode(' ', trim(strval(Post('login_or_name')))) ;
    $surname       = $loginOrName[0] ?? '';
    $first_name    = $loginOrName[1] ?? '';
    $patronymic    = $loginOrName[2] ?? '';

    // Производим поиск по каждому варианту (ФИО, ФИ, Ф)
    $m = new UserModel();
    $searchBy3     = $m->find(['surname' => $surname, 'first_name' => $first_name, 'patronymic' => $patronymic]);
    $searchBy2     = $m->find(['surname' => $surname, 'first_name' => $first_name]);
    $searchBy1     = $m->find(['surname' => $surname]);

    // Если где у нас по поискам однозначно определился пользователь, то мы нашли нужного юзера, иначе считаем что юзер в поле login_or_name ввёл логин
    $login = '';
    if (count($searchBy3) === 1) {
        $login = $searchBy3[0]->login;
    } elseif (count($searchBy2) === 1) {
        $login = $searchBy2[0]->login;
    } elseif (count($searchBy1) === 1) {
        $login = $searchBy1[0]->login;
    } else {
        $justLogin = $loginOrName[0];
        $login = $m->isLoginBusy($justLogin) ? $justLogin : '';
    }

    if (!$login) {
        switch (count($loginOrName)) {
            case 3:
                if (count($searchBy3) > 1) {
                    throw new RuntimeException("Больше одного подходящего пользователя под ваше ФИО. Обратитесь к администратору");
                }
                break;
            case 2:
                if (count($searchBy2) > 1) {
                    throw new RuntimeException("Больше одного подходящего пользователя под фамилию и имя. Попробуйте добавить отчество");
                }
                break;
            case 1:
                if (count($searchBy1) > 1) {
                    throw new RuntimeException("Больше одного подходящего пользователя под фамилию. Попробуйте добавить имя");
                }
                break;
            default:
                $login = Post('login_or_name');
        }
    }

    return $login;
};

// Защита от подбора пароля
$attempts   = new LoginAttemptModel();
$typedLogin = (string)Post('login_or_name');
if ($attempts->isBlocked('api', $typedLogin)) {
    $apiResponse->error('Слишком много неудачных попыток входа. Попробуйте через 15 минут', 429);
}

// Пытаемся получить user_id для авторизации человека
try {
    $user_id = (new UserModel())->getIdByLogin( $tryGetLoginByManyWays() );
} catch (\Throwable $exception) {
    $apiResponse->error($exception->getMessage());
}

$userModel = new UserModel($user_id);
if ($user_id && $userModel->verifyPassword((string)Post('password'))) {
    $attempts->clear('api', $typedLogin);
    $apiResponse->normal(
        array_merge($userModel->getPublicData(), ['token' => $userModel->issueApiToken()])
    );
    return;
}
$attempts->registerFail('api', $typedLogin);
$apiResponse->error('Неверный логин или пароль', 401);
