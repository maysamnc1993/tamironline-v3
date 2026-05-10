<?php

// Template Name: Settings SMS
IFLoggedAndVerify("sms_setting");
get_header();
$sms = get_option("sms");

?>
    <section class="DashboardSection">
        <?=NavDashboard();?>
        <div class="main">
            <div class="content">
                <h1 class="title">تنظیمات پیامک</h1>

                <form action="#" method="post" id="SettingAjax" >

                    <div class="form-group">
                        <label>کد وب سرویس</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="token" value="<?=$sms['token'];?>">
                    </div>
                    <div class="form-group">
                        <label>پیامک سفارش برای مشتری</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="customer_order"  value="<?=$sms['customer_order'];?>">
                    </div>
                    <div class="form-group">
                        <label>پیامک سفارش برای تکنسین</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="technician_order"   value="<?=$sms['technician_order'];?>">
                    </div>
                    <div class="form-group">
                        <label>پیامک کنسل شدن سفارش برای مشتری</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="customer_cancel_order"   value="<?=$sms['customer_cancel_order'];?>">
                    </div>
                    <div class="form-group">
                        <label>پیامک شارژ کیف پول تکنسین</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="tech_wallet_charge"   value="<?=$sms['tech_wallet_charge'];?>">
                    </div>
                    <input type="submit" class="btn" value="به روز رسانی">

                </form>

            </div>
        </div>
    </section>
<?php
get_footer();

?>