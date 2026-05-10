<?php

// Template Name: Add Device

get_header();

if(isset($_GET['uid'])){
    IFLoggedAndVerify("edit_device");
    $title_section = 'ویرایش دستگاه';
    $btn = 'ویرایش دستگاه';
    $action = 'update';
    $uid = $_GET['uid'];
    $name = get_term($uid)->name;




}else{
    IFLoggedAndVerify("add_device");
    $title_section = 'افزودن دستگاه';
    $btn = 'افزودن دستگاه';
    $action = 'add';
    $uid = "";
    $name = "";



}
?>
    <section class="DashboardSection">
        <?=NavDashboard();?>
        <div class="main">
            <div class="content">
                <h1 class="title"><?=$title_section?></h1>

                <form action="#" method="post" id="DeviceAjax" >

                    <input type="hidden" id="type_action" value="<?=$action?>">
                    <input type="hidden" id="uid" value="<?=$uid?>">
                    <div class="form-group">
                        <label>نام دستگاه</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="name" value="<?=$name?>">
                    </div>

                    <input type="submit" class="btn" value="<?=$btn;?>">

                </form>

            </div>
        </div>
    </section>
<?php
get_footer();

?>