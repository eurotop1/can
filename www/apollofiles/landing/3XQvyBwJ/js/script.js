$('.navbar-collapse > .navbar-nav').on('click', 'span', function () {
    var id = $(this).attr('data-href'),
        top = $(id).offset().top;
    $('body, html').animate({
        scrollTop: top
    }, 800);
});

$('.to-form').click(function() {
    $('html, body').animate({ scrollTop: $('#form-block').offset().top }, 800);
});