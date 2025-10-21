<?php

if(!defined('ABSPATH')){exit;}

/** виправляємо деякі посилання в хлібних крихтах yoast */
//add_filter('wpseo_breadcrumb_links', 'custom_breadcrumb_links');
//function custom_breadcrumb_links($links) {
//    $catalog_page_id = get_option('catalog_page');
//    $catalog_page_slug = '/' . get_post_field('post_name', $catalog_page_id) . '/';
//    $gallery_page_id = get_option('gallery_page');
//    $gallery_page_slug = '/' . get_post_field('post_name', $gallery_page_id) . '/';
//    $designers_page_id = get_option('designers_page');
//    $designers_page_slug = '/' . get_post_field('post_name', $designers_page_id) . '/';
//    if (!empty($links)) {
//        foreach ($links as &$link) {
//            if (isset($link['url'])) {
//                $link['url'] = str_replace('/catalog-reserved-Wc29vWai/', $catalog_page_slug, $link['url']);
//                $link['url'] = str_replace('/gallery-reserved-Z0LKdjOV/', $gallery_page_slug, $link['url']);
//                $link['url'] = str_replace('/designers-reserved-IG9n0Svs/', $designers_page_slug, $link['url']);
//            }
//        }
//    }
//    return $links;
//}

/** виправляємо деякі заголовки в хлібних крихтах yoast */
add_filter('wpseo_breadcrumb_single_link', 'custom_breadcrumb_text', 10, 2);
function custom_breadcrumb_text($link_output, $link) {
    $link_output = str_replace('Error 404: Page not found', __('Error 404: Page not found', TEXTDOMAIN), $link_output);
    $link_output = str_replace('Archives for', __('Archives for', TEXTDOMAIN), $link_output);
    $link_output = str_replace('You searched for', __('You searched for', TEXTDOMAIN), $link_output);
    $link_output = str_replace('Page', __('Page', TEXTDOMAIN), $link_output);
    $link_output = str_replace('Home', __('Home', TEXTDOMAIN), $link_output);
    return $link_output;
}

/** виправляємо заголовок сторінки 404 в yoast */
add_filter('wpseo_title', function($title) {
    if (is_404()) {
        $general_fields = cache_general_fields();
        $site_name = wpseo_replace_vars('%%sitename%%', []);
        return __('Error 404: Page not found', TEXTDOMAIN) . ' - ' . $site_name;
    }
    return $title;
});

/** виправляємо заголовки сторінки пошуку в yoast */
//add_filter('wpseo_title', function($title) {
//    // Якщо це сторінка пошуку
//    if (is_search()) {
//        $search_query = get_search_query();
//        $site_name = wpseo_replace_vars('%%sitename%%', []);
//        return sprintf(__('Search results for: %s', TEXTDOMAIN), $search_query) . ' - ' . $site_name;
//    }
//    return $title;
//});

//add_filter('wpseo_opengraph_type', 'change_catalog_og_type', 10, 2);
//function change_catalog_og_type($type, $presentation) {
//    // Для одиночних сторінок типу "catalog"
//    if (is_singular('catalog')) {
//        return 'product.item';
//    }
//    if (is_singular('post')) {
//        return 'article';
//    }
//    if (is_singular('page') && !get_field('og_type_product_group')) {
//        return 'website';
//    }
//    // Для таксономій типу "catalog"
//    $catalog_taxonomies = [
//        'catalog_categories', 'product_type', 'appointments',
//        'recommendations', 'characteristics', 'colors',
//        'shapes', 'materials', 'cat_tag'
//    ];
//    foreach ($catalog_taxonomies as $tax) {
//        if (is_tax($tax)) {
//            return 'product.group';
//        }
//    }
//    if (is_singular('page') && get_field('og_type_product_group')) {
//        return 'product.group';
//    }
//    $queried_object = get_queried_object();
//    if(!empty($queried_object->ID)){
//        if ($queried_object->ID == PAGE_FOR_POSTS) {
//            return 'website';
//        }
//    }
//    return $type;
//}
