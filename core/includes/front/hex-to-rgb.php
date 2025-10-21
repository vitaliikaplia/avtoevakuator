<?php

if(!defined('ABSPATH')){exit;}

function hex_to_rgb($hex) {

    $hex = ltrim($hex, '#');

    if (strlen($hex) === 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }

// return as array
//    return [
//        'r' => hexdec(substr($hex, 0, 2)),
//        'g' => hexdec(substr($hex, 2, 2)),
//        'b' => hexdec(substr($hex, 4, 2))
//    ];

    // return as string
    return hexdec(substr($hex, 0, 2)) . ',' . hexdec(substr($hex, 2, 2)) . ',' . hexdec(substr($hex, 4, 2));

}
