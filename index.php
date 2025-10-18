<?php

if(!defined('ABSPATH')){exit;}

$context = Timber::context();
$context['posts'] = Timber::get_posts();
$templates = array( 'index.twig' );
if ( is_home() ) {
    array_unshift( $templates, 'front-page.twig', 'home.twig' );
}
Timber::render( $templates, $context, TIMBER_CACHE_TIME );
