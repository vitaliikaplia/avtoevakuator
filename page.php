<?php

if(!defined('ABSPATH')){exit;}

$context = Timber::context();

$timber_post = Timber::get_post();
$context['post'] = $timber_post;

$context['custom_fields'] = cache_fields($context['post']->ID);

if (post_password_required($context['post']->ID)) {
    Timber::render('password.twig', $context);
} else {
    Timber::render(array('page-' . $context['post']->post_name . '.twig', 'page.twig'), $context, TIMBER_CACHE_TIME);
}
