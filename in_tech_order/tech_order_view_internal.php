<?php

// Template Name: Internal Tech Order View
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
    $order_images = get_post_meta($uid,"order_images",true);
    $percent_operator = get_user_meta(get_current_user_id(),"percent",true);
    if($total_invoice > 0){
        $co_side_price= ($total_invoice / 100) * $percent_operator;
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
                <h1 class="title">مشاهده سفارش <?=$uid?>
                    | مشتری <?=$first_name?>
                    | کد اشتراک <?=$subscription?>
                </h1>

                <table class="Show_Order table">

                    <tr>
                        <th>مشتری</th>
                        <td><?=$first_name?></td>
                        <th>اشتراک</th>
                        <td><?=$subscription?></td>
                    </tr>
                    <?php if($status == 4 || $status == 5 ){}else{?>
                        <tr>
                            <th>شماره موبایل</th>
                            <td><a id="CallLog" data-uid="<?=$_GET['uid'];?>" href="tel:<?=$mobile?>" target="_blank">تماس با شماره موبایل</a> </td>
                            <th>تلفن تماس</th>
                            <td><a id="CallLog" data-uid="<?=$_GET['uid'];?>" href="tel:<?=$phone?>" target="_blank">تماس با تلفن ثابت</a> </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th>استان</th>
                        <td><?=$state?></td>
                        <th>شهر</th>
                        <td><?=$city?></td>
                    </tr>
                    <?php if($status == 4 || $status == 5 ){}else{?>
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
                    <?php } ?>
                    <tr>
                        <th>تکنسین</th>
                        <td colspan="3"><?=$technician_name?></td>
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

                    </form>


                    <?php
                    if($Have_invoice == ""){
                        $dontEdit = "";
                    }
                    else{
                        $dontEdit = "dontEdit";
                    }
                    ?>

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
                <ul class="list_of_img">
                    <?php
                    for($i = 0 ; $i < count($order_images);$i++){
                        if($order_images[$i] == ""){}else{
                            echo '<li><a href="'.$order_images[$i].'" target="_blank">دانلود تصویر</a></li>';
                        }
                    }
                    ?>
                </ul>
                <div class="Detai_order tech ">

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

                    <form action="#" method="post" id="internal_Tech_DetailAjax" class="<?php if($status == 4 || $status == 5 ){echo 'dontEdit';}?>">
                        <input type="hidden" id="uid" value="<?=$uid?>">
                        <span>وضعیت کار</span>
<!--                        <div class="form-group two">-->
<!--                            <label class="--><?php //if($status == 1 ){echo 'active';}?><!--">-->
<!--                                <span>هماهنگ شده</span>-->
<!--                                <input --><?//=$s_001?><!-- --><?//=$active?><!-- --><?php //if($status == 1 ){echo 'checked';}?><!-- type="radio" name="status" id="status" value="1" data-action="desc">-->
<!--                            </label>-->
<!--                        </div>-->
<!--                        <div class="form-group two">-->
<!--                            <label class="--><?php //if($status == 2 ){echo 'active';}?><!--">-->
<!--                                <span>بازشد</span>-->
<!--                                <input --><?//=$s_002?><!-- --><?//=$active?><!-- --><?php //if($status == 2 ){echo 'checked';}?><!-- type="radio" name="status" id="status" value="2" data-action="desc">-->
<!--                            </label>-->
<!--                        </div>-->
<!--                        <div class="form-group two">-->
<!--                            <label class="--><?php //if($status == 3 ){echo 'active';}?><!--">-->
<!--                                <span>نامشخص</span>-->
<!--                                <input --><?//=$s_003?><!-- --><?//=$active?><!--  --><?php //if($status == 3 ){echo 'checked';}?><!-- type="radio" name="status" id="status" value="3"  data-action="desc">-->
<!--                            </label>-->
<!--                        </div>-->
                        <div class="form-group two">
                            <label class="<?php if($status == 4 ){echo 'active';}?>">
                                <span>کنسل شده</span>
                                <input  <?=$s_004?> <?=$active?>  <?php if($status == 4 ){echo 'checked';}?> type="radio" name="status" id="status" value="4"  data-action="cancel">
                            </label>
                        </div>

                        <div class="form-group two">
                            <label class="<?php if($status == 5 ){echo 'active';}?>">
                                <span>پایان کار</span>
                                <input  <?=$s_005?> <?=$active?>  <?php if($status == 5 ){echo 'checked';}?> type="radio" name="status" id="status" value="5"  data-action="done">
                            </label>
                        </div>


                        <div class="box_status_return <?php if(in_array($status,array(1,2,3))){echo 'active';}?>" id="desc">

                            <div class="form-group">

                                <label>توضیحات</label>
                                <textarea class="form-control " id="description_tech"></textarea>

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
                                <input type="text" class="form-control" id="cancel_desc_other" value="<?=$cancel_desc_other?>">
                            </div>

                        </div>
                        <div class="box_status_return <?php if($status == 5 ){echo 'active';}?>"" id="done">

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
                            <div class="List_Of_piece">
                                <ul class="head_in">
                                    <li>قطعه</li>
                                    <li> قیمت به مشتری (تومان)</li>
                                    <li>قیمت خرید (تومان)</li>
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
                                    if($piece_list == ''){

                                        echo '
                                                 <ul class="body_in">
                                                    <li><input type="text" class="form-control" id="piece" placeholder="قطعه"></li>
                                                    <li><input type="text" class="form-control PriceFormat" id="customer_price" placeholder="قیمت به مشتری"></li>
                                                    <li><input type="text" class="form-control PriceFormat" id="buy_price" placeholder="قیمت خرید"></li>
                                                </ul>
                                                ';

                                    }else{
                                        for($i = 0 ; $i < count($piece_list); $i++){
                                            echo '
                                                 <ul class="body_in">
                                                    <li><input type="text" class="form-control" id="piece" placeholder="قطعه" value="'.$piece_list[$i].'"></li>
                                                    <li><input type="text" class="form-control PriceFormat" id="customer_price" value="'.number_format($customer_price_list[$i]).'" placeholder="قیمت به مشتری"></li>
                                                    <li><input type="text" class="form-control PriceFormat" id="buy_price" value="'.number_format($buy_price_list[$i]).'" placeholder="قیمت خرید"></li>
                                                </ul>
                                                ';
                                        }
                                    }
                                    ?>

                                </div>
                                <div class="add_box_temp">افزودن قطعات بیشتر</div>
                            </div>
                            <input type="hidden" id="percent_operator_number" value="<?=$percent_operator?>">
                            <div class="other_price">

                                <div class="form-group half">
                                    <label>اجرت (تومان)</label>
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

                            <div class="TotalInvoice">

                                <table>
                                    <tr>
                                        <th>جمع کل صورت حساب</th>
                                        <td><span id="total_invoice"><i><?php if($price_customer == ''){echo '0';}else{ echo number_format($price_customer);}?></i> <b>تومان</b></span> </td>
                                    </tr>
                                    <tr>
                                        <th>جمع هزینه ها</th>
                                        <td><span id="total_cost"><i><?php if($cost_price == ''){echo '0';}else{echo number_format($cost_price);}?></i> <b>تومان</b></span> </td>
                                    </tr>
                                    <tr>
                                        <th>مانده</th>
                                        <td><span id="total_left"><i><?php if($total_invoice == ''){echo '0';}else{echo number_format($total_invoice);}?></i> <b>تومان</b></span> </td>
                                    </tr>
                                    <tr>

                                        <th>سهم تکنسین</th>
                                        <td><span id="total_Company"><i><?php if($co_side_price == ''){echo '0';}else{echo number_format($co_side_price);}?></i> <b>تومان</b></span> </td>
                                    </tr>
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
                <?php if($status == 4 || $status == 5 ){}else{?>
                    <input  <?=$active?>  type="submit" class="btn" value="ثبت">
                <?php } ?>
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
                <span class="title">لیست پیام ها</span>
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
            <div class="crmMessage" id="noteListOrder">
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