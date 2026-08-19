define(['jquery', 'theme_academi/jquery.sudoSlider'], function($) {
    var defaults = {
        autoplay: false,
        interval: 500,
    };
    var Carousel = function(selector, options) {
        var results = $.extend(defaults, options);
        this.initializeslider(selector, results);
    };

    // Initialize the slider.
    Carousel.prototype.initializeslider = function(selector, data) {
        var autostopped = false;
        var sudoSlider = $(selector).sudoSlider({
            prevNext: true,
            prevHtml: '.homepage-carousel .prevBtn.carousel-control',
            nextHtml: '.homepage-carousel .nextBtn.carousel-control',
            speed: 1400,
            ease: 'swing',
            responsive: true,
            updateBefore: true,
            useCSS: true,
            interruptible: false,
            numeric: true,
            pause: (data.autoplay == 'false') ? false : data.interval,
            auto: (data.autoplay == 'true') ? true : false,
            customLink: ".homepage-carouselLink",
            afterAnimation: function(t) {
                $('.homecarousel-slide-item.carousel-item').not('[data-slide="' + t + '"]').removeClass('active');
                $('.homecarousel-slide-item.carousel-item[data-slide="' + t + '"]').addClass('active');
                $('.slide-text').show();
            },
            beforeAnimation: function() {
                animation();
            }
        });

        // Fade in the first slide's text on page load too - beforeAnimation
        // only fires on slide changes, so without this the initial slide's
        // heading stays at opacity:0 until the carousel moves at least once.
        animation();

        sudoSlider.mouseenter(function() {
            var auto = sudoSlider.getValue('autoAnimation');
            if (auto) {
                sudoSlider.stopAuto();
            } else {
                autostopped = true;
            }
        }).mouseleave(function() {
            if (!autostopped) {
                sudoSlider.startAuto();
            }
        });

        /**
         * Animation for slider.
         *
         * Fades the whole heading block (title, text, button) in together on
         * a single CSS transition instead of staggering each element with a
         * setInterval, which is what caused the text to appear piece by
         * piece and feel jerky.
         */
        function animation() {
            var $content = $('.slide-content .slide-text .heading-content [data-animation ^= "animated"]');
            if ($content.length) {
                $content.removeClass('slide-content-visible');
                // Force reflow so the browser registers the opacity:0 state
                // before we transition back to opacity:1.
                void $content[0].offsetWidth;
                $content.addClass('slide-content-visible');
            }
        }
    };

    return {
        init: function(selector, options) {
            return new Carousel(selector, options);
        }
    };
});