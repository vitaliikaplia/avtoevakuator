<?php

if(!defined('ABSPATH')){exit;}

$context = Timber::context();
$timber_post = Timber::get_post();
$context['post'] = $timber_post;

if ( post_password_required( $context['post']->ID ) ) {
    Timber::render( 'password.twig', $context );
} else {
    Timber::render( array( 'single-' . $context['post']->ID . '.twig', 'single-' . $context['post']->post_type . '.twig', 'single.twig' ), $context, TIMBER_CACHE_TIME );
}
