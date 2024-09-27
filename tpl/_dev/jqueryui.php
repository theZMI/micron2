<head>
    <link rel="stylesheet" type="text/css" href="<?= Root('i/css/_dev/jqueryui/jquery-ui.css') ?>"/>
    <link rel="stylesheet" type="text/css" href="<?= Root('i/css/_dev/jqueryui/jquery-ui.theme.css') ?>"/>
    <link rel="stylesheet" type="text/css" href="<?= Root('i/css/_dev/jqueryui/micron_theme.less') ?>"/>

    <script type="text/javascript" src="<?= Root('i/js/_dev/jqueryui/jquery-ui.js') ?>"></script>
    <script type="text/javascript" src="<?= Root('i/js/_dev/jqueryui/datepicker-ru.js') ?>"></script>
    <script type="text/javascript">
        $(function () {
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
