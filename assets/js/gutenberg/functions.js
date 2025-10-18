// function thumbnails_gallery(el){
//
//     let frameSingle,
//         thumbnailsSingle;
//
//     if(el.find('.thumbnails')){
//         thumbnailsSingle = new Swiper(el.find('.thumbnails')[0], {
//             loop: true,
//             spaceBetween: 10,
//             slidesPerView: 4,
//             freeMode: true,
//             watchSlidesProgress: true,
//             breakpoints: {
//                 480: {
//                     slidesPerView: 5,
//                 },
//                 640: {
//                     slidesPerView: 6,
//                 }
//             }
//         });
//     }
//
//     if(el.find('.frame')){
//         frameSingle = new Swiper(el.find('.frame')[0], {
//             loop: true,
//             spaceBetween: 10,
//             autoHeight: true,
//             thumbs: {
//                 swiper: thumbnailsSingle,
//             },
//         });
//     }
//
// }
