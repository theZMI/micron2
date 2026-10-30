<?php

// Генерирует и сохраняет одноразовый код подтверждения для пользователя (см. UserPwdRecoverModel)
function GeneratePinCode(CommonUserModel $userModel): string
{
    $code_id = (new UserPwdRecoverModel())->generate($userModel->id);
    return (new UserPwdRecoverModel($code_id))->code;
}

function CheckPinCode(CommonUserModel $userModel, $userCode): bool
{
    return (new UserPwdRecoverModel())->isValidCode($userCode, $userModel->email);
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
    return UserPwdRecoverModel::CODE_LENGTH;
}
