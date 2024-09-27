import $ from "jquery";
import IMask from "imask";

$(() => {
    $('.datepicker-mask').each(
        function () {
            return new IMask($(this).get(0), {
                mask: Date,
                // min: new Date(2024, 0, 1),
                // max: new Date(2034, 0, 1),
                overwrite: true,
                autofix: true,
                pattern: "d-m-Y",
                dateFormat: "d-m-Y"
            });
        }
    );

    $('.timepicker-mask').each(
        function () {
            let maskOptions = {
                overwrite: true,
                autofix: true,
                mask: 'HH:MM',
                blocks: {
                    HH: {
                        mask: IMask.MaskedRange,
                        placeholderChar: 'HH',
                        from: 0,
                        to: 23,
                        maxLength: 2
                    },
                    MM: {
                        mask: IMask.MaskedRange,
                        placeholderChar: 'MM',
                        from: 0,
                        to: 59,
                        maxLength: 2
                    }
                }
            }
            return new IMask($(this).get(0), maskOptions);
        }
    );

    $('.phone-mask').each(
        function () {
            return new IMask($(this).get(0), {
                mask: '+{0}(000)000-00-00'
            });
        }
    );

    $('.money-mask, .number-mask').each(
        function () {
            return new IMask(
                $(this).get(0),
                {
                    mask: Number,
                    thousandsSeparator: '',
                    radix: '.'
                }
            );
        }
    );

    $('.ru-car-gov-number').each(
        function () {
            return new IMask(
                $(this).get(0),
                {
                    mask: 'a 000 aa 00',
                    prepare: function (str) {
                        return str.toUpperCase();
                    },
                }
            );
        }
    );
});