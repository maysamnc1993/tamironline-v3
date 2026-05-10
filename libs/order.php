<?php


// operator
add_action( 'wp_ajax_order', 'order' );

if( !function_exists('order') ):

    function order(){


        $type_action = $_POST['type_action'];
        $uid = $_POST['uid'];
        $order_type = $_POST['order_type'];
        $introduction = $_POST['introduction'];
        $customer = $_POST['customer'];
        $subscription = $_POST['subscription'];
        $first_name = $_POST['first_name'];
        $mobile = $_POST['mobile'];
        $phone = $_POST['phone'];
        $state = $_POST['state'];
        $city = $_POST['city'];
        $address = $_POST['address'];
        $device = $_POST['device'];
        $brand = $_POST['brand'];
        $objection = $_POST['objection'];
        $objection_description = $_POST['objection_description'];
        $technician = $_POST['technician'];
        $tech_mobile = get_user_meta($technician,"mobile",true);

        $update_name = update_user_meta($customer,"first_name",$first_name);

        if($customer == "new_customer"){

            if(username_exists( $mobile ) ) {
                $status = false;
                $message = "مشتری قبلا در سیستم ثبت شده است";
                print json_encode(array("status" => $status , "message" => $message));
                exit();

            }else{
                $pass = rand("100000","1000000000");
                $user = wp_create_user($mobile, $pass, '');
                $add = update_user_meta($user,"role","customer");
                $add = update_user_meta($user,"first_name",$first_name);
                $add = update_user_meta($user,"mobile",$mobile);
                $add = update_user_meta($user,"phone",$phone);
                $subscription = + $user + "10000";
                $customer = $user;
            }
        }

        if($type_action == "add"){

            $post_id = wp_insert_post(array(
                'post_title'=>'سفارش جدید',
                'post_type'=>'orders',
            ));
            $add = update_post_meta($post_id,"status","0");

            $subject = "ثبت سفارش";
            $content = "سفارش شماره ".$post_id." ثبت شد";
            $status_title = "ثبت سفارش";
            addStatusDescription($post_id,$subject,$content,get_current_user_id(),jdate("Y/m/d H:i"),$status_title);

        }
        else if($type_action == "update"){

            $order_type_get = get_post_meta($uid,"order_type",true);
            $customer_get = get_post_meta($uid,"customer",true);
            $subscription_get = get_post_meta($uid,"subscription",true);
            $mobile_get = get_post_meta($uid,"mobile",true);
            $phone_get = get_post_meta($uid,"phone",true);
            $address_get = get_post_meta($uid,"address",true);
            $objection_get = get_post_meta($uid,"objection",true);
            $objection_description_get = get_post_meta($uid,"objection_description",true);
            $technician_get = get_post_meta($uid,"technician",true);
            $state_get = wp_get_post_terms($uid,"state")[0]->term_id;
            $city_get = wp_get_post_terms($uid,"city")[0]->term_id;
            $device_get = wp_get_post_terms($uid,"device")[0]->term_id;
            $brand_get = wp_get_post_terms($uid,"brand")[0]->term_id;
            $status_get = get_post_meta($uid,"status",true);

            $desc_alert = '';

            if($order_type == $order_type_get){}else{
                $desc_alert.= ' - نوع سفارش ';
                $desc_alert.= 'از ('.$order_type_get.')';
                $desc_alert.= 'به ('.$order_type.')';
                $desc_alert.= '-';
            }
            if($customer == $customer_get){}else{
                $desc_alert.= '- مشتری ';
                $desc_alert.= 'از ('.$customer_get.')';
                $desc_alert.= 'به ('.$customer.')';
                $desc_alert.= '-';
            }
            if($subscription == $subscription_get){}else{
                $desc_alert.= '- توضیحات ';
                $desc_alert.= 'از ('.$subscription_get.')';
                $desc_alert.= 'به ('.$subscription.')';
                $desc_alert.= '-';
            }
            if($mobile == $mobile_get){}else{
                $desc_alert.= '- موبایل ';
                $desc_alert.= 'از ('.$mobile_get.')';
                $desc_alert.= 'به ('.$mobile.')';
                $desc_alert.= '-';
            }
            if($phone == $phone_get){}else{
                $desc_alert.= '- تلفن ثابت ';
                $desc_alert.= 'از ('.$phone_get.')';
                $desc_alert.= 'به ('.$phone.')';
                $desc_alert.= '-';
            }
            if($address == $address_get){}else{
                $desc_alert.= '- آدرس ';
                $desc_alert.= 'از ('.$address_get.')';
                $desc_alert.= 'به ('.$address.')';
                $desc_alert.= '-';
            }
            if($objection == $objection_get){}else{
                $desc_alert.= '- ایرادات ';
                $desc_alert.= 'از ('.$objection_get.')';
                $desc_alert.= 'به ('.$objection.')';
                $desc_alert.= '-';
            }
            if($objection_description == $objection_description_get){}else{
                $desc_alert.= '- توضیح ایرادات ';
                $desc_alert.= 'از ('.$objection_description_get.')';
                $desc_alert.= 'به ('.$objection_description.')';
                $desc_alert.= '-';
            }
            if($technician == $technician_get){}else{
                $desc_alert.= '- تکنسین ';
                $desc_alert.= 'از ('.$technician_get.')';
                $desc_alert.= 'به ('.$technician.')';
                $desc_alert.= '-';
            }
            if($state == $state_get){}else{
                $desc_alert.= '- استان ';
                $desc_alert.= 'از ('.$state_get.')';
                $desc_alert.= 'به ('.$state.')';
                $desc_alert.= '-';
            }
            if($city == $city_get){}else{
                $desc_alert.= '- منطقه ';
                $desc_alert.= 'از ('.$city_get.')';
                $desc_alert.= 'به ('.$city.')';
                $desc_alert.= '-';
            }
            if($device == $device_get){}else{
                $desc_alert.= '- دستگاه ';
                $desc_alert.= 'از ('.$device_get.')';
                $desc_alert.= 'به ('.$device.')';
                $desc_alert.= '-';
            }
            if($brand == $brand_get){}else{
                $desc_alert.= '- برند ';
                $desc_alert.= 'از ('.$brand_get.')';
                $desc_alert.= 'به ('.$brand.')';
                $desc_alert.= '-';
            }


            $post_id = $uid;
            $subject = "ویرایش سفارش";
            $content = "سفارش شماره ".$post_id." ویرایش شد";
            $content.= ' | موارد ویرایش شده: ' . $desc_alert;
            $status_title = "ویرایش سفارش";
            addStatusDescription($post_id,$subject,$content,get_current_user_id(),jdate("Y/m/d H:i"),$status_title);

        }
        if($post_id){
            $add = update_post_meta($post_id,"order_type",$order_type);
            $add = update_post_meta($post_id,"introduction",$introduction);
            $add = update_post_meta($post_id,"customer",$customer);
            $add = update_post_meta($post_id,"subscription",$subscription);
            $add = update_post_meta($post_id,"mobile",$mobile);
            $add = update_post_meta($post_id,"phone",$phone);
            $add = update_post_meta($post_id,"address",$address);
            $add = update_post_meta($post_id,"objection",$objection);
            $add = update_post_meta($post_id,"objection_description",$objection_description);
            $add = update_post_meta($post_id,"technician",$technician);
            $cat = wp_set_post_terms($post_id,$state,"state");
            $cat = wp_set_post_terms($post_id,$city,"city");
            $cat = wp_set_post_terms($post_id,$device,"device");
            $cat = wp_set_post_terms($post_id,$brand,"brand");

            if($technician == ""){}else{
                if($status_get == 100){
                    $update = update_post_meta($post_id,"status",0);
                }
                $subject = "ارسال کار برای تکنسین";
                $first_name_tech = get_user_meta($technician,"first_name",true);
                $content = "سفارش شماره ".$post_id." برای تکنسین ". $first_name_tech . " ارسال گردید";
                $status_title = "ارسال کار برای تکنسین";
                addStatusDescription($post_id,$subject,$content,get_current_user_id(),jdate("Y/m/d H:i"),$status_title);
                $add = update_post_meta($post_id,"send_technician",jdate("Y/m/d H:i"));
                $sms_tec = get_post_meta($post_id,"send_sms_tec",true);
                if($sms_tec == 1){}else{


                    $sms_send_tech =  SendSMS($tech_mobile,get_option("sms")["technician_order"],$subscription);
                    $update = update_post_meta($post_id,"send_sms_tec",1);
                }
            }
            $sms = get_post_meta($post_id,"send_sms_customer",true);
            if($sms == 1){}else{
                $order_type_name = 'خرید';
                if($order_type == "repair"){$order_type_name = "تعمیر";}
                else if($order_type == "buy"){$order_type_name = "خرید";}
                else if($order_type == "service"){$order_type_name = "سرویس در مجموعه";}
                $sms_send_customer =  SendSMS($mobile,get_option("sms")["customer_order"],$first_name,$order_type_name,$subscription);
                $update = update_post_meta($post_id,"send_sms_customer",1);
            }



            $status = true;
            $message = "سفارش با موفقیت ثبت گردید";
        }
        else{
            $status = false;
            $message = "خطا سیستمی ! اطلاعات دریافتی نا معتبر است.";
        }

        print json_encode(array("status" => $status , "message" => $message));
        exit();

    }
endif;

// return_order
add_action( 'wp_ajax_return_order', 'return_order' );

if( !function_exists('return_order') ):

    function return_order(){



        $uid = $_POST['uid'];
        $return_type = $_POST['return_type'];
        $return_description = $_POST['return_description'];

        $add = update_post_meta($uid,"return_type",$return_type);
        $add = update_post_meta($uid,"status",0);
        $add = update_post_meta($uid,"status_internal_order","");
        $add = update_post_meta($uid,"return_description",$return_description);
        $add = update_post_meta($uid,"qc_status","");
        $status = true;
        $message = "وضعیت سفارش تغییر گردید";
        $content = $return_description ;
        $subject = "بازگشت سفارش";
        $status_title = "بازگشت سفارش";
        $log =  CreateLogReturnOrder($uid,$return_type,$return_description);
        $addContent = addStatusDescription($uid,$subject,$content,get_current_user_id(),jdate("Y/m/d H:i"),$status_title);
        print json_encode(array("status" => $status , "message" => $message));
        exit();

    }
endif;

function CreateLogReturnOrder($uid,$return_type,$return_description){
    if($return_type == 1){
        $return_type_message = "برگشت انجام شده";
    }
    elseif($return_type == 2){
        $return_type_message = "برگشت کنسل شده";
    }
    $invoice_descripotion = get_post_meta($uid,"invoice_descripotion",true);
    $cancel_desc = get_post_meta($uid,"cancel_desc",true);
    $cancel_desc_other = get_post_meta($uid,"cancel_desc_other",true);
    $customer_price = get_post_meta($uid,"customer_price",true);
    $buy_price = get_post_meta($uid,"buy_price",true);
    $piece_list = get_post_meta($uid,"piece_list",true);
    $customer_price_list = get_post_meta($uid,"customer_price_list",true);
    $buy_price_list = get_post_meta($uid,"buy_price_list",true);
    $hire = get_post_meta($uid,"hire",true);
    $transportation = get_post_meta($uid,"transportation",true);
    $discount = get_post_meta($uid,"discount",true);
    $price_customer = get_post_meta($uid,"price_customer",true);
    $cost_price = get_post_meta($uid,"cost_price",true);
    $total_invoice = get_post_meta($uid,"total_invoice",true);
    $negative_invoice = get_post_meta($uid,"negative_invoice",true);
    $device_image_input = get_post_meta($uid,"device_img1",true);
    $order_description_content = get_post_meta($uid,"order_description_content",true);
    $order_note_content = get_post_meta($uid,"order_note_content",true);

    $return_array = array(
        "return_type" => $return_type,
        "return_type_message" => $return_type_message,
        "return_description" => $return_description,
        "invoice_descripotion" => $invoice_descripotion,
        "cancel_desc" => $cancel_desc,
        "cancel_desc_other" => $cancel_desc_other,
        "customer_price" => $customer_price,
        "buy_price" => $buy_price,
        "piece_list" => $piece_list,
        "customer_price_list" => $customer_price_list,
        "buy_price_list" => $buy_price_list,
        "hire" => $hire,
        "transportation" => $transportation,
        "discount" => $discount,
        "price_customer" => $price_customer,
        "cost_price" => $cost_price,
        "total_invoice" => $total_invoice,
        "negative_invoice" => $negative_invoice,
        "device_image_input" => $device_image_input,
        "order_description_content" => $order_description_content,
        "order_note_content" => $order_note_content,
        "date" => jdate("Y/m/d H:i"),
    );

    $log_return = get_post_meta($uid,"log_return",true);

    if($log_return == "" || empty($log_return)){
        $data = array();
    }else{
        $data = $log_return;
    }
    array_push($data,$return_array);
    $update = update_post_meta($uid,"log_return",$data);
    return true;

}

// operator
add_action( 'wp_ajax_order_update', 'order_update' );

if( !function_exists('order_update') ):

    function order_update(){
        $uid = $_POST['uid'];
        $status = $_POST['status'];
        $price_return = $_POST['price_return'];
        $add = update_post_meta($uid,"status",$status);
        $add = update_post_meta($uid,"price_return",$price_return);
        $status = true;
        $message = "سفارش با موفقیت ویرایش گردید";
        print json_encode(array("status" => $status , "message" => $message));
        exit();
    }
endif;

// operator
add_action( 'wp_ajax_request_invoice', 'request_invoice' );

if( !function_exists('request_invoice') ):

    function request_invoice(){
        $uid = $_POST['uid'];
        $Have_invoice = $_POST['Have_invoice'];
        $Type_Of_send_invoice = $_POST['Type_Of_send_invoice'];
        $invoice_email = $_POST['invoice_email'];
        $invoice_paper = $_POST['invoice_paper'];

        $add = update_post_meta($uid,"Have_invoice",$Have_invoice);
        $add = update_post_meta($uid,"Type_Of_send_invoice",$Type_Of_send_invoice);
        $add = update_post_meta($uid,"invoice_email",$invoice_email);
        $add = update_post_meta($uid,"invoice_paper",$invoice_paper);


        $first_name = get_user_meta(get_current_user_id(),"first_name",true);

        if($Have_invoice == 1){
            if($Type_Of_send_invoice == "sms"){
                $type = "پیامک";

                $mobile = get_post_meta($uid,"mobile",true);
                $c_id = get_post_meta($uid,"customer",true);
                $token = get_user_meta($c_id,"first_name",true);
                $invoice = get_posts(array(
                    "post_type" => "financial",
                    "post_status" => "any",
                    "meta_query" => array(array(
                        "key" => "order_id",
                        "value" => $uid,
                        "compare" => "=",
                    )),
                ));

                $total = get_post_meta($invoice[0]->ID,"total_invoice",true);
                $cost = get_post_meta($invoice[0]->ID,"cost_price",true);
                $token2 = +$total + $cost;
                $token2 = number_format($token2) . "تومان";
                $send_sms = SendSMS($mobile,"OrderCustomer",$token,$token2,$uid);

            }
            else if($Type_Of_send_invoice == "email"){
                $type = "ایمیل";
            }

            else if($Type_Of_send_invoice == "paper"){
                $type = "صورت حساب کاغذی";
            }

            $content = " فاکتور برای مشتری با ". $type . " ارسال گردید";
        }
        else{
            $content = "مشتری درخواست فاکتور نکرده بود";
        }

        $subject = "ارسال فاکتور";
        $status_title = "ارسال فاکتور";
        $addContent = addStatusDescription($uid,$subject,$content,get_current_user_id(),jdate("Y/m/d H:i"),$status_title);


        $status = true;
        $message = "سفارش با موفقیت ویرایش گردید";
        print json_encode(array("status" => $status , "message" => $message));
        exit();
    }
endif;
// operator
add_action( 'wp_ajax_add_status_order', 'add_status_order' );
if( !function_exists('add_status_order') ):

    function add_status_order(){

        $uid = $_POST['uid'];
        $customer_id = get_post_meta($uid,'customer',true);
        $customer_name = get_user_meta($customer_id,'first_name',true);
        $customer_mobile = get_post_meta($uid,'mobile',true);
        $status = $_POST['status'];
        $invoice_descripotion = $_POST['invoice_descripotion'];
        $description_tech = $_POST['description_tech'];
        $description_tech1 = $_POST['description_tech1'];
        $description_tech2 = $_POST['description_tech2'];
        $cancel_desc = $_POST['cancel_desc'];
        $cancel_desc_other = $_POST['cancel_desc_other'];
        $customer_price = $_POST['customer_price'];
        $buy_price = $_POST['buy_price'];
        $piece_list = $_POST['piece_list'];
        $customer_price_list = $_POST['customer_price_list'];
        $buy_price_list = $_POST['buy_price_list'];
        $hire = $_POST['hire'];
        $transportation = $_POST['transportation'];
        $discount = $_POST['discount'];
        $price_customer = $_POST['price_customer'];
        $cost_price = $_POST['cost_price'];
        $total_invoice = $_POST['total_invoice'];
        $negative_invoice = $_POST['negative_invoice'];
        $device_img1 = $_POST['device_img1'];

        $add = update_post_meta($uid,"status",$status);
        $add = update_post_meta($uid,"invoice_descripotion",$invoice_descripotion);
        $add = update_post_meta($uid,"description_tech",$description_tech);
        $add = update_post_meta($uid,"description_tech1",$description_tech1);
        $add = update_post_meta($uid,"description_tech2",$description_tech2);
        $add = update_post_meta($uid,"cancel_desc",$cancel_desc);
        $add = update_post_meta($uid,"cancel_desc_other",$cancel_desc_other);
        $add = update_post_meta($uid,"customer_price",$customer_price);
        $add = update_post_meta($uid,"buy_price",$buy_price);
        $add = update_post_meta($uid,"piece_list",$piece_list);
        $add = update_post_meta($uid,"customer_price_list",$customer_price_list);
        $add = update_post_meta($uid,"buy_price_list",$buy_price_list);
        $add = update_post_meta($uid,"hire",$hire);
        $add = update_post_meta($uid,"transportation",$transportation);
        $add = update_post_meta($uid,"discount",$discount);
        $add = update_post_meta($uid,"price_customer",$price_customer);
        $add = update_post_meta($uid,"cost_price",$cost_price);
        $add = update_post_meta($uid,"total_invoice",$total_invoice);
        $add = update_post_meta($uid,"negative_invoice",$negative_invoice);
        $add = update_post_meta($uid,"device_img1",$device_img1);
        $add = update_post_meta($uid,"save_as_draft",'');

        if($status == 4 || $status == 5){
            $update = update_post_meta($uid,"finish_order",date("Y/m/d"));
            $update = update_post_meta($uid,"finish_order_sh",jdate("Y/m/d H:i"));
        }

        if($status == 100){
            $old_tech = get_post_meta($uid,"technician",true);
            $update = update_post_meta($uid,"technician","");
            $add = update_post_meta($uid,"status",100);
            $add = update_post_meta($uid,"send_sms_tec",0);
            $status_title = 'رد سفارش' ;

            $report_decline_user = get_option('report_decline');

// بررسی صحیح برای آرایه
            if (!is_array($report_decline_user)) {
                $report_decline_user = array();
            }

// دریافت ماه جاری
            $month_array = jdate("Y/m");

// بررسی و ایجاد آرایه برای ماه جاری
            if (!isset($report_decline_user[$month_array]) || !is_array($report_decline_user[$month_array])) {
                $report_decline_user[$month_array] = array();
            }

// بررسی و مقداردهی اولیه برای تکنسین
            if (!isset($report_decline_user[$month_array][$old_tech])) {
                $report_decline_user[$month_array][$old_tech] = 0;
            }

// افزایش تعداد
            $report_decline_user[$month_array][$old_tech]++;

// ذخیره در دیتابیس با بررسی موفقیت
            $update = update_option('report_decline', $report_decline_user);
        }

        if($status == 5 || $status == 10 ){

            if($price_customer == "" || $cost_price == "" || $total_invoice == ""){}else{

                // $sms_send_tech =  SendSMS($customer_mobile,'CRMOrderSendInvoiceForCustomer',$customer_name,$uid,number_format($price_customer));
                // $sms_send_tech =  SendSMS($customer_mobile,'crmhappycalltemplate',$customer_name,$uid);
                $technician = get_post_meta($uid,"technician",true);
                $post_id = wp_insert_post(array(
                    'post_title'=>'فاکتور جدید',
                    'post_type'=>'financial',
                    'post_author' => $technician,
                ));
                if($post_id){



                    $add = update_post_meta($post_id,"order_id",$uid);
                    $add = update_post_meta($post_id,"customer_id",$uid);
                    $add = update_post_meta($post_id,"technician_id",$technician);
                    $add = update_post_meta($post_id,"price_customer",$price_customer);
                    $add = update_post_meta($post_id,"cost_price",$cost_price);
                    $add = update_post_meta($post_id,"total_invoice",$total_invoice);
                    if($negative_invoice == 0){
                        $add = update_post_meta($post_id,"payment_status",0);
                    }
                    else{
                        $add = update_post_meta($post_id,"payment_status",3);
                    }

                }



            }

        }

        if($status == 0){}else{

            if($status == 1){
                $status_title = "هماهنگ شده";
            }
            else if($status == 2){
                $status_title = "باز شده";
            }
            else if($status == 3){
                $status_title = "معلق";
            }
            else if($status == 4){
                $status_title = "کنسل شده";
            }
            else if($status == 5){
                $status_title = "انجام کار";
            }

            $first_name = get_user_meta(get_current_user_id(),"first_name",true);

            if($status == 1){
                $tech_desc = $description_tech;
            }else if($status == 3){
                $tech_desc = $description_tech1;
            }
            else if($status == 2){
                $tech_desc = $description_tech2;
            }
            $content = $tech_desc. ' ' . $cancel_desc_other . ' '. $invoice_descripotion ;
            $subject = "تغییر وضعیت سفارش به " . $status_title;
            $addContent = addStatusDescription($uid,$subject,$content,get_current_user_id(),jdate("Y/m/d H:i"),$status_title);




            if($status == 5 || $status == 10){

                $percent = get_user_meta(get_current_user_id(),"percent",true);
                $type_of_calc_tech = get_user_meta(get_current_user_id(),"type_of_calc_tech",true);
                $tech_per_of_all = get_user_meta(get_current_user_id(),"tech_per_of_all",true);
                if($status == 10){
                    $percent = 100;
                }

                if($type_of_calc_tech == 1){
                    $total_invoice = $total_invoice + $cost_price;
                    $all_incoome = $total_invoice ;
                    $tech_per = 1 - ($tech_per_of_all / 100);
                    $total_invoice_customer = $all_incoome * $tech_per;
                    $total_invoice_co = $total_invoice_customer;
                }else{

                    $total_invoice_customer = ($total_invoice / 100) * $percent;
                    $total_invoice_co = $total_invoice - $total_invoice_customer;
                    $all_incoome = $total_invoice + $cost_price;
                }


                $content = "ایجاد فاکتور برای سفارش" . $uid;
                $content.='</br>';
                $content = "جمع کل صورت حساب : " . number_format($all_incoome);
                $content.='</br>';
                $content.= " مانده کل : " . number_format($total_invoice);
                $content.='</br>';
                $content.= "هزینه قطعات مصرفی: " . number_format($cost_price);
                $content.='</br>';
                $content.= "سهم تکنسین" . number_format($total_invoice_co);
                $content.='</br>';
                $content.=" توضیحات : " . $invoice_descripotion;
                $subject = "ایجاد فاکتور";
                $status_title = "ایجاد فاکتور";
                $addContent = addStatusDescription($uid,$subject,$content,get_current_user_id(),jdate("Y/m/d H:i"),$status_title);


            }

        }

        $status = true;
        $message = "سفارش با موفقیت ویرایش گردید";
        print json_encode(array("status" => $status , "message" => $message));
        exit();

    }
endif;//

//allow_invoice
add_action( 'wp_ajax_AllowNagetiveInvoice', 'AllowNagetiveInvoice' );
if( !function_exists('AllowNagetiveInvoice') ):

    function AllowNagetiveInvoice(){
        $uid = $_POST['uid'];
        $allow_invoice = $_POST['allow_invoice'];

        if($allow_invoice == 1){

            $add = update_post_meta($uid,"negative_invoice",0);
            $invoice = get_posts(array("post_type" => "financial" , "post_status" => "any", "meta_query" =>
                array(
                    array(
                        "key" => "order_id",
                        "value" => $uid,
                        "compare" => "LIKE",
                    )
                )
            ));
            $add = update_post_meta($invoice[0]->ID,"payment_status",0);

            $subject = "تایید سفارش منفی";
            $status_title = "تایید سفارش منفی";
            $content = "فاکتور منفی توسط اپراتور تایید گردید";
            $addContent = addStatusDescription($uid,$subject,$content,get_current_user_id(),jdate("Y/m/d H:i"),$status_title);

        }
        else{
            $add = update_post_meta($uid,"negative_invoice",0);
            $add = update_post_meta($uid,"status",0);
            $subject = "عدم تایید سفارش منفی";
            $status_title = "عدم تایید سفارش منفی";
            $content = "فاکتور منفی توسط اپراتور تایید نگردید";
            $addContent = addStatusDescription($uid,$subject,$content,get_current_user_id(),jdate("Y/m/d H:i"),$status_title);

        }

        $status = true;
        $message = "وضعیت سفارش منفی ویرایش گردید";
        print json_encode(array("status" => $status , "message" => $message));
        exit();

    }
endif;



function addStatusDescription($uid,$subject,$content,$author,$date,$status){

    $data = get_post_meta($uid,"order_description_content",true);
    if($data == "" || empty($data)){
        $data_old = array();
    }else{
        $data_old = $data;
    }

    $message = array("subject" => $subject, "content" => $content , "author" => $author , "date" => $date , "status" => $status);
    array_push($data_old,$message);
    $update = update_post_meta($uid,"order_description_content",$data_old);
    return true;

}

function DebtCalc($uid){

    $financial_debt = get_posts( array(
        "post_type" => "financial",
        "posts_per_page" => -1,
        "post_status" => "any",
        "author" => $uid,
        "meta_query" => array(
            array(
                "key" => "payment_status",
                "value" => 0,
                "compare" => "LIKE"
            )
        )
    ) );

    $financial_reward = get_posts( array(
        "post_type" => "financial",
        "posts_per_page" => -1,
        "post_status" => "any",
        "author" => $uid,
        "meta_query" => array(
            array(
                "key" => "reward_type",
                "value" => 1,
                "compare" => "="
            )
        )
    ) );
    $financial_penalti = get_posts( array(
        "post_type" => "financial",
        "posts_per_page" => -1,
        "post_status" => "any",
        "author" => $uid,
        "meta_query" => array(
            array(
                "key" => "reward_type",
                "value" => 0,
                "compare" => "="
            )
        )
    ) );

    $financial_wallet = get_posts( array(
        "post_type" => "financial",
        "posts_per_page" => -1,
        "post_status" => "any",
        "author" => $uid,
        "meta_query" => array(
            array(
                "key" => "payment_status",
                "value" => 1,
                "compare" => "LIKE"
            )
        )
    ) );
    $percent = get_user_meta($uid,"percent",true);

    $total_invoice = 0;
    foreach($financial_debt as $item){

        $invoice_price = get_post_meta($item->ID,"total_invoice",true);
        $wallet_pay = get_post_meta($item->ID,"wallet_pay",true);
        if($invoice_price < 0){
            $invoice_price = $invoice_price * -1;
        }else{
            $invoice_price = $invoice_price;

        }

        $total_invoice = + $total_invoice + $invoice_price;
    }

    $reward_total = 0;
    foreach($financial_reward as $item){
        $invoice_price = get_post_meta($item->ID,"total_invoice",true);
        if($total_invoice == '' || empty($total_invoice)){$total_invoice = 0;}
        $reward_total = + $reward_total + $invoice_price;
    }


    $penalti_total = 0;
    foreach($financial_penalti as $item){
        $invoice_price = get_post_meta($item->ID,"total_invoice",true);
        if($total_invoice == '' || empty($total_invoice)){$total_invoice = 0;}
        $penalti_total = + $penalti_total + $invoice_price;
    }

    $total_invoice_wallet = 0;
    foreach($financial_wallet as $item){
        $reward_type = get_post_meta($item->ID,"reward_type",true);
        $wallet_pay = get_post_meta($item->ID,"wallet_pay",true);

        $wallet = get_post_meta($item->ID,"wallet",true);

        if($reward_type == 1){
            if(is_numeric($total_invoice_wallet) && is_numeric(get_post_meta($item->ID,"total_invoice",true))) {
                $total_invoice_wallet = +$total_invoice_wallet + get_post_meta($item->ID, "total_invoice", true);
            }
            else{
                $total_invoice_wallet = $total_invoice_wallet;
            }
        }

        else if($reward_type == 0){
            if(is_numeric($total_invoice_wallet) && is_numeric(get_post_meta($item->ID,"total_invoice",true))){
                $total_invoice_wallet = + $total_invoice_wallet - get_post_meta($item->ID,"total_invoice",true);
            }
            else{
                $total_invoice_wallet = $total_invoice_wallet;
            }
        }
        if($wallet == 1){
            $total_invoice_wallet = + $total_invoice_wallet + $wallet_pay;

        }

    }

    $financial_debt = ($total_invoice / 100) * $percent;

    $tech_total_debt =  (+$total_invoice_wallet + $reward_total) - $penalti_total;
    $total_tech_total_debt = +$tech_total_debt - $financial_debt;

    return $total_invoice_wallet;
}


// operator
add_action( 'wp_ajax_SearchCustomer', 'SearchCustomer' );

if( !function_exists('SearchCustomer') ):

    function SearchCustomer(){

        $key = $_POST['key'];
        $value = $_POST['value'];
        $return = false;
        $user_id = 0;
        if($key == "subscription") {

            $u_id = +$value - 10000;
            $operator = get_user_by("ID", $u_id);
        }
        else if($key == "mobile"){

            $user  = get_users(array(
                'meta_key' => 'mobile',
                'meta_value' => $value,
                'meta_compare' => '=' // exact match only
            ));
            $u_id = $user[0]->ID;
            $value = +$u_id + 10000;
            $operator = get_user_by("ID", $u_id);

        }

        if(count($operator) > 0){
            $role = get_user_meta($operator->ID,"role",true);
            if($role == "customer"){

                $first_name = get_user_meta($operator->ID,"first_name",true);
                $mobile = get_user_meta($operator->ID,"mobile",true);
                $phone = get_user_meta($operator->ID,"phone",true);

                $order_list = get_posts(array("post_type" => array("orders","internal_orders"), "posts_per_page" => -1,  "post_status" => "any", "meta_query" => array(
                    array(
                        "key" => "customer",
                        "value" => $u_id,
                        "compare" => "LIKE"
                    )
                )
                ));

                $order_list_item = array();

                foreach($order_list as $item){
                    $device = wp_get_post_terms($item->ID,"device");
                    $brand = wp_get_post_terms($item->ID,"brand");
                    $status = get_post_meta($item->ID,"status",true);
                    $address = get_post_meta($item->ID,"address",true);

                    $state = wp_get_post_terms($item->ID,"state")[0]->term_id;
                    $city = wp_get_post_terms($item->ID,"city")[0]->term_id;
                    $address = get_post_meta($item->ID,"address",true);
                    $objection = get_post_meta($item->ID,"objection",true);
                    $objection_description = get_post_meta($item->ID,"objection_description",true);
                    $technician = get_post_meta($item->ID,"technician",true);


                    if($status == 0 || $status == ""){
                        $status_title = "جدید";
                    }
                    if($status == 1){
                        $status_title = "هماهنگ شده";
                    }
                    else if($status == 2){
                        $status_title = "باز شده";
                    }
                    else if($status == 3){
                        $status_title = "معلق";
                    }
                    else if($status == 4){
                        $status_title = "کنسل شده";
                    }
                    else if($status == 5){
                        $status_title = "انجام کار";
                    }
                    if($item->post_type == "orders"){
                        $type_order = "سفارش خارجی";
                    }else if($item->post_type == "internal_orders"){
                        $type_order = "سفارش داخلی";
                    }
                    $item_order = array(
                        "order_id" => $item->ID,
                        "type_order" => $type_order,
                        "device" => $device[0]->name,
                        "brand" => $brand[0]->name,
                        "status" => $status_title,
                        "address" => $address,
                        "state" => $state,
                        "city" => $city,
                        "objection" => $objection,
                        "objection_description" => $objection_description,
                        "technician" => $technician,
                        "device_id" => $device[0]->term_id,
                        "brand_id" => $brand[0]->term_id,
                        "order_type" => $item->post_type,

                    );
                    array_push($order_list_item,$item_order);
                }



                $user_return = array(
                    array(
                        "id" => $operator->ID,
                        "subscription" => $value,
                        "first_name" => $first_name,
                        "mobile" => $mobile,
                        "phone"=> $phone,
                        "orders" => $order_list_item,

                    )
                );
                print json_encode(array("status" => true , "return" => ListOfCustomerPopUp($user_return)  ));
                exit();
            }
            else{
                print json_encode(array("status" => false));
                exit();
            }
        }
        else{
            print json_encode(array("status" => false));
            exit();
        }



    }
endif;

function ListOfCustomerPopUp($data){
    $return = '<ul class="listCustomerFound">';
    if(count($data) > 0){
        foreach($data as $item){
            if(count($item['orders']) > 0){
                $order_list = '<ul class="order_list_item_customer">';
                foreach($item['orders'] as $order_item){
                    if($order_item["order_type"] == "orders"){
                        $p_show_order = 135;
                    }else{
                        $p_show_order = 14285;
                    }
                    $order_list.= '<li>';
                    $order_list.= '<div class="item_order selectstate">
                            <input type="radio" id="SelectCustumerTechnician" name="SelectCustumerTechnician" data-state="'.$order_item["state"].'" data-city="'.$order_item["city"].'"
                                data-address="'.$order_item["address"].'"  data-brand="'.$order_item["brand_id"].'"  data-device="'.$order_item["device_id"].'"  data-objection="'.$order_item["objection"].'"   data-objection_description="'.$order_item["objection_description"].'" 
                             >
                             <span>انتخاب اطلاعات سفارش</span>
                             <a class="view" target="_blank" href="'.get_the_permalink($p_show_order).'?uid='.$order_item["order_id"].'">مشاهده سفارش</a>

                        </div>';
                    $order_list.= '<div class="item_order">

                            <span>نوع سفارش</span>
                            <b>'.$order_item["type_order"].'</b>

                        </div>';
                    $order_list.= '<div class="item_order">

                            <span>دستگاه</span>
                            <b>'.$order_item["device"].'</b>

                        </div>';
                    $order_list.= '<div class="item_order">

                            <span>برند</span>
                            <b>'.$order_item["brand"].'</b>

                        </div>';
                    $order_list.= '<div class="item_order">

                            <span>وضعیت</span>
                            <b>'.$order_item["status"].'</b>

                        </div>';
                    $order_list.= '<div class="item_order">

                            <span>آدرس</span>
                            <b>'.$order_item["address"].'</b>

                        </div>';
                    $order_list.= '</li>';

                }

                $order_list.='</ul>';
            }else{
                $order_list = '';
            }
            $return.='
                <li class="customerItem">
                    <div class="itemCustomer">
                        <span>کد اشتراک:</span>
                        <b>'.$item["subscription"].'</b>
                    </div>
                    <div class="itemCustomer">
                        <span>نام و نام خانوادگی:</span>
                        <b>'.$item["first_name"].' '.SelectTypeUserName($item["id"]).'</b>
                    </div>
                     <div class="itemCustomer">
                        <span>شماره موبایل:</span>
                        <b>'.$item["mobile"].'</b>
                    </div> 
                    <div class="itemCustomer">
                        <span>تلفن ثابت:</span>
                        <b>'.$item["phone"].'</b>
                    </div>
                    <div class="list_of_order_customer">
                        <h3>لیست سفارشات</h3>
                        <div class="body_list_order_customer">
                            '.$order_list.'
                        </div>
                    </div>
                    <div class="SelectCustomerItem" id="'.$item["id"].'" data-subscription="'.$item["subscription"].'" data-first-name="'.$item["first_name"].'" data-mobile="'.$item["mobile"].'" data-phone="'.$item["phone"].'">
                        برای انتخاب مشتری کلیک کنید
                    </div>
                </li>
            ';

        }

    }
    $return.='</ul>';

    return $return;
}


// operator
add_action( 'wp_ajax_CallLog', 'CallLog' );

if( !function_exists('CallLog') ):

    function CallLog(){

        $uid = $_POST['uid'];
        $number = $_POST['number'];

        $first_name = get_user_meta(get_current_user_id(),"first_name",true);
        $content = "با شماره " . $number . " تماس گرفته شد";
        $subject = "تماس با مشتری";
        $status_title = "تماس با مشتری";
        $addContent = addStatusDescription($uid,$subject,$content,get_current_user_id(),jdate("Y/m/d H:i"),$status_title);

        print json_encode(array("status" => true));
        exit();

    }
endif;

// operator
add_action( 'wp_ajax_add_message_order', 'add_message_order' );

if( !function_exists('add_message_order') ):

    function add_message_order(){

        $uid = $_POST['uid'];
        $descriptionMSG = $_POST['descriptionMSG'];


        $content = $descriptionMSG;
        $subject = "پیام اپراتور";
        $status_title = "پیام اپراتور";
        $addContent = addStatusDescription($uid,$subject,$content,get_current_user_id(),jdate("Y/m/d H:i"),$status_title);

        print json_encode(array("status" => true , "message" => "پیام شما ارسال گردید"));
        exit();

    }
endif;


// operator
add_action( 'wp_ajax_add_note_order', 'add_note_order' );

if( !function_exists('add_note_order') ):

    function add_note_order(){

        $uid = $_POST['uid'];
        $descriptionMSG = $_POST['descriptionMSG'];

        $content = $descriptionMSG;
        $subject = "پیام اپراتور";
        $status_title = "پیام اپراتور";
        $addContent = addNoteDescription($uid,$subject,$content,get_current_user_id(),jdate("Y/m/d H:i"),$status_title);

        print json_encode(array("status" => true , "message" => "پیام شما ارسال گردید"));
        exit();

    }
endif;


function addNoteDescription($uid,$subject,$content,$author,$date,$status){

    $data = get_post_meta($uid,"order_note_content",true);
    if($data == "" || empty($data)){
        $data_old = array();
    }else{
        $data_old = $data;
    }

    $message = array("subject" => $subject, "content" => $content , "author" => $author , "date" => $date , "status" => $status);
    array_push($data_old,$message);
    $update = update_post_meta($uid,"order_note_content",$data_old);
    return true;

}

function filterData(&$str){
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
}


function Export2Excel($data){
    $data["posts_per_page"] = -1;
    unset($data["paged"]);
    

    $data = get_posts($data);

// Excel file name for download
    $fileName = "order-data_" . date('Y-m-d') . ".xls";
// Column names
    $fields = array('ID', 'Customer Name', 'Customer subscribe', 'Customer Mobile', 'Customer Phone', 'City', 'Region', 'Address' , "Device" , "Brand" , "Objection" , "Description" , "Technician" , "Status" , "Invoice Total Price" , "Order registration date " , "Order registration date");

// Display column names as first row
    $excelData = implode("\t", array_values($fields)) . "\n";

// Fetch records from database


    foreach($data as $item){

        $customer = get_post_meta($item->ID,"customer",true);
        $first_name = get_user_meta($customer,"first_name",true);
        $subscription = get_post_meta($item->ID,"subscription",true);
        $mobile = get_post_meta($item->ID,"mobile",true);
        $phone = get_post_meta($item->ID,"phone",true);
        $state = wp_get_post_terms($item->ID,"state")[0]->name;
        $city = wp_get_post_terms($item->ID,"city")[0]->name;
        $address = get_post_meta($item->ID,"address",true);
        $brand = wp_get_post_terms($item->ID,"brand")[0]->name;;
        $device = wp_get_post_terms($item->ID,"device")[0]->name;;
        $objection = get_post_meta($item->ID,"objection",true);
        $objection_description = get_post_meta($item->ID,"objection_description",true);
        $technician = get_post_meta($item->ID,"technician",true);
        $technician_name = get_user_meta($technician,"first_name",true);
        $status = get_post_meta($item->ID,"status",true);
        $price_return = get_post_meta($item->ID,"price_return",true);
        $send_technician = get_post_meta($item->ID,"send_technician",true);
        if($status == 0 || $status == ""){
            $status_title = "جدید";
        }
        if($status == 1){
            $status_title = "هماهنگ شده";
        }
        else if($status == 2){
            $status_title = "باز شده";
        }
        else if($status == 3){
            $status_title = "معلق";
        }
        else if($status == 4){
            $status_title = "کنسل شده";
        }
        else if($status == 5){
            $status_title = "انجام کار";
        }
          else if($status == 10){
            $status_title = "ایاب و ذهاب";
        }

        $lineData =
            array($item->ID,
                $first_name,
                $subscription,
                $mobile,
                $phone,
                $state,
                $city,
                $address ,
                $device ,
                $brand ,
                $objection ,
                $objection_description ,
                $technician_name ,
                $status_title ,
                $price_return,
                get_the_date("Y/m/d",$item->ID),
                $send_technician
            );
        array_walk($lineData, 'filterData');
        $excelData .= implode("\t", array_values($lineData)) . "\n";
    }


// Headers for download
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$fileName\"");

// Render excel data
    echo $excelData;

    exit;
}

// operator
add_action( 'wp_ajax_saveShaygan', 'saveShaygan' );

if( !function_exists('saveShaygan') ):

    function saveShaygan(){

        $oid = $_POST['oid'];
        $SaveToShayganStatus = $_POST['SaveToShayganStatus'];

        if($SaveToShayganStatus == 1){
            $update = update_post_meta($oid,'happyCall',$SaveToShayganStatus);
        }else{
            $update =  delete_post_meta($oid, 'happyCall', 0);

        }

        $content = 'وضعیت هپی کال تغییر گردید';
        $subject = "پیام اپراتور";
        $status_title = "پیام اپراتور";
        $addContent = addStatusDescription($oid,$subject,$content,get_current_user_id(),jdate("Y/m/d H:i"),$status_title);

        print json_encode(array("status" => true , "message" => "تغییر وضعیت در هپی کال اعمال گردید"));
        exit();

    }
endif;

// upload_device_image
add_action( 'wp_ajax_upload_device_image', 'upload_device_image' );

if( !function_exists('upload_device_image') ):

    function upload_device_image(){

        $uid = $_POST['uid'];
        $device_image_input = $_POST['device_image_input'];

        $update = update_post_meta($uid,'device_image_input',$device_image_input);

        $content = 'تصویر دستگاه با موفقیت بارگذاری گردید';
        $subject = "پیام تکنسین";
        $status_title = "پیام تکنسین";
        $addContent = addStatusDescription($uid,$subject,$content,get_current_user_id(),jdate("Y/m/d H:i"),$status_title);

        print json_encode(array("status" => true , "message" => "تصویر دستگاه با موفقیت بارگذاری گردید"));
        exit();

    }
endif;


// upload_device_image
add_action( 'wp_ajax_list_count_order', 'list_count_order' );

if( !function_exists('list_count_order') ):

    function list_count_order(){

        $all_count_count = new WP_Query(array(
            "post_type" => "orders",
            "post_status" => "any",

        ));
        $all_count = $all_count_count->found_posts;

        $new_count_count = new WP_Query(array(
            "post_type" => "orders",
            "post_status" => "any",
            "meta_query" => array(
                "relation" => "AND",
                array(
                    "key" => "status",
                    "value" => "0",
                    "compare" => "LIKE",
                ),
                array(
                    "key" => "status",
                    "value" => "",
                    "compare" => "LIKE",
                ),
                array(
                    "key" => "return_type",
                    "compare" => "NOT EXISTS",
                )
            ),
        ));
        $new_count = $new_count_count->found_posts;

        $decline_count_count = new WP_Query(array(
            "post_type" => "orders",
            "post_status" => "any",
            "meta_query" => array(
                "relation" => "AND",
                array(
                    "key" => "status",
                    "value" => "100",
                    "compare" => "==",
                ),
                array(
                    "key" => "return_type",
                    "compare" => "NOT EXISTS",
                )
            ),
        ));
        $decline_count = $decline_count_count->found_posts;

        $back_open_count_count = new WP_Query(array(
            "post_type" => "orders",
            "post_status" => "any",
            "meta_query" => array(
                "relation" => "AND",
                array(
                    "key" => "status",
                    "value" => "0",
                    "compare" => "LIKE",
                ),
                array(
                    "key" => "return_type",
                    "value" => "",
                    "compare" => "!=",
                ),
            ),
        ));
        $back_open_count = $back_open_count_count->found_posts;

        $sync_count_count = new WP_Query(array(
            "post_type" => "orders",
            "post_status" => "any",
            "meta_query" => array(
                array(
                    "key" => "status",
                    "value" => "1",
                    "compare" => "=",
                )
            ),
        ));
        $sync_count = $sync_count_count->found_posts;


        $open_count_count = new WP_Query(array(
            "post_type" => "orders",
            "post_status" => "any",
            "meta_query" => array(
                array(
                    "key" => "status",
                    "value" => "2",
                    "compare" => "LIKE",
                )
            ),
        ));
        $open_count = $open_count_count->found_posts;
        $pend_count_count = new WP_Query(array(
            "post_type" => "orders",
            "post_status" => "any",
            "meta_query" => array(
                array(
                    "key" => "status",
                    "value" => "3",
                    "compare" => "LIKE",
                )
            ),
        ));
        $pend_count = $pend_count_count->found_posts;
        $cancel_count_count = new WP_Query(array(
            "post_type" => "orders",

            "post_status" => "any",
            "meta_query" => array(
                array(
                    "key" => "status",
                    "value" => "4",
                    "compare" => "LIKE",
                )
            ),
        ));
        $cancel_count = $cancel_count_count->found_posts;
        $down_count_count = new WP_Query(array(
            "post_type" => "orders",
            "post_status" => "any",
            "meta_query" => array(
                array(
                    "key" => "status",
                    "value" => "5",
                    "compare" => "LIKE",
                )
            ),
        ));
        $down_count = $down_count_count->found_posts;
        $notech_count_count = new WP_Query(array(
            "post_type" => "orders",
            "post_status" => "any",
            "meta_query" => array(
                array(
                    "key" => "technician",
                    "value" => "",
                    "compare" => "=",
                )
            ),
        ));
        $notech_count = $notech_count_count->found_posts;
        $archivecount_count = new WP_Query(array(
            "post_type" => "orders",
            "post_status" => "any",
            "meta_query" => array(
                "relation" => "AND",
                array(
                    "key" => "status",
                    "value" => array("4","5"),
                    "compare" => "IN",
                ),
                array(
                    "key" => "happyCall",
                    "value" => "1",
                    "compare" => "=",
                ),
            ),
        ));
        $archive = $archivecount_count->found_posts;
        $unarchive_count = new WP_Query(array(
            "post_type" => "orders",
            "post_status" => "any",
            "meta_query" => array(
                "relation" => "AND",
                array(
                    "key" => "status",
                    "value" => array("4","5"),
                    "compare" => "IN",
                ),
                array(
                    "key" => "happyCall",
                    "compare" => "NOT EXISTS",
                ),
            ),
        ));
        $unarchive = $unarchive_count->found_posts;
        $back_counts = new WP_Query(array(
            "post_type" => "orders",
            "post_status" => "any",
            "meta_query" => array(
                array(
                    "key" => "return_type",
                    "compare" => "EXIST",
                )
            ),
        ));
        $back_count = $back_counts->found_posts;
        print json_encode(array("status" => true ,
            "all_count" => $all_count,
            "new_count" => $new_count,
            "decline_count" => $decline_count,
            "sync_count" => $sync_count,
            "open_count" => $open_count,
            "pend_count" => $pend_count,
            "cancel_count" => $cancel_count,
            "down_count" => $down_count,
            "notech_count" => $notech_count,
            "archive" => $archive,
            "unarchive" => $unarchive,
            "back_open" => $back_open_count,
            "back_count" => $back_count,
        ));
        exit();

    }
endif;

// upload_device_image
add_action( 'wp_ajax_ReturnTechDetail', 'ReturnTechDetail' );

if( !function_exists('ReturnTechDetail') ):

    function ReturnTechDetail(){

        $uid = $_POST["uid"];
        $max_order = get_user_meta($uid,"max_order",true);
        $max_price = get_user_meta($uid,"max_price",true);

        $order = count(get_posts( array(
            "post_type" => "orders",
            "posts_per_page" => -1,
            "post_status" => "any",
            "meta_query" => array(
                "relation" => "AND",
                array(
                    "key" => "technician",
                    "value" => $uid,
                    "compare" => "LIKE"
                ),
                array(
                    "key" => "status",
                    "value" => array("0","1","2","3"),
                    "compare" => "IN",
                )
            )
        ) ));
        $debt = loadAllDataFinَUser($uid);
        if($debt < 1){
            $debt = $debt * -1;
        }else{
            $debt = 0;
        }
        if($debt > $max_price || $order > $max_order){
            $message = '(تکنسین بیش از حد مجاز سفارش یا بدهی دارد. )';

            $error = false;
        }
        else{
            $message = '';
            $error = true;
        }

        print json_encode(array("status" => $error , "max_order" => $max_order , "max_price" => $max_price , "order" => $order , "debt" => $debt));
        exit();


    }
endif;

add_action( 'wp_ajax_LIst_OF_Order_user', 'LIst_OF_Order_user' );
function LIst_OF_Order_user(){
    $user_id = $_POST["user_id"];
    $data_order_type = $_POST["data_order_type"];

    if($data_order_type == "external"){
        $post_type = "orders";
    }else if($data_order_type == "internal"){
        $post_type = "internal_orders";
    }

    $order =
    count(
        get_posts(
            array(
                "post_type" => array("orders","internal_orders"),
                "posts_per_page" => -1,
                "post_status" => "any",
                "meta_query" => array(
                    array(
                        "key" => "customer",
                        "value" => $user_id,
                        "compare" => "LIKE"
                    )
                )
            )
        )
    );


    print json_encode(array("status" => true, "counts" => $order));
    exit();
}



add_action( 'wp_ajax_SetCustomerHappyCall', 'SetCustomerHappyCall' );
function SetCustomerHappyCall(){

   $order_id = $_POST["order_id"];
   $hc_key = $_POST["hc_key"];
   $hc_value = $_POST["hc_value"];
   $hc_customer = array_sum($hc_value) / count($hc_value);


   $update = update_post_meta($order_id,"hc_customer_key",$hc_key);
   $update = update_post_meta($order_id,"hc_customer_value",$hc_value);
   $update = update_post_meta($order_id,"hc_customer",$hc_customer);
   $count_hc_customer = get_option("HappyCallCustomer_Count");

    if($count_hc_customer == ""){
        $count_hc_customer = array();
    }
   foreach($hc_key as $key => $item){
        if($count_hc_customer[$item] == ""){
            $count_hc_customer[$item]["count"] = 1;
            $count_hc_customer[$item]["result"] = array($hc_value[$key]);
        }else{
            $count_hc_customer[$item]["count"] = + $count_hc_customer[$item]["count"] + 1;
            array_push($count_hc_customer[$item]["result"],$hc_value[$key]);

        }
    }


    $content = 'ثبت هپی کال مشتریان';
    $subject = "هپی کال مشتریان";
    $status_title = "هپی کال مشتریان";
    $addContent = addStatusDescription($order_id,$subject,$content,get_current_user_id(),jdate("Y/m/d H:i"),$status_title);


    update_option("HappyCallCustomer_Count",$count_hc_customer);

    print json_encode(array("status" => true, "message" => "هپی کال با موفقیت ثبت شد."));
    exit();
}


add_action( 'wp_ajax_SetTechHappyCall', 'SetTechHappyCall' );
function SetTechHappyCall(){

   $order_id = $_POST["order_id"];
   $technician = get_post_meta($order_id,"technician",true);
   $hc_key = $_POST["hc_key"];
   $hc_value = $_POST["hc_value"];
   $hc_customer = array_sum($hc_value) / count($hc_value);
    $update = update_post_meta($order_id,"happyCall",1);
   $update = update_post_meta($order_id,"hc_tech_key",$hc_key);
   $update = update_post_meta($order_id,"hc_tech_value",$hc_value);
   $update = update_post_meta($order_id,"hc_tech",$hc_customer);
   $count_hc_customer = get_option("HappyCallTech_Count");
   $tech_point = get_user_meta($technician,"tech_point",true);
    if($count_hc_customer == ""){
        $count_hc_customer = array();
    }
    if($tech_point == ""){
        $tech_point = array();
        $tech_point["point"] = array();
        $tech_point["hc_key"] = array();
        $tech_point["hc_value"] = array();
    }
    array_push($tech_point["point"],$hc_customer);
    array_push($tech_point["hc_key"],$hc_key);
    array_push($tech_point["hc_value"],$hc_value);

   foreach($hc_key as $key => $item){
        if($count_hc_customer[$item] == ""){
            $count_hc_customer[$item]["count"] = 1;
            $count_hc_customer[$item]["result"] = array($hc_value[$key]);
        }else{
            $count_hc_customer[$item]["count"] = + $count_hc_customer[$item]["count"] + 1;
            array_push($count_hc_customer[$item]["result"],$hc_value[$key]);
        }
    }

   $update_tech = update_user_meta($technician,"tech_point",$tech_point);
   update_option("HappyCallTech_Count",$count_hc_customer);

    $content = 'ثبت هپی کال تکنسین';
    $subject = "هپی کال تکنسین";
    $status_title = "هپی کال تکنسین";
    $addContent = addStatusDescription($order_id,$subject,$content,get_current_user_id(),jdate("Y/m/d H:i"),$status_title);
    print json_encode(array("status" => true, "message" => "هپی کال با موفقیت ثبت شد."));
    exit();
}


add_action( 'wp_ajax_LoadPointOfTech', 'LoadPointOfTech' );
function LoadPointOfTech(){

   $id = $_POST["id"];
   $tech_point = get_user_meta($id,"tech_point",true);
    $HappyCallTech = get_option("HappyCallTech");
    $list_of_key = array();
    $list_of_key_in_array = array();
    foreach($HappyCallTech as $key => $value){
        $list_of_key[$key] = array();
        array_push($list_of_key_in_array,$key);
    }
    foreach($tech_point["hc_key"] as $key => $value ){
        foreach($value as $key1 => $value1){
            if(in_array($value1,$list_of_key_in_array)){
                array_push($list_of_key[$value1],$tech_point["hc_value"][$key1]);
            }
        }
    }

    $list = '';
    $i = 0;
    foreach($HappyCallTech as $key => $value){

    $per2 = 0;
    $count = 0;
    foreach($list_of_key as  $value1){
        foreach ($value1 as $value2){
            $per2 = + $per2 + $value2[$key];
            $count = $count + 1;
        }
    }
    $per = $per2 / $count;
    $s_1 = '';
    $s_2 = '';
    $s_3 = '';
    $s_4 = '';
    $s_5 = '';
    if($per > 1){
        $s_1 = "active";
    }
    if($per > 19){
        $s_2 = "active";
    }
    if($per > 39){
        $s_3 = "active";
    }
    if($per > 59){
        $s_4 = "active";
    }
    if($per > 89){
        $s_5 = "active";
    }

    $i++;
    $list.= '
    <tr>
                                    <td>'.$i.'</td>
                                    <td>'.$value.'</td>
                                    <td>
                                        
                                           <span class="AvgPer">
                                            <b class="'.$s_1.'">*</b>
                                            <b class="'.$s_2.'">*</b>
                                            <b class="'.$s_3.'">*</b>
                                            <b class="'.$s_4.'">*</b>
                                            <b class="'.$s_5.'">*</b>
                                            
                                        </span>
                                        <span class="AvgPer">'.$per.'٪</span>
                                        <span class="AvgPer">'.$count.' رای ثبت شده</span>
                                    
                                    </td>
                                    </tr>
    ';
}

   $return = '
   <table class="table ListTable">

        <tr>
            <th>#</th>
            <th>عنوان</th>
            <th>عملیات</th>
        </tr>
        '.$list.'
                            
                    
    </table>
   ';

    print json_encode(array("status" => true, "message" => $return));
    exit();
}

add_action( 'wp_ajax_SetTypeCustomer', 'SetTypeCustomer' );
function SetTypeCustomer(){

   $uid = $_POST["uid"];
   $selectTypeUsers = $_POST["selectTypeUsers"];

   $SetTypeCustomer = update_user_meta($uid,"selectTypeUsers",$selectTypeUsers);


   print json_encode(array("status" => true, "message" => "انتخاب نوع مشتری با موفقیت انجام شد"));
   exit();
}

function SelectTypeUserName($uid){

    $selectTypeUsers = get_user_meta($uid,"selectTypeUsers",true);
    $return = '';
    if($selectTypeUsers == "white"){
        $return = '<span class="typeUser white">سفید</span>';
    }else if($selectTypeUsers == "black"){
        $return = '<span class="typeUser black">سیاه</span>';
    }else if($selectTypeUsers == "gold"){
        $return = '<span class="typeUser gold">طلایی</span>';
    }
    return $return;
}



// operator
add_action( 'wp_ajax_Save_as_Draft_order', 'Save_as_Draft_order' );
if( !function_exists('Save_as_Draft_order') ):

    function Save_as_Draft_order(){

        $uid = $_POST['uid'];
        $invoice_descripotion = $_POST['invoice_descripotion'];
        $customer_price = $_POST['customer_price'];
        $buy_price = $_POST['buy_price'];
        $piece_list = $_POST['piece_list'];
        $customer_price_list = $_POST['customer_price_list'];
        $buy_price_list = $_POST['buy_price_list'];
        $price_customer = $_POST['price_customer'];
        $cost_price = $_POST['cost_price'];
        $total_invoice = $_POST['total_invoice'];
        $negative_invoice = $_POST['negative_invoice'];
        $device_img1 = $_POST['device_img1'];

        $add = update_post_meta($uid,"invoice_descripotion",$invoice_descripotion);
        $add = update_post_meta($uid,"customer_price",$customer_price);
        $add = update_post_meta($uid,"buy_price",$buy_price);
        $add = update_post_meta($uid,"piece_list",$piece_list);
        $add = update_post_meta($uid,"customer_price_list",$customer_price_list);
        $add = update_post_meta($uid,"buy_price_list",$buy_price_list);
        $add = update_post_meta($uid,"price_customer",$price_customer);
        $add = update_post_meta($uid,"cost_price",$cost_price);
        $add = update_post_meta($uid,"total_invoice",$total_invoice);
        $add = update_post_meta($uid,"negative_invoice",$negative_invoice);
        $add = update_post_meta($uid,"device_img1",$device_img1);
        $add = update_post_meta($uid,"save_as_draft",1);


        $status = true;
        $message = "سفارش با موفقیت ذخیره گردید";
        print json_encode(array("status" => $status , "message" => $message));
        exit();

    }
endif;



add_action( 'wp_ajax_send_sms_delivery_to_customer', 'send_sms_delivery_to_customer' );
function send_sms_delivery_to_customer(){

    $id = $_POST["id"];
    $mobile = get_post_meta($id,"mobile",true);
    $customer_id = get_post_meta($id,"customer",true);
    $customer_name = get_user_meta($customer_id,"first_name",true);
    $device = wp_get_post_terms($id,"device")[0]->name;
    $sms_send_tech =  SendSMS($mobile,'CRMinorderInvoice',$customer_name,$device);
    $send_sms_delivery = update_post_meta($id,"send_sms_delivery",'send');
    $message = "ارسال پیامک آماده تحویل";
    $content = 'پیامک آماده تحویل برای مشتری ارسال شد' ;
    $subject = "پیامک آماده تحویل";
    $status_title = "پیامک آماده تحویل";
    $addContent = addStatusDescription($id,$subject,$content,get_current_user_id(),jdate("Y/m/d H:i"),$status_title);

    print json_encode(array("status" => true, "message" => " پیامک تحویل دستگاه ارسال شد"));
    exit();
}
?>
