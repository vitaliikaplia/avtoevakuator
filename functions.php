<?php

if(!defined('ABSPATH')){exit;}

/** let's go! */
require_once 'core' . DIRECTORY_SEPARATOR . 'init.php';

///**
// * Lightweight Gutenberg Save Debug
// * mu-plugins/gutenberg-debug-light.php
// */
//
//// Тільки для POST/PUT до pages
//add_action('rest_api_init', function () {
//    $method = $_SERVER['REQUEST_METHOD'] ?? '';
//    $uri = $_SERVER['REQUEST_URI'] ?? '';
//
//    if (($method === 'POST' || $method === 'PUT') && strpos($uri, '/wp-json/wp/v2/pages') !== false) {
//
//        $log_file = WP_CONTENT_DIR . '/gutenberg-debug.log';
//
//        // Очистити старий лог
//        file_put_contents($log_file, date('Y-m-d H:i:s') . " - Debug started\n");
//
//        // Логувати тільки критичні хуки
//        $critical_hooks = [
//            'wp_insert_post_data',
//            '_wp_put_post_revision',
//            'wp_save_post_revision',
//            'wpml_save_post',
//            'acf/save_post'
//        ];
//
//        foreach ($critical_hooks as $hook) {
//            add_filter($hook, function ($data) use ($hook, $log_file) {
//                $msg = $hook;
//
//                if (is_array($data) && isset($data['post_type'])) {
//                    $msg .= ' | Type: ' . $data['post_type'];
//                    if ($data['post_type'] === 'revision') {
//                        $msg .= ' ⚠️ REVISION!';
//                    }
//                }
//
//                $msg .= ' | Memory: ' . round(memory_get_usage() / 1024 / 1024, 2) . 'MB';
//
//                file_put_contents($log_file, date('H:i:s') . " - $msg\n", FILE_APPEND);
//
//                return $data;
//            }, 1);
//        }
//
//        // Ловити fatal errors
//        register_shutdown_function(function () use ($log_file) {
//            $error = error_get_last();
//            if ($error && $error['type'] === E_ERROR) {
//                file_put_contents($log_file, "FATAL: " . $error['message'] . "\n", FILE_APPEND);
//            }
//        });
//    }
//});
//
//add_filter('wp_insert_post_data', function($data, $postarr) {
//    if (defined('REST_REQUEST') && REST_REQUEST) {
//        if ($data['post_type'] === 'revision') {
//            // Підміняємо тип, щоб ревізія не створилась
//            $data['post_type'] = 'page';
//            $data['post_status'] = 'draft_never_save';
//            return $data;
//        }
//    }
//    return $data;
//}, 1, 2);
//
//add_filter('wp_insert_post_empty_content', function($maybe_empty, $postarr) {
//    if (isset($postarr['post_status']) && $postarr['post_status'] === 'draft_never_save') {
//        return true; // Блокує збереження
//    }
//    return $maybe_empty;
//}, 1, 2);
//
//
///**
// * Deep debug for _wp_put_post_revision
// * mu-plugins/revision-debug.php
// */
//
//add_action('rest_api_init', function () {
//    $method = $_SERVER['REQUEST_METHOD'] ?? '';
//    $uri = $_SERVER['REQUEST_URI'] ?? '';
//
//    if (($method === 'POST' || $method === 'PUT') && strpos($uri, '/wp-json/wp/v2/pages') !== false) {
//
//        $log_file = WP_CONTENT_DIR . '/revision-debug.log';
//        file_put_contents($log_file, date('Y-m-d H:i:s') . " - Start\n");
//
//        // Перехоплюємо _wp_put_post_revision
//        add_action('_wp_put_post_revision', function ($revision_id) use ($log_file) {
//            $msg = "_wp_put_post_revision called:\n";
//            $msg .= "  Revision ID: $revision_id\n";
//
//            if ($revision_id) {
//                $revision = get_post($revision_id);
//                if ($revision) {
//                    $msg .= "  Type: {$revision->post_type}\n";
//                    $msg .= "  Parent: {$revision->post_parent}\n";
//                    $msg .= "  Status: {$revision->post_status}\n";
//                }
//            }
//
//            // Дивимося які хуки зареєстровані
//            global $wp_filter;
//            if (isset($wp_filter['_wp_put_post_revision'])) {
//                $msg .= "  Registered hooks:\n";
//                foreach ($wp_filter['_wp_put_post_revision'] as $priority => $hooks) {
//                    foreach ($hooks as $hook_key => $hook) {
//                        $msg .= "    [$priority] $hook_key\n";
//                    }
//                }
//            }
//
//            // Стек викликів
//            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
//            $msg .= "  Call stack:\n";
//            foreach ($backtrace as $i => $trace) {
//                $func = $trace['function'] ?? 'unknown';
//                $file = isset($trace['file']) ? basename($trace['file']) : 'unknown';
//                $msg .= "    $i: $func in $file\n";
//            }
//
//            file_put_contents($log_file, $msg . "\n", FILE_APPEND);
//
//            // Блокуємо виконання інших хуків
//            remove_all_actions('_wp_put_post_revision');
//
//        }, -9999); // Максимально ранній пріоритет
//    }
//});
