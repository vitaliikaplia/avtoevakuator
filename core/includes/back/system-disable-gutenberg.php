<?php

if(!defined('ABSPATH')){exit;}

/** disable gutenberg for blog archive and single pages by default */
add_filter('use_block_editor_for_post_type', 'disable_gutenberg_for_blog', 10, 2);
function disable_gutenberg_for_blog($current_status, $post_type){
    global $post;
    //if (isset($post->ID) && $post->ID == get_option('page_for_posts')) {
    //    return false;
    //}
    if (isset($post->ID) && get_post_type($post->ID) == 'post') {
        return false;
    }
    return $current_status;
}
