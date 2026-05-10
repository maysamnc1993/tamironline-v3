<?php
IFLoggedAndVerify("add_cost");
// Template Name: add Cost
get_header();

$title_section = 'افزودن هزینه';
$btn = 'افزودن شارژ کیف پول';
$action = 'add';
$uid = "";
$name = "";

$title_of_desc_acc = get_option("cost_list");

?>

    <section class="DashboardSection">
        <?=NavDashboard();?>
        <div class="main">
            <div class="content" style="margin-top:50px;">
                <h1 class="title">افزودن هزینه</h1>

                <form action="#" method="post" id="AddCostAjax" >

                    <div class="form-group">
                        <label>نوع هزینه</label>
                        <select class="form-control validate" data-validate="empty" id="cost_type">
                            <option value="">انتخاب کنید</option>
                            <?php
                            for($i = 0; $i < count($title_of_desc_acc); $i++ ){
                                if($title_of_desc_acc[$i] == ""){}else{

                                    echo '<option value="'.$title_of_desc_acc[$i].'">'.$title_of_desc_acc[$i].'</option>';

                                }
                            }
                            ?>

                        </select>
                    </div>
                    <div class="form-group">
                        <label>توضیحات</label>
                        <textarea class="form-control" id="cost_desc"></textarea>
                    </div>
                    <div class="form-group half">
                        <label>هزینه به عدد</label>
                        <input type="text" class="form-control validate" data-validate="empty" id="cost_price">
                    </div>
                    <div class="form-group half">
                        <label>هزینه به حروف</label>
                        <div class="price_char">صفر تومان</div>
                    </div>
                    <div class="form-group half ">

                        <label>آپلود سند</label>
                        <div class="Img_selector">
                            <div class="loading">
                            </div>
                            <div class="thumb" style=""></div>
                            <div class="box_upload">
                                <?=file_get_contents(wp_get_attachment_url(97));?>
                                <span>برای بارگذاری تصویر (بکشید / کلیک کنید)</span>
                                <input class="imgUpload" type="file" >
                                <input class="source_image" type="hidden" id="document" value="" >
                            </div>
                        </div>
                    </div>
                    <div class="form-group half">

                        <label>آپلود سند</label>
                        <div class="Img_selector">
                            <div class="loading">
                            </div>
                            <div class="thumb" style=""></div>
                            <div class="box_upload">
                                <?=file_get_contents(wp_get_attachment_url(97));?>
                                <span>برای بارگذاری تصویر (بکشید / کلیک کنید)</span>
                                <input class="imgUpload" type="file" >
                                <input class="source_image" type="hidden" id="document_2" value="" >
                            </div>
                        </div>
                    </div>

                    <input type="submit" class="btn" value="افزودن هزینه">

                </form>

            </div>

        </div>
    </section>
<?php
get_footer();

?>