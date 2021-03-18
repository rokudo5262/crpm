<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Send message to telegram bot
function send_message_telegram($msg = '')
{
    
    $CI = get_instance();
    
    $CI->load->model('telegram_model');
    $currentUserID = $GLOBALS['current_user']->staffid;
    $userTelegramInfo = $CI->telegram_model->get($currentUserID);
    if (isset($userTelegramInfo) && $userTelegramInfo->id && $userTelegramInfo->chat_id && $userTelegramInfo->bot_token) {
        $curl = curl_init();
     
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.telegram.org/bot".$userTelegramInfo->bot_token."/sendmessage?chat_id=".$userTelegramInfo->chat_id."&text=".$msg."&parse_mode=html",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
          ));
          $response = curl_exec($curl);
          curl_close($curl);
         
    }

}




