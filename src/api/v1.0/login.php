<?php

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
    $login = $loginOrName[0];
    if (count($searchBy3) === 1) {
        $login = $searchBy3[0]->login;
    } elseif (count($searchBy2) === 1) {
        $login = $searchBy2[0]->login;
    } elseif (count($searchBy1) === 1) {
        $login = $searchBy1[0]->login;
    }

    return $login;
};

// Обычная авторизация по логину и паролю
$user_id       = (new UserModel())->getIdByLogin( $tryGetLoginByManyWays() );
$userModel     = new UserModel($user_id);
$password_hash = UserModel::makeHash(Post('password'));
$isCorrectAuth = $user_id && $userModel->pwd_hash === $password_hash;
$response      = $isCorrectAuth ? $password_hash : '';
$apiResponse   = new ApiResponse();

if ($isCorrectAuth) {
    $apiResponse->normal(
        array_merge($userModel->getData(), ['token' => $password_hash])
    );
    return;
}
$apiResponse->error('Неверный логин или пароль');