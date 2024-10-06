import $ from "jquery";
import IMask from "imask";

$(() => {
    $('.datepicker-mask').each(
        function () {
            return new IMask($(this).get(0), {
                mask: Date,  // enable date mask

                // other options are optional
                pattern: 'd-`m-`Y',  // Pattern mask with defined blocks, default is 'd{.}`m{.}`Y'
                // you can provide your own blocks definitions, default blocks for date mask are:
                blocks: {
                    d: {
                        mask: IMask.MaskedRange,
                        from: 1,
                        to: 31,
                        maxLength: 2,
                    },
                    m: {
                        mask: IMask.MaskedRange,
                        from: 1,
                        to: 12,
                        maxLength: 2,
                    },
                    Y: {
                        mask: IMask.MaskedRange,
                        from: 1900,
                        to: 9999,
                    }
                },

                // define date -> str convertion
                format: date => {
                    let day = date.getDate();
                    let month = date.getMonth() + 1;
                    const year = date.getFullYear();

                    if (day < 10) day = "0" + day;
                    if (month < 10) month = "0" + month;

                    return [day, month, year].join('-');
                },

                // define str -> date convertion
                parse: str => {
                    const date = str.split('-');
                    return new Date(date[2], date[1] - 1, date[0]);
                },

                // optional interval options
                // min: new Date(2000, 0, 1),  // defaults to `1900-01-01`
                // max: new Date(2030, 0, 1),  // defaults to `9999-01-01`

                autofix: true,  // defaults to `false`

                // pattern options can be set as well
                lazy: false,

                // and other common options
                overwrite: true  // defaults to `false`
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
                        placeholderChar: '_',
                        from: 0,
                        to: 23,
                        maxLength: 2
                    },
                    MM: {
                        mask: IMask.MaskedRange,
                        placeholderChar: '_',
                        from: 0,
                        to: 59,
                        maxLength: 2
                    }
                },
                lazy: false,
            }
            return new IMask($(this).get(0), maskOptions);
        }
    );

    $('.phone-mask').each(
        function () {
            return new IMask($(this).get(0), {
                mask: '+{0}(000)000-00-00',
                lazy: false
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
                    mask: '* 000 aa 00[0]',
                    prepare: function (str) {
                        return str.toUpperCase();
                    },
                    lazy: false
                }
            );
        }
    );
});