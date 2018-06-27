$(document).on('ready', function () {
    $('.one-time').slick({
        dots: false,
        infinite: true,
        speed: 300,
        slidesToShow: 1,
        adaptiveHeight: true,
        autoplay: true,
        autoplaySpeed: 2000,
        accessibility: false,
        arrows: false,
        pauseOnHover: true,
        pauseOnFocus: true,
    });

    var height = $('.slick-slide').width() * 9 / 16;
    $('.one-time .mimic-img-video').height(height);

    var slick_slide_width = $('.slick-slide').width();
    $('.blur-bg').width(slick_slide_width);
    
    if($(window).width() > 1200) {
        $('.height-100').height($(window).height() - $('header').outerHeight());
    }

});