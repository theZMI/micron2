"use strict";
$(function () {
    $(document).on('click', '.pwd-shower', function () {
        const toggleButton = $(this);
        const input = $(toggleButton.data('field'));
        const buttonIcon = toggleButton.find('span');
        const showPwdIconClass = toggleButton.data('class-show');
        const hidePwdIconClass = toggleButton.data('class-hide');
        const isPwdShowClass = 'pwd-shower-show';
        const isPasswordVisible = buttonIcon.hasClass(isPwdShowClass);
        if (isPasswordVisible) {
            buttonIcon
                .removeClass(isPwdShowClass)
                .removeClass(showPwdIconClass)
                .addClass(hidePwdIconClass);
            input.attr('type', 'password');
        }
        else {
            buttonIcon
                .addClass(isPwdShowClass)
                .addClass(showPwdIconClass)
                .removeClass(hidePwdIconClass);
            input.attr('type', 'text');
        }
    });
});
//# sourceMappingURL=pwdShower.js.map