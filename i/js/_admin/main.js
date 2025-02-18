function trClick(td) {
    let href = $(td).closest('tr').find('.default-click:first');
    let hrefAlt = $(td).closest('tr').find('a:first');
    let gotoLink = href.length ? href.attr('href') : ( hrefAlt.length ? hrefAlt.attr('href') : '' );

    if (!gotoLink) {
        return;
    }

    location.href = gotoLink;
}

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