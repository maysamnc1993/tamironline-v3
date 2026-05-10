<?php

// Template Name: Shipping Setting
IFLoggedAndVerify("shipping_setting");
get_header();
$objectionsList = get_option("shipping_list");
?>
    <section class="DashboardSection">
        <?=NavDashboard();?>
        <div class="main">
            <div class="content">
                <h1 class="title">لیست نحوه ارسال </h1>

                <form action="#" method="post" id="ShippingAjax" >

                    <div class="thempObjectionslist" style="display: none">
                        <div class="form-group">
                            <label>ایراد</label>
                            <input type="text" class="form-control" id="objectionsList" value="">
                        </div>
                    </div>
                    <div class="PlaceObjectionslist">

                        <?php
                        for($i = 0; $i < count($objectionsList); $i++){
                            if($objectionsList[$i] == ""){}else{
                                echo '
                                <div class="form-group">
                                        <label>نحوه ارسال</label>
                                        <input type="text" class="form-control" id="objectionsList" value="'.$objectionsList[$i].'">
                                    </div>
                                ';
                            }
                        }
                        ?>

                    </div>
                    <div class="addPlaceObjectionslist">افزودن نحوه ارسال</div>



                    <input type="submit" class="btn" value="به روز رسانی">

                </form>

            </div>
        </div>
    </section>
<?php
get_footer();

?>