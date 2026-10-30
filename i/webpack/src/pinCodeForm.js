import $ from "jquery";

export default {
    data() {
        return {
            COUNT_PIN_CODE_SYMBOLS: 6, // Берётся из data-length компонента _dev/pin_code_block

            canResendPinCode: false, // Если true, то будет показываться форма с кнопкой "запросить пин-код повторно"

            resendPinCodeTimerHandle: null, // Указатель на запущенную функцию таймера
            resendPinCodeTimer: 30, // Текущее значение таймера "запросить пин-код повторно можно через <...> секунд"
            resendPinCodeTimerLimit: 30, // Кол-во секунд через сколько можно запросить повторный пин-код (через это кол-во секунд появится форма запроса повторного пин-кода)
        };
    },
    async mounted() {
        if ($('.pin-code-block').length) {
            this.COUNT_PIN_CODE_SYMBOLS = +$('.pin-code-block').data('length') || this.COUNT_PIN_CODE_SYMBOLS;
            this.initPinCodeForm();
            this.startTimerRepeatPinCode();
            this.focusFirstPinCodeInput();
        }
    },
    methods: {
        initPinCodeForm() {
            $(document).on(
                'paste',
                '.pin-code-block-input',
                e => {
                    const text = e.originalEvent.clipboardData.getData('text').replace(/\D/g, '');
                    if (text.length < this.COUNT_PIN_CODE_SYMBOLS) {
                        return;
                    }
                    e.preventDefault();
                    for (let i = 1; i <= this.COUNT_PIN_CODE_SYMBOLS; i++) {
                        $(`.pin-code-block-input_${i}`)
                            .val(text[i - 1])
                            .trigger('input');
                    }
                }
            );

            const autoFormSubmit = () => {
                const symbols = [];
                for (let i = 1; i <= this.COUNT_PIN_CODE_SYMBOLS; i++) {
                    symbols.push( $(`.pin-code-block-input_${i}`).val() );
                }
                if (symbols.join('').length !== this.COUNT_PIN_CODE_SYMBOLS) {
                    return;
                }
                $('.pin-code-block-form').find('[type="submit"]').trigger('click');
            }

            $('.input-focus-sequence').each(function () {
                $(this).on(
                    'input',
                    () => {
                        let cur = $(this).val();
                        let curLen = cur.length;
                        let maxLen = +$(this).attr('maxlength');
                        if (curLen >= maxLen) {
                            let nextElement = $(this).data('next-focus');
                            $(nextElement).trigger('focus');
                        }

                        autoFormSubmit();
                    }
                );
            });
        },
        resendPinCode() {
            $('#resend-pin-code-form_submit-button').trigger('click');
        },
        startTimerRepeatPinCode() {
            clearInterval(this.resendPinCodeTimerHandle);
            this.resendPinCodeTimerHandle = setInterval(() => {
                this.canResendPinCode = false;
                this.resendPinCodeTimer--;

                if (this.resendPinCodeTimer <= 0) {
                    this.canResendPinCode = true;
                    this.resendPinCodeTimer = this.resendPinCodeTimerLimit;
                    clearInterval(this.resendPinCodeTimerHandle);
                }
            }, 1e3);
        },
        focusFirstPinCodeInput() { // Выделяем первое поле среди полей ввода пин-кода
            return $('.input-focus-sequence:first').trigger('focus');
        },
    },
}