<head>
    <script type='text/javascript' src='<?= Root('i/js/_dev/jquery.autosize.input.js') ?>'></script>
    <script type="text/javascript">
        $(document).ready
        (
            function () {
                $('.input-autosize').autosizeInput
                (
                    {
                        minWidth: 60,
                        maxWidth: 225
                    }
                );
            }
        );
    </script>
</head>