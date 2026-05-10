<?php
// operator
add_action( 'wp_ajax_settingSMS', 'settingSMS' );

if( !function_exists('settingSMS') ):

    function settingSMS(){
        $token = $_POST['token'];
        $customer_order = $_POST['customer_order'];
        $technician_order = $_POST['technician_order'];
        $customer_cancel_order = $_POST['customer_cancel_order'];
        $tech_wallet_charge = $_POST['tech_wallet_charge'];

        $settingSMS = array(
            "token" => $token,
            "customer_order" => $customer_order,
            "technician_order" => $technician_order,
            "customer_cancel_order" => $customer_cancel_order,
            "tech_wallet_charge" => $tech_wallet_charge,
        );

        update_option("sms",$settingSMS);
        print json_encode(array("status" => true , "message" => "تنظیمات به روز رسانی شد."));
        exit();

    }
endif;

// objectionsList
add_action( 'wp_ajax_introductionList', 'introductionList' );

if( !function_exists('introductionList') ):

    function introductionList(){
        $objectionsList = $_POST['objectionsList'];
        update_option("introductionList",$objectionsList);
        print json_encode(array("status" => true , "message" => "تنظیمات به روز رسانی شد."));
        exit();

    }
endif;

// objectionsList
add_action( 'wp_ajax_cost_setting', 'cost_setting' );

if( !function_exists('cost_setting') ):

    function cost_setting(){
        $objectionsList = $_POST['objectionsList'];
        update_option("cost_list",$objectionsList);
        print json_encode(array("status" => true , "message" => "تنظیمات به روز رسانی شد."));
        exit();

    }
endif;
add_action( 'wp_ajax_objectionsList', 'objectionsList' );

if( !function_exists('objectionsList') ):

    function objectionsList(){
        $objectionsList = $_POST['objectionsList'];
        update_option("objectionsList",$objectionsList);
        print json_encode(array("status" => true , "message" => "تنظیمات به روز رسانی شد."));
        exit();

    }
endif;

add_action( 'wp_ajax_ShippingSetting', 'ShippingSetting' );
if( !function_exists('ShippingSetting') ):

    function ShippingSetting(){
        $objectionsList = $_POST['objectionsList'];
        update_option("shipping_list",$objectionsList);
        print json_encode(array("status" => true , "message" => "تنظیمات به روز رسانی شد."));
        exit();

    }
endif;

// accounting_setting
add_action( 'wp_ajax_accounting_setting', 'accounting_setting' );

if( !function_exists('accounting_setting') ):

    function accounting_setting(){
        $type_of_desc_acc = $_POST['type_of_desc_acc'];
        $title_of_desc_acc = $_POST['title_of_desc_acc'];
        update_option("type_of_desc_acc",$type_of_desc_acc);
        update_option("title_of_desc_acc",$title_of_desc_acc);
        print json_encode(array("status" => true , "message" => "تنظیمات به روز رسانی شد."));
        exit();

    }
endif;

// Payment Settign
add_action( 'wp_ajax_settingPayment', 'settingPayment' );

if( !function_exists('settingPayment') ):

    function settingPayment(){

        $type_of_payment1 = $_POST['type_of_payment1'];
        $token1 = $_POST['token1'];
        $max_pay1 = $_POST['max_pay1'];

        $type_of_payment2 = $_POST['type_of_payment2'];
        $token2 = $_POST['token2'];
        $max_pay2 = $_POST['max_pay2'];

        $type_of_payment3 = $_POST['type_of_payment3'];
        $token3 = $_POST['token3'];
        $max_pay3 = $_POST['max_pay3'];

        $wallet1 = $_POST['wallet1'];
        $wallet1_charge = $_POST['wallet1_charge'];

        $wallet2 = $_POST['wallet2'];
        $wallet2_charge = $_POST['wallet2_charge'];

        $wallet3 = $_POST['wallet3'];
        $wallet3_charge = $_POST['wallet3_charge'];

        $wallet4 = $_POST['wallet4'];
        $wallet4_charge = $_POST['wallet4_charge'];

        $wallet5 = $_POST['wallet5'];
        $wallet5_charge = $_POST['wallet5_charge'];

        $settingPayment = array(
            "type_of_payment1" => $type_of_payment1,
            "token1" => $token1,
            "max_pay1" => $max_pay1,
            "type_of_payment2" => $type_of_payment2,
            "token2" => $token2,
            "max_pay2" => $max_pay2,
            "type_of_payment3" => $type_of_payment3,
            "token3" => $token3,
            "max_pay3" => $max_pay3,
            "wallet1" => $wallet1,
            "wallet1_charge" => $wallet1_charge,
            "wallet2" => $wallet2,
            "wallet2_charge" => $wallet2_charge,
            "wallet3" => $wallet3,
            "wallet3_charge" => $wallet3_charge,
            "wallet4" => $wallet4,
            "wallet4_charge" => $wallet4_charge,
            "wallet5" => $wallet5,
            "wallet5_charge" => $wallet5_charge,
        );

        update_option("payment",$settingPayment);
        print json_encode(array("status" => true , "message" => "تنظیمات به روز رسانی شد."));
        exit();

    }
endif;

// cancel_lsit
add_action( 'wp_ajax_cancel_lsit', 'cancel_lsit' );

if( !function_exists('cancel_lsit') ):

    function cancel_lsit(){

        $cancle_desc_1 = $_POST['cancle_desc_1'];
        $cancle_desc_2 = $_POST['cancle_desc_2'];
        $cancle_desc_3 = $_POST['cancle_desc_3'];
        $cancle_desc_4 = $_POST['cancle_desc_4'];
        $cancle_desc_5 = $_POST['cancle_desc_5'];
        $cancle_desc_6 = $_POST['cancle_desc_6'];
        $cancle_desc_7 = $_POST['cancle_desc_7'];
        $cancle_desc_8 = $_POST['cancle_desc_8'];
        $cancle_desc_9 = $_POST['cancle_desc_9'];
        $cancle_desc_10 = $_POST['cancle_desc_10'];
        $cancle_desc_11 = $_POST['cancle_desc_11'];
        $cancle_desc_12 = $_POST['cancle_desc_12'];
        $cancle_desc_13 = $_POST['cancle_desc_13'];
        $cancle_desc_14 = $_POST['cancle_desc_14'];
        $cancle_desc_15 = $_POST['cancle_desc_15'];



        $settingCancel = array(
            "cancle_desc_1" => $cancle_desc_1,
            "cancle_desc_2" => $cancle_desc_2,
            "cancle_desc_3" => $cancle_desc_3,
            "cancle_desc_4" => $cancle_desc_4,
            "cancle_desc_5" => $cancle_desc_5,
            "cancle_desc_6" => $cancle_desc_6,
            "cancle_desc_7" => $cancle_desc_7,
            "cancle_desc_8" => $cancle_desc_8,
            "cancle_desc_9" => $cancle_desc_9,
            "cancle_desc_10" => $cancle_desc_10,
            "cancle_desc_11" => $cancle_desc_11,
            "cancle_desc_12" => $cancle_desc_12,
            "cancle_desc_13" => $cancle_desc_13,
            "cancle_desc_14" => $cancle_desc_14,
            "cancle_desc_15" => $cancle_desc_15,
        );

        update_option("cancel",$settingCancel);
        print json_encode(array("status" => true , "message" => "تنظیمات به روز رسانی شد."));
        exit();

    }
endif;


// cancel_lsit
add_action( 'wp_ajax_invoice_setting', 'invoice_setting' );

if( !function_exists('invoice_setting') ):

    function invoice_setting(){

        $invoice_descritpion_show = $_POST['invoice_descritpion_show'];
        $print_invoice_descritpion_show = $_POST['print_invoice_descritpion_show'];


        update_option("invoice_descritpion_show",$invoice_descritpion_show);
        update_option("print_invoice_descritpion_show",$print_invoice_descritpion_show);
        print json_encode(array("status" => true , "message" => "تنظیمات به روز رسانی شد."));
        exit();

    }
endif;


// cancel_lsit
add_action( 'wp_ajax_addHappyCallTech', 'addHappyCallTech' );

if( !function_exists('addHappyCallTech') ):

    function addHappyCallTech(){


        $key = $_POST['key'];
        $title =  $_POST['title'];

        $HappyCallTech = get_option("HappyCallTech");
        if($HappyCallTech == ""){
            $HappyCallTech = array();
        }

        if($key == ""){
            array_push($HappyCallTech,$title);
        }else{
            $HappyCallTech[$key] = $title;
        }

        update_option("HappyCallTech",$HappyCallTech);
        print json_encode(array("status" => true , "message" => "تنظیمات به روز رسانی شد."));
        exit();

    }
endif;


// cancel_lsit
add_action( 'wp_ajax_addHappyCallCustomer', 'addHappyCallCustomer' );

if( !function_exists('addHappyCallCustomer') ):

    function addHappyCallCustomer(){


        $key = $_POST['key'];
        $title =  $_POST['title'];

        $HappyCallTech = get_option("HappyCallCustomer");
        if($HappyCallTech == ""){
            $HappyCallTech = array();
        }

        if($key == ""){
            array_push($HappyCallTech,$title);
        }else{
            $HappyCallTech[$key] = $title;
        }

        update_option("HappyCallCustomer",$HappyCallTech);
        print json_encode(array("status" => true , "message" => "تنظیمات به روز رسانی شد."));
        exit();

    }
endif;