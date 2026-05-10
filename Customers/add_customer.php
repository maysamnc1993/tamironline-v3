<?php

// Template Name: Add Customers

get_header();

if(isset($_GET['uid'])){
    IFLoggedAndVerify("edit_customer");
    $title_section = 'ویرایش مشتری';
    $btn = 'ویرایش مشتری';
    $action = 'update';
    $uid = $_GET['uid'];
    $user = get_user_by("ID",$uid);
    $username = $user->user_login;
    $firstname = get_user_meta($uid,"first_name",true);
    $mobile = get_user_meta($uid,"mobile",true);
    $phone = get_user_meta($uid,"phone",true);



}else{
    IFLoggedAndVerify("add_customer");
    $title_section = 'افزودن مشتری';
    $btn = 'افزودن مشتری';
    $action = 'add';

    $uid = "";
    $username = "";
    $firstname = "";
    $mobile = "";
    $phone ="";


}
?>
    <section class="DashboardSection">
        <?=NavDashboard();?>
        <div class="main">
            <div class="content">
                <h1 class="title"><?=$title_section?></h1>

                <form action="#" method="post" id="CustomersAjax" >

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
                        <label>شماره موبایل</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="mobile" value="<?=$mobile?>">
                    </div>

                    <div class="form-group third">
                        <label>شماره ثابت</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="phone" value="<?=$phone?>">
                    </div>

                    <input type="submit" class="btn" value="<?=$btn;?>">

                </form>

            </div>
        </div>
    </section>
<?php
get_footer();

?>