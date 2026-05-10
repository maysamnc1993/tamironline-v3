<?php

// Template Name: Show Invoice Customer
get_header();


if(isset($_GET['oid'])){
    $uid = $_GET['oid'];
    $order = get_post($uid);
    $customer = get_post_meta($uid,"customer",true);
    $first_name = get_user_meta($customer,"first_name",true);
    $subscription = get_post_meta($uid,"subscription",true);
    $mobile = get_post_meta($uid,"mobile",true);
    $phone = get_post_meta($uid,"phone",true);
    $state = wp_get_post_terms($uid,"state")[0]->name;
    $city = wp_get_post_terms($uid,"city")[0]->name;
    $address = get_post_meta($uid,"address",true);
    $brand = wp_get_post_terms($uid,"brand")[0]->name;;
    $device = wp_get_post_terms($uid,"device")[0]->name;;
    $objection = get_post_meta($uid,"objection",true);
    $introduction = get_post_meta($uid,"introduction",true);
    $objection_list = '';
    foreach($objection as $item){
        $objection_list.='<span class="objection">'.$item.'</span>';
    }

    $objection_description = get_post_meta($uid,"objection_description",true);
    $technician = get_post_meta($uid,"technician",true);
    $technician_name = get_user_meta($technician,"first_name",true);
    $cart_img = get_user_meta($technician,"cart_img",true);

    $status = get_post_meta($uid,"status",true);
    $price_return = get_post_meta($uid,"price_return",true);

    $status = get_post_meta($uid,"status",true);
    $invoice_descripotion = update_post_meta($uid,"invoice_descripotion",true);
    $description_tech = get_post_meta($uid,"description_tech",true);
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

    $Have_invoice = get_post_meta($uid,"Have_invoice",true);
    $Type_Of_send_invoice = get_post_meta($uid,"Type_Of_send_invoice",true);
    $invoice_email = get_post_meta($uid,"invoice_email",true);
    $invoice_paper = get_post_meta($uid,"invoice_paper",true);
    $order_description_content = get_post_meta($uid,"order_description_content",true);
    $return_description = get_post_meta($uid,"return_description",true);
    $return_type = get_post_meta($uid,"return_type",true);
    $SaveToShayganStatus = get_post_meta($uid,"SaveToShayganStatus",true);
    $percent_operator = get_user_meta($technician,"percent",true);
    $total_show_invoice = + $cost_price + $total_invoice;
    $log_return = get_post_meta($uid,"log_return",true);
    $number = new Number2Word();
    $price_char = $number->numberToWords($total_show_invoice);

    $rand = rand(10000,900000);
    $send_sms = SendSMS($mobile,"auth-once-pass",$rand);


    if($total_invoice > 0){
        $co_side_price= ($total_invoice / 100) * $percent_operator;
        $co_side_price_1 = $total_invoice - $co_side_price;
    }else{
        $co_side_price = 0;
        $co_side_price_1 = 0;
    }
}

?>

<div class="User_Register_show_invoice" >
    <div class="box_register">

        <form action="#" method="post" id="check_register_user">
            <h2>برای مشاهده فاکتور، لطفا کد احراز هویت ارسال شده را وارد نمایید.</h2>
            <div class="form-group">
                <label>کد احراز هویت</label>
                <input type="hidden" id="old_verify_code" value="<?=$rand?>">
                <input type="text" class="form-control validate" data-validate="empty" id="verify_code">
            </div>
            <input type="submit" class="btn" value="ارسال">
        </form>

    </div>
</div>

<div class="customer_view_order">

    <div class="box_order">

<!--        <img src="https://crm.tamironline.com/wp-content/uploads/2021/09/logo-tamironline.png">-->
        <h2>صورت حساب خدمات تعمیرآنلاین</h2>

        <div class="Box_show_order">

            <ul class="ShowDetailOreder">

                <li>
                    <span>نام مشتری:</span>
                    <b><?=$first_name?></b>
                </li>
                <li>
                    <span>کد اشتراک</span>
                    <b><?=$subscription?></b>
                </li>
                <li>
                    <span>شماره همراه</span>
                    <b><?=$mobile?></b>
                </li>
                <li>
                    <span>شماره ثابت</span>
                    <b><?=$phone?></b>
                </li>
                <li class="full">
                    <span>آدرس</span>
                    <b><?=$address?></b>
                </li>
                <li>
                    <span>دستگاه</span>
                    <b><?=$device?></b>
                </li>
                <li>
                    <span>تاریخ ثبت</span>
                    <b><?=get_the_date("Y/m/d",$uid)?></b>
                </li>

                <li class="full">
                    <span>توضیحات</span>
                    <b><?=$invoice_descripotion?></b>
                </li>
                <li >
                    <span>مبلغ به عدد</span>
                    <b><?=number_format($total_show_invoice)?> تومان</b>
                </li>
                <li>
                    <span>مبلغ به حروف</span>
                    <b><?=$price_char;?> تومان</b>
                </li>

                <li class="full">
                   <div class="technican_cart" style="background-image: url(<?=$cart_img?>)"></div>
                </li>
            </ul>
            <div class="footer_order">
                <b>لطفا موارد ذیل را مطالعه نمایید:</b>
                <?=get_option("invoice_descritpion_show")?>


            </div>

        </div>

    </div>

</div>


<?php
get_footer();

?>