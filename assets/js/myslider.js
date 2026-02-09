jQuery(document).ready(function ($) {
    var $slider = $(".myslider");
    $slider.slick({
        dots: true,
        infinite: true,
        speed: 600, /* Faster for response */
        slidesToShow: 1,
        autoplay: true,
        autoplaySpeed: 3000,
        arrows: false, /* Disable arrows */
        fade: false, /* Slide instead of Fade */
        cssEase: "ease-in-out", /* Smoother ease */
        touchThreshold: 20, /* Swipe faster */
        swipeToSlide: true, /* Allow dragging freely */
        waitForAnimate: false /* Allow clicking while animating */
    });

    // Stop autoplay on manual interaction
    $slider.on("swipe", function (event, slick, direction) {
        $slider.slick("slickPause");
    });

    // Pause on dot click
    $("body").on("click", ".slick-dots li button", function () {
        $slider.slick("slickPause");
    });

    // Pause on mouse drag start
    $slider.on("mousedown", function () {
        $slider.slick("slickPause");
    });
});
