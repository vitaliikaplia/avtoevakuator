<?php

if(!defined('ABSPATH')){exit;}

add_action("init", function() {
    add_filter('upload_mimes', function ($mimes) {
        $mimes['json'] = 'application/json';
        return $mimes;
    });
});
