<?php

if(!defined('ABSPATH')){exit;}

/** disable application passwords */
add_filter('wp_is_application_passwords_available', '__return_false');
