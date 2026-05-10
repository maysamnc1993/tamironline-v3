



<?php

// Template Name: Tech Generate Invoice for Customer


get_header();

$oid = $_GET['oid'];
$order_date = get_the_date('Y/m/d' , $oid);
$user_id = get_post_meta($oid,'customer',true);
$customer_name = get_user_meta($user_id,'first_name',true);
$customer_mobile = get_post_meta($oid,'mobile',true);
$customer_phone = get_post_meta($oid,'phone',true);
$customer_address = get_post_meta($oid,'address',true);
$invoice_descripotion = get_post_meta($oid,'invoice_descripotion',true);
$price_customer = get_post_meta($oid,'price_customer',true);
$finish_order_sh = get_post_meta($oid,'finish_order_sh',true);
?>


<seciton class="InvoiceCustomer" id="PrintInvoiceArea">

    <div class="container1">

        <div class="InvoiceStructure">


            <div class="content InvoiceCustomer">



<!--                    <ul class="BTNInvoice PreInvoice">-->
<!--                        <li>-->
<!--                            <div id="printInvoiceBtn">نسخه PDF پیش فاکتور</div>-->
<!--                        </li>-->
<!--                    </ul>-->





                    <div class="InvoiceLogoANDPDF">
                        <div class="Logo">
                            <img src="https://crm.tamironline.com/wp-content/uploads/2024/09/tamironline-logo-1.png">
                        </div>
                        <div class="SavePDF">
                            <div class="CTA_Save_PDF" id="printInvoiceBtn">پرینت / دانلود</div>
                        </div>
                    </div>
                    <div class="HeadInvoice">
                        <h3 class="TitleInvoice">صورتحساب خدمات</h3>

                        <ul class="DetailInvoiceSerial">
<!--                            <li>-->
<!--                                <span>وضعیت</span>-->
<!--                                <b><span class="notpayment">پرداخت نشده</span></b>-->
<!--                            </li>-->
                            <li>
                                <span>شماره پیش فاکتور</span>
                                <b><?=$oid?></b>
                            </li>
                            <li>
                                <span>تاریخ</span>
                                <b><?=strtok($finish_order_sh," ")?></b>
                            </li>
                        </ul>
                    </div>

                    <div class="InvoiceTable buyer">
                        <div class="head">ارائه دهنده</div>

                        <ul class="Detail_side">
                            <li class="third">
                                <span>نام شخص :</span>
                                <b>تعمیرآنلاین</b>
                            </li>
                            <li class="third">
                                <span>شماره تلفن:</span>
                                <b>02145396</b>
                            </li>
                            <li class="third">
                                <span>کد پستی:</span>
                                <b>1566855713</b>
                            </li>
                            <li class="full">
                                <span>آدرس:</span>
                                <b>تهران، خ مطهری، نرسیده به ترکمنستان، پ ۲۰، ساختمان تعمیرآنلاین</b>
                            </li>

                        </ul>

                    </div>
                <div class="InvoiceTable buyer">
                    <div class="head">مشتری</div>

                    <ul class="Detail_side">
                        <li class="third">
                            <span>نام شخص :</span>
                            <b><?=$customer_name?></b>
                        </li>
                        <li class="third">
                            <span> شماره موبایل:</span>
                            <b><?=$customer_mobile?></b>
                        </li>
                        <li class="third">
                            <span>شماره تلفن:</span>
                            <b><?=$customer_phone?></b>
                        </li>
                        <li class="full">
                            <span>نشانی:</span>
                            <b><?=$customer_address?></b>
                        </li>

                    </ul>

                </div>
                    <div class="InvoiceTable ProductList">
                        <div class="head">مشخصات کالا یا خدمات مورد معامله</div>

                        <ul class="InvoiceTableRecord">
                            <li><span>ردیف</span></li>
<!--                            <li>کد کالا</li>-->
                            <li style="width: 200%">شرح کالا/خدمت</li>
                            <li>تعداد/مقدار</li>

                            <li>مبلغ کل (تومان)</li>
                        </ul>
                        <ul class="InvoiceTableRecord">
                            <li>1</li>
<!--                            <li></li>-->
                            <li  style="width: 200%"><?=$invoice_descripotion?></li>
                            <li>1</li>
<!--                            <li>--><?php //=number_format($price_customer)?><!--</li>-->
<!--                            <li>0</li>-->
                            <li><?=number_format($price_customer)?></li>
                        </ul>

                        <ul class="InvoiceTableRecord TotalInvoiceRecord">
                            <li>جمع کل</li>
                            <li><?=number_format($price_customer)?> تومان</li>
                        </ul>

                        <ul class="InvoiceTableRecord Description">
                            <li class="desc">

                                <p>این صورتحساب بر اساس اظهارات کارشناس و با توافق مشتری ثبت شده است و واحد بازرسی موارد را بررسی می نماید.
                                </br>

                                    در صورت مغایرت عدد پرداختی با مبلغ صورتحساب، لطفا با شماره ۴۵۳۹۶-۰۲۱ تماس گرفته و این موضوع را اطلاع دهید.
                                    </br>

                                    مبلغ صورتحساب را فقط به شماره کارت کارشناس واریز کرده و از پرداخت نقدی خودداری نمایید.
                                    </br>
                                    لطفا از تماس مستقیم با کارشناس خودداری نمایید. تعمیرآنلاین در قبال تماس مستقیم با تکنسین مسئولیتی ندارد.</p>

                            </li>
                            <li class="signature_invoice">
                                <img src="https://crm.tamironline.com/wp-content/uploads/2024/10/مهرررر-Copy.jpeg;">
                            </li>
                        </ul>

                    </div>


                    <form action="#" method="post" id="Final_official_payment_bill_form" class="customer_form Order_legal_Payment">
                        <input type="hidden" id="oid" value="240641">

                        <input type="checkbox" checked="" style="display: none;" id="Approvelaw" class="">
                        <input type="submit" class="btn" value="پرداخت فاکتور">

                    </form>
                </div>

        </div>

    </div>

</seciton>


<?php

get_footer();

?>


<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script>
    document.getElementById('printInvoiceBtn').addEventListener('click', function() {
        ALERT("در حال خروجی PDF فاکتور","s");
        html2canvas(document.getElementById('PrintInvoiceArea')).then(canvas => {
            var imgData = canvas.toDataURL('image/png');
            var doc = new jspdf.jsPDF('l', 'mm', 'a4'); // Set to landscape orientation
            var width = doc.internal.pageSize.getWidth();
            var height = doc.internal.pageSize.getHeight();
            var ratio = canvas.width / canvas.height;
            var newWidth = height * ratio; // Calculate new width based on aspect ratio

            // Check if the width exceeds the page size and adjust if needed
            if (newWidth > width) {
                newWidth = width;
                height = newWidth / ratio;
            }

            doc.addImage(imgData, 'PNG', 0, 0, newWidth, height);
            doc.save('<?=$oid?>');
        });
    });


</script>
