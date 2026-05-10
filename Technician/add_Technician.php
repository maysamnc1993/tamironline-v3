<?php

// Template Name: Add Technician

get_header();

if(isset($_GET['uid'])){
//    IFLoggedAndVerify("edit_technician");
    $title_section = 'ویرایش اپراتور';
    $btn = 'ویرایش اپراتور';
    $action = 'update';
    $uid = $_GET['uid'];
    $user = get_user_by("ID",$uid);
    $username = $user->user_login;
    $firstname = get_user_meta($uid,"first_name",true);
    $firstname_tech = get_user_meta($uid,"first_name_tech",true);
    $technician_id = get_user_meta($uid,"technician_id",true);
    $national_code = get_user_meta($uid,"national_code",true);
    $mobile = get_user_meta($uid,"mobile",true);
    $phone = get_user_meta($uid,"phone",true);
    $phone_force = get_user_meta($uid,"phone_force",true);
    $address = get_user_meta($uid,"address",true);
    $description = get_user_meta($uid,"description",true);
    $percent = get_user_meta($uid,"percent",true);
    $max_order = get_user_meta($uid,"max_order",true);
    $max_price = get_user_meta($uid,"max_price",true);
    $status = get_user_meta($uid,"status",true);
    $cart_img = get_user_meta($uid,"cart_img",true);
    $type_tech = get_user_meta($uid,"type_tech",true);
    $province = get_user_meta($uid, 'province', true);
    $specialty = get_user_meta($uid, 'specialty', true);
    $ready_for_derliver = get_user_meta($uid, 'ready_for_derliver', true);
    $type_of_calc_tech = get_user_meta($uid, 'type_of_calc_tech', true);
    $tech_per_of_all = get_user_meta($uid, 'tech_per_of_all', true);
    $specialty = explode(',', $specialty);


}else{
//    IFLoggedAndVerify("add_technician");
    $title_section = 'افزودن تکنسین';
    $btn = 'افزودن تکنسین';
    $action = 'add';

    $uid = "";
    $username = "";
    $firstname = "";
    $firstname_tech="";
    $technician_id = "";
    $national_code = "";
    $mobile = "";
    $phone ="";
    $phone_force ="";
    $address = "";
    $description = "";
    $percent = "";
    $max_order = "";
    $max_price = "";
    $cart_img = "";
    $type_tech = "";
    $specialty = array(); // ایجاد آرایه خالی در حالت افزودن تکنسین
    $province = ""; // استان خالی در حالت افزودن
    $type_of_calc_tech = ""; // استان خالی در حالت افزودن


}

?>
    <section class="DashboardSection">
        <?=NavDashboard();?>
        <div class="main">
            <div class="content">
                <h1 class="title"><?=$title_section?></h1>

                <form action="#" method="post" id="TechnicianAjax" >

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
                    <div class="form-group half">
                        <label>نام و نام خانوادگی</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="firstname" value="<?=$firstname?>">
                    </div>
                    <div class="form-group third">
                        <label> نام و نام خانوادگی کارت تکنسین</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="firstname_tech" value="<?=$firstname_tech?>">
                    </div>


                    <div class="form-group third">
                        <label>شماره شناسامه</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="technician_id_sh" value="<?=$technician_id?>">
                    </div>

                    <div class="form-group third">
                        <label>کدملی</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="national_code"  value="<?=$national_code?>">
                    </div>

                    <div class="form-group third">
                        <label>شماره موبایل</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="mobile" value="<?=$mobile?>">
                    </div>

                    <div class="form-group third">
                        <label>شماره ثابت</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="phone" value="<?=$phone?>">
                    </div>
                    <div class="form-group third">
                        <label>شماره اضطراری</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="phone_force" value="<?=$phone_force?>">
                    </div>



                    <div class="form-group half">
                        <label>آدرس محل سکونت</label>
                        <textarea class="form-control validate"  data-validate="empty" id="address"><?=$address?></textarea>
                    </div>

                    <div class="form-group half">
                        <label>توضیحات</label>
                        <textarea class="form-control validate"  data-validate="empty" id="description"><?=$description?></textarea>
                    </div>

                    <div class="form-group third">
                        <label>میزان سهم تکنسین (درصد)</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="percent" value="<?=$percent?>">
                    </div>
                    <div class="form-group third">
                        <label>میزان سقف تعداد سفارش</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="max_order" value="<?=$max_order?>">
                    </div>
                    <div class="form-group third">
                        <label>میزان سقف مبلغ (تومان)</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="max_price" value="<?=$max_price?>" >
                    </div>
                    <div class="form-group third">

                        <label>وضعیت تکنسین</label>

                        <select class="form-control" id="status">
                            <option <?php if($status == 1){echo 'selected';}?> value="1">فعال</option>
                            <option <?php if($status == 0){echo 'selected';}?> value="0">غیرفعال</option>
                        </select>
                    </div>
                    <div class="form-group third">

                        <label>نوع تکنسین</label>

                        <select class="form-control" id="type_tech">
                            <option <?php if($type_tech == "external"){echo 'selected';}?> value="external">خارج مجموعه</option>
                            <option <?php if($type_tech == "internal"){echo 'selected';}?> value="internal">داخل مجموعه</option>
                        </select>
                    </div>
                    <div class="form-group third">
                        <label>منطقه</label>
                        <select class="form-control validate" id="province" name="province" data-validate="empty">
                            <option value="">انتخاب استان</option>
                            <option value="آذربایجان شرقی" <?php if($province == 'آذربایجان شرقی'){echo 'selected';}?>>آذربایجان شرقی</option>
                            <option value="آذربایجان غربی" <?php if($province == 'آذربایجان غربی'){echo 'selected';}?>>آذربایجان غربی</option>
                            <option value="اردبیل" <?php if($province == 'اردبیل'){echo 'selected';}?>>اردبیل</option>
                            <option value="اصفهان" <?php if($province == 'اصفهان'){echo 'selected';}?>>اصفهان</option>
                            <option value="البرز" <?php if($province == 'البرز'){echo 'selected';}?>>البرز</option>
                            <option value="ایلام" <?php if($province == 'ایلام'){echo 'selected';}?>>ایلام</option>
                            <option value="بوشهر" <?php if($province == 'بوشهر'){echo 'selected';}?>>بوشهر</option>
                            <option value="تهران" <?php if($province == 'تهران'){echo 'selected';}?>>تهران</option>
                            <option value="چهارمحال و بختیاری" <?php if($province == 'چهارمحال و بختیاری'){echo 'selected';}?>>چهارمحال و بختیاری</option>
                            <option value="خراسان جنوبی" <?php if($province == 'خراسان جنوبی'){echo 'selected';}?>>خراسان جنوبی</option>
                            <option value="خراسان رضوی" <?php if($province == 'خراسان رضوی'){echo 'selected';}?>>خراسان رضوی</option>
                            <option value="خراسان شمالی" <?php if($province == 'خراسان شمالی'){echo 'selected';}?>>خراسان شمالی</option>
                            <option value="خوزستان" <?php if($province == 'خوزستان'){echo 'selected';}?>>خوزستان</option>
                            <option value="زنجان" <?php if($province == 'زنجان'){echo 'selected';}?>>زنجان</option>
                            <option value="سمنان" <?php if($province == 'سمنان'){echo 'selected';}?>>سمنان</option>
                            <option value="سیستان و بلوچستان" <?php if($province == 'سیستان و بلوچستان'){echo 'selected';}?>>سیستان و بلوچستان</option>
                            <option value="فارس" <?php if($province == 'فارس'){echo 'selected';}?>>فارس</option>
                            <option value="قزوین" <?php if($province == 'قزوین'){echo 'selected';}?>>قزوین</option>
                            <option value="قم" <?php if($province == 'قم'){echo 'selected';}?>>قم</option>
                            <option value="کردستان" <?php if($province == 'کردستان'){echo 'selected';}?>>کردستان</option>
                            <option value="کرمان" <?php if($province == 'کرمان'){echo 'selected';}?>>کرمان</option>
                            <option value="کرمانشاه" <?php if($province == 'کرمانشاه'){echo 'selected';}?>>کرمانشاه</option>
                            <option value="کهگیلویه و بویراحمد" <?php if($province == 'کهگیلویه و بویراحمد'){echo 'selected';}?>>کهگیلویه و بویراحمد</option>
                            <option value="گلستان" <?php if($province == 'گلستان'){echo 'selected';}?>>گلستان</option>
                            <option value="گیلان" <?php if($province == 'گیلان'){echo 'selected';}?>>گیلان</option>
                            <option value="لرستان" <?php if($province == 'لرستان'){echo 'selected';}?>>لرستان</option>
                            <option value="مازندران" <?php if($province == 'مازندران'){echo 'selected';}?>>مازندران</option>
                            <option value="مرکزی" <?php if($province == 'مرکزی'){echo 'selected';}?>>مرکزی</option>
                            <option value="هرمزگان" <?php if($province == 'هرمزگان'){echo 'selected';}?>>هرمزگان</option>
                            <option value="همدان" <?php if($province == 'همدان'){echo 'selected';}?>>همدان</option>
                            <option value="یزد" <?php if($province == 'یزد'){echo 'selected';}?>>یزد</option>
                        </select>
                    </div>
                    <div class="form-group third">

                        <label>ارسال پیامک آماده تحویل</label>

                        <select class="form-control" id="ready_for_derliver">
                            <option <?php if($ready_for_derliver == 0){echo 'selected';}?> value="0">غیرفعال</option>
                            <option <?php if($ready_for_derliver == 1){echo 'selected';}?> value="1">فعال</option>
                        </select>
                    </div>

                    <div class="form-group checkbox-technician">
                        <label>تخصص‌ها</label>

                        <div class="checkbox-group">
                            <label><input type="checkbox" name="specialty[]" value="ماشین لباسشویی" <?php if(in_array('ماشین لباسشویی', $specialty)){echo 'checked';}?>> ماشین لباسشویی</label>
                            <label><input type="checkbox" name="specialty[]" value=" ماشین ظرفشویی" <?php if(in_array(' ماشین ظرفشویی', $specialty)){echo 'checked';}?>> ماشین ظرفشویی</label>
                            <label><input type="checkbox" name="specialty[]" value=" یخچال و ساید بای ساید" <?php if(in_array(' یخچال و ساید بای ساید', $specialty)){echo 'checked';}?>> یخچال و ساید بای ساید</label>
                            <label><input type="checkbox" name="specialty[]" value=" ماکروفر و سولاردوم" <?php if(in_array(' ماکروفر و سولاردوم', $specialty)){echo 'checked';}?>> ماکروفر و سولاردوم</label>
                            <label><input type="checkbox" name="specialty[]" value=" اجاق گاز و گاز رومیزی" <?php if(in_array('اجاق گاز و گاز رومیزی', $specialty)){echo 'checked';}?>> اجاق گاز و گاز رومیزی</label>
                            <label><input type="checkbox" name="specialty[]" value=" پکیج و آبگرمکن" <?php if(in_array(' پکیج و آبگرمکن', $specialty)){echo 'checked';}?>> پکیج و آبگرمکن</label>
                            <label><input type="checkbox" name="specialty[]" value=" سیستم‌های صوتی تصویری" <?php if(in_array(' سیستم‌های صوتی تصویری', $specialty)){echo 'checked';}?>> سیستم‌های صوتی تصویری</label>
                            <label><input type="checkbox" name="specialty[]" value=" لوازم کوچک برقی" <?php if(in_array(' لوازم کوچک برقی', $specialty)){echo 'checked';}?>> لوازم کوچک برقی</label>
                            <label><input type="checkbox" name="specialty[]" value=" کولرگازی و اسپیلت" <?php if(in_array(' کولرگازی و اسپیلت', $specialty)){echo 'checked';}?>> کولرگازی و اسپیلت</label>
                        </div>
                    </div>
                    <div class="form-group half">

                        <label>کارت شناسایی</label>
                        <div class="Img_selector">
                            <div class="loading">

                            </div>
                            <div class="thumb" style="background-image:url(<?=$cart_img?>) "></div>
                            <div class="box_upload">
<!--                                --><?php //=file_get_contents(wp_get_attachment_url(97));?>
                                <span>برای بارگذاری تصویر (بکشید / کلیک کنید)</span>
                                <input class="imgUpload" type="file" >
                                <input class="source_image" type="hidden" id="cart_img" value="<?=$cart_img?>" >
                            </div>
                        </div>

                    </div>

                    <div class="form-group">

                        <label>محاسبه درصد تکنسین</label>
                        <select class="form-control" id="type_of_calc_tech">
                            <option <?php if($type_of_calc_tech == 1){echo 'selected';}?> value="0">پیش فرض</option>
                            <option <?php if($type_of_calc_tech == 1){echo 'selected';}?> value="1">درصد از کل</option>
                        </select>

                    </div>

                    <div class="form-group">

                        <label>مقدار درصد از کل</label>
                        <input type="text" class="form-control" id="tech_per_of_all" value="<?=$tech_per_of_all?>">

                    </div>
                    <input type="submit" class="btn" value="<?=$btn;?>">

                </form>

            </div>
        </div>
    </section>

<?php

get_footer();

?>