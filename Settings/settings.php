<?php

// Template Name: Settings
IFLoggedAndVerify("setting");
get_header();
$objectionsList = get_option("objectionsList");
?>
    <section class="DashboardSection">
        <?=NavDashboard();?>
        <div class="main">
            <div class="content">
                <h1 class="title">لیست ایرادات </h1>

                <form action="#" method="post" id="ObjectionAjax" >

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
                                        <label>ایراد</label>
                                        <input type="text" class="form-control" id="objectionsList" value="'.$objectionsList[$i].'">
                                    </div>
                                ';
                            }
                            }
                        ?>

                    </div>
                    <div class="addPlaceObjectionslist">افزودن ایراد</div>



                    <input type="submit" class="btn" value="به روز رسانی">

                </form>

            </div>
        </div>
    </section>
<?php
get_footer();

?>