$(() => {
    $('.more-info').on('click', function() {
        const btn = this;
        $(btn).css(
            'transform',
            $(btn).css('transform') === 'none' ? 'rotate(180deg)' : ''
        );
        $(btn).parent().next('.more-info').toggle();
    })
});