
<?php

// Zibal 
function ZibalRequest($merchant, $price, $callback, $description, $mobile = '') {
    $data = array(
        "merchant" => $merchant,
        "amount" => $price,
        "callbackUrl" => $callback,
        "description" => $description,
        "orderId" => uniqid('zibal_'),
        "mobile" => $mobile, 
        "allowedCards" => [],
        "ledgerId" => "",
        "linkToPay" => false, 
        "sms" => false 
    );

    $jsonData = json_encode($data);
    $ch = curl_init('https://gateway.zibal.ir/v1/request');
    curl_setopt($ch, CURLOPT_USERAGENT, 'Zibal Rest Api v1');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonData)
    ));

    $result = curl_exec($ch);
    $err = curl_error($ch);
    $result = json_decode($result, true);
    curl_close($ch);

    if ($err) {
        return array("status" => false, "message" => "cURL Error: " . $err, "error_code" => 0);
    } else {
        if ($result['result'] == 100) {
            $url = 'https://gateway.zibal.ir/start/' . $result['trackId'];
            return array(
                "status" => true, 
                "message" => "درحال انتقال به درگاه پرداخت زیبال", 
                "url" => $url, 
                "authority" => $result['trackId'] // Keep 'authority' for compatibility
            );
        } else {
            $error_message = getZibalErrorMessage($result['result']);
            return array(
                "status" => false, 
                "message" => $error_message, 
                "error_code" => $result['result']
            );
        }
    }
}

//Verify
function ZibalVerify($merchant, $amount, $trackId) {
    $data = array(
        "merchant" => $merchant,
        "trackId" => $trackId
    );

    $jsonData = json_encode($data);
    $ch = curl_init('https://gateway.zibal.ir/v1/verify');
    curl_setopt($ch, CURLOPT_USERAGENT, 'Zibal Rest Api v1');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonData)
    ));

    $result = curl_exec($ch);
    $err = curl_error($ch);
    $result = json_decode($result, true);
    curl_close($ch);

    if ($err) {
        return array("status" => false, "message" => "cURL Error: " . $err);
    } else {
        if ($result['result'] == 100) {
            return array(
                "status" => true, 
                "message" => $result['refNumber'],
                "ref_id" => $result['refNumber']
            );
        } else {
            $error_message = getZibalErrorMessage($result['result']);
            return array(
                "status" => false, 
                "message" => $error_message,
                "error_code" => $result['result']
            );
        }
    }
}
//خطاهای زیبال از خود داکیومنت
function getZibalErrorMessage($error_code) {
    $error_messages = array(
        100 => 'موفق',
        102 => 'merchant یافت نشد',
        103 => 'merchant غیرفعال',
        104 => 'merchant نامعتبر',
        201 => 'قبلا تایید شده',
        202 => 'سفارش یافت نشد',
        203 => 'trackId نامعتبر',
        -1 => 'در انتظار پردازش',
        -2 => 'خطای داخلی',
        105 => 'amount بایستی بزرگتر از 1,000 ریال باشد',
        106 => 'callbackUrl نامعتبر می‌باشد (شروع با http و یا https)',
        113 => 'amount مبلغ تراکنش از سقف میزان تراکنش بیشتر است'
    );
    
    return isset($error_messages[$error_code]) ? $error_messages[$error_code] : 'خطای نامشخص';
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

        $payment_list = get_option("payment");
        $merchant = $payment_list['token1']; // Changed from token1 to merchant for Zibal
        $payment = ZibalRequest($merchant,$price,$callback,$description,$mobile);
        if($payment['status']){
            $add_option = add_option($payment["authority"],$price);
        }
        print json_encode($payment);
        exit();
    }
endif;


function VerifyNow(){
    $payment_list = get_option("payment");
    $merchant = '68946f47a45c720011b8951c';

    $trackId = isset($_GET['trackId']) ? $_GET['trackId'] : $_GET['Authority'];
    $amount = get_option($trackId);
    $payment = get_option("payment");
    if($payment['type_of_payment1'] == 'zarinpal'){
        $verify_payment = ZibalVerify($merchant, $amount, $trackId);
    }else if($payment['type_of_payment1'] == 'novin'){
        $token_zarin = '031D183B18C1482FA7DD8D0C4B004122';
        $verify_payment = NovinVerify($token_zarin, $amount, $trackId);

    }




    
    if($verify_payment['status']){

        $post_id = wp_insert_post(array(
            'post_title'=>'پرداخت بدهی',
            'post_type'=>'financial',
        ));

        if($post_id){
            $amount = $amount / 10;
            $add = update_post_meta($post_id,"payment_status",1);
            $add = update_post_meta($post_id,"wallet",1);
            $add = update_post_meta($post_id,"wallet_pay",$amount);
            $add = update_post_meta($post_id,"refid",$verify_payment["ref_id"]);
        }

        $tech_id = get_current_user_id();
        $mobile = get_user_meta($tech_id,"mobile",true);
        $template = get_option("sms")["tech_wallet_charge"];
        $token = get_user_meta($tech_id,"first_name",true);
        $token2 = number_format($amount)."تومان";
        $sms = SendSMS($mobile,$template,$token,$token2);

        delete_option($trackId);

        return array(
            "status" => true, 
            "message" => "با تشکر از شما! پرداخت با موفقیت انجام گردید", 
            "refid" => $verify_payment["message"]
        );
    }
    else{
        return array(
            "status" => false, 
            "message" => "خطا! تراکنش انجام نشد لطفا دوباره تلاش نمایید.", 
            "error_message" => $verify_payment["message"]
        );
    }
}

?>
