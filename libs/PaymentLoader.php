<?php

Function ZarinPalRequest($token,$price,$callback,$description,$mobile){
    $data = array(
        "merchant_id" => $token,
        "amount" => $price,
        "callback_url" => $callback,
        "description" => $description,
        "metadata" => [
            "email" => "",
            "mobile"=> $mobile
        ],
    );

    $jsonData = json_encode($data);
    $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/request.json');
    curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonData)
    ));

    $result = curl_exec($ch);
    $err = curl_error($ch);
    $result = json_decode($result, true, JSON_PRETTY_PRINT);
    curl_close($ch);


    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        if (empty($result['errors'])) {
            if ($result['data']['code'] == 100) {
                $url = 'https://www.zarinpal.com/pg/StartPay/'.$result['data']["authority"];
                return array("status" => true , "message" => "درحال انتقال به درگاه پرداخت" , "url" => $url , "authority" => $result['data']["authority"]);
            }
        } else {
            return array("status" => false, "message" => $result['errors']['message'] , "error_code" => $result['errors']['code']);
//            echo'Error Code: ' . $result['errors']['code'];
//            echo'message: ' .  $result['errors']['message'];
        }
    }

}
function ZarinPalVerify($token,$amount,$Authority){

    $data = array("merchant_id" => $token, "authority" => $Authority, "amount" => $amount);
    $jsonData = json_encode($data);
    $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/verify.json');
    curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonData)
    ));

    $result = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($result, true);
    if ($err) {
        return array("status" => false , "message" => $err);
    } else {
        if ($result['data']['code'] == 100) {
            return array("status" => true, "message" => result['data']['ref_id']);
        } else {
            return array("status" => false , "message" => $result['errors']['code']);
//            echo'code: ' . $result['errors']['code'];
//            echo'message: ' .  $result['errors']['message'];
        }
    }
}

function getNovinErrorMessage($code) {
    $map = [
        0   => 'عملیات موفق',
        1   => 'تراکنش ناموفق',
        2   => 'اطلاعات ورودی نامعتبر',
        3   => 'توکن نامعتبر/منقضی',
        100 => 'خطای داخلی سامانه',
    ];
    return isset($map[$code]) ? $map[$code] : 'خطا در ارتباط با درگاه (کد: '.$code.')';
}

/* REQUEST */
function NovinRequest($corporationPin, $amount, $callback, $description = '', $mobile = '') {
    // amount باید ریال باشد
    $amount = (int)$amount;

    // OrderId باید int64 باشد (فقط عدد). از timestamp میلی‌ثانیه‌ای استفاده می‌کنیم.
    $orderId = (int) floor(microtime(true) * 1000); // مثال: 1730740000000

    // طبق پیام خطا، بدنه باید داخل requestData باشد
//    $payload = [
//        "requestData" => [
//            "CorporationPin" => trim($corporationPin),
//            "Amount"        => $amount,
//            "OrderId"       => $orderId,
//            "CallBackUrl"   => $callback,
//            // فیلدهای اختیاری اگر API پشتیبانی کند؛ در صورت عدم نیاز حذف‌شان کنید:
//            // "Description" => $description,
//            // "Mobile"      => $mobile,
//        ]
//    ];

    $payload = array(
        "CorporationPin" => $corporationPin,
        "Amount" => $amount,
        "OrderId" => $orderId,
        "CallBackUrl" => $callback
    );

    $ch = curl_init('https://pna.shaparak.ir/mhipg/api/Payment/NormalSale');
    $jsonData = json_encode($payload, JSON_UNESCAPED_UNICODE);

    curl_setopt_array($ch, [
        CURLOPT_USERAGENT      => 'ENBank PNA REST v1',
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => $jsonData,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ],
    ]);

    $raw  = curl_exec($ch);
    $cerr = curl_error($ch);
    curl_close($ch);

    if ($cerr) {
        return ["status" => false, "message" => "cURL Error: ".$cerr, "error_code" => 0];
    }

    $res   = json_decode($raw, true);
    $token = $res['token'] ?? ($res['Token'] ?? null);

    if ($token) {
        $payUrl = 'https://pna.shaparak.ir/mhui/home/index/' . $token;
        return [
            "status"    => true,
            "message"   => "درحال انتقال به درگاه پرداخت نوین",
            "url"       => $payUrl,
            "authority" => $token,     // برای سازگاری
            "order_id"  => $orderId,
            "raw"       => $res,
        ];
    }

    $code = $res['code'] ?? ($res['result'] ?? -1);
    return [
        "status"     => false,
        "message"    => getNovinErrorMessage($code),
        "error_code" => $code,
        "raw"        => $res,
    ];
}

/* VERIFY */
function NovinVerify($corporationPin, $amount, $token) {
    // amount در بسیاری از درگاه‌ها برای verify نیاز نیست؛ اگر لازم نبود 0 بگذار
    $payload = [
            "CorporationPin" => trim($corporationPin),
            "Token"          => $token,
    ];

    $ch = curl_init('https://pna.shaparak.ir/mhipg/api/Payment/confirm');
    $jsonData = json_encode($payload, JSON_UNESCAPED_UNICODE);

    curl_setopt_array($ch, [
        CURLOPT_USERAGENT      => 'ENBank PNA REST v1',
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => $jsonData,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ],
    ]);

    $raw  = curl_exec($ch);
    $cerr = curl_error($ch);
    curl_close($ch);

    if ($cerr) {
        return ["status" => false, "message" => "cURL Error: ".$cerr, "error_code" => 0];
    }

    $res = json_decode($raw, true);

    // موفقیت را با چند الگو چک می‌کنیم
    $ok = (
        (isset($res['isSuccess']) && $res['isSuccess'] == true) ||
        (isset($res['status'])    && (string)$res['status'] === '0') ||
        (isset($res['Result'])    && (string)$res['Result'] === '0') ||
        (isset($res['result'])    && (string)$res['result'] === '0')
    );

    if ($ok) {
        $rrn     = $res['RRN']      ?? ($res['rrn'] ?? null);
        $orderId = $res['OrderId']  ?? ($res['orderId'] ?? null);
        $ref     = $res['refNumber']?? ($res['RefNumber'] ?? $rrn);

        return [
            "status"   => true,
            "message"  => $ref ?: "تأیید پرداخت موفق",
            "ref_id"   => $ref ?: $rrn,
            "rrn"      => $rrn,
            "order_id" => $orderId,
            "raw"      => $res,
        ];
    }

    $code = $res['code'] ?? ($res['result'] ?? ($res['status'] ?? -1));
    return [
        "status"     => false,
        "message"    => getNovinErrorMessage($code),
        "error_code" => $code,
        "raw"        => $res,
    ];
}



add_action( 'wp_ajax_paymentNow', 'paymentNow' );

if( !function_exists('paymentNow') ):

    function paymentNow(){

        $price = $_POST['debt'] * 10;
        $user_id = get_current_user_id();
        $mobile = get_user_meta($user_id,"mobile",true);
        $first_name = get_user_meta($user_id,"first_name",true);
        $description = "تسویه کیف پول آقای ". $first_name;

        $callback = get_the_permalink("157")."?verify";

        $payment = get_option("payment");
        if($payment['type_of_payment1'] == 'zarinpal'){
            $payment_list = get_option("payment");
            $token = '68946f47a45c720011b8951c';
            $payment = ZibalRequest($token,$price,$callback,$description,$mobile);
        }else if($payment['type_of_payment1'] == 'novin'){

        $token_zarin = '031D183B18C1482FA7DD8D0C4B004122';
        $payment = NovinRequest($token_zarin,$price,$callback,$description,$mobile);
        }
        if($payment['status']){
            $add_option = add_option($payment["authority"],$price);
        }
        print json_encode($payment);
        exit();
    }
endif;

//function VerifyNow(){
//    $payment_list = get_option("payment");
//    $token = $payment_list['token1'];
//    $Authority = $_GET['Authority'];
//    $amount = get_option($Authority);
//    $verify_payment = ZarinPalVerify($token,$amount,$Authority);
//    if($verify_payment['status']){
//
//        $post_id = wp_insert_post(array(
//            'post_title'=>'پرداخت بدهی',
//            'post_type'=>'financial',
//        ));
//
//        if($post_id){
//            $amount = $amount / 10;
//            $add = update_post_meta($post_id,"payment_status",1);
//            $add = update_post_meta($post_id,"wallet",1);
//            $add = update_post_meta($post_id,"wallet_pay",$amount);
//            $add = update_post_meta($post_id,"refid",$verify_payment["ref_id"]);
//        }
//
//        $tech_id = get_current_user_id();
//        $mobile = get_user_meta($tech_id,"mobile",true);
//        $template = get_option("sms")["tech_wallet_charge"];
//        $token = get_post_meta($tech_id,"first_name",true);
//        $token2 = number_format($amount)."تومان";
//        $sms = SendSMS($mobile,$template,$token,$token2);
//
//        return array("status" => true, "message" => "با تشکر از شما! پرداخت با موفقیت انجام گردید" , "refid" => $verify_payment["message"]);
//    }
//    else{
//        return array("status" => false, "message" => "خطا! تراکنش انجام نشد لطفا دوباره تلاش نمایید." , "error_message" => $verify_payment["message"]);
//    }
//
//
//}

?>