/**
 * JS inside blocks
 */
if( window.acf ) {

    window.acf.addAction( 'render_block_preview', function( elem, blockDetails ) {

        if(blockDetails.name == 'acf/main-first-screen'){
            run_simple_swiper_slider(elem);
        }
        if(blockDetails.name == 'acf/main-reviews'){
            run_reviews_swiper_slider(elem);
        }

        elem.find('a,button').click(function(){
            return false;
        });

    } );
}
