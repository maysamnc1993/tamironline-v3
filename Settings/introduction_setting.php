<?php

// Template Name: introduction Setting
IFLoggedAndVerify("introduction_setting");
get_header();
$introductionList = get_option("introductionList");
?>
    <section class="DashboardSection">
        <?=NavDashboard();?>
        <div class="main">
            <div class="content">
                <h1 class="title">لیست نحوه آشنایی </h1>

                <form action="#" method="post" id="introductionAjax" >

                    <div class="thempObjectionslist" style="display: none">
                        <div class="form-group">
                            <label>نحوه آشنایی</label>
                            <input type="text" class="form-control" id="objectionsList" value="">
                        </div>
                    </div>
                    <div class="PlaceObjectionslist">

                        <?php
                        for($i = 0; $i < count($introductionList); $i++){
                            if($introductionList[$i] == ""){}else{
                                echo '
                                <div class="form-group">
                                        <label>نحوه آشنایی</label>
                                        <input type="text" class="form-control" id="objectionsList" value="'.$introductionList[$i].'">
                                    </div>
                                ';
                            }
                        }
                        ?>

                    </div>
                    <div class="addPlaceObjectionslist">افزودن نحوه آشنایی</div>



                    <input type="submit" class="btn" value="به روز رسانی">

                </form>

            </div>
        </div>
    </section>
<?php
get_footer();

?>