<?php

// Template Name: accounting_setting
IFLoggedAndVerify("accounting_setting");
get_header();
$type_of_desc_acc = get_option("type_of_desc_acc");
$title_of_desc_acc = get_option("title_of_desc_acc");
?>
<section class="DashboardSection">
    <?=NavDashboard();?>
    <div class="main">
        <div class="content">
            <h1 class="title">تنظیمات حسابداری</h1>

            <form action="#" method="post" id="accounting_setting_Ajax" >

                <div class="thempObjectionslist" style="display: none">
                    <div class="form-group half">
                        <label>انتخاب نوع</label>
                        <select class="form-control" name="type_of_desc_acc[]" id="type_of_desc_acc">
                            <option value="1">ایجاد بستانکاری</option>
                            <option value="0">ایجاد بدهکاری</option>
                        </select>
                    </div>
                    <div class="form-group half">
                        <label>توضیحات</label>
                        <input class="form-control" name="title_of_desc_acc[]" id="title_of_desc_acc">

                    </div>
                </div>
                <div class="PlaceObjectionslist">

                    <?php


                    for($i = 0; $i < count($title_of_desc_acc); $i++){
                        if($title_of_desc_acc[$i] == ""){}else{
                            $select_1 = '';
                            $select_2 = '';
                            if($type_of_desc_acc[$i] == 1){
                                $select_1 = 'selected';
                            }
                            else if($type_of_desc_acc[$i] == 0){
                                $select_2 = 'selected';
                            }
                            echo '
                                <div class="form-group half">
                                        <label>انتخاب نوع</label>
                                        <select class="form-control" name="type_of_desc_acc[]" id="type_of_desc_acc">
                                            <option '.$select_1.' value="1">ایجاد بستانکاری</option>
                                            <option '.$select_2.' value="0">ایجاد بدهکاری</option>
                                        </select>
                                    </div>
                                    <div class="form-group half">
                                        <label>توضیحات</label>
                                        <input class="form-control" name="title_of_desc_acc[]" value="'.$title_of_desc_acc[$i].'" id="title_of_desc_acc">
                                           
                                    </div>
                                ';
                        }
                    }
                    ?>

                </div>
                <div class="addPlaceObjectionslist">افزودن</div>



                <input type="submit" class="btn" value="به روز رسانی">

            </form>

        </div>
    </div>
</section>
<?php
get_footer();

?>