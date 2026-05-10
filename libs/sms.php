<?php
function SendSMSCurl($request,$params,$result = false){
// Generate curl request
    $session = curl_init($request);

// Tell curl to use HTTP POST
    curl_setopt ($session, CURLOPT_POST, true);

// Tell curl that this is the body of the POST
    curl_setopt ($session, CURLOPT_POSTFIELDS, $params);

// Tell curl not to return headers, but do return the response
    curl_setopt($session, CURLOPT_HEADER, false);
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

// obtain response
    $response = curl_exec($session);
    curl_close($session);

    if($result){
        return $response;
    }
}

// function SendSMS($mobile,$template,$token = "", $token2 = "" , $token3 = '')
// {
//     $token = str_replace(" ","‌",$token);
//     $token = str_replace(" ","‌",$token);
//     $token = str_replace(" ","‌",$token);
//     $token = str_replace(" ","‌",$token);
//     $token = str_replace(" ","‌",$token);

//     $token2 = str_replace(" ","‌",$token2);
//     $token2 = str_replace(" ","‌",$token2);
//     $token2 = str_replace(" ","‌",$token2);
//     $token2 = str_replace(" ","‌",$token2);
//     $token2 = str_replace(" ","‌",$token2);

//     $token3 = str_replace(" ","‌",$token3);
//     $token3 = str_replace(" ","‌",$token3);
//     $token3 = str_replace(" ","‌",$token3);
//     $token3 = str_replace(" ","‌",$token3);
//     $token3 = str_replace(" ","‌",$token3);

//     $apiCode = '32567151713953512B434C514C7366496B687356422B4E62486961797934424468686C4B6D4B74503338633D';
//     $request = 'https://api.kavenegar.com/v1/' . $apiCode . '/verify/lookup.json';
//     $params = array();
//     $params['receptor'] = $mobile;
//     $params['token'] = $token;
//     $params['token2'] = $token2;
//     $params['token3'] = $token3;
//     $params['template'] = $template;
//     $params['type'] = "sms";;
//     return SendSMSCurl($request, $params);
// }
function SendSMS($mobile, $template, $token = "", $token2 = "", $token3 = '')
{
    // Define a cleanup function to handle unwanted characters
    $cleanupToken = function ($token) {
        // Replace ZWNJ or spaces with a period
        $token = str_replace(["‌", " "], ".", $token);
        // Ensure only Persian letters, numbers, and periods remain
        return preg_replace('/[^آابپتثجچحخدذرزژسشصضطظعغفقکگلمنوهی0-9.]/u', '', $token);
    };

    // Clean up the tokens
    $token = $cleanupToken($token);
    $token2 = $cleanupToken($token2);
    $token3 = $cleanupToken($token3);

    // Kavenegar API details
    $apiCode = '32567151713953512B434C514C7366496B687356422B4E62486961797934424468686C4B6D4B74503338633D';
    $request = 'https://api.kavenegar.ir/v1/' . $apiCode . '/verify/lookup.json';

    // Prepare parameters
    $params = array();
    $params['receptor'] = $mobile;
    $params['token'] = $token;
    $params['token2'] = $token2;
    $params['token3'] = $token3;
    $params['template'] = $template;
    $params['type'] = "sms";

    // Send the SMS
    return SendSMSCurl($request, $params);
}




?>