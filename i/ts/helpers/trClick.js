export function trClick(td) {
    let href = $(td).closest('tr').find('.default-click:first');
    let hrefAlt = $(td).closest('tr').find('a:first');
    let gotoLink = href.length ? href.attr('href') : ( hrefAlt.length ? hrefAlt.attr('href') : '' );

    if (!gotoLink) {
        return;
    }

    location.href = gotoLink;
}