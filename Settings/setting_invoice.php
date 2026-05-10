<?php
// Template Name: Settings ّInvoice

IFLoggedAndVerify("invoice_setting");
get_header();

$invoice_descritpion_show = get_option("invoice_descritpion_show");
$print_invoice_descritpion_show = get_option("print_invoice_descritpion_show");
?>
    <section class="DashboardSection">
        <?=NavDashboard();?>
        <div class="main">
            <div class="content">
                <h1 class="title">تنظیمات توضیحات فاکتور</h1>

                <form action="#" method="post" id="SettingAjaxDescription" >

                    <div class="form-group">
                        <label>توضیحات فاکتور</label>
                        <?=wp_editor($invoice_descritpion_show,"invoice_descritpion_show")?>
                    </div>
                    <div class="form-group">
                        <label>توضیحات پرینت فاکتور</label>
                        <?=wp_editor($print_invoice_descritpion_show,"print_invoice_descritpion_show")?>
                    </div>


                    <input type="submit" class="btn" value="به روز رسانی">

                </form>

            </div>
        </div>
    </section>
<?php
get_footer();

?>