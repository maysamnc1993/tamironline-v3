<?php

// Template Name: Settings Payment
IFLoggedAndVerify("payment_setting");
get_header();
$payment = get_option("payment");

?>
    <section class="DashboardSection">
        <?=NavDashboard();?>
        <div class="main">
            <div class="content">
                <h1 class="title">تنظیمات درگاه</h1>

                <form action="#" method="post" id="SettingAjaxPayment" >
    
                    <span class="title">درگاه اول</span>
                    <div class="form-group">
                        <label>انتخاب درگاه</label>
                        <select class="form-control" id="type_of_payment1">
                            <option value="">انتخاب کنید</option>
                            <option <?php if($payment['type_of_payment1'] == "zarinpal"){echo 'selected';}?> value="zarinpal">زیبال</option>
                            <option <?php if($payment['type_of_payment1'] == "novin"){echo 'selected';}?> value="novin">اقتصاد نوین</option>
                        </select>
                    </div>

                    <span class="title">پکیج کیف پول ۱</span>
                    <div class="form-group half">
                        <label>مبلغ کیف پول</label>
                        <input type="text" class="form-control"  data-validate="empty" id="wallet1" value="<?=$payment['wallet1'];?>">
                    </div>
                    <div class="form-group half">
                        <label>مبلغ شارژ شده</label>
                        <input type="text" class="form-control "  data-validate="empty" id="wallet1_charge"  value="<?=$payment['wallet1_charge'];?>">
                    </div>


                    <span class="title">پکیج کیف پول ۲</span>
                    <div class="form-group half">
                        <label>مبلغ کیف پول</label>
                        <input type="text" class="form-control"  data-validate="empty" id="wallet2" value="<?=$payment['wallet2'];?>">
                    </div>
                    <div class="form-group half">
                        <label>مبلغ شارژ شده</label>
                        <input type="text" class="form-control "  data-validate="empty" id="wallet2_charge"  value="<?=$payment['wallet2_charge'];?>">
                    </div>


                    <span class="title">پکیج کیف پول ۳</span>
                    <div class="form-group half">
                        <label>مبلغ کیف پول</label>
                        <input type="text" class="form-control"  data-validate="empty" id="wallet3" value="<?=$payment['wallet3'];?>">
                    </div>
                    <div class="form-group half">
                        <label>مبلغ شارژ شده</label>
                        <input type="text" class="form-control "  data-validate="empty" id="wallet3_charge"  value="<?=$payment['wallet3_charge'];?>">
                    </div>


                    <span class="title">پکیج کیف پول ۴</span>
                    <div class="form-group half">
                        <label>مبلغ کیف پول</label>
                        <input type="text" class="form-control"  data-validate="empty" id="wallet4" value="<?=$payment['wallet4'];?>">
                    </div>
                    <div class="form-group half">
                        <label>مبلغ شارژ شده</label>
                        <input type="text" class="form-control "  data-validate="empty" id="wallet4_charge"  value="<?=$payment['wallet4_charge'];?>">
                    </div>


                    <span class="title">پکیج کیف پول ۵</span>
                    <div class="form-group half">
                        <label>مبلغ کیف پول</label>
                        <input type="text" class="form-control"  data-validate="empty" id="wallet5" value="<?=$payment['wallet5'];?>">
                    </div>
                    <div class="form-group half">
                        <label>مبلغ شارژ شده</label>
                        <input type="text" class="form-control "  data-validate="empty" id="wallet5_charge"  value="<?=$payment['wallet5_charge'];?>">
                    </div>


                    <input type="submit" class="btn" value="به روز رسانی">

                </form>

            </div>
        </div>
    </section>
<?php
get_footer();

?>