<head>
    <script type='text/javascript' src='<?= Root('i/js/_dev/jquery.liveFilter.js') ?>'></script>
    <script type='text/javascript'>
        function InitLiveFilter() {
            $('.live-filter-data-container').liveFilter(
                '.live-filter-input',
                '.live-filter-data-block',
                {
                    filterChildSelector: '.live-filter-data-block-filterinfo',
                    after: function () {
                        const emptyHtml = $('.live-filter-data-if-empty').html();
                        const isEmpty = !$('.live-filter-data-block').is(":visible");
                        $('.live-filter-data-if-empty-instance').remove();
                        if (isEmpty) {
                            $('.live-filter-data-container').append(`<div class='live-filter-data-if-empty-instance'>${emptyHtml}</div>`);
                        }
                    }
                }
            );
        }

        $( () => InitLiveFilter() );
    </script>
</head>
