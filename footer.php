<div class="MessageLog">
    <div class="msg">

    </div>
</div>

<script type="text/javascript" defer src="<?=get_template_directory_uri()?>/assets/js/jquery.js"></script>
<script type="text/javascript" defer src="<?=get_template_directory_uri()?>/assets/js/bootstrap.min.js"></script>
<script type="text/javascript" defer src="<?=get_template_directory_uri()?>/assets/js/owl.carousel.min.js"></script>
<!--<script type="text/javascript" defer src="https://cdnjs.cloudflare.com/ajax/libs/parallax/3.1.0/parallax.min.js"></script>-->
<!--<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>-->
<script type="text/javascript" defer src="<?=get_template_directory_uri()?>/assets/js/ajax/operator.js?var<?=rand(1000,10000);?>"></script>
<script type="text/javascript" defer src="<?=get_template_directory_uri()?>/assets/js/ajax/technician.js?var<?=rand(1000,10000);?>"></script>
<script type="text/javascript" defer src="<?=get_template_directory_uri()?>/assets/js/ajax/customers.js?var<?=rand(1000,10000);?>"></script>
<script type="text/javascript" defer src="<?=get_template_directory_uri()?>/assets/js/ajax/taxonomy.js?var<?=rand(1000,10000);?>"></script>
<script type="text/javascript" defer src="<?=get_template_directory_uri()?>/assets/js/ajax/order.js?var<?=rand(1000,10000);?>"></script>
<script type="text/javascript" defer src="<?=get_template_directory_uri()?>/assets/js/ajax/internal_order.js?var<?=rand(1000,10000);?>"></script>
<script type="text/javascript" defer src="<?=get_template_directory_uri()?>/assets/js/ajax/ticket.js?var<?=rand(1000,10000);?>"></script>
<script type="text/javascript" defer src="<?=get_template_directory_uri()?>/assets/js/select2.full.min.js?var<?=rand(1000,10000);?>"></script>
<script type="text/javascript" defer src="<?=get_template_directory_uri()?>/assets/js/select2.min.js?var<?=rand(1000,10000);?>"></script>
<script type="text/javascript" defer src="<?=get_template_directory_uri()?>/assets/js/ajax/setting.js?var<?=rand(1000,10000);?>"></script>
<script type="text/javascript" defer src="<?=get_template_directory_uri()?>/assets/js/ajax/payment.js?var<?=rand(1000,10000);?>"></script>
<script type="text/javascript" defer src="<?=get_template_directory_uri()?>/assets/js/ajax/financial.js?var<?=rand(1000,10000);?>"></script>
<script type="text/javascript" defer src="<?=get_template_directory_uri()?>/assets/js/ajax/cost.js?var<?=rand(1000,10000);?>"></script>
<script type="text/javascript" defer src="<?=get_template_directory_uri()?>/assets/js/persian-date.min.js?var<?=rand(1000,10000);?>"></script>
<script type="text/javascript" defer src="<?=get_template_directory_uri()?>/assets/js/persian-datepicker.min.js?var<?=rand(1000,10000);?>"></script>
<script type="text/javascript" defer src="<?=get_template_directory_uri()?>/assets/js/script.js?var<?=rand(1000,10000);?>"></script>
<?php wp_footer();?>
<?php

if(is_user_logged_in()){
    $role = get_user_meta(get_current_user_id(),"role",true);
    if($role == "operator"){}else{
        $Debt = loadAllDataFinَUser(get_current_user_id());
        if($Debt < 1){
            $Debt = $Debt * -1;
        }else{
            $Debt = 0;
        }


        $max_price = get_user_meta(get_current_user_id(),"max_price",true);

        if($Debt > $max_price){
                if(is_page("157")){}else{
            ?>
            <div class="modal fade" id="PaymentNowTechModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <h1>پرداخت صورت حساب</h1>
                        <p>تکنسین گرامی شما مبلغ <b><?=number_format($Debt)?> تومان</b>  به تعمیرآنلاین بدهکار هستید. لطفا همین الان پرداخت نمایید.</p>
                        <a href="<?=get_the_permalink(157)?>" class="paymentNowModalbtn">همین الان پرداخت نمایید</a>
                    </div>
                </div>
            </div>
            <script>
                jQuery(document).ready(function (e){
                    jQuery("#PaymentNowTechModal").modal('show');
                })
            </script>
            <?php
                }
        }
    }
}
?>
</body>
</html>