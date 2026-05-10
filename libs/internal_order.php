<?php


// operator
add_action( 'wp_ajax_internal_order', 'internal_order' );

if( !function_exists('internal_order') ):

    function internal_order(){
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
        $shipping = $_POST['shipping'];
        $status_internal_order = $_POST['status_internal_order'];
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
                'post_type'=>'internal_orders',
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
            $shipping_get = get_post_meta($uid,"shipping",true);
            $status_internal_order_get = get_post_meta($uid,"status_internal_order",true);
            $state_get = wp_get_post_terms($uid,"state")[0]->term_id;
            $city_get = wp_get_post_terms($uid,"city")[0]->term_id;
            $device_get = wp_get_post_terms($uid,"device")[0]->term_id;
            $brand_get = wp_get_post_terms($uid,"brand")[0]->term_id;

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
            $add = update_post_meta($post_id,"shipping",$shipping);
            $add = update_post_meta($post_id,"status_internal_order",$status_internal_order);
            $cat = wp_set_post_terms($post_id,$state,"state");
            $cat = wp_set_post_terms($post_id,$city,"city");
            $cat = wp_set_post_terms($post_id,$device,"device");
            $cat = wp_set_post_terms($post_id,$brand,"brand");
            $update = update_post_meta($post_id,"change_status_s_r","");
            if($status_internal_order == "sent" || $status_internal_order== "receive"){
                if($technician == ""){
                }else{
                    $update = update_post_meta($post_id,"change_status_s_r","after_tech");
                }
            }

            if($technician == ""){}else{
                $subject = "ارسال کار برای تکنسین";
                $first_name_tech = get_user_meta($technician,"first_name",true);
                $content = "سفارش شماره ".$post_id." برای تکنسین ". $first_name_tech . " ارسال گردید";
                $status_title = "ارسال کار برای تکنسین";
                addStatusDescription($post_id,$subject,$content,get_current_user_id(),jdate("Y/m/d H:i"),$status_title);
                $add = update_post_meta($post_id,"send_technician",jdate("Y/m/d H:i"));
            }

            $sms = get_post_meta($post_id,"send_sms_customer",true);
            if($sms == 1){}else{
                if($order_type == "repair"){$order_type_name = "تعمیر";}
                else if($order_type == "buy"){$order_type_name = "خرید";}
                else if($order_type == "service"){$order_type_name = "سرویس در مجموعه";}
                $sms_send_customer =  SendSMS($mobile,get_option("sms")["customer_order"],$first_name,$order_type_name,$subscription);
                $update = update_post_meta($post_id,"send_sms_customer",1);
            }
            $sms_tec = get_post_meta($post_id,"send_sms_tec",true);

                $sms_send_tech =  SendSMS($tech_mobile,get_option("sms")["technician_order"],$subscription);
                $update = update_post_meta($post_id,"send_sms_tec",1);
            

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

// operator
add_action( 'wp_ajax_add_status_order_internal', 'add_status_order_internal' );
if( !function_exists('add_status_order_internal') ):

    function add_status_order_internal(){

        $uid = $_POST['uid'];
        $status = $_POST['status'];
        $invoice_descripotion = $_POST['invoice_descripotion'];
        $description_tech = $_POST['description_tech'];
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
        $add = update_post_meta($uid,"status_internal_order",$status);
        $add = update_post_meta($uid,"invoice_descripotion",$invoice_descripotion);
        $add = update_post_meta($uid,"description_tech",$description_tech);
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

        if($status == 4 || $status == 5){
            $update = update_post_meta($uid,"finish_order",date("Y/m/d"));
            $update = update_post_meta($uid,"finish_order_sh",jdate("Y/m/d H:i"));
            $update = update_post_meta($uid,"qc_approve_status",$status);
        }

        if($status == 5 ){

//            if($price_customer == "" || $cost_price == "" || $total_invoice == ""){}else{
//
//                $post_id = wp_insert_post(array(
//                    'post_title'=>'فاکتور جدید',
//                    'post_type'=>'financial',
//                ));
//                if($post_id){
//
//                    $technician = get_post_meta($uid,"technician",true);
//
//                    $add = update_post_meta($post_id,"order_id",$uid);
//                    $add = update_post_meta($post_id,"customer_id",$uid);
//                    $add = update_post_meta($post_id,"technician_id",$technician);
//                    $add = update_post_meta($post_id,"price_customer",$price_customer);
//                    $add = update_post_meta($post_id,"cost_price",$cost_price);
//                    $add = update_post_meta($post_id,"total_invoice",$total_invoice);
//                    if($negative_invoice == 0){
//                        $add = update_post_meta($post_id,"payment_status",0);
//                    }
//                    else{
//                        $add = update_post_meta($post_id,"payment_status",3);
//                    }
//
//                }
//
//            }

        }


            if($status == "sent"){
                $status_title = "ارسال بشود";
            }
            else if($status == "receive"){
                $status_title = "دریافت بشود";
            }
            else if($status == "qc"){
                $status_title = "واحد کنترل";
            }
            else if($status == 4){
                $status_title = "کنسل شده";
            }
            else if($status == 5){
                $status_title = "انجام کار";
            }

            $first_name = get_user_meta(get_current_user_id(),"first_name",true);
            $content = $description_tech. ' ' . $cancel_desc_other . ' '. $invoice_descripotion;
            $subject = "تغییر وضعیت سفارش به " . $status_title;
            $addContent = addStatusDescription($uid,$subject,$content,get_current_user_id(),jdate("Y/m/d H:i"),$status_title);




        $status = true;
        $message = "سفارش با موفقیت ویرایش گردید";
        print json_encode(array("status" => $status , "message" => $message));
        exit();

    }
endif;//



// operator
add_action( 'wp_ajax_internal_add_image', 'internal_add_image' );
if( !function_exists('internal_add_image') ):

    function internal_add_image(){

        $uid = $_POST['uid'];
        $image = $_POST['image'];
        $update = update_post_meta($uid,"order_images",$image);

        $status = true;
        $message = "تصاویر با موفقیت اضافه گردید.";
        print json_encode(array("status" => $status , "message" => $message));
        exit();

    }
endif;//


// operator
add_action( 'wp_ajax_qc_approve_order', 'qc_approve_order' );
if( !function_exists('qc_approve_order') ):

    function qc_approve_order(){

        $uid = $_POST['uid'];
        $status = get_post_meta($uid,"qc_approve_status",true);
        if($status == "4"){$d_s = "کنسل شده";}else if($status == "5"){$d_s = "انجام شده";}
        $content = 'تایید واحد کنترل برای وضعیت '.$d_s;

        $addContent = addStatusDescription($uid,"تایید واحد کنترل",$content,get_current_user_id(),jdate("Y/m/d H:i"),"تایید واحد کنترل");
        $update = update_post_meta($uid,"qc_status",1);
        $update = update_post_meta($uid,"status_internal_order","1000");

        if($status == 5 ){
            $update = update_post_meta($uid,"status","5");
            $invoice_descripotion = get_post_meta($uid,"invoice_descripotion",true);
            $discount = get_post_meta($uid,"discount",true);
            $price_customer = get_post_meta($uid,"price_customer",true);
            $cost_price = get_post_meta($uid,"cost_price",true);
            $total_invoice = get_post_meta($uid,"total_invoice",true);
            if($price_customer == "" || $cost_price == "" || $total_invoice == ""){}else{
                $technician = get_post_meta($uid,"technician",true);
                $post_id = wp_insert_post(array(
                    'post_title'=>'فاکتور جدید',
                    'post_type'=>'financial_internal',
                    "post_author" => $technician
                ));
                if($post_id){



                    $add = update_post_meta($post_id,"order_id",$uid);
                    $add = update_post_meta($post_id,"customer_id",$uid);
                    $add = update_post_meta($post_id,"technician_id",$technician);
                    $add = update_post_meta($post_id,"price_customer",$price_customer);
                    $add = update_post_meta($post_id,"cost_price",$cost_price);
                    $add = update_post_meta($post_id,"total_invoice",$total_invoice);
                    $add = update_post_meta($post_id,"type_fin","internal");
//                    if($negative_invoice == 0){
                    $add = update_post_meta($post_id,"payment_status",0);
//                    }
//                    else{
//                        $add = update_post_meta($post_id,"payment_status",3);
//                    }

                }
                $percent = get_user_meta($technician,"percent",true);
                $total_invoice_customer = ($total_invoice / 100) * $percent;
                $total_invoice_co = $total_invoice - $total_invoice_customer;
                $all_incoome = $total_invoice + $cost_price;

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
                $addContent = addStatusDescription($uid,$subject,$content,$technician,jdate("Y/m/d H:i"),$status_title);


            }

        }
        if($status == 4){
            $update = update_post_meta($uid,"status","4");
        }
        $status = true;
        $message = "سفارش با موفقیت تایید گردید.";
        print json_encode(array("status" => $status , "message" => $message));
        exit();

    }
endif;//


// operator
add_action( 'wp_ajax_qc_approve_send_recive', 'qc_approve_send_recive' );
if( !function_exists('qc_approve_send_recive') ):

    function qc_approve_send_recive(){

        $uid = $_POST['uid'];
        $status = $_POST['status'];
        $status_update = update_post_meta($uid,"status_internal_order","");
        $status_update = update_post_meta($uid,"status_send_recive",$status);
        if($status == "sent"){$d_s = "ارسال بشود";}else if($status == "receive"){$d_s = "دریافت بشود";}
        $content = 'تایید برای وضعیت '.$d_s;
        $addContent = addStatusDescription($uid,"تایید برای ارسال و دریافت",$content,get_current_user_id(),jdate("Y/m/d H:i"),"تایید برای ارسال و دریافت");
        $status_m = true;
        $message = "سفارش با موفقیت تایید گردید.";
        print json_encode(array("status" => $status_m , "message" => $message));
        exit();

    }
endif;//


// upload_device_image
add_action( 'wp_ajax_list_count_order_internal', 'list_count_order_internal' );

if( !function_exists('list_count_order_internal') ):

    function list_count_order_internal(){

        $all_count_count = new WP_Query(array(
            "post_type" => "internal_orders",
            "posts_per_page" => -1,
            "post_status" => "any",

        ));
        $all_count = $all_count_count->found_posts;
        $unknown_count_count = new WP_Query(array(
            "post_type" => "internal_orders",
            "posts_per_page" => -1,
            "post_status" => "any",
            "meta_query" => array(
                "relation" => "OR",
                array(
                    "key" => "technician",
                    "value" => "",
                    "compare" => "=",
                ),
                array(
                    "key" => "status_internal_order",
                    "value" => array("","4","5"),
                    "compare" => "IN",
                ),
            ),
        ));
        $unknown_count = $unknown_count_count->found_posts;
        $repairing_count_count = new WP_Query(array(
            "post_type" => "internal_orders",
            "posts_per_page" => -1,
            "post_status" => "any",
            "meta_query" => array(
                "relation" => "AND",
                array(
                    "key" => "technician",
                    "value" => "",
                    "compare" => "!=",
                ),
                array(
                    "key" => "status",
                    "value" => array("0","sent","receive"),
                    "compare" => "IN"
                )
            ),
        ));
        $repairing_count = $repairing_count_count->found_posts;
        $sending_count_count = new WP_Query(array(
            "post_type" => "internal_orders",
            "posts_per_page" => -1,
            "post_status" => "any",
            "meta_query" => array(
                array(
                    "key" => "status_internal_order",
                    "value" => "sent",
                    "compare" => "=",
                )
            ),
        ));
        $sending_count = $sending_count_count->found_posts;
        $payment_count_count = new WP_Query(array(
            "post_type" => "internal_orders",
            "posts_per_page" => -1,
            "post_status" => "any",
            "meta_query" => array(
                array(
                    "key" => "status_internal_order",
                    "value" => "payment_pend",
                    "compare" => "=",
                )
            ),
        ));
        $payment_count = $payment_count_count->found_posts;
        $reciving_count_count = new WP_Query(array(
            "post_type" => "internal_orders",
            "posts_per_page" => -1,
            "post_status" => "any",
            "meta_query" => array(
                array(
                    "key" => "status_internal_order",
                    "value" => "receive",
                    "compare" => "=",
                )
            ),
        ));
        $reciving_count = $reciving_count_count->found_posts;
        $qc_count_count = new WP_Query(array(
            "post_type" => "internal_orders",
            "posts_per_page" => -1,
            "post_status" => "any",
            "meta_query" => array(
                array(
                    "key" => "status_internal_order",
                    "value" => "qc",
                    "compare" => "=",
                )
            ),
        ));
        $qc_count = $qc_count_count->found_posts;
        $cancel_count_count = new WP_Query(array(
            "post_type" => "internal_orders",
            "posts_per_page" => -1,
            "post_status" => "any",
            "meta_query" => array(
                "relation" => "AND",
                array(
                    "key" => "status_internal_order",
                    "value" => "1000",
                    "compare" => "=",
                ),
                array(
                    "key" => "status",
                    "value" => "4",
                    "compare" => "=",
                )
            ),
        ));
        $cancel_count = $cancel_count_count->found_posts;
        $down_count_count = new WP_Query(array(
            "post_type" => "internal_orders",
            "posts_per_page" => -1,
            "post_status" => "any",
            "meta_query" => array(
                "relation" => "AND",
                array(
                    "key" => "status_internal_order",
                    "value" => "1000",
                    "compare" => "=",
                ),
                array(
                    "key" => "status",
                    "value" => "5",
                    "compare" => "=",
                )
            ),
        ));
        $down_count = $down_count_count->found_posts;

        print json_encode(array("status" => true ,
            "all_count" => $all_count,
            "unknown_count" => $unknown_count,
            "repairing_count" => $repairing_count,
            "sending_count" => $sending_count,
            "reciving_count" => $reciving_count,
            "qc_count" => $qc_count,
            "cancel_count" => $cancel_count,
            "down_count" => $down_count,
            "payment_pend_count" => $payment_count,
        ));
        exit();

    }
endif;


