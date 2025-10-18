<?php

if(!defined('ABSPATH')){exit;}

function add_picture_tag_to_images($content) {
    if (is_single()) {
        if (preg_match_all('/<img[^>]+>/', $content, $matches)) {
            foreach ($matches[0] as $img_tag) {
                preg_match('/src="([^"]+)"/', $img_tag, $src_match);
                $src_url = $src_match[1];

                preg_match('/wp-image-([0-9]+)/', $img_tag, $id_match);
                $image_id = $id_match[1] ?? null;

                if (!$image_id) {
                    $image_id = attachment_url_to_postid($src_url);
                }

                $webp_url = get_post_meta($image_id, 'webp_url', true);

                $picture_tag = '<picture>';
                if ($webp_url) {
                    $picture_tag .= '<source srcset="' . esc_url($webp_url) . '" type="image/webp">';
                }
                $picture_tag .= $img_tag;
                $picture_tag .= '</picture>';

                $content = str_replace($img_tag, $picture_tag, $content);
            }
        }
    }
    return $content;
}
add_filter('the_content', 'add_picture_tag_to_images', 99999);

if(get_option('remove_default_image_sizes')){
    function remove_default_image_sizes( $sizes) {
        unset( $sizes['large']);
        unset( $sizes['thumbnail']);
        unset( $sizes['medium']);
        unset( $sizes['medium_large']);
        unset( $sizes['1536x1536']);
        unset( $sizes['2048x2048']);
        return $sizes;
    }
    add_filter('intermediate_image_sizes_advanced', 'remove_default_image_sizes');
}

function resize_images_at_upload($image_data){

    if(get_option('enable_resize_at_upload') && in_array($image_data['type'], get_option('resize_at_upload_formats'))) {
        $max_width  = get_option('resize_upload_width');
        $max_height = get_option('resize_upload_height');
        $resize_quality = get_option('resize_upload_quality');
        $image_editor = wp_get_image_editor($image_data['file']);
        $image_editor->resize($max_width, $max_height, false);
        $image_editor->set_quality($resize_quality);
        $image_editor->save($image_data['file']);
    }

    return $image_data;

}
add_action('wp_handle_upload', 'resize_images_at_upload');

/** convert to webp */
function convert_to_webp($image_path, $destination_path, $quality) {
    $imagick = new \Imagick(realpath($image_path));
    $imagick->setImageFormat('webp');
    $imagick->setImageCompressionQuality($quality);
    $imagick->stripImage();
    $imagick->writeImage($destination_path);
    $imagick->clear();
    $imagick->destroy();
}

if(get_option('enable_webp_convert')) {

    /** convert action */
    function optimize_images_at_upload($image_data){
        if(in_array($image_data['type'], array(
            'image/gif',
            'image/png',
            'image/jpeg',
            'image/jpg'
        ))){
            $path_parts = pathinfo($image_data['file']);
            $webp_path = $path_parts['dirname'] . DS . $path_parts['filename'] . '-' . $path_parts['extension'] . '.webp';
            convert_to_webp($image_data['file'], $webp_path, intval(get_option('webp_convert_quality')));
        }
        return $image_data;
    }
    add_action('wp_handle_upload', 'optimize_images_at_upload');

    function add_custom_attachment_meta( $attachment_id ) {
        $attached_file = get_attached_file($attachment_id);
        $path_parts = pathinfo($attached_file);
        $webp_path = $path_parts['dirname'] . DS . $path_parts['filename'] . '-' . $path_parts['extension'] . '.webp';
        $origin_url = wp_get_attachment_url($attachment_id);
        $url_info = pathinfo($origin_url);
        $webp_url = $url_info['dirname'] . '/' . $url_info['filename'] . '-' . $url_info['extension'] . '.webp';
        if (file_exists($webp_path)) {
            add_post_meta( $attachment_id, 'webp_path', $webp_path, true );
            add_post_meta( $attachment_id, 'webp_url', $webp_url, true );
        }
    }
    add_action( 'add_attachment', 'add_custom_attachment_meta', 100000 );

    /** delete webp on original file deletion */
    function delete_webp_shadow_image($post_id) {
        $webp_path = get_post_meta($post_id, 'webp_path', true);
        if (file_exists($webp_path)) {
            unlink($webp_path);
        }
    }
    add_action('delete_attachment', 'delete_webp_shadow_image');

}

/** convert old images */
//if(is_admin() && isset($_GET['CONVERT_OLD_IMAGES']) && $_GET['CONVERT_OLD_IMAGES'] && isset($_GET['PER_IMAGE']) && $_GET['PER_IMAGE']){
//
//    $args = array(
//        'post_type' => 'attachment',
//        'post_mime_type' => array('image/jpeg', 'image/jpg', 'image/png'),
//        'posts_per_page' => intval(stripslashes($_GET['PER_IMAGE'])),
//        'meta_query' => array(
//            'relation' => 'AND',
//            array(
//                'key' => 'webp_path',
//                'compare' => 'NOT EXISTS',
//            ),
//            array(
//                'key' => 'webp_url',
//                'compare' => 'NOT EXISTS',
//            ),
//        ),
//    );
//
//    $attachments = get_posts($args);
//
//    foreach ($attachments as $attachment) {
//        $file = get_attached_file($attachment->ID);
//        $path_parts = pathinfo($file);
//        $webp_path = $path_parts['dirname'] . DS . $path_parts['filename'] . '-' . $path_parts['extension'] . '.webp';
//        if (!file_exists($webp_path)) {
//            convert_to_webp($file, $webp_path, 88);
//        }
//        $origin_url = wp_get_attachment_url($attachment->ID);
//        $url_info = pathinfo($origin_url);
//        $webp_url = $url_info['dirname'] . '/' . $url_info['filename'] . '-' . $url_info['extension'] . '.webp';
//        add_post_meta( $attachment->ID, 'webp_path', $webp_path, true );
//        add_post_meta( $attachment->ID, 'webp_url', $webp_url, true );
//    }
//
//    $query_with_webp = new WP_Query(array(
//        'post_type'  => 'attachment',
//        'post_mime_type' => array('image/jpeg', 'image/jpg', 'image/png'),
//        'post_status'    => 'inherit',
//        'meta_query' => array(
//            array(
//                'key'     => 'webp_url',
//                'compare' => 'EXISTS',
//            ),
//        ),
//        'posts_per_page' => -1,
//    ));
//    $total_with_webp = $query_with_webp->found_posts;
//
//    $query_without_webp = new WP_Query(array(
//        'post_type'  => 'attachment',
//        'post_mime_type' => array('image/jpeg', 'image/jpg', 'image/png'),
//        'post_status'    => 'inherit',
//        'meta_query' => array(
//            array(
//                'key'     => 'webp_url',
//                'compare' => 'NOT EXISTS',
//            ),
//        ),
//        'posts_per_page' => -1,
//    ));
//    $total_without_webp = $query_without_webp->found_posts;
//
//    // pr('Images with webp_url: ' . $total_with_webp . ', ' . 'Images without webp_url: ' . $total_without_webp);
//
//}
//
///** flush webp data */
//if(is_admin() && isset($_GET['flush_webp_data']) && $_GET['flush_webp_data']){
//    $args = array(
//        'post_type' => 'attachment',
//        'post_mime_type' => array('image/jpeg', 'image/jpg', 'image/png', 'image/gif'),
//        'posts_per_page' => -1
//    );
//    $attachments = get_posts($args);
//    $c = 0;
//    foreach ($attachments as $attachment) {
//        $file = get_attached_file($attachment->ID);
//        $path_parts = pathinfo($file);
//        $webp_path = $path_parts['dirname'] . DS . $path_parts['filename'] . '-' . $path_parts['extension'] . '.webp';
//        if(file_exists($webp_path)){
//            unlink($webp_path);
//            $c++;
//        }
//        delete_post_meta( $attachment->ID, 'webp_path' );
//        delete_post_meta( $attachment->ID, 'webp_url' );
//    }
//    // pr('WebP flushed:' . $c);
//}
