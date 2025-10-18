/**
 * JS inside blocks
 */
if( window.acf ) {

    window.acf.addAction( 'render_block_preview', function( elem, blockDetails ) {

        // if(blockDetails.name == 'acf/main-columns'){
        //     thumbnails_gallery(elem.find('.galleryWrapper'));
        // }

        elem.find('a,button').click(function(){
            return false;
        });

    } );
}
