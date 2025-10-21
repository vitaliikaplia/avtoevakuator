<?php

if(!defined('ABSPATH')){exit;}

function telegram_bot($message){

    if( ($telegram_token = get_option('telegram_token')) && ($telegram_chat_id = get_option('telegram_chat_id')) ){

        $message = str_replace(["<br>", "<br/>", "<p>"], "\n", $message);
        $message = str_replace("</p>", "", $message);
        $message = strip_tags($message);
        $message = str_replace("&amp;", "&", $message);
        $message = trim($message,"\n");

        $url = "https://api.telegram.org/bot" . $telegram_token . "/sendMessage?chat_id=" . $telegram_chat_id;
        $url = $url . "&text=" . urlencode($message);
        $ch = curl_init();
        $optArray = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $optArray);
        $result = curl_exec($ch);
        //write_log($result);
        curl_close($ch);

    }

}

// test telegram bot
add_action( 'init', function(){
    if(is_admin() && isset($_GET['test_telegram_bot']) && $_GET['test_telegram_bot']=='ok' && current_user_can('manage_options')){
        telegram_bot("This is a test message from your website: " . get_bloginfo('name') . " (" . get_bloginfo('url') . ")");
    }
});
