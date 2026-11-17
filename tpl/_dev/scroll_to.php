<?php
/**
 * Плавная прокрутка страницы или блока (плагин jQuery.ScrollTo).
 * Подключается там, где нужна: IncludeCom('_dev/scroll_to')
 *
 * В коде: $.scrollTo('#id', 500, {offset: -70}) - окно, $('.box').scrollTo('.item', 300) - блок с прокруткой.
 * Без JS: <a href="#id" data-scroll-to> или <button data-scroll-to="#id" data-scroll-offset="-70">
 */
?>
<head>
    <script type="text/javascript" src="<?= Root('i/js/_dev/jquery.scrollTo.js') ?>"></script>
    <script type="text/javascript">
        $(function () {
            // Ссылки и кнопки с data-scroll-to: цель берём из атрибута или из href
            $(document).on('click', '[data-scroll-to]', function (e) {
                const target = $(this).attr('data-scroll-to') || $(this).attr('href');
                if (!target || !$(target).length) {
                    return;
                }
                e.preventDefault();
                $.scrollTo(target, parseInt($(this).attr('data-scroll-duration') || 500), {
                    offset: parseInt($(this).attr('data-scroll-offset') || 0)
                });
            });
        });
    </script>
</head>
