<head>
    <link rel="stylesheet" type="text/css" href="<?= Root('i/css/_dev/jqueryui/jquery-ui.css') ?>"/>
    <link rel="stylesheet" type="text/css" href="<?= Root('i/css/_dev/jqueryui/micron_theme.less') ?>"/>

    <script type="text/javascript" src="<?= Root('i/js/_dev/jqueryui/jquery-ui.js') ?>"></script>
    <script type="text/javascript" src="<?= Root('i/js/_dev/jqueryui/datepicker-ru.js') ?>"></script>
    <script type="text/javascript">
        $(function () {
            // Bootstrap 5 при загрузке страницы заменяет $.fn.button своим плагином - возвращаем кнопки jQuery UI (на них крестик и кнопки диалогов),
            // бутстраповский плагин остаётся доступен как $.fn.bsButton
            if ($.fn.button && $.fn.button.noConflict) {
                $.fn.bsButton = $.fn.button.noConflict();
            }

            $(".datepicker").datepicker({
                dateFormat: "dd-mm-yy",
                regional: "ru"
            });

            $(".datepicker-with-min-date").datepicker({
                dateFormat: "dd-mm-yy",
                regional: "ru",
                minDate: 0
            });
        });
    </script>
</head>
