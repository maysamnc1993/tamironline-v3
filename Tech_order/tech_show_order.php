<?php

// Template Name: Tech Order View
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
    $device_img1 = get_post_meta($uid,"device_img1",true);
    $save_as_draft = get_post_meta($uid,"save_as_draft",true);
    $ready_for_derliver = get_user_meta(get_current_user_id(),"ready_for_derliver",true);
    $objection_list = '';
    if($objection){
    foreach($objection as $item){
        $objection_list.='<span class="objection">'.$item.'</span>';
    }
    }
    $objection_description = get_post_meta($uid,"objection_description",true);
    $technician = get_post_meta($uid,"technician",true);
    $technician_name = get_user_meta($technician,"first_name",true);

    $log_return = get_post_meta($uid,"log_return",true);

    $status = get_post_meta($uid,"status",true);
    $invoice_descripotion = get_post_meta($uid,"invoice_descripotion",true);
    $description_tech = get_post_meta($uid,"description_tech",true);
    $description_tech1 = get_post_meta($uid,"description_tech1",true);
    $description_tech2 = get_post_meta($uid,"description_tech2",true);
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
    $percent_operator = get_user_meta(get_current_user_id(),"percent",true);
    $type_of_calc_tech = get_user_meta(get_current_user_id(), 'type_of_calc_tech', true);
    $tech_per_of_all = get_user_meta(get_current_user_id(), 'tech_per_of_all', true);
    if($status == 10){
     $percent_operator = 100;   
    }



    if($total_invoice > 0){


        if($type_of_calc_tech == 1){
            $tech_per = 1 - ($tech_per_of_all / 100);
            $co_side_price = ($total_invoice + $cost_price) * $tech_per;
        }else{
            $co_side_price= ($total_invoice / 100) * $percent_operator;
        }
    }else{
        $co_side_price = 0;
    }



    $device_image_input = get_post_meta($uid,"device_img1",true);

    $Have_invoice = get_post_meta($uid,"Have_invoice",true);
    $Type_Of_send_invoice = get_post_meta($uid,"Type_Of_send_invoice",true);
    $invoice_email = get_post_meta($uid,"invoice_email",true);
    $invoice_paper = get_post_meta($uid,"invoice_paper",true);
    $order_description_content = get_post_meta($uid,"order_description_content",true);
    $order_note_content = get_post_meta($uid,"order_note_content",true);
    if(!$order_note_content){
        $order_note_content = array();
    }
    $return_description = get_post_meta($uid,"return_description",true);
    $return_type = get_post_meta($uid,"return_type",true);

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

}
$cancel = get_option("cancel");

?>

    <div class="modal fade" id="ّInvoiceOrderPopUp" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
<!--            <div class="modal-content">-->
<!--                <form action="#" id="request_invoice">-->
<!--                    <h3>آیا مشتری فاکتور میخواهد؟</h3>-->
<!--                    <ul class="Have_invoice">-->
<!--<!--                         -->-->
<!--                        <li id="1" class="yes --><?php //if($Have_invoice == 1){echo 'active';}?><!--">-->
<!--                            <input type="radio" id="Have_invoice" name="Have_invoice"  --><?php //if($Have_invoice == 1){echo 'checked';}?><!-- value="1">-->
<!--                            <span>بلی</span>-->
<!--                        </li>-->
<!--                        <li id="0" class="no  --><?php //if($Have_invoice == 0){echo 'active';}?><!--">-->
<!--                            <input type="radio" id="Have_invoice" name="Have_invoice"  --><?php //if($Have_invoice == 0){echo 'checked';}?><!-- value="0">-->
<!--                            <span>خیر</span>-->
<!--                        </li>-->
<!---->
<!--                    </ul>-->
<!---->
<!--                    <div class="next_step  --><?php //if($Have_invoice == 1){echo 'active';}?><!--">-->
<!--                        <h3>نوع ارسال فاکتور را انتخاب نمایید؟</h3>-->
<!--                        <ul class="Type_Of_send_invoice">-->
<!--                            <li id="sms" class="--><?php //if($Type_Of_send_invoice == "sms"){echo 'active';}?><!--">-->
<!--                                <input type="radio" id="Type_Of_send_invoice"  --><?php //if($Type_Of_send_invoice == "sms"){echo 'checked';}?><!-- name="Type_Of_send_invoice" value="sms">-->
<!--                                <span>ارسال صورت حساب با پیامک</span>-->
<!--                            </li>-->
<!--<!--                            <li id="email" class=" -->--><?php ////if($Type_Of_send_invoice == "email"){echo 'active';}?><!--<!--">-->-->
<!--<!--                                <input type="radio" id="Type_Of_send_invoice"  -->--><?php ////if($Type_Of_send_invoice == "email"){echo 'checked';}?><!--<!-- name="Type_Of_send_invoice" value="email">-->-->
<!--<!--                                <span>ارسال صورت حساب با ایمیل</span>-->-->
<!--<!--                            </li>-->-->
<!--<!--                            <li id="paper" class="  -->--><?php ////if($Type_Of_send_invoice == "paper"){echo 'active';}?><!--<!--">-->-->
<!--<!--                                <input type="radio" id="Type_Of_send_invoice"  -->--><?php ////if($Type_Of_send_invoice == "paper"){echo 'checked';}?><!--<!-- name="Type_Of_send_invoice" value="paper">-->-->
<!--<!--                                <span>صورت حساب کاغذی</span>-->-->
<!--<!--                            </li>-->-->
<!--                        </ul>-->
<!--                    </div>-->
<!---->
<!--                    <div class="next_step_two ">-->
<!--                        <div class="form-group email  --><?php //if($Type_Of_send_invoice == "email"){echo 'active';}?><!--">-->
<!--                            <label>آدرس ایمیل برای ارسال صورت حساب</label>-->
<!--                            <input type="text" class="form-control" id="invoice_email" value="--><?php //=$invoice_email?><!--">-->
<!--                        </div>-->
<!---->
<!---->
<!--                    </div>-->
<!---->
<!--                    <input type="submit" class="btn" value="ارسال">-->
<!---->
<!--                </form>-->
<!--            </div>-->
        </div>
    </div>

    <section class="DashboardSection">

        <?=NavDashboard();?>

        <div class="main">
            <div class="messageCRM note">
                <div class="CrmBTnMessage">یاداشت ها</div>
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

                <?php

                    if($save_as_draft == 1){
                        
                        echo '<span class="DraftMesage">این سفارش فقط ذخیره شده است لطفا بعد از بررسی سفارش را ثبت نمایید.</span>';
                        
                    }

                ?>

<!--                <h1 class="title">مشاهده سفارش --><?php //=$uid?>
<!--                    | مشتری --><?php //=$first_name?>
<!--                    | کد اشتراک --><?php //=$subscription?>
<!--                </h1>-->

                <table class="Show_Order table">

                    <tr>
                        <th>مشتری</th>
                        <td><?=$first_name?></td>
                        <th>کد اشتراک</th>
                        <td><?=$subscription?></td>
                    </tr>
                    <?php if($status == 4 || $status == 5 ){}else{?>

                    <?php } ?>
                    <tr>
                        <th>استان</th>
                        <td><?=$state?></td>
                        <th>شهر</th>
                        <td><?=$city?></td>
                    </tr>
                    <?php if($status == 4 || $status == 5 ){}else{?>
                    <tr>
                        <th>  آدرس پستی</th>
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
                            <td colspan="3" ><?=$objection_list?></td>
                        </tr>
                        <tr>
                            <th >توضیحات</th>
                            <td colspan="3"><?=$objection_description?></td>
                        </tr>
                    <?php } ?>
<!--                    <tr>-->
<!--                        <th>تکنسین</th>-->
<!--                        <td colspan="3">--><?php //=$technician_name?><!--</td>-->
<!--                    </tr>-->
                        <tr class="style-Btn-CallLog">
                            <td><a id="CallLog" data-uid="<?=$_GET['uid'];?>" href="tel:<?=$mobile?>" target="_blank"> <i class="las la-mobile"></i>تماس با شماره موبایل</a> </td>
                            <td><a id="CallLog" data-uid="<?=$_GET['uid'];?>" href="tel:<?=$phone?>" target="_blank">  <i class="las la-phone-volume"></i>تماس با تلفن ثابت</a> </td>
                        </tr>

                </table>




                <?php
                if(CheckAccess("order_status")){
                    $active = '';
                }else{
                    $active = "";
                }
                ?>

                <?php
                if($status == 5 || $status == 10){
                    ?>

                            <?php
                                if($device_image_input == ''){}else{
                                    echo '
                                        <div class="ThumbDevice" style="background-image:url('.$device_image_input.')"></div>
                                        <a href="'.$device_image_input.'" class="DownloadIMG" target="_blank">دانلود تصویر دستگاه</a>
                                    ';
                                }
                            ?>

                        </form>


                        <?php
                            if($Have_invoice == ""){
                                $dontEdit = "";
                            }
                            else{
                                $dontEdit = "dontEdit";
                            }
                        ?>
                    <!--
<form action="#" id="request_invoice" class="<?=$dontEdit?>">
    <h3>آیا مشتری فاکتور میخواهد؟</h3>
    <ul class="Have_invoice">
        <li class="yes <?php if($Have_invoice == 1){echo 'active';}?>">
            <input type="radio" id="Have_invoice" name="Have_invoice" value="1"
            <?php if($Have_invoice == 1){echo 'checked';}?>>
            <span>بلی </span>
        </li>
        <li class="no <?php if($Have_invoice == 0){echo 'active';}?>">
            <input type="radio" id="Have_invoice" name="Have_invoice" value="0"
            <?php if($Have_invoice == 0){echo 'checked';}?>>
            <span>خیر</span>
        </li>
    </ul>

    <div class="next_step <?php if($Have_invoice == 1){echo 'active';}?>">
        <h3>نوع ارسال فاکتور را انتخاب نمایید؟</h3>
        <ul class="Type_Of_send_invoice">
            <li id="sms" class="<?php if($Type_Of_send_invoice == "sms"){echo 'active';}?>">
                <input type="radio" id="Type_Of_send_invoice" name="Type_Of_send_invoice" value="sms"
                <?php if($Type_Of_send_invoice == "sms"){echo 'checked';}?>>
                <span>ارسال صورت حساب با پیامک</span>
            </li>
            <li id="email" class="<?php if($Type_Of_send_invoice == "email"){echo 'active';}?>">
                <input type="radio" id="Type_Of_send_invoice" name="Type_Of_send_invoice" value="email"
                <?php if($Type_Of_send_invoice == "email"){echo 'checked';}?>>
                <span>ارسال صورت حساب با ایمیل</span>
            </li>
            <li id="paper" class="<?php if($Type_Of_send_invoice == "paper"){echo 'active';}?>">
                <input type="radio" id="Type_Of_send_invoice" name="Type_Of_send_invoice" value="paper"
                <?php if($Type_Of_send_invoice == "paper"){echo 'checked';}?>>
                <span>صورت حساب کاغذی</span>
            </li>
        </ul>
    </div>

    <div class="next_step_two">
        <div class="form-group email <?php if($Type_Of_send_invoice == "email"){echo 'active';}?>">
            <label>آدرس ایمیل برای ارسال صورت حساب</label>
            <input type="text" class="form-control" id="invoice_email" value="<?=$invoice_email?>">
        </div>
        <div class="form-group paper <?php if($Type_Of_send_invoice == "paper"){echo 'active';}?>">
            <label>بارگذاری صورت حساب کاغذی</label>
            <div class="Img_selector">
                <div class="loading"></div>
                <div class="thumb" style="background-image:url(<?=$invoice_paper?>)"></div>
                <div class="box_upload">
                    <?=file_get_contents(wp_get_attachment_url(97));?>
                    <span>برای بارگذاری تصویر (بکشید / کلیک کنید)</span>
                    <input class="imgUpload" type="file">
                    <input class="source_image" type="hidden" id="invoice_paper" value="<?=$invoice_paper?>">
                </div>
            </div>
        </div>
    </div>

    <input type="submit" class="btn" value="ارسال">
</form>
-->


                    <?php
                }

                if($return_type == 1){
                    $s_001 = "disabled";
                    $s_002 = "disabled";
                    $s_003 = "disabled";
                    $s_004 = "disabled";
                    $s_005 = "";
                }
                else if($return_type == 2){
                    $s_001 = "disabled";
                    $s_002 = "disabled";
                    $s_003 = "disabled";
                    $s_004 = "";
                    $s_005 = "";
                }
                else{
                    $s_001 = "";
                    $s_002 = "";
                    $s_003 = "";
                    $s_004 = "";
                    $s_005 = "";
                }
                ?>
         
                <div class="Detai_order tech ">
                    <form action="#" method="post"  id="Tech_DetailAjax" class="<?php if($status == 4 || $status == 5 ||  $status == 10 ){echo 'dontEdit';}?>">
                        <input type="hidden" id="uid" value="<?=$uid?>">
                        <span>تعیین وضعیت سفارش</span>
                        <div class="form-group three">
                            <label class="<?php if($status == 1 ){echo 'active';}?>">
                                <span>هماهنگ شده</span>
                                <input <?=$s_001?> <?=$active?> <?php if($status == 1 ){echo 'checked';}?> type="radio" name="status" id="status" value="1" data-action="desc">
                            </label>
                        </div>
                        <div class="form-group three">
                            <label class="<?php if($status == 3 ){echo 'active';}?>">
                                <span>نامشخص</span>
                                <input <?=$s_003?> <?=$active?>  <?php if($status == 3 ){echo 'checked';}?> type="radio" name="status" id="status" value="3"  data-action="desc1">
                            </label>
                        </div>
                        <div class="form-group three">
                            <label class="<?php if($status == 2 ){echo 'active';}?>">
                                <span>انتقال
                                <b> به تعمیرگاه</b>
                                </span>
                                <input <?=$s_002?> <?=$active?> <?php if($status == 2 ){echo 'checked';}?> type="radio" name="status" id="status" value="2" data-action="desc2">
                            </label>
                        </div>



                        <div class="form-group two ">
                            <label class="<?php if($status == 5 || $status == 10 ){echo 'active';} if($save_as_draft == 1){echo 'active';}?> done_Btn ">
                                <span>پایان سفارش </span>
                                <span class="Sub_Btn">ارسال فاکتور مشتری </span>
                                <input  <?=$s_005?> <?=$active?>  <?php if($status == 5 ){echo 'checked';}?> type="radio" name="status" id="status" value="5"  data-action="done">
                            </label>
                        </div>
                        <div class="form-group two ">
                            <label class="<?php if($status == 100 ){echo 'active';}?> cancel_Btn">
                                <span>رد سفارش</span>
                                <input  <?=$s_004?> <?=$active?>  <?php if($status == 4 ){echo 'checked';}?> type="radio" name="status" id="status" value="100"  data-action="cancel">
                            </label>
                        </div>
                        
                        <div class="form-group ">
                            <label class="<?php if($status == 10 ){echo 'active';}?> cancel_Btn TransferBTN">
                                <span>فقط ایاب و ذهاب و تشخیص عیب</span>
                                <input  <?=$s_004?> <?=$active?>  <?php if($status == 10 ){echo 'checked';}?> type="radio" name="status" id="status" value="10"  data-action="done" data-action-transfer="filter">
                            </label>
                        </div>

                        <div class="box_status_return <?php if(in_array($status,array(1))){echo 'active';}?>" id="desc">

                            <div class="form-group">

                                <label for="description_tech">توضیحات</label>
                                <textarea class="form-control" id="description_tech" placeholder="برای چه روز و ساعتی هماهنگ کرده اید؟
"></textarea>
                                
                            </div>

                        </div>
                        <div class="box_status_return <?php if(in_array($status,array(2))){echo 'active';}?>" id="desc1">

                            <div class="form-group">

                                <label for="description_tech">توضیحات</label>
                                <textarea class="form-control" id="description_tech1" placeholder="علت نامشخص بودن وضعیت سفارش چیست؟
"><?=$description_tech1?></textarea>


                            </div>

                        </div>
                        <div class="box_status_return <?php if(in_array($status,array(3))){echo 'active';}?>" id="desc2">

                            <div class="form-group">

                                <label for="description_tech">توضیحات</label>
                                <textarea class="form-control" id="description_tech2" placeholder="چه دستگاه یا قطعاتی را از منزل مشتری خارج کرده اید؟

"><?=$description_tech2?></textarea>
                            <span>توجه: این توضیحات به عنوان رسید برای مشتری ارسال
 خواهد شد. در ثبت اطلاعات دستگاه و قطعات دقت نمایید. </span>

                            </div>

                        </div>
                        <div class="box_status_return <?php if($status == 4 ){echo 'active';}?>" id="cancel">

                            <div class="form-group">
                                <span>توضیحات</span>
                                <textarea class="form-control" id="cancel_desc_other" placeholder="علت رد سفارش"></textarea>
                            </div>

                        </div>

                        <div class="box_status_return <?php if($status == 5 || $status == 10 ){echo 'active';} if($save_as_draft == 1){echo 'active';}?>"" id="done">

                        <label>فاکتور سفارش</label>

                        <div class="InvoiceMaker">

                            <div class="header_invoice">
                                <div class="form-group third">
                                    <label>نام مشتری</label>
                                    <div class="value"><?=$first_name?></div>
                                </div>
                                <div class="form-group third">
                                    <label>تاریخ</label>
                                    <div class="value"><?=get_the_date("Y/m/d")?></div>
                                </div>
                                <div class="form-group third">
                                    <label>اشتراک</label>
                                    <div class="value"><?=$subscription?></div>
                                </div>
                            </div>

                            <div class="totalInvoiceSection">
                                <div class="TotalInvoiceHead">کل مبلغ دریافتی از مشتری</div>
                                <div class="TotalInvoiceBody">
                                    <input type="text" class="form-control PriceFormat" value="<?php if($price_customer == ''){echo '';}else{ echo number_format($price_customer);}?>" id="totalInvoice" style="text-align: center;" placeholder="در اینجا مبلغ را به تومان وارد کنید." oninput="convertToWords(this.value)">
                                </div>

                                <label>مبلغ به حروف</label>
                                <div class="price_char">صفر تومان</div>

                            </div>

                        <div class="List_Of_piece">
                            <ul class="head_in">
                                <li>نام قطعه</li>
                                <!--                                        <li> قیمت به مشتری-->
                                <!---->
                                <!--                                        </li>-->
                                <li>قیمت خرید قطعه
                                </li>
                            </ul>
                            <div class="invoice_box_temp">
                                <ul class="body_in">
                                    <li><input type="text" class="form-control" id="piece" placeholder="نام قطعه"></li>
                                    <!--                                            <li><input type="text" class="form-control PriceFormat" id="customer_price" placeholder=" به تومان وارد کنید"></li>-->
                                    <li><input type="text" class="form-control PriceFormat" id="buy_price" placeholder=" به تومان وارد کنید"></li>
                                </ul>
                            </div>
                            <div class="place_box_temp">

                                <?php
                                if($piece_list == ''){

                                    echo '
                                                 <ul class="body_in">
                                                    <li><input type="text" class="form-control" id="piece" placeholder="نام قطعه"></li>
                                                    <li><input type="text" class="form-control PriceFormat" id="buy_price" placeholder=" به تومان وارد کنید"></li>
                                                </ul>
                                                    <ul class="body_in">
                                                    <li><input type="text" class="form-control" id="piece" placeholder="نام قطعه"></li>
                                                    <li><input type="text" class="form-control PriceFormat" id="buy_price" placeholder=" به تومان وارد کنید"></li>
                                                </ul>
                                                ';

                                }else{
                                    for($i = 0 ; $i < count($piece_list); $i++){
                                        echo '
                                                 <ul class="body_in">
                                                    <li><input type="text" class="form-control" id="piece" placeholder="قطعه" value="'.$piece_list[$i].'"></li>
                                                    
                                                    <li><input type="text" class="form-control PriceFormat" id="buy_price" value="'.number_format($buy_price_list[$i]).'" placeholder="قیمت خرید"></li>
                                                </ul>
                                                ';
                                    }
                                }
                                ?>
                                <!--                                        <li><input type="text" class="form-control PriceFormat" id="customer_price" value="'.number_format($customer_price_list[$i]).'" placeholder="قیمت به مشتری"></li>-->

                            </div>
                            <div class="add_box_temp">
                                <i class="las la-plus"></i>
                                افزودن قطعات بیشتر</div>
                        </div>
                        <input type="hidden" id="percent_operator_number" value="<?=$percent_operator?>">
                        <input type="hidden" id="type_of_calc_tech" value="<?=$type_of_calc_tech?>">
                        <input type="hidden" id="tech_per_of_all" value="<?=$tech_per_of_all?>">
                        <?php if(isset($_GET['test'])){?>
                            <div class="other_price">

                                <div class="form-group half">
                                    <label>اجرت کلی خدمات (تومان)</label>
                                    <input type="text" class="form-control PriceFormat" id="hire" placeholder="اجرت" value="<?php if($hire == ''){}else{echo number_format($hire);}?>">
                                </div>
                                <div class="form-group half">
                                    <label>ایاب و ذهاب (تومان)</label>
                                    <input type="text" class="form-control PriceFormat" id="transportation" placeholder="ایاب و ذهاب" value="<?php if($transportation == ''){}else{echo number_format($transportation);}?>">
                                </div>
                                <div class="form-group third" style="display: none;">
                                    <label>تخفیف</label>
                                    <input type="text" class="form-control PriceFormat" id="discount" disabled placeholder="تخفیف" value="<?php if($discount == ''){}else{number_format($discount);}?>">
                                </div>

                            </div>
                        <?php } ?>
                        <div class="TotalInvoice">

                            <table>
                                <tr>
                                    <th>جمع کل مبلغ فاکتور</th>
                                    <td><span id="total_invoice"><i><?php if($price_customer == ''){echo '0';}else{ echo number_format($price_customer);}?></i> <b>تومان</b></span> </td>
                                </tr>
                                <tr>
                                    <th>جمع هزینه ها قطعات</th>
                                    <td><span id="total_cost"><i><?php if($cost_price == ''){echo '0';}else{echo number_format($cost_price);}?></i> <b>تومان</b></span> </td>
                                </tr>
                                <tr>
                                    <th>مانده(پس از کسر هزینه‌ها)</th>
                                    <td><span id="total_left"><i><?php if($total_invoice == ''){echo '0';}else{echo number_format($total_invoice);}?></i> <b>تومان</b></span> </td>
                                </tr>
                                <tr>

                                    <th>سهم شما از سفارش</th>
                                    <td><span id="total_Company"><i><?php if($co_side_price == ''){echo '0';}else{echo number_format($co_side_price);}?></i> <b>تومان</b></span> </td>
                                </tr>
                            </table>

                        </div>

                        <div class="img_for_orders">

                            <div class="row">

                                <div class="form-group ">
                                    <label>نمای کامل دستگاه به همراه لیبل (اجباری)</label>
                                    <div class="Img_selector <?php if($device_img1 == ''){}else{echo 'uploaded';}?>">
                                        <div class="loading">

                                        </div>
                                        <div class="thumb" style="background-image: url(<?=$device_img1?>) "></div>
                                        <div class="box_upload">
                                            <?=file_get_contents(wp_get_attachment_url(125779));?>
                                            <span>برای بارگزاری تصویر کلیک کنید</span>
                                            <input class="imgUpload" type="file" >
                                            <input class="source_image" type="hidden" id="device_img1"  value="<?=$device_img1?>" >
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="description_invoice">
                            <div class="form-group">
                                <label>
                                متن فاکتور
                                </label>
                                <textarea class="form-control" id="invoice_descripotion" placeholder="قطعات تعویض شده، اجرت و ایاب ذهاب و ..." value="" ><?=$invoice_descripotion?></textarea>

                                <!--<p>توجه: توضیحات وارد شده در کادر بالا به صورت فاکتور آنلاین برای مشتری ارســــال می گردد و اعتبار قانونی دارد، پس از شما خواهشمندیم در ثبت قطعات و توضیحات، دقت لازم را به عمل آوردید. از همکاری شما سپاسگزاریم.</p>-->
                            </div>
                        </div>

                </div>
                     
                        <div class="saveAsDraft">فقط در پیش نویس ذخیره شود</div>
                        <?php
                            if($ready_for_derliver == 1){?>
                                <div class="SendSMSForDeliverCustomer" id="<?=$uid?>">ارسال پیامک آماده تحویل برای مشتری</div>
                            <?php
                            }
                        ?>


            </div>

                        <?php if($status == 4 || $status == 5 ){}else{?>

                            <input  <?=$active?>  type="submit" class="btn" value="ثبت">
                        <?php } ?>
                    </form>
                </div>
                <div style="display: none" class="Ticket_list">
                    <span>لیست تیکت ها</span>
                    <?php
                    $ticket = get_posts(array(
                        "post_type" => "ticket" ,
                        "posts_per_page" => -1 ,
                        "post_status" => "any",
                        "meta_query" => array(
                            "relation" => "OR",
                            array(
                                "key" => "order_ticket",
                                "value" => $uid,
                                "compare" => "LIKE",
                            ),
                            array(
                                "key" => "technician",
                                "value" => get_current_user_id(),
                                "compare" => "LIKE"
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
                <div class="crmMessage">
                <span class="title">لیست اقدامات</span>
                <ul class="desc">
                    <?php
                    krsort($order_description_content);
                    foreach($order_description_content as $item){
                        if($item["author"] == get_current_user_id()){
                            $name_author = get_user_meta($item["author"],"first_name",true);
                            echo '

                                    <li>
                                        <a href="#">
                                            <div class="contentDescription">
                                                <h3>تغییر وضعیت سفارش به :';
                                                echo str_replace("معلق","نامشخص",$item['status']) ; // برای حالت‌هایی که مقدار وضعیت غیر از 1، 2 یا 3 است
                            echo '</h3>
                                            <p>'.$item["content"].'</p>
                                              
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
                                                <b>';
                            echo str_replace("معلق","نامشخص",$item['status']) ; // برای حالت‌هایی که مقدار وضعیت غیر از 1، 2 یا 3 است

                                                            echo '</b>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                ';
                        }
                    }

                    ?>
                </ul>


            </div>
                <div class="crmMessage" style="display: none;" id="noteListOrder">
                    <span class="title">لیست یاداشت ها</span>
                    <ul class="desc">
                        <?php
                        krsort($order_note_content);
                        foreach($order_note_content as $item){
                            if($item["author"] == get_current_user_id()){
                            $name_author = get_user_meta($item["author"],"first_name",true);
                            echo '
                                
                                        <li>
                                            <a href="#">
                                                <div class="contentDescription">
                                                    <h3>'.$item["subject"].'</h3>
                                                    <p>'.$item["content"].'</p>
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
                        }
                        ?>
                    </ul>


                </div>
                <div  class="crmMessage" id="RetrunBackOrderLog">
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
                                if($type_of_calc_tech == 1){
                                    $tech_per = 1 - ($tech_per_of_all / 100);
                                    $item_co_side_price = $item["total_invoice"]  * $tech_per;
                                    $item_co_side_price_1 = $item["total_invoice"] - $item_co_side_price;
                                }else{
                                    $item_co_side_price = ($item["total_invoice"] / 100) * $percent_operator;
                                    $item_co_side_price_1 = $item["total_invoice"] - $item_co_side_price;
                                }


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
                                            <li><input type="text" class="form-control PriceFormat" id="customer_price" placeholder="قیمت به مشتری"></li>

                                            <li><input type="text" class="form-control PriceFormat" id="buy_price" placeholder="قیمت خرید"></li>

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

                                                echo '<input type="text" class="form-control PriceFormat" id="" value="'.number_format($item["customer_price_list"][$i]).'" placeholder="قیمت به مشتری">';

                                            echo '
                                                    </li>
    
                                                        <li>
                                                        ';

                                                echo '<input type = "text" class="form-control PriceFormat" id = "" value = "'.number_format($item["buy_price_list"][$i]).'" placeholder = "قیمت خرید" >';

                                            echo '
                                                    </li >
                                                    </ul>
                                                    ';
                                        }
                                        ?>

                                    </div>
                                </div>
                                <div class="other_price">
                                        <div class="form-group third">
                                            <label>اجرت</label>
                                            <input type="text" class="form-control PriceFormat" id="" placeholder="اجرت" value="<?=number_format($item["hire"])?>">
                                        </div>


                                        <div class="form-group third">
                                            <label>ایاب و ذهاب</label>
                                            <input type="text" class="form-control PriceFormat" id="" placeholder="ایاب و ذهاب" value="<?=number_format($item["transportation"])?>">
                                        </div>


                                        <div class="form-group third">
                                            <label>تخفیف</label>
                                            <input type="text" class="form-control PriceFormat" id="" placeholder="ایاب و ذهاب" value="<?=number_format($item["discount"])?>">
                                        </div>

                                </div>

                                <div class="TotalInvoice">

                                    <table>

                                            <tr>
                                                <th>جمع کل صورت حساب</th>
                                                <td><span id=""><i><?=number_format($item["price_customer"])?></i> <b>تومان</b></span> </td>
                                            </tr>


                                            <tr>
                                                <th>جمع هزینه ها</th>
                                                <td><span id=""><i><?php if($item["cost_price"] == ''){echo '0';}else{echo number_format($item["cost_price"]);}?></i> <b>تومان</b></span> </td>
                                            </tr>


                                            <tr>
                                                <th>مانده</th>
                                                <td><span id=""><i><?=number_format($item["total_invoice"])?></i> <b>تومان</b></span> </td>
                                            </tr>

                                            <tr>
                                                <th>سهم تکنسین</th>
                                                <td><span id=""><i><?php if($item_co_side_price == ''){echo '0';}else{echo number_format($item["co_side_price"]);}?></i> <b>تومان</b></span> </td>
                                            </tr>

                                            <tr>
                                                <th>سهم شرکت</th>
                                                <td><span id=""><i><?php if($item_co_side_price_1 == ''){echo '0';}else{echo number_format($item["co_side_price_1"]);}?></i> <b>تومان</b></span> </td>
                                            </tr>

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

            </div>



        </div>
    </section>

<?php
get_footer();
if($return_description == ""){}else{
    if($status == 4 || $status == 5 ){}else{

        ?>
        <div class="modal fade" id="PopUpOrderReturn" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <h1>بازگشت سفارش</h1>
                    <p><?=$return_description?></p>
                </div>
            </div>
        </div>
        <script>jQuery(document).ready(function (){jQuery("#PopUpOrderReturn").modal("show");})</script>
        <?php

    }
}
?>

       <?php if($status == 10 ){
                        
                        echo '<script>
                                jQuery(document).ready(function(){
                                    jQuery("body input#totalInvoice").keyup();
                                    jQuery("body input#totalInvoice").keydown();
                                    jQuery("body .List_Of_piece").hide();
                                });
                            </script>';
                    } 
                    ?>