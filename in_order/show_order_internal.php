<?php
IFLoggedAndVerify("order_list");
// Template Name: Order View Internal
get_header();

if(isset($_GET['uid'])){
    $uid = $_GET['uid'];
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
    $qc_status = get_post_meta($uid,"qc_status",true);
    $hc_tech = get_post_meta($uid,"hc_tech",true);
    $hc_customer = get_post_meta($uid,"hc_customer",true);
    $hc_customer_value = get_post_meta($uid,"hc_customer_value",true);
    $hc_tech_value = get_post_meta($uid,"hc_tech_value",true);
    $objection_list = '';
    foreach($objection as $item){
        $objection_list.='<span class="objection">'.$item.'</span>';
    }

    $objection_description = get_post_meta($uid,"objection_description",true);
    $technician = get_post_meta($uid,"technician",true);
    $technician_name = get_user_meta($technician,"first_name",true);

    $status = get_post_meta($uid,"status",true);
    $price_return = get_post_meta($uid,"price_return",true);

    $status = get_post_meta($uid,"status",true);
    $status_internal_order = get_post_meta($uid,"status_internal_order",true);
    $invoice_descripotion = get_post_meta($uid,"invoice_descripotion",true);
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
    $order_images = get_post_meta($uid,"order_images",true);
    $log_return = get_post_meta($uid,"log_return",true);



    if($total_invoice > 0){
        $co_side_price= ($total_invoice / 100) * $percent_operator;
        $co_side_price_1 = $total_invoice - $co_side_price;
    }else{
        $co_side_price = 0;
        $co_side_price_1 = 0;
    }
}
if($return_type == '' || $status == "5"){}else{
    $piece_list = '';
    $customer_price_list = '';
    $buy_price_list = '';
    $hire = '';
    $transportation = '';
    $discount = '';
    $price_customer = 0;
    $price_customer = 0;
    $total_invoice = 0;
    $co_side_price = 0;
    $co_side_price_1 = 0;
}
$cancel = get_option("cancel");
$HappyCallCustomer = get_option("HappyCallCustomer");
$HappyCallTech = get_option("HappyCallTech");
?>

<div class="modal fade" id="SelectTypeUserPopUp" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="SelectType">
                <h1>تعیین نوع مشتری : <?=$first_name?></h1>
                <form action="#" method="post" id="SelectTypeUserFOrmAJax">
                    <input type="hidden" id="uid" value="<?=$customer?>">
                    <ul class="SelectTypeUserSwitch">

                        <li class="white">
                            <input type="radio" id="selectTypeUsers" name="selectTypeUsers" value="withe">
                            <span>سفید</span>
                        </li>
                        <li class="black">
                            <input type="radio" id="selectTypeUsers" name="selectTypeUsers" value="black">
                            <span>سیاه</span>
                        </li>
                        <li class="gold">
                            <input type="radio" id="selectTypeUsers" name="selectTypeUsers" value="gold">
                            <span>طلایی</span>
                        </li>

                    </ul>
                    <input type="submit" class="btn" value="ارسال">
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade ShowCUstomerOrder" id="PopUpCustomerList" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="customerList">
                <h1>لیست مشتریان</h1>
                <div class="List_of_customer">

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade ShowCUstomerOrder" id="PopUpHappyCallCustomer" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="hcList">
                <h1>هپی کال مشتریان </h1>
                <form action="#" method="post" id="OrderHappyCallCustomerAjax">
                    <input type="hidden" id="order_id" value="<?=$_GET['uid'];?>">
                    <?php
                    foreach($HappyCallCustomer as $key => $value){
                        if($hc_customer_value == ""){$disabled = '';}else{$disabled = "disabled";}
                        echo '
                            
                                <div class="ListOfCustomerHappyCall">
                                    <span>'.$value.'</span>
                                    <div class="Input">
                                        <label>امتیاز (از ۱ تا ۱۰۰)</label>
                                        <input type="hidden" id="order_hc_customer_key" value="'.$key.'">
                                        <input type="text" class="form-control" id="Order_hc_customer_point" '.$disabled.'  value="'.$hc_customer_value[$key].'">
                                    </div>
                                </div>
                            
                            ';
                    }
                    ?>
                    <input type="submit" class="btn" value="ارسال" <?=$disabled?>>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade ShowCUstomerOrder" id="PopUpHappyCallTech" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="hcList">
                <h1>هپی کال تکنسین </h1>
                <form action="#" method="post" id="OrderHappyCallTechAjax">
                    <input type="hidden" id="order_id" value="<?=$_GET['uid'];?>">
                    <?php
                    foreach($HappyCallTech as $key => $value){
                        if($hc_tech_value == ""){$disabled = '';}else{$disabled = "disabled";}
                        echo '
                            
                                <div class="ListOfCustomerHappyCall">
                                    <span>'.$value.'</span>
                                    <div class="Input">
                                        <label>امتیاز (از ۱ تا ۱۰۰)</label>
                                        <input type="hidden" id="order_hc_tech_key" value="'.$key.'">
                                        <input type="text" class="form-control" id="Order_hc_tech_point" '.$disabled.'  value="'.$hc_tech_value[$key].'">
                                    </div>
                                </div>
                            
                            ';
                    }
                    ?>
                    <input type="submit" class="btn" value="ارسال" <?=$disabled?>>
                </form>
            </div>
        </div>
    </div>
</div>



<section class="DashboardSection">

    <?=NavDashboard();?>
    <div class="main">

        <?php
        if(CheckAccess("payment_order_internal")){
            if($status == "5" || $status == "4"){?>
                <div class="AddTOShaygan">
                    <form action="#" method="post" id="SaveToShaygan">
                        <input type="hidden" id="oid" value="<?=$_GET['uid']?>">
                        <h1>سفارش پرداخت شده است؟</h1>
                        <select class="form-control validate" id="SaveToShayganStatus">
                            <option <?php if($SaveToShayganStatus == ""){echo 'selected';}?> value="">خیر</option>
                            <option <?php if($SaveToShayganStatus == 1){echo 'selected';}?> value="1">بلی</option>
                        </select>
                        <input class="btn" type="submit" value="ارسال">
                    </form>
                </div>
                <?php
            }
            ?>
        <?php } ?>
        <?php
        if (CheckAccess("accept_qc_order_internal")) {
            if($qc_status == 1){}else{
                ?>
                <div class="AddTOShaygan">
                    <form action="#" method="post" id="Qc_Start">
                        <input type="hidden" id="oid" value="<?=$_GET['uid']?>">
                        <h1>تایید واحد کنترل</h1>
                        <input class="btn" type="submit" value="برای تایید این سفارش کلیک نمایید.">
                    </form>
                </div>
                <?php
            }
        }
        ?>

        <?php
        if($status_internal_order == "sent" || $status_internal_order == "receive"){

            if($status_internal_order == "sent"){$status_order_txt = "ارسال بشود";}
            if($status_internal_order == "receive"){$status_order_txt = "دریافت بشود";}
            ?>
            <div class="AddTOShaygan">
                <form action="#" method="post" id="send_recive_status">
                    <input type="hidden" id="oid" value="<?=$_GET['uid']?>">
                    <input type="hidden" id="status" value="<?=$status_internal_order?>">
                    <h1>تایید برای <?=$status_order_txt?> </h1>
                    <input class="btn" type="submit" value="برای تایید این سفارش کلیک نمایید.">
                </form>
            </div>
            <?php
        }

        ?>


        <div class="messageCRM msg">
            <div class="CrmBTnMessage">پیام ها</div>
<!--            <div class="LIst_OF_Order_user" id="--><?php //=$customer?><!--" data-user-mobile="--><?php //=$mobile?><!--" data-order-type="internal">سفارشات قبلی <i>0</i></div>-->
<!--            --><?php
//
//            if(CheckAccess("order_internal_happycall_customer")){
//                if($status == "5" || $status == "4"){
//                    if ($hc_customer == "") {
//                        $per_hc_customer = '';
//                    } else {
//                        $per_hc_customer = '('.$hc_customer.'%)';
//                    }
//                    echo '<div class="HappyCallCustomer">هپی کال مشتریان'.$per_hc_customer.'</div>';
//                }
//            }
//
//            ?>

            <div class="CrmListMessage">
                <form action="#" method="post" id="AddMessageForOrder">
                    <input type="hidden" id="uid" value="<?=$uid?>">
                    <div class="form-group">
                        <label>توضیحات</label>
                        <textarea class="form-control" id="descriptionMSG"></textarea>
                    </div>
                    <input type="submit" class="btn" value="ارسال پیام">
                    <a id="allMessage" href="#messageListOrder">مشاهده همه</a>

                </form>
            </div>
        </div>
        <div class="messageCRM note">
            <div class="CrmBTnMessage">یاداشت ها</div>

            <?php

            if(CheckAccess("order_internal_happycall_tech")){
                if($status == "5" || $status == "4"){
                    if ($hc_tech == "") {
                        $per_hc_tech = '';
                    } else {
                        $per_hc_tech = '('.$hc_tech.'%)';
                    }
                    echo '<div class="HappyCalltech">هپی کال تکنسین'.$per_hc_tech.'</div>';
                }
            }

            if(CheckAccess("order_internal_select_type_user")){
                echo '<div class="SelectTypeUser">تعیین نوع مشتری</div>';
            }

            ?>
            <div class="CrmListMessage">
                <form action="#" method="post" id="AddNoteForOrder">
                    <input type="hidden" id="uid" value="<?=$uid?>">
                    <div class="form-group">
                        <label>توضیحات</label>
                        <textarea class="form-control" id="descriptionMSG"></textarea>
                    </div>
                    <input type="submit" class="btn" value="ارسال یاداشت">
                    <a id="allMessage" href="#messageListOrder">مشاهده همه</a>

                </form>
            </div>
        </div>
        <div class="content">
            <h1 class="title">مشاهده سفارش <?=$uid?>
                | مشتری <?=$first_name?>
                | کد اشتراک <?=$subscription?>
            </h1>

            <ul class="list_of_img">
                <?php
                for($i = 0 ; $i < count($order_images);$i++){
                    if($order_images[$i] == ""){}else{
                        echo '<li><a href="'.$order_images[$i].'" target="_blank">دانلود تصویر</a></li>';
                    }
                }
                ?>
            </ul>
            <form action="#" method="post" id="SendImageOrder">
                <input type="hidden" id="uid" value="<?=$uid?>">
                <h4>افزودن تصاویر</h4>
                <div class="placeholder_image_order">
                    <?php
                    for($i = 0 ; $i < count($order_images);$i++){
                        if($order_images[$i] == ""){}else{
                            echo '
                                        <div class="form-group third">
                                            <div class="row">
            
                                                <div class="form-group ">
                                                    <label>تصویر</label>
                                                    <div class="Img_selector">
                                                        <div class="loading">
            
                                                        </div>
                                                        <div class="thumb" style="background-image: url('.$order_images[$i].')"></div>
                                                        <div class="box_upload">
                                                            '.file_get_contents(wp_get_attachment_url(97)).'
                                                            <span>برای بارگذاری تصویر (بکشید / کلیک کنید)</span>
                                                            <input class="imgUpload" type="file" >
                                                            <input class="source_image" type="hidden" id="order_images"  value="'.$order_images[$i].'">
                                                        </div>
                                                    </div>
                                                </div>
            
                                            </div>
            
                                        </div>
                                        ';
                        }
                    }
                    ?>
                </div>
                <div class="add_image_order_internal">افزودن تصویر</div>
                <input type="submit" class="btn" value="ذخیره تصاویر">
            </form>

            <?php
            if(CheckAccess("order_return")){
                if($status == "5" || $status == "4"){
                    ?>

                    <div class="returnOrderStatus">

                        <div class="returnBTN">بازگشت سفارش (تغییر وضعیت سفارش) </div>
                        <form action="#" method="post" id="ReturnOrderAjax">
                            <input type="hidden" value="<?=$uid?>" id="uid" >
                            <h3>برای برگشت سفارش وضعیت سفارش را تغییر و دلیل آن را بنویسید</h3>
                            <div class="form-group">
                                <label>نوع بازگشت سفارش</label>
                                <select class="form-control validate" data-validate="empty" id="return_type">
                                    <option value="">نوع بازگشت را انتخاب نمایید.</option>
                                    <option value="1">برگشت انجام شده</option>
                                    <option value="2">برگشت کنسل شده</option>

                                </select>
                            </div>
                            <div class="form-group">
                                <label>توضیح</label>
                                <textarea class="form-control valdiate" data-validate="empty" id="return_description"></textarea>
                            </div>
                            <input type="submit" class="btn" value="بازگشت سفارش">
                        </form>

                    </div>
                    <?php
                }
            }
            ?>


            <table class="Show_Order table">

                <tr>
                    <th>مشتری</th>
                    <td><?=$first_name?>  <?=SelectTypeUserName($customer);?></td>
                    <th>اشتراک</th>
                    <td><?=$subscription?></td>
                </tr>

                <tr>
                    <th>شماره موبایل</th>
                    <td><?=$mobile?></td>
                    <th>شماره ثابت</th>
                    <td><?=$phone?></td>
                </tr>

                <tr>
                    <th>استان</th>
                    <td><?=$state?></td>
                    <th>شهر</th>
                    <td><?=$city?></td>
                </tr>

                <tr>
                    <th>آدرس</th>
                    <td colspan="3"><?=$address?></td>
                </tr>

                <tr>
                    <th>نوع دستگاه</th>
                    <td><?=$device?></td>
                    <th>برند</th>
                    <td><?=$brand?></td>
                </tr>

                <tr>
                    <th>ایراد دستگاه</th>
                    <td><?=$objection_list?></td>
                    <th >توضیحات</th>
                    <td style="width:30%"><?=$objection_description?></td>
                </tr>
                <tr>
                    <th>تکنسین</th>
                    <td><?=$technician_name?></td>
                    <th>نحوه آشنایی</th>
                    <td><?=$introduction?></td>
                </tr>


            </table>

            <?php
            if(CheckAccess("order_status")){
                $active = '';
            }else{
                $active = "disabled";
            }
            ?>

            <?php
            if($status == 5){
                ?>

                <?php
                if($device_image_input == ''){}else{
                    echo '
                                        <div class="ThumbDevice" style="background-image:url('.$device_image_input.')"></div>
                                        <a href="'.$device_image_input.'" class="DownloadIMG" target="_blank">دانلود تصویر دستگاه</a>
                                    ';
                }
                ?>

                <?php
                if (CheckAccess("qc_order_internal")) {
                    if($Have_invoice == ""){
                        $dontEdit = "";
                    }
                    else{
                        $dontEdit = "dontEdit";
                    }

                    if($status_internal_order == 1000){
                        $dontEdit = "dontEdit";
                    }


                    ?>
                    <form action="#" id="request_invoice" class="<?=$dontEdit?>" style="display:none;">
                        <h3>آیا مشتری فاکتور میخواهد؟</h3>
                        <ul class="Have_invoice">
                            <li id="1" class="yes <?php if($Have_invoice == 1){echo 'active';}?>">
                                <input type="radio" id="Have_invoice" name="Have_invoice"  <?php if($Have_invoice == 1){echo 'checked';}?> value="1">
                                <span>بلی</span>
                            </li>
                            <li id="0" class="no  <?php if($Have_invoice == 0){echo 'active';}?>">
                                <input type="radio" id="Have_invoice" name="Have_invoice"  <?php if($Have_invoice == 0){echo 'checked';}?> value="0">
                                <span>خیر</span>
                            </li>

                        </ul>

                        <div class="next_step  <?php if($Have_invoice == 1){echo 'active';}?>">
                            <h3>نوع ارسال فاکتور را انتخاب نمایید؟</h3>
                            <ul class="Type_Of_send_invoice">
                                <li id="sms" class="<?php if($Type_Of_send_invoice == "sms"){echo 'active';}?>">
                                    <input type="radio" id="Type_Of_send_invoice"  <?php if($Type_Of_send_invoice == "sms"){echo 'checked';}?> name="Type_Of_send_invoice" value="sms">
                                    <span>ارسال صورت حساب با پیامک</span>
                                </li>
                                <li id="email" class=" <?php if($Type_Of_send_invoice == "email"){echo 'active';}?>">
                                    <input type="radio" id="Type_Of_send_invoice"  <?php if($Type_Of_send_invoice == "email"){echo 'checked';}?> name="Type_Of_send_invoice" value="email">
                                    <span>ارسال صورت حساب با ایمیل</span>
                                </li>
                                <li id="paper" class="  <?php if($Type_Of_send_invoice == "paper"){echo 'active';}?>">
                                    <input type="radio" id="Type_Of_send_invoice"  <?php if($Type_Of_send_invoice == "paper"){echo 'checked';}?> name="Type_Of_send_invoice" value="paper">
                                    <span>صورت حساب کاغذی</span>
                                </li>
                            </ul>
                        </div>

                        <div class="next_step_two ">
                            <div class="form-group email  <?php if($Type_Of_send_invoice == "email"){echo 'active';}?>">
                                <label>آدرس ایمیل برای ارسال صورت حساب</label>
                                <input type="text" class="form-control" id="invoice_email" value="<?=$invoice_email?>">
                            </div>
                            <div class="form-group paper  <?php if($Type_Of_send_invoice == "paper"){echo 'active';}?>">
                                <label>بارگذاری صورت حساب کاغذی</label>
                                <div class="Img_selector">
                                    <div class="loading">

                                    </div>
                                    <div class="thumb"  style="background-image:url(<?=$invoice_paper?>) "></div>
                                    <div class="box_upload">
                                        <?=file_get_contents(wp_get_attachment_url(97));?>
                                        <span>برای بارگذاری تصویر (بکشید / کلیک کنید)</span>
                                        <input class="imgUpload" type="file" >
                                        <input class="source_image" type="hidden" id="invoice_paper"  value="<?=$invoice_paper?>">
                                    </div>
                                </div>
                            </div>

                            <?php
                            if($invoice_paper == ""){}else{
                                echo '<a href="'.$invoice_paper.'" target="_blank" id="DownloadInvoice"> دانلود صورت حساب</a>';
                            }
                            ?>
                        </div>

                        <input type="submit" class="btn" value="ارسال">

                    </form>

                    <?php
                }}
            ?>

            <div class="Detai_order tech">

                <?php
                if($negative_invoice == 1) {
                    ?>

                    <form action="#" method="post" id="AllowNagetiveInvoice">
                        <h2>فاکتور منفی می باشد آیا مورد تایید می باشد</h2>
                        <input type="hidden" id="uid" value="<?=$uid?>">
                        <ul class="selectStatusInput">
                            <li class="yes">
                                <label>
                                    <input type="radio" class="form-control" id="allow_invoice" value="1">
                                    <span class="">مورد تایید می باشد</span>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" class="form-control" id="allow_invoice" value="0">
                                    <span class="">مورد تایید نمی باشد دوباره فاکتور صادر شود</span>
                                </label>
                            </li>
                        </ul>
                        <input type="submit" class="btn" value="ارسال">
                    </form>

                    <?php
                }
                ?>

                <?php
                if($status_internal_order == 1000){
                    $dontEditNOW = "dontEdit";
                }
                ?>

                <form action="#" method="post" id="internal_Tech_DetailAjax" class="<?=$dontEditNOW?>">



                    <input type="hidden" id="uid" value="<?=$uid?>">
                    <span>وضعیت کار</span>

                    <div class="list_of_status">
                        <?php if(CheckAccess("send_order_internal")){?>
                            <div class="form-group two">
                                <label class="<?php if($status == "sent" ){echo 'active';}?>">
                                    <span>ارسال بشود</span>
                                    <input <?=$active?> <?php if($status == "sent" ){echo 'checked';}?> type="radio" name="status" id="status" value="sent" data-action="desc">
                                </label>
                            </div>
                        <?php } ?>
                        <?php if(CheckAccess("receive_order_internal")){?>
                            <div class="form-group two">
                                <label class="<?php if($status == "receive" ){echo 'active';}?>">
                                    <span>دریافت بشود</span>
                                    <input <?=$active?> <?php if($status == "receive" ){echo 'checked';}?> type="radio" name="status" id="status" value="receive" data-action="desc">
                                </label>
                            </div>
                        <?php } ?>
                        <?php if(CheckAccess("payment_pend")){?>
                            <div class="form-group two">
                                <label class="<?php if($status == "payment_pend" ){echo 'active';}?>">
                                    <span>منتظر پرداخت</span>
                                    <input <?=$active?> <?php if($status == "payment_pend" ){echo 'checked';}?> type="radio" name="status" id="status" value="payment_pend" data-action="desc">
                                </label>
                            </div>
                        <?php } ?>
                        <?php if(CheckAccess("qc_order_internal")){?>
                            <div class="form-group two">
                                <label class="<?php if($status == "qc" ){echo 'active';}?>">
                                    <span>واحد کنترل</span>
                                    <input <?=$active?>  <?php if($status == "qc" ){echo 'checked';}?> type="radio" name="status" id="status" value="qc"  data-action="desc">
                                </label>
                            </div>
                        <?php } ?>
                        <?php if(CheckAccess("cancel_order_internal")){?>
                            <div class="form-group two">
                                <label class="<?php if($status == 4 ){echo 'active';}?>">
                                    <span>کنسل شده</span>
                                    <input <?=$active?>  <?php if($status == 4 ){echo 'checked';}?> type="radio" name="status" id="status" value="4"  data-action="cancel">
                                </label>
                            </div>
                        <?php } ?>
                        <?php if(CheckAccess("done_order_internal")){?>
                            <div class="form-group two">
                                <label class="<?php if($status == 5 ){echo 'active';}?>">
                                    <span>پایان کار</span>
                                    <input <?=$active?>  <?php if($status == 5 ){echo 'checked';}?> type="radio" name="status" id="status" value="5"  data-action="done">
                                </label>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="box_status_return <?php if(in_array($status,array(1,2,3))){echo 'active';}?>" id="desc">

                        <div class="form-group">

                            <label>توضیحات</label>
                            <textarea class="form-control " id="description_tech"><?=$description_tech?></textarea>

                        </div>

                    </div>

                    <div class="box_status_return <?php if($status == 4 ){echo 'active';}?>" id="cancel">
                        <label>علت کنسلی</label>

                        <?php if($cancel["cancle_desc_1"] == ""){}else{?>
                            <div class="form-group">

                                <input <?php if($cancel_desc == $cancel["cancle_desc_1"]){echo 'checked';}?> type="radio" id="cancel_desc" name="cancel_desc" value="<?=$cancel["cancle_desc_1"]?>">
                                <span><?=$cancel["cancle_desc_1"]?></span>
                            </div>
                        <?php } ?>
                        <?php if($cancel["cancle_desc_2"] == ""){}else{?>
                            <div class="form-group">

                                <input <?php if($cancel_desc == $cancel["cancle_desc_2"]){echo 'checked';}?> type="radio" id="cancel_desc" name="cancel_desc" value="<?=$cancel["cancle_desc_2"]?>">
                                <span><?=$cancel["cancle_desc_2"]?></span>
                            </div>
                        <?php } ?>
                        <?php if($cancel["cancle_desc_3"] == ""){}else{?>
                            <div class="form-group">

                                <input <?php if($cancel_desc == $cancel["cancle_desc_3"]){echo 'checked';}?> type="radio" id="cancel_desc" name="cancel_desc" value="<?=$cancel["cancle_desc_3"]?>">
                                <span><?=$cancel["cancle_desc_3"]?></span>
                            </div>
                        <?php } ?>
                        <?php if($cancel["cancle_desc_4"] == ""){}else{?>
                            <div class="form-group">

                                <input <?php if($cancel_desc == $cancel["cancle_desc_4"]){echo 'checked';}?> type="radio" id="cancel_desc" name="cancel_desc" value="<?=$cancel["cancle_desc_4"]?>">
                                <span><?=$cancel["cancle_desc_4"]?></span>
                            </div>
                        <?php } ?>
                        <?php if($cancel["cancle_desc_5"] == ""){}else{?>
                            <div class="form-group">

                                <input <?php if($cancel_desc == $cancel["cancle_desc_5"]){echo 'checked';}?> type="radio" id="cancel_desc" name="cancel_desc" value="<?=$cancel["cancle_desc_5"]?>">
                                <span><?=$cancel["cancle_desc_5"]?></span>
                            </div>
                        <?php } ?>
                        <?php if($cancel["cancle_desc_6"] == ""){}else{?>
                            <div class="form-group">

                                <input <?php if($cancel_desc == $cancel["cancle_desc_6"]){echo 'checked';}?> type="radio" id="cancel_desc" name="cancel_desc" value="<?=$cancel["cancle_desc_6"]?>">
                                <span><?=$cancel["cancle_desc_6"]?></span>
                            </div>
                        <?php } ?>
                        <?php if($cancel["cancle_desc_7"] == ""){}else{?>
                            <div class="form-group">

                                <input <?php if($cancel_desc == $cancel["cancle_desc_7"]){echo 'checked';}?> type="radio" id="cancel_desc" name="cancel_desc" value="<?=$cancel["cancle_desc_7"]?>">
                                <span><?=$cancel["cancle_desc_7"]?></span>
                            </div>
                        <?php } ?>
                        <?php if($cancel["cancle_desc_8"] == ""){}else{?>
                            <div class="form-group">

                                <input <?php if($cancel_desc == $cancel["cancle_desc_8"]){echo 'checked';}?> type="radio" id="cancel_desc" name="cancel_desc" value="<?=$cancel["cancle_desc_8"]?>">
                                <span><?=$cancel["cancle_desc_8"]?></span>
                            </div>
                        <?php } ?>
                        <?php if($cancel["cancle_desc_9"] == ""){}else{?>
                            <div class="form-group">

                                <input <?php if($cancel_desc == $cancel["cancle_desc_9"]){echo 'checked';}?> type="radio" id="cancel_desc" name="cancel_desc" value="<?=$cancel["cancle_desc_9"]?>">
                                <span><?=$cancel["cancle_desc_9"]?></span>
                            </div>
                        <?php } ?>
                        <?php if($cancel["cancle_desc_10"] == ""){}else{?>
                            <div class="form-group">

                                <input <?php if($cancel_desc == $cancel["cancle_desc_10"]){echo 'checked';}?> type="radio" id="cancel_desc" name="cancel_desc" value="<?=$cancel["cancle_desc_10"]?>">
                                <span><?=$cancel["cancle_desc_10"]?></span>
                            </div>
                        <?php } ?>
                        <?php if($cancel["cancle_desc_11"] == ""){}else{?>
                            <div class="form-group">

                                <input <?php if($cancel_desc == $cancel["cancle_desc_11"]){echo 'checked';}?> type="radio" id="cancel_desc" name="cancel_desc" value="<?=$cancel["cancle_desc_11"]?>">
                                <span><?=$cancel["cancle_desc_11"]?></span>
                            </div>
                        <?php } ?>
                        <?php if($cancel["cancle_desc_12"] == ""){}else{?>
                            <div class="form-group">

                                <input <?php if($cancel_desc == $cancel["cancle_desc_12"]){echo 'checked';}?> type="radio" id="cancel_desc" name="cancel_desc" value="<?=$cancel["cancle_desc_12"]?>">
                                <span><?=$cancel["cancle_desc_12"]?></span>
                            </div>
                        <?php } ?>
                        <?php if($cancel["cancle_desc_13"] == ""){}else{?>
                            <div class="form-group">

                                <input <?php if($cancel_desc == $cancel["cancle_desc_13"]){echo 'checked';}?> type="radio" id="cancel_desc" name="cancel_desc" value="<?=$cancel["cancle_desc_13"]?>">
                                <span><?=$cancel["cancle_desc_13"]?></span>
                            </div>
                        <?php } ?>
                        <?php if($cancel["cancle_desc_14"] == ""){}else{?>
                            <div class="form-group">

                                <input <?php if($cancel_desc == $cancel["cancle_desc_14"]){echo 'checked';}?> type="radio" id="cancel_desc" name="cancel_desc" value="<?=$cancel["cancle_desc_14"]?>">
                                <span><?=$cancel["cancle_desc_14"]?></span>
                            </div>
                        <?php } ?>
                        <?php if($cancel["cancle_desc_15"] == ""){}else{?>
                            <div class="form-group">

                                <input <?php if($cancel_desc == $cancel["cancle_desc_15"]){echo 'checked';}?> type="radio" id="cancel_desc" name="cancel_desc" value="<?=$cancel["cancle_desc_15"]?>">
                                <span><?=$cancel["cancle_desc_15"]?></span>
                            </div>
                        <?php } ?>
                        <div class="form-group">
                            <span>سایر</span>
                            <input type="text" class="form-control" id="cancel_desc_other" value="<?=$cancel_desc_other?>" >
                        </div>

                    </div>
                    <div class="box_status_return <?php if($status == 5 ){echo 'active';}?>" id="done">

                        <label>فاکتور سفارش</label>



                        <div class="InvoiceMaker <?php if(CheckAccess("edit_invoice_order_internal")){}else{echo 'cantedit';} ?>">

                            <div class="header_invoice">
                                <div class="form-group third">
                                    <label>نام مشتری</label>
                                    <div class="value"><?=$first_name?></div>
                                </div>
                                <div class="form-group third">
                                    <label>تاریخ</label>
                                    <div class="value"><?=get_the_date("Y/m/d",$_GET['uid'])?></div>
                                </div>
                                <div class="form-group third">
                                    <label>اشتراک</label>
                                    <div class="value"><?=$subscription?></div>
                                </div>
                            </div>

                            <div class="List_Of_piece">

                                <ul class="head_in">
                                    <li>قطعه</li>
                                    <li>قیمت به مشتری</li>
                                    <li>قیمت خرید</li>
                                </ul>
                                <div class="invoice_box_temp">
                                    <ul class="body_in">
                                        <li><input type="text" class="form-control" id="piece" placeholder="قطعه"></li>
                                        <li><input type="text" class="form-control PriceFormat" id="customer_price" placeholder="قیمت به مشتری"></li>

                                        <li><input type="text" class="form-control PriceFormat" id="buy_price" placeholder="قیمت خرید"></li>

                                    </ul>
                                </div>

                                <div class="place_box_temp">

                                    <?php
                                    for($i = 0 ; $i < count($piece_list); $i++){
                                        echo '
                                                 <ul class="body_in">
                                                    <li><input type="text" class="form-control" id="piece" placeholder="قطعه" value="'.$piece_list[$i].'"></li>
                                                    <li>
                                            ';
                                        if(CheckAccess("order_unit_buy_internal")){
                                            echo '<input type="text" class="form-control PriceFormat" id="customer_price" value="'.number_format($customer_price_list[$i]).'" placeholder="قیمت به مشتری">';
                                        }else{
                                            echo ' <input type="text" class="form-control" value="-">';
                                        }
                                        echo '
                                                </li>

                                                    <li>
                                                    ';
                                        if(CheckAccess("order_unit_sales_internal")) {
                                            echo '<input type = "text" class="form-control PriceFormat" id = "buy_price" value = "'.number_format($buy_price_list[$i]).'" placeholder = "قیمت خرید" >';
                                        }else{
                                            echo ' <input type="text" class="form-control" value="-">';
                                        }
                                        echo '
                                                </li >
                                                </ul>
                                                ';
                                    }
                                    ?>

                                </div>
                                <div class="add_box_temp">افزودن</div>
                            </div>
                            <input type="hidden" id="percent_operator_number" value="<?=$percent_operator?>">
                            <div class="other_price">
                                <?if(CheckAccess("order_hire_internal")){?>
                                    <div class="form-group third">
                                        <label>اجرت</label>
                                        <input type="text" class="form-control PriceFormat" id="hire" placeholder="اجرت" value="<?=number_format($hire)?>">
                                    </div>
                                <?php }?>
                                <?if(CheckAccess("order_delivery_internal")){?>
                                    <div class="form-group third">
                                        <label>ایاب و ذهاب</label>
                                        <input type="text" class="form-control PriceFormat" id="transportation" placeholder="ایاب و ذهاب" value="<?=number_format($transportation)?>">
                                    </div>
                                <?php }?>
                                <?if(CheckAccess("order_discount_internal")){?>
                                    <div class="form-group third">
                                        <label>تخفیف</label>
                                        <input type="text" class="form-control PriceFormat" id="discount" placeholder="ایاب و ذهاب" value="<?=number_format($discount)?>">
                                    </div>
                                <?php }?>
                            </div>

                            <div class="TotalInvoice">

                                <table>
                                    <?if(CheckAccess("order_total_price_internal")){?>
                                        <tr>
                                            <th>جمع کل صورت حساب</th>
                                            <td><span id="total_invoice"><i><?=number_format($price_customer)?></i> <b>تومان</b></span> </td>
                                        </tr>
                                    <?php }?>
                                    <?if(CheckAccess("order_total_cost_internal")){?>
                                        <tr>
                                            <th>جمع هزینه ها</th>
                                            <td><span id="total_cost"><i><?php if($cost_price == ''){echo '0';}else{echo number_format($cost_price);}?></i> <b>تومان</b></span> </td>
                                        </tr>
                                    <?php }?>
                                    <?if(CheckAccess("order_total_left_internal")){?>
                                        <tr>
                                            <th>مانده</th>
                                            <td><span id="total_left"><i><?=number_format($total_invoice)?></i> <b>تومان</b></span> </td>
                                        </tr>
                                    <?php }?>
                                    <?if(CheckAccess("order_tech_side_internal")){?>
                                        <tr>
                                            <th>سهم تکنسین</th>
                                            <td><span id="total_Company"><i><?php if($co_side_price == ''){echo '0';}else{echo number_format($co_side_price);}?></i> <b>تومان</b></span> </td>
                                        </tr>
                                    <?php }?>
                                    <?if(CheckAccess("order_co_side_internal")){?>
                                        <tr>
                                            <th>سهم شرکت</th>
                                            <td><span id="total_Company"><i><?php if($co_side_price_1 == ''){echo '0';}else{echo number_format($co_side_price_1);}?></i> <b>تومان</b></span> </td>
                                        </tr>
                                    <?php }?>
                                </table>

                            </div>
                            <div class="description_invoice">
                                <div class="form-group">
                                    <label>توضیحات</label>
                                    <textarea class="form-control" id="invoice_descripotion"><?=$invoice_descripotion?></textarea>
                                </div>
                            </div>

                        </div>

                    </div>

                    <input  <?=$active?>  type="submit" class="btn" value="ثبت">
                </form>
            </div>
            <div class="Ticket_list">
                <span>لیست تیکت ها</span>
                <?php
                $ticket = get_posts(array(
                    "post_type" => "ticket" ,
                    "posts_per_page" => -1 ,
                    "post_status" => "any",
                    "meta_query" => array(
                        array(
                            "key" => "order_ticket",
                            "value" => $uid,
                            "compare" => "LIKE",
                        ),
                    ),
                ));
                echo '<ul class="ListOfTicketOrder">';
                foreach($ticket as $t_item){

                    $sender = get_post_meta($t_item->ID,"sender",true);
                    $sender_name = get_user_meta($sender,"first_name",true);
                    echo '<li>
                                    <a href="'.get_the_permalink(167).'?uid='.$t_item->ID.'">
                                        <span class="subject">
                                            <b>موضوع:</b>
                                            <i>'.$t_item->post_title.'</i>
                                        </span>
                                        <span class="date">
                                            <b>تاریخ:</b>
                                            <i>'.get_the_date("Y/m/d h:s" , $t_item->ID).'</i>
                                        </span>
                                        <span class="sender">
                                            <b>ارسال کننده:</b>
                                            <i>'.$sender_name.'</i>
                                        </span>
                                        <span class="btnShow">مشاهده تیکت</span>
                                    </a>
                                </li>';

                }
                echo '</ul>'
                ?>
            </div>
            <div class="crmMessage" id="messageListOrder">
                <span class="title">لیست پیام ها</span>
                <ul class="desc">
                    <?php
                    krsort($order_description_content);
                    foreach($order_description_content as $item){
                        $name_author = get_user_meta($item["author"],"first_name",true);

                        if($item["subject"] == "ایجاد فاکتور"){
                            if(CheckAccess("order_total_price_internal") && CheckAccess("order_total_cost_internal") && CheckAccess("order_total_left_internal") && CheckAccess("order_tech_side_internal") && CheckAccess("order_co_side_internal")){
                                $content = $item["content"];
                            }else{
                                $content = 'جمع کل صورت حساب: ' . number_format($price_customer) . ' تومان';
                            }
                        }
                        else{
                            $content = $item["content"];
                        }
                        echo '
                            
                                    <li>
                                        <a href="#">
                                            <div class="contentDescription">
                                                <h3>'.$item["subject"].'</h3>
                                                <p>'.$content.'</p>
                                            </div>
                                            <div class="DetailDescription">
                                                <div class="item_list">
                                                    <span>اپراتور/تکنسین</span>
                                                    <b>'.$name_author.'</b>
                                                </div>
                                                <div class="item_list">
                                                    <span>تاریخ</span>
                                                    <b>'.$item["date"].'</b>
                                                </div>
                                                <div class="item_list">
                                                    <span>وضعیت</span>
                                                    <b>'.$item["status"].'</b>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                ';
                    }
                    ?>
                </ul>


            </div>
            <div class="crmMessage" id="noteListOrder">
                <span class="title">لیست یاداشت ها</span>
                <ul class="desc">
                    <?php
                    krsort($order_note_content);
                    foreach($order_note_content as $item) {

                        $name_author = get_user_meta($item["author"], "first_name", true);
                        echo '
                            
                                    <li>
                                        <a href="#">
                                            <div class="contentDescription">
                                                <h3>یاداشت ' . $name_author . '</h3>
                                                <p>' . $item["content"] . '</p>
                                            </div>
                                            <div class="DetailDescription">
                                                <div class="item_list">
                                                    <span>اپراتور/تکنسین</span>
                                                    <b>' . $name_author . '</b>
                                                </div>
                                                <div class="item_list">
                                                    <span>تاریخ</span>
                                                    <b>' . $item["date"] . '</b>
                                                </div>
                                                <div class="item_list">
                                                    <span>وضعیت</span>
                                                    <b>' . $item["status"] . '</b>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                ';

                    }
                    ?>
                </ul>
            </div>

            <?php if(CheckAccess("order_return_log")){?>

                <div class="crmMessage" id="RetrunBackOrderLog">
                    <span class="title">لاگ بازگشت سفارش</span>
                    <ul class="desc">
                        <?php

                        foreach($log_return as $item){

                            if($item["return_type"] == 1){
                                $description_msg = $item["invoice_descripotion"];

                            }
                            else{
                                $description_msg = $item["cancel_desc"] . " " . $item["cancel_desc_other"];
                            }
                            if($item["negative_invoice"] == 0){
                                $n_msg = "خیر";
                            }
                            else if($item["negative_invoice"] == 0){
                                $n_msg = "بلی";
                            }

                            if($item["device_image_input"] == ""){
                                $img = 'ندارد';
                            }
                            else{
                                $img = '<a href="'.$item["device_image_input"] .'" target="_blank" id="download_img">دانلود</a>';
                            }

                            if($item["total_invoice"] > 0){
                                $item_co_side_price = ($item["total_invoice"] / 100) * $percent_operator;
                                $item_co_side_price_1 = $item["total_invoice"] - $item_co_side_price;
                            }else{
                                $co_side_price = 0;
                                $co_side_price_1 = 0;
                            }

                            echo '
                                  
                                  <li>
                                  
                                    <div class="Log_Return">
                                    
                                        <div class="row">
                                        
                                            <div class="col-md-4">
                                                <div class="log_detail">
                                                    <b>تاریخ بازگشت : </b>
                                                    <span>'.$item["date"].'</span>
                                                </div>    
                                                <div class="log_detail">
                                                    <b>نوع بازگشت : </b>
                                                    <span>'.$item["return_type_message"].'</span>
                                                </div>
                                                <div class="log_detail">
                                                    <b>توضیح بازگشت : </b>
                                                    <span>'.$item["return_description"].'</span>
                                                </div>
                                                <div class="log_detail">
                                                    <b>توضیحات : </b>
                                                    <span>'.$description_msg.'</span>
                                                </div>
                                                <div class="log_detail">
                                                    <b>سفارش منفی : </b>
                                                    <span>'.$n_msg.'</span>
                                                </div>
                                                 <div class="log_detail">
                                                    <b>سفارش منفی : </b>
                                                    <span>'.$n_msg.'</span>
                                                </div>
                                                <div class="log_detail">
                                                    <b>تصویر : </b>
                                                    <span>'.$img.'</span>
                                                </div>
                                            
                                            </div>
                                            
                                            <div class="col-md-8">

                                        ';
                            ?>
                            <div class="InvoiceMaker">

                                <div class="header_invoice">
                                    <div class="form-group third">
                                        <label>نام مشتری</label>
                                        <div class="value"><?=$first_name?></div>
                                    </div>
                                    <div class="form-group third">
                                        <label>تاریخ</label>
                                        <div class="value"><?=get_the_date("Y/m/d",$_GET['uid'])?></div>
                                    </div>
                                    <div class="form-group third">
                                        <label>اشتراک</label>
                                        <div class="value"><?=$subscription?></div>
                                    </div>
                                </div>

                                <div class="List_Of_piece">

                                    <ul class="head_in">
                                        <li>قطعه</li>
                                        <li>قیمت به مشتری</li>
                                        <li>قیمت خرید</li>
                                    </ul>
                                    <div class="invoice_box_temp">
                                        <ul class="body_in">
                                            <li><input type="text" class="form-control" id="piece" placeholder="قطعه"></li>
                                            <li><input type="text" class="form-control PriceFormat" id="" placeholder="قیمت به مشتری"></li>

                                            <li><input type="text" class="form-control PriceFormat" id="" placeholder="قیمت خرید"></li>

                                        </ul>
                                    </div>

                                    <div class="place_box_temp">

                                        <?php
                                        for($i = 0 ; $i < count($item["piece_list"]); $i++){
                                            echo '
                                                 <ul class="body_in">
                                                    <li><input type="text" class="form-control" id="" placeholder="قطعه" value="'.$item["piece_list"][$i].'"></li>
                                                    <li>
                                            ';
                                            if(CheckAccess("order_unit_buy_internal")){
                                                echo '<input type="text" class="form-control PriceFormat" id="" value="'.number_format($item["customer_price_list"][$i]).'" placeholder="قیمت به مشتری">';
                                            }else{
                                                echo ' <input type="text" class="form-control" value="-">';
                                            }
                                            echo '
                                                </li>

                                                    <li>
                                                    ';
                                            if(CheckAccess("order_unit_sales_internal")) {
                                                echo '<input type = "text" class="form-control PriceFormat" id="" value = "'.number_format($item["buy_price_list"][$i]).'" placeholder = "قیمت خرید" >';
                                            }else{
                                                echo ' <input type="text" class="form-control" value="-">';
                                            }
                                            echo '
                                                </li >
                                                </ul>
                                                ';
                                        }
                                        ?>

                                    </div>
                                </div>
                                <div class="other_price">
                                    <?if(CheckAccess("order_hire_internal")){?>
                                        <div class="form-group third">
                                            <label>اجرت</label>
                                            <input type="text" class="form-control PriceFormat" id="" placeholder="اجرت" value="<?=number_format($item["hire"])?>">
                                        </div>
                                    <?php }?>
                                    <?if(CheckAccess("order_delivery_internal")){?>
                                        <div class="form-group third">
                                            <label>ایاب و ذهاب</label>
                                            <input type="text" class="form-control PriceFormat" id="" placeholder="ایاب و ذهاب" value="<?=number_format($item["transportation"])?>">
                                        </div>
                                    <?php }?>
                                    <?if(CheckAccess("order_discount_internal")){?>
                                        <div class="form-group third">
                                            <label>تخفیف</label>
                                            <input type="text" class="form-control PriceFormat" id="" placeholder="ایاب و ذهاب" value="<?=number_format($item["discount"])?>">
                                        </div>
                                    <?php }?>
                                </div>

                                <div class="TotalInvoice">

                                    <table>
                                        <?if(CheckAccess("order_total_price_internal")){?>
                                            <tr>
                                                <th>جمع کل صورت حساب</th>
                                                <td><span id=""><i><?=number_format($item["price_customer"])?></i> <b>تومان</b></span> </td>
                                            </tr>
                                        <?php }?>
                                        <?if(CheckAccess("order_total_cost_internal")){?>
                                            <tr>
                                                <th>جمع هزینه ها</th>
                                                <td><span id=""><i><?php if($item["cost_price"] == ''){echo '0';}else{echo number_format($item["cost_price"]);}?></i> <b>تومان</b></span> </td>
                                            </tr>
                                        <?php }?>
                                        <?if(CheckAccess("order_total_left_internal")){?>
                                            <tr>
                                                <th>مانده</th>
                                                <td><span id=""><i><?=number_format($item["total_invoice"])?></i> <b>تومان</b></span> </td>
                                            </tr>
                                        <?php }?>
                                        <?if(CheckAccess("order_tech_side_internal")){?>
                                            <tr>
                                                <th>سهم تکنسین</th>
                                                <td><span id=""><i><?php if($item_co_side_price == ''){echo '0';}else{echo number_format($item["co_side_price"]);}?></i> <b>تومان</b></span> </td>
                                            </tr>
                                        <?php }?>
                                        <?if(CheckAccess("order_co_side_internal")){?>
                                            <tr>
                                                <th>سهم شرکت</th>
                                                <td><span id=""><i><?php if($item_co_side_price_1 == ''){echo '0';}else{echo number_format($item["co_side_price_1"]);}?></i> <b>تومان</b></span> </td>
                                            </tr>
                                        <?php }?>
                                    </table>

                                </div>
                                <div class="description_invoice">
                                    <div class="form-group">
                                        <label>توضیحات</label>
                                        <textarea class="form-control" id=""><?=$item["invoice_descripotion"]?></textarea>
                                    </div>
                                </div>

                            </div>
                            <?php
                            echo '

                                            </div>
                                        
                                        </div>
                                    
                                    </div>
                                  
                                  </li>
                                    
                                ';
                        }

                        ?>
                    </ul>
                </div>
            <?php } ?>


        </div>


    </div>
</section>

<?php
get_footer();

?>

<script>
    jQuery(window).on("load",function (){
        LIst_OF_Order_user();
    });
</script>