function InitRatingStars() {
    var stars = $('#stars li');

    stars.off('mouseover').on('mouseover', function () {
        var onStar = parseInt($(this).data('value'), 10); // The star currently mouse on

        // Now highlight all the stars that's not after the current hovered star
        $(this).parent().children('li.star.selected').removeClass('selected').addClass('-selected');
        $(this).parent().children('li.star').each(function (e) {
            if (e < onStar) {
                $(this).addClass('hover');
            } else {
                $(this).removeClass('hover');
            }
        });

    }).on('mouseout', function () {
        $(this).parent().children('li.star.-selected').removeClass('-selected').addClass('selected');
        $(this).parent().children('li.star').removeClass('hover');
    });

    /* 2. Action to perform on click */
    stars.off('click').on('click', function () {
        var onStar = parseInt($(this).data('value'), 10); // The star currently selected
        var stars = $(this).parent().children('li.star');

        stars.removeClass('-selected');
        stars.removeClass('selected');

        for (i = 0; i < onStar; i++) {
            $(stars[i]).addClass('selected');
        }

        // JUST RESPONSE (Not needed)
        var ratingValue = parseInt($('#stars li.selected').last().data('value'), 10);

        var userFunc = $('.rating-widget').data('callback-func');
        window[userFunc](ratingValue);
        return false;
    });
}
$( () => InitRatingStars() );