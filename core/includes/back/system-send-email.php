<?php

if(!defined('ABSPATH')){exit;}

function send_email($params = array()){
    if(!empty($params)){
        $no_reply_mail = "no-reply@".BLOGINFO_JUST_DOMAIN;
        $subject = stripslashes($params['subject']);
        $context = Timber::context();
        $context['TEXTDOMAIN'] = TEXTDOMAIN;
        $context['BLOGINFO_NAME'] = BLOGINFO_NAME;
        $context['BLOGINFO_URL'] = BLOGINFO_URL;
        $context['subject'] = $subject;
        $context['fields'] = $params['fields'];
        $mail_html = Timber::compile( 'email/'.$params['template'].'.twig', $context);
        $headers  = "Content-type: text/html; charset=utf-8 \r\n";
        $headers .= "From: ".BLOGINFO_NAME." <".$no_reply_mail.">\r\n";
        $response = wp_mail($params['email'], $subject, $mail_html, $headers);
        $mail_post = array(
            'post_type' => 'mail-log',
            'post_title' => $subject,
            'post_content' => $mail_html,
            'post_status' => 'publish'
        );
        $log_id = wp_insert_post( $mail_post );
        update_post_meta( $log_id, 'recipient', $params['email']);
    }
}

add_action( 'init', function(){
    if(is_admin() && isset($_GET['send_test_email']) && $_GET['send_test_email'] == '1' && current_user_can('manage_options')){
        send_email(array(
            'email' => 'vitalii.kaplia@gmail.com',
            'subject' => __('Test', TEXTDOMAIN),
            'template' => 'test',
            'fields' => array(
                'test' => 'Vitalii Kaplia',
                'session' => get_session_info(get_user_ip()),
            )
        ));
    }
});
