function run_simple_swiper_slider(el){

    if(el.find('.background.background-slider').length){

        let swiperSimple = el.find('.background.background-slider'),
            autoplay = {};

        if(swiperSimple.attr('data-slider-timer')){
            autoplay = {
                delay: parseInt(swiperSimple.attr('data-slider-timer'))*1000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            };
        } else {
            autoplay = false;
        }

        if (swiperSimple[0].swiper) {
            swiperSimple[0].swiper.destroy(true, true); // Деініціалізація
        }

        let swiper = new Swiper(swiperSimple[0], {
            lazy: true,
            loop: true,
            spaceBetween: 0,
            autoHeight: true,
            slidesPerView: 1,
            autoplay,
            effect: 'fade',
            fadeEffect: {
                crossFade: false
            },
        });

        // if(swiperSimple.attr('data-slider-timer')){
        //     const observer = new IntersectionObserver(entries => {
        //         entries.forEach(entry => {
        //             if (entry.isIntersecting) {
        //                 swiper.autoplay.start();
        //             } else {
        //                 swiper.autoplay.stop();
        //             }
        //         });
        //     }, {threshold: 0.1});
        //     observer.observe(el.find('.swiper-simple')[0]);
        // }
    }
}

function run_reviews_swiper_slider(el){

    if(el.find('.reviews.swiper').length){

        el.find('.reviews.swiper .swiper-wrapper').append(el.find('.reviews.swiper .swiper-wrapper').html());

        let swiperSimple = el.find('.reviews.swiper'),
            autoplay = {};

        if(swiperSimple.attr('data-slider-timer')){
            autoplay = {
                delay: parseInt(swiperSimple.attr('data-slider-timer'))*1000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            };
        } else {
            autoplay = false;
        }

        if (swiperSimple[0].swiper) {
            swiperSimple[0].swiper.destroy(true, true); // Деініціалізація
        }

        let swiper = new Swiper(swiperSimple[0], {
            slidesPerView: "auto",
            centeredSlides: true,
            loop: true,
            spaceBetween: false,
            freeMode: false,
            watchSlidesProgress: true,
            speed: 600,
            autoplay,
        });

    }
}
