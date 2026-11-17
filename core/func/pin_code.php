<?php

// Генерирует и сохраняет одноразовый код подтверждения для пользователя (см. UserAuthCodeModel)
function GeneratePinCode(CommonUserModel $userModel): string
{
    $code_id = (new UserAuthCodeModel())->generate($userModel->id);
    return (new UserAuthCodeModel($code_id))->code;
}

function CheckPinCode(CommonUserModel $userModel, $userCode): bool
{
    return (new UserAuthCodeModel())->isValidCode($userModel->id, $userCode);
}

// Код из полей компонента _dev/pin_code_block (по одной цифре в каждом поле code[])
function GetCodeFromForm(): string
{
    $code = Post('code', [], M_XSS_FILTER_OFF);
    return is_array($code) ? implode('', array_map(fn($c) => preg_replace('/\D/', '', (string)$c), $code)) : '';
}

// Сколько цифр в коде
function PinCodeLength(): int
{
    return UserAuthCodeModel::CODE_LENGTH;
}
