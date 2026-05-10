<?php

// Template Name: Add Operator

get_header();

if(isset($_GET['uid'])){
    IFLoggedAndVerify("edit_operator");
    $title_section = 'ویرایش اپراتور';
    $btn = 'ویرایش اپراتور';
    $action = 'update';
    $uid = $_GET['uid'];
    $user = get_user_by("ID",$uid);
    $username = $user->user_login;
    $firstname = get_user_meta($uid,"first_name",true);
    $operator_id = get_user_meta($uid,"operator_id",true);
    $national_code = get_user_meta($uid,"national_code",true);
    $mobile = get_user_meta($uid,"mobile",true);
    $phone = get_user_meta($uid,"phone",true);
    $address = get_user_meta($uid,"address",true);
    $description = get_user_meta($uid,"description",true);
    $operator = get_user_meta($uid,"operator",true);
    $call = get_user_meta($uid,"call",true);
    $price = get_user_meta($uid,"price",true);
    $StartTimeWork = get_user_meta($uid,"StartTimeWork",true);
    $endTimeWork = get_user_meta($uid,"endTimeWork",true);
    $document = get_user_meta($uid,"document",true);
    $img_profile = get_user_meta($uid,"img_profile",true);
    $access = get_user_meta($uid,"access",true);
    $status = get_user_meta($uid,"status",true);

}else{
    IFLoggedAndVerify("add_operator");
    $title_section = 'افزودن اپراتور';
    $btn = 'افزودن اپراتور';
    $action = 'add';

    $uid = "";
    $username = "";
    $firstname = "";
    $operator_id = "";
    $national_code = "";
    $mobile = "";
    $phone ="";
    $address = "";
    $description = "";
    $operator = "";
    $call = "";
    $price = "";
    $StartTimeWork = "";
    $endTimeWork ="";
    $document = "";
    $img_profile = "";
    $access = "";
}
?>
    <section class="DashboardSection">
        <?=NavDashboard();?>
        <div class="main">
            <div class="content">
                <h1 class="title"><?=$title_section?></h1>

                <form action="#" method="post" id="OperatorAjax" >

                    <input type="hidden" id="type_action" value="<?=$action?>">
                    <input type="hidden" id="uid" value="<?=$uid?>">


                    <div class="form-group half">
                        <label>نام کاربری</label>
                        <input type="text" <?php if($action == "update"){echo 'disabled';}?> class="form-control validate" value="<?=$username?>"  data-validate="empty" id="username">
                    </div>

                    <div class="form-group half">
                        <?php if($action == "update"){?>
                            <label>کلمه عبور (در صورتی که میخواهید کلمه عبور تغییر نکند این فیلد را خالی بگذارید.)</label>
                            <input type="password" class="form-control" id="password">
                            <?php } else{?>
                                <label>کلمه عبور</label>
                                <input type="password" class="form-control validate" data-validate="password" id="password">
                            <?php } ?>
                    </div>

                    <div class="form-group third">
                        <label>نام و نام خانوادگی</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="firstname" value="<?=$firstname?>">
                    </div>

                    <div class="form-group third">
                        <label>شماره شناسامه</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="operator_id" value="<?=$operator_id?>">
                    </div>

                    <div class="form-group third">
                        <label>کدملی</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="national_code"  value="<?=$national_code?>">
                    </div>
                    <div class="form-group half">
                        <label>شماره موبایل</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="mobile" value="<?=$mobile?>">
                    </div>

                    <div class="form-group half">
                        <label>شماره ثابت</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="phone" value="<?=$phone?>">
                    </div>

                    <div class="form-group half">
                        <label>آدرس محل سکونت</label>
                        <textarea class="form-control validate"  data-validate="empty" id="address"><?=$address?></textarea>
                    </div>

                    <div class="form-group half">
                        <label>توضیحات</label>
                        <textarea class="form-control validate"  data-validate="empty" id="description"><?=$description?></textarea>
                    </div>

                    <div class="form-group half">
                        <label>سمت اپراتور</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="operator" value="<?=$operator?>">
                    </div>
                    <div class="form-group half">
                        <label>داخلی اپراتور</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="call" value="<?=$call?>">
                    </div>
                    <div class="form-group half">
                        <label>میزان حقوق دریافتی</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="price" value="<?=$price?>" >
                    </div>

                    <div class="form-group half">
                        <label>ساعت کاری</label>
                        <select class="form-control half validate"  data-validate="empty" id="StartTimeWork">
                            <option value="">شروع ساعت کاری</option>
                            <?php
                                for($i = 1; $i <= 24 ; $i++){
                                    if($i == $StartTimeWork){
                                        $selected = "selected";
                                    }
                                    else{
                                        $selected = '';
                                    }
                                    echo '<option '.$selected.' value="'.$i.'">'.$i.'</option>';
                                }
                            ?>
                        </select>
                        <select class="form-control half validate"  data-validate="empty" id="endTimeWork">
                            <option value="">پایان ساعت کاری</option>
                            <?php
                            for($i = 1; $i <= 24 ; $i++){
                                if($i == $endTimeWork){
                                    $selected = "selected";
                                }
                                else{
                                    $selected = '';
                                }
                                echo '<option '.$selected.' value="'.$i.'">'.$i.'</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group half">

                        <label>بارگذاری مدراک</label>
                        <div class="Img_selector">
                            <div class="loading">

                            </div>
                            <div class="thumb" style="background-image:url(<?=$document?>) "></div>
                            <div class="box_upload">
                              <?=file_get_contents(wp_get_attachment_url(125779));?>
                                <span>برای بارگذاری تصویر (بکشید / کلیک کنید)</span>
                                <input class="imgUpload" type="file" >
                                <input class="source_image" type="hidden" id="document" value="<?=$document?>" >
                            </div>
                        </div>
                    </div>
                    <div class="form-group half">
                        <label>عکس پروفایل</label>
                        <div class="Img_selector">
                            <div class="loading">

                            </div>
                            <div class="thumb"  style="background-image:url(<?=$img_profile?>) "></div>
                            <div class="box_upload">
                                <?=file_get_contents(wp_get_attachment_url(125779));?>
                                <span>برای بارگذاری تصویر (بکشید / کلیک کنید)</span>
                                <input class="imgUpload" type="file" >
                                <input class="source_image" type="hidden" id="img_profile" value="<?=$img_profile?>" >
                            </div>
                        </div>
                    </div>

                    <div class="form-group">

                        <label>وضعیت اپراتور</label>

                        <select class="form-control" id="status">
                            <option <?php if($status == 1){echo 'selected';}?> value="1">فعال</option>
                            <option <?php if($status == 0){echo 'selected';}?> value="0">غیرفعال</option>
                        </select>
                    </div>


                    <div class="form-group">

                        <label>دسترسی های</label>

                        <div class="Box_Access">

                            <span class="Title">اپراتور ها</span>

                            <div class="access_list">
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("operator_list",$access)){echo 'checked';}?> value="operator_list">
                                    <span class="desc">مشاهده اپراتور</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("add_operator",$access)){echo 'checked';}?> value="add_operator">
                                    <span class="desc">افزودن اپراتور</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("edit_operator",$access)){echo 'checked';}?> value="edit_operator">
                                    <span class="desc">ویرایش اپراتور</span>
                                </div>
                            </div>


                        </div>

                        <div class="Box_Access">

                            <span class="Title">نمایش ها</span>

                            <div class="access_list">
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("hr_list",$access)){echo 'checked';}?> value="hr_list">
                                    <span class="desc">منابع انسانی</span>
                                </div>
                            </div>
                            <div class="access_list">
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_setting",$access)){echo 'checked';}?> value="order_setting">
                                    <span class="desc">تنظیمات سفارش</span>
                                </div>
                            </div>


                        </div>

                        <div class="Box_Access">
                            <span class="Title">مشتری ها</span>

                            <div class="access_list">
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("customer_list",$access)){echo 'checked';}?> value="customer_list">
                                    <span class="desc">مشاهده مشتری</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("add_customer",$access)){echo 'checked';}?> value="add_customer">
                                    <span class="desc">افزودن مشتری</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("edit_customer",$access)){echo 'checked';}?> value="edit_customer">
                                    <span class="desc">ویرایش مشتری</span>
                                </div>
                            </div>


                        </div>

                        <div class="Box_Access">

                            <span class="Title">تکنسین ها</span>

                            <div class="access_list">
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("technician_list",$access)){echo 'checked';}?> value="technician_list">
                                    <span class="desc">مشاهده تکنسین</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("add_technician",$access)){echo 'checked';}?> value="add_technician">
                                    <span class="desc">افزودن تکنسین</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("edit_technician",$access)){echo 'checked';}?> value="edit_technician">
                                    <span class="desc">ویرایش تکنسین</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("search_technician",$access)){echo 'checked';}?> value="search_technician">
                                    <span class="desc">جستجوی تکنسین</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("point_technician",$access)){echo 'checked';}?> value="point_technician">
                                    <span class="desc">مشاهده امتیازات تکنسین ها</span>
                                </div>
                            </div>


                        </div>

                        <div class="Box_Access">

                            <span class="Title">سفارش ها</span>

                            <div class="access_list">
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_list",$access)){echo 'checked';}?> value="order_list">
                                    <span class="desc">مشاهده سفارش</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("add_order",$access)){echo 'checked';}?> value="add_order">
                                    <span class="desc">افزودن سفارش</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("edit_order",$access)){echo 'checked';}?> value="edit_order">
                                    <span class="desc">ویرایش سفارش</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_technician",$access)){echo 'checked';}?> value="order_technician">
                                    <span class="desc">انتخاب تکنسین برای سفارش</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_status",$access)){echo 'checked';}?> value="order_status">
                                    <span class="desc">تغییر وضعیت سفارش</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_archive",$access)){echo 'checked';}?> value="order_archive">
                                    <span class="desc">بایگانی سفارشات</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_export",$access)){echo 'checked';}?> value="order_export">
                                    <span class="desc">خروجی اکسل سفارشات</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_return",$access)){echo 'checked';}?> value="order_return">
                                    <span class="desc">بازگشت سفارش</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_return_log",$access)){echo 'checked';}?> value="order_return_log">
                                    <span class="desc">مشاهده لاگ بازگشت سفارش</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_unit_buy",$access)){echo 'checked';}?> value="order_unit_buy">
                                    <span class="desc">مشاهده قیمت خرید قطعه ها</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_unit_sales",$access)){echo 'checked';}?> value="order_unit_sales">
                                    <span class="desc">مشاهده قیمت به مشتری قطعه ها</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_hire",$access)){echo 'checked';}?> value="order_hire">
                                    <span class="desc">مشاهده هزینه اجرت</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_delivery",$access)){echo 'checked';}?> value="order_delivery">
                                    <span class="desc">مشاهده هزینه ایاب و ذهاب</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_discount",$access)){echo 'checked';}?> value="order_discount">
                                    <span class="desc">مشاهده تخفیف</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_total_price",$access)){echo 'checked';}?> value="order_total_price">
                                    <span class="desc">مشاهده کل صورت حساب</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_total_cost",$access)){echo 'checked';}?> value="order_total_cost">
                                    <span class="desc">مشاهده جمع هزینه ها</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_total_left",$access)){echo 'checked';}?> value="order_total_left">
                                    <span class="desc">مشاهده مانده</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_tech_side",$access)){echo 'checked';}?> value="order_tech_side">
                                    <span class="desc">مشاهده سهم تکنسین</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_co_side",$access)){echo 'checked';}?> value="order_co_side">
                                    <span class="desc">مشاهده سهم شرکت</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_happycall_customer",$access)){echo 'checked';}?> value="order_happycall_customer">
                                    <span class="desc">هپی کال مشتریان</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_happycall_tech",$access)){echo 'checked';}?> value="order_happycall_tech">
                                    <span class="desc">هپی کال تکنسین</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_select_type_user",$access)){echo 'checked';}?> value="order_select_type_user">
                                    <span class="desc">تعیین نوع مشتری</span>
                                </div>


                            </div>


                        </div>

                        <div class="Box_Access">

                            <span class="Title">سفارش ها داخل مجموعه</span>

                            <div class="access_list">
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_list_internal",$access)){echo 'checked';}?> value="order_list_internal">
                                    <span class="desc">مشاهده سفارش</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("add_order_internal",$access)){echo 'checked';}?> value="add_order_internal">
                                    <span class="desc">افزودن سفارش</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("edit_order_internal",$access)){echo 'checked';}?> value="edit_order_internal">
                                    <span class="desc">ویرایش سفارش</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("show_order_internal",$access)){echo 'checked';}?> value="show_order_internal">
                                    <span class="desc">مشاهده سفارش</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("print_order_internal",$access)){echo 'checked';}?> value="print_order_internal">
                                    <span class="desc">پرینت سفارش</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("add_shipping_method_internal",$access)){echo 'checked';}?> value="add_shipping_method_internal">
                                    <span class="desc">نحوه ارسال کار</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("add_status_internal",$access)){echo 'checked';}?> value="add_status_internal">
                                    <span class="desc">تغییر وضعیت</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("add_tech_order_internal",$access)){echo 'checked';}?> value="add_tech_order_internal">
                                    <span class="desc">انتخاب تکنسین</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("qc_order_internal",$access)){echo 'checked';}?> value="qc_order_internal">
                                    <span class="desc">واحد کنترل</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("send_order_internal",$access)){echo 'checked';}?> value="send_order_internal">
                                    <span class="desc">ارسال بشود</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("receive_order_internal",$access)){echo 'checked';}?> value="receive_order_internal">
                                    <span class="desc">دریافت بشود</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("cancel_order_internal",$access)){echo 'checked';}?> value="cancel_order_internal">
                                    <span class="desc">کنسل شده</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("done_order_internal",$access)){echo 'checked';}?> value="done_order_internal">
                                    <span class="desc">انجام شده</span>
                                </div>




                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_unit_buy_internal",$access)){echo 'checked';}?> value="order_unit_buy_internal">
                                    <span class="desc">مشاهده قیمت خرید قطعه ها</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_unit_sales_internal",$access)){echo 'checked';}?> value="order_unit_sales_internal">
                                    <span class="desc">مشاهده قیمت به مشتری قطعه ها</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_hire_internal",$access)){echo 'checked';}?> value="order_hire_internal">
                                    <span class="desc">مشاهده هزینه اجرت</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_delivery_internal",$access)){echo 'checked';}?> value="order_delivery_internal">
                                    <span class="desc">مشاهده هزینه ایاب و ذهاب</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_discount_internal",$access)){echo 'checked';}?> value="order_discount_internal">
                                    <span class="desc">مشاهده تخفیف</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_total_price_internal",$access)){echo 'checked';}?> value="order_total_price_internal">
                                    <span class="desc">مشاهده کل صورت حساب</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_total_cost_internal",$access)){echo 'checked';}?> value="order_total_cost_internal">
                                    <span class="desc">مشاهده جمع هزینه ها</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_total_left_internal",$access)){echo 'checked';}?> value="order_total_left_internal">
                                    <span class="desc">مشاهده مانده</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_tech_side_internal",$access)){echo 'checked';}?> value="order_tech_side_internal">
                                    <span class="desc">مشاهده سهم تکنسین</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_co_side_internal",$access)){echo 'checked';}?> value="order_co_side_internal">
                                    <span class="desc">مشاهده سهم شرکت</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("accept_qc_order_internal",$access)){echo 'checked';}?> value="accept_qc_order_internal">
                                    <span class="desc">تایید واحد کنترل</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("payment_order_internal",$access)){echo 'checked';}?> value="payment_order_internal">
                                    <span class="desc">وضعیت پرداخت</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("edit_invoice_order_internal",$access)){echo 'checked';}?> value="edit_invoice_order_internal">
                                    <span class="desc">ویرایش فاکتور</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_internal_happycall_customer",$access)){echo 'checked';}?> value="order_internal_happycall_customer">
                                    <span class="desc">هپی کال مشتریان</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_internal_happycall_tech",$access)){echo 'checked';}?> value="order_internal_happycall_tech">
                                    <span class="desc">هپی کال تکنسین</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("order_internal_select_type_user",$access)){echo 'checked';}?> value="order_internal_select_type_user">
                                    <span class="desc">تعیین نوع مشتری</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("payment_pend",$access)){echo 'checked';}?> value="payment_pend">
                                    <span class="desc">منتظر پرداخت</span>
                                </div>
                            </div>


                        </div>

                        <div class="Box_Access">

                            <span class="Title">برند ها</span>

                            <div class="access_list">
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("brand_list",$access)){echo 'checked';}?> value="brand_list">
                                    <span class="desc">مشاهده برند</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("add_brand",$access)){echo 'checked';}?> value="add_brand">
                                    <span class="desc">افزودن برند</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("edit_brand",$access)){echo 'checked';}?> value="edit_brand">
                                    <span class="desc">ویرایش برند</span>
                                </div>
                            </div>


                        </div>

                        <div class="Box_Access">

                            <span class="Title">دستگاه ها</span>

                            <div class="access_list">
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("device_list",$access)){echo 'checked';}?> value="device_list">
                                    <span class="desc">مشاهده دستگاه</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("add_device",$access)){echo 'checked';}?> value="add_device">
                                    <span class="desc">افزودن دستگاه</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("edit_device",$access)){echo 'checked';}?> value="edit_device">
                                    <span class="desc">ویرایش دستگاه</span>
                                </div>
                            </div>


                        </div>

                        <div class="Box_Access">

                            <span class="Title">استان ها</span>

                            <div class="access_list">
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("state_list",$access)){echo 'checked';}?> value="state_list">
                                    <span class="desc">مشاهده استان</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("add_state",$access)){echo 'checked';}?> value="add_state">
                                    <span class="desc">افزودن استان</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("edit_state",$access)){echo 'checked';}?> value="edit_state">
                                    <span class="desc">ویرایش استان</span>
                                </div>
                            </div>


                        </div>

                        <div class="Box_Access">

                            <span class="Title">منطقه ها</span>

                            <div class="access_list">
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("city_list",$access)){echo 'checked';}?> value="city_list">
                                    <span class="desc">مشاهده منطقه</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("add_city",$access)){echo 'checked';}?> value="add_city">
                                    <span class="desc">افزودن منطقه</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("edit_city",$access)){echo 'checked';}?> value="edit_city">
                                    <span class="desc">ویرایش منطقه</span>
                                </div>
                            </div>


                        </div>

                        <div class="Box_Access">

                            <span class="Title">تیکت ها</span>

                            <div class="access_list">
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("ticket_list",$access)){echo 'checked';}?> value="ticket_list">
                                    <span class="desc">مشاهده تیکت</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("add_ticket",$access)){echo 'checked';}?> value="add_ticket">
                                    <span class="desc">افزودن تیکت</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("edit_ticket",$access)){echo 'checked';}?> value="edit_ticket">
                                    <span class="desc">ویرایش تیکت</span>
                                </div>
                            </div>


                        </div>

                        <div class="Box_Access">

                            <span class="Title">تنظیمات</span>

                            <div class="access_list">
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("setting",$access)){echo 'checked';}?> value="setting">
                                    <span class="desc">تنظیمات</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("sms_setting",$access)){echo 'checked';}?> value="sms_setting">
                                    <span class="desc">تنظیمات پیامک</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("payment_setting",$access)){echo 'checked';}?> value="payment_setting">
                                    <span class="desc">تنظیمات پرداخت</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("cancel_setting",$access)){echo 'checked';}?> value="cancel_setting">
                                    <span class="desc">تنظیمات کنسلی</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("objection_setting",$access)){echo 'checked';}?> value="objection_setting">
                                    <span class="desc">تنظیمات ایرادات</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("accounting_setting",$access)){echo 'checked';}?> value="accounting_setting">
                                    <span class="desc">تنظیمات حسابداری</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("introduction_setting",$access)){echo 'checked';}?> value="introduction_setting">
                                    <span class="desc">تنظیمات نحوه آشنایی</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("invoice_setting",$access)){echo 'checked';}?> value="invoice_setting">
                                    <span class="desc">تنظیمات فاکتور</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("shipping_setting",$access)){echo 'checked';}?> value="shipping_setting">
                                    <span class="desc">تنظیمات نحوه ارسال</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("happycall_tech_setting",$access)){echo 'checked';}?> value="happycall_tech_setting">
                                    <span class="desc">تنظیمات هپی کال تکنسین</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("happycall_customer_setting",$access)){echo 'checked';}?> value="happycall_customer_setting">
                                    <span class="desc">تنظیمات هپی کال مشتریان</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("sms_deliver_setting",$access)){echo 'checked';}?> value="sms_deliver_setting">
                                    <span class="desc">تنظیمات پیامک آماده تحویل</span>
                                </div>
                            </div>

                        </div>

                        <div class="Box_Access">

                            <span class="Title">حسابداری خارجی</span>

                            <div class="access_list">
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("list_financial",$access)){echo 'checked';}?> value="list_financial">
                                    <span class="desc">حسابداری</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("add_financial",$access)){echo 'checked';}?> value="add_financial">
                                    <span class="desc">افزودن فاکتور حسابداری</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("tech_debt",$access)){echo 'checked';}?> value="tech_debt">
                                    <span class="desc">لیست بدهی های تکنسین ها</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("edit_financial",$access)){echo 'checked';}?> value="edit_financial">
                                    <span class="desc">ویرایش فاکتور</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("delete_financial",$access)){echo 'checked';}?> value="delete_financial">
                                    <span class="desc">حذف فاکتور</span>
                                </div>
                            </div>


                        </div>
                        <div class="Box_Access">

                            <span class="Title">حسابداری داخلی</span>

                            <div class="access_list">
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("list_financial_internal",$access)){echo 'checked';}?> value="list_financial_internal">
                                    <span class="desc">حسابداری داخلی</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("add_financial_internal",$access)){echo 'checked';}?> value="add_financial_internal">
                                    <span class="desc">افزودن فاکتور حسابداری داخلی</span>
                                </div>
                            </div>


                        </div>

                        <?php
                            if(get_current_user_id() == "12" || get_current_user_id()== "22"){
                        ?>

                        <div class="Box_Access">

                            <span class="Title">هزینه ها</span>

                            <div class="access_list">
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("add_cost",$access)){echo 'checked';}?> value="add_cost">
                                    <span class="desc">افزودن هزینه</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("edit_cost",$access)){echo 'checked';}?> value="edit_cost">
                                    <span class="desc">ویرایش هزینه</span>
                                </div>

                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("list_cost",$access)){echo 'checked';}?> value="list_cost">
                                    <span class="desc">لیست هزینه ها</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("setting_cost",$access)){echo 'checked';}?> value="setting_cost">
                                    <span class="desc">تنظیمات هزینه ها</span>
                                </div>
                            </div>


                        </div>

                        <?php } ?>


                        <div class="Box_Access">

                            <span class="Title">هپی کال</span>

                            <div class="access_list">
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("show_happyCall",$access)){echo 'checked';}?> value="show_happyCall">
                                    <span class="desc">هپی کال</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("show_happyCall_internal",$access)){echo 'checked';}?> value="show_happyCall_internal">
                                    <span class="desc">هپی کال سفارشات داخلی</span>
                                </div>
                                <div class="form-group two">
                                    <input type="checkbox" id="access" name="access[]" <?php if(in_array("show_happyCall_external",$access)){echo 'checked';}?> value="show_happyCall_external">
                                    <span class="desc">هپی کال سفارشات خارجی</span>
                                </div>
                            </div>


                        </div>
                    </div>

                    <input type="submit" class="btn" value="<?=$btn;?>">

                </form>

            </div>
        </div>
    </section>
<?php
get_footer();

?>