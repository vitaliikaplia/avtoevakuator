<?php

if(!defined('ABSPATH')){exit;}

function contact_form_submit_action() {

    if(
        !empty($full_name = stripslashes($_POST['user_full_name']))
        &&
        !empty($phone = stripslashes($_POST['user_phone']))
    ){

        $return = array();

        if(!empty($_POST['user_email'])){
            $user_email = stripslashes($_POST['user_email']);
        } else {
            $user_email = '';
        }

        if(!empty($_POST['user_message'])){
            $user_message = stripslashes($_POST['user_message']);
        } else {
            $user_message = '';
        }

        $message  = __('New contact form message', TEXTDOMAIN).':' . "\n\n";
        $message .= __('Name', TEXTDOMAIN) . ": " . $full_name . "\n";
        $message .= __('Phone', TEXTDOMAIN) . ": " . $phone . "\n";
        if($user_email){
            $message .= __('Email', TEXTDOMAIN) . ": " . $user_email . "\n";
        }
        if($user_message){
            $message .= __('Message', TEXTDOMAIN) . ": " . $user_message . "\n";
        }

        telegram_bot($message);

        $return['title'] = __('Your message has been sent', TEXTDOMAIN);
        $return['message'] = __('Thank you for contacting us. We will get back to you shortly.', TEXTDOMAIN);

        wp_send_json_success( $return );

    }

    exit;
}
add_action( 'wp_ajax_contact_form_submit', 'contact_form_submit_action' );
add_action( 'wp_ajax_nopriv_contact_form_submit', 'contact_form_submit_action' );
