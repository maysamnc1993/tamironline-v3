<?php
// Template Name: Settings Cancel Description

IFLoggedAndVerify("cancel_setting");
get_header();

$cancel = get_option("cancel");
?>
    <section class="DashboardSection">
        <?=NavDashboard();?>
        <div class="main">
            <div class="content">
                <h1 class="title">تنظیمات توضیحات کنسلی</h1>

                <form action="#" method="post" id="SettingAjaxCancel" >

                    <div class="form-group">
                        <label>علت کنسلی شماره یک</label>
                        <input type="text" class="form-control" id="cancle_desc_1" value="<?=$cancel["cancle_desc_1"]?>">
                    </div>
                    <div class="form-group">
                        <label>علت کنسلی شماره دو</label>
                        <input type="text" class="form-control" id="cancle_desc_2" value="<?=$cancel["cancle_desc_2"]?>">
                    </div>

                    <div class="form-group">
                        <label>علت کنسلی شماره سه</label>
                        <input type="text" class="form-control" id="cancle_desc_3" value="<?=$cancel["cancle_desc_3"]?>">
                    </div>

                    <div class="form-group">
                        <label>علت کنسلی شماره چهار</label>
                        <input type="text" class="form-control" id="cancle_desc_4" value="<?=$cancel["cancle_desc_4"]?>">
                    </div>
                    <div class="form-group">
                        <label>علت کنسلی شماره پنج</label>
                        <input type="text" class="form-control" id="cancle_desc_5" value="<?=$cancel["cancle_desc_5"]?>">
                    </div>
                    <div class="form-group">
                        <label>علت کنسلی شماره شش</label>
                        <input type="text" class="form-control" id="cancle_desc_6" value="<?=$cancel["cancle_desc_6"]?>">
                    </div>
                    <div class="form-group">
                        <label>علت کنسلی شماره هفت</label>
                        <input type="text" class="form-control" id="cancle_desc_7" value="<?=$cancel["cancle_desc_7"]?>">
                    </div>
                    <div class="form-group">
                        <label>علت کنسلی شماره هشت</label>
                        <input type="text" class="form-control" id="cancle_desc_8" value="<?=$cancel["cancle_desc_8"]?>">
                    </div>
                    <div class="form-group">
                        <label>علت کنسلی شماره نه</label>
                        <input type="text" class="form-control" id="cancle_desc_9" value="<?=$cancel["cancle_desc_9"]?>">
                    </div>
                    <div class="form-group">
                        <label>علت کنسلی شماره ده</label>
                        <input type="text" class="form-control" id="cancle_desc_10" value="<?=$cancel["cancle_desc_10"]?>">
                    </div>
                    <div class="form-group">
                        <label>علت کنسلی شماره یازده</label>
                        <input type="text" class="form-control" id="cancle_desc_11" value="<?=$cancel["cancle_desc_11"]?>">
                    </div>

                    <div class="form-group">
                        <label>علت کنسلی شماره دوازده</label>
                        <input type="text" class="form-control" id="cancle_desc_12" value="<?=$cancel["cancle_desc_12"]?>">
                    </div>

                    <div class="form-group">
                        <label>علت کنسلی شماره سیزده</label>
                        <input type="text" class="form-control" id="cancle_desc_13" value="<?=$cancel["cancle_desc_13"]?>">
                    </div>

                    <div class="form-group">
                        <label>علت کنسلی شماره چهارده</label>
                        <input type="text" class="form-control" id="cancle_desc_14" value="<?=$cancel["cancle_desc_14"]?>">
                    </div>

                    <div class="form-group">
                        <label>علت کنسلی شماره پانزده</label>
                        <input type="text" class="form-control" id="cancle_desc_15" value="<?=$cancel["cancle_desc_15"]?>">
                    </div>


                    <input type="submit" class="btn" value="به روز رسانی">

                </form>

            </div>
        </div>
    </section>
<?php
get_footer();

?>