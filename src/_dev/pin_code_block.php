<?php

/**
 * Поля ввода одноразового кода (по одной цифре в поле, автопереход, вставка из буфера, автосабмит)
 *
 * Использование:
 *  - форма с классом pin-code-block-form, внутри IncludeCom('_dev/pin_code_block'); код на сервере - GetCodeFromForm()
 *  - Vue-приложение страницы с mixins: [window.pinCodeForm]
 *  - для кнопки "Отправить код повторно" - отдельная форма с кнопкой #resend-pin-code-form_submit-button
 */
$pinCodeLength = PinCodeLength();
$colWidth      = max(1, intdiv(12, $pinCodeLength));
