let g_isModalWndOpen = false;

$(function () {
    $(document).on(
        'click',
        '*[data-toggle="lightbox"]',
        function (event) {
            event.preventDefault();

            $(this).ekkoLightbox({
                alwaysShowClose: true,
                onShow: () => {
                    console.log('opened!');
                    g_isModalWndOpen = true;
                },
                onHide: () => {
                    console.log('closed');
                    g_isModalWndOpen = false;
                }
            });
        }
    );
});