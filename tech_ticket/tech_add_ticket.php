<?php

// Template Name: Technician Add Ticket

get_header();
if(isset($_GET['uid'])){
    IFLoggedAndVerify("tech_order_list");
    $title_section = 'ویرایش تیکت';
    $btn = 'ویرایش تیکت';
    $action = 'update';
    $uid = $_GET['uid'];
    $customer = get_post_meta($uid,"customer",true);
    $subscription = get_post_meta($uid,"subscription",true);
    $mobile = get_post_meta($uid,"mobile",true);
    $phone = get_post_meta($uid,"phone",true);
    $state = wp_get_post_terms($uid,"state")[0]->term_id;
    $city = wp_get_post_terms($uid,"city")[0]->term_id;
    $address = get_post_meta($uid,"address",true);
    $brand = wp_get_post_terms($uid,"brand")[0]->term_id;;
    $device = wp_get_post_terms($uid,"device")[0]->term_id;;
    $objection = get_post_meta($uid,"objection",true);
    $objection_description = get_post_meta($uid,"objection_description",true);
    $technician = get_post_meta($uid,"technician",true);

}else{

    IFLoggedAndVerify("tech_order_list");
    $title_section = 'افزودن تیکت';
    $btn = 'افزودن تیکت';
    $action = 'add';
    $operator = "";
    $technician = "";
    $subject = "";
    $content = "";
    $attachment = "";
}
?>
    <section class="DashboardSection">
        <?=NavDashboard();?>
        <div class="main">
            <div class="content">
                <h1 class="title"><?=$title_section?></h1>

                <form action="#" method="post" id="TicketAjax" >

                    <input type="hidden" id="type_action" value="<?=$action?>">
                    <input type="hidden" id="uid" value="<?=$uid?>">

                    <div class="form-group" style="display: none">

                        <label>دریافت کننده</label>
                        <select class="form-control validate" data-validate="empty" id="send_to">
                            <option value="">انتخاب کنید</option>
                            <option selected value="operator">اپراتور</option>
                            <option value="technician">تکنسین</option>
                        </select>
                    </div>

                    <div id="operator_part" style="display:none" class="form-group">
                        <label>اپراتور</label>
                        <select class="form-control" id="operator">

                            <option selected value="operator">انتخاب اپراتور</option>
                            <?php
                            $customer_list = get_users( array(
                                'meta_key' => 'role',
                                'meta_value' => 'operator',
                                'meta_compare' => '=' // exact match only
                            ) );
                            foreach($customer_list as $item){
                                if($item->ID == get_current_user_id()){}else{
                                    if($customer == $item->ID){
                                        $selected = "selected";
                                    }
                                    else{
                                        $selected = "";
                                    }
                                    $firstname_u = get_user_meta($item->ID,"first_name",true);

                                    echo '<option '.$selected.' value="'.$item->ID.'">'.$firstname_u.'</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div id="technician_part" style="display: none" class="form-group">
                        <label>تکنسین</label>
                        <select class="form-control " id="technician">
                            <option value="">انتخاب تکنسین</option>
                            <?php
                            $customer_list = get_users( array(
                                'meta_key' => 'role',
                                'meta_value' => 'technician',
                                'meta_compare' => '=' // exact match only
                            ) );
                            foreach($customer_list as $item){
                                if($item->ID == get_current_user_id()){}else{
                                    if($customer == $item->ID){
                                        $selected = "selected";
                                    }
                                    else{
                                        $selected = "";
                                    }
                                    $firstname_u = get_user_meta($item->ID,"first_name",true);

                                    echo '<option '.$selected.' value="'.$item->ID.'">'.$firstname_u.'</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>سفارش مربوطه</label>
                        <select class="form-control" id="order_ticket">
                            <option value="">سفارش را انتخاب کنید</option>

                            <?php
                            $orders = get_posts(array(
                                "post_type" => "orders" ,
                                "posts_per_page" => -1 ,
                                "post_status" => "any",
                                "meta_query" => array(
                                    array(
                                        "key" => "technician",
                                        "value" => get_current_user_id(),
                                        "compare" => "LIKE"
                                    )
                                )
                            ));
                            foreach($orders as $item){

                                $customer = get_post_meta($item->ID,"customer",true);
                                $customer_name = get_user_meta($customer,"first_name",true);
                                echo '<option  value="'.$item->ID.'">'.$item->ID.' - '.$customer_name.' </option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>موضوع</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="subject" value="<?=$subject?>">
                    </div>
                    <div class="form-group">

                        <label>پیام</label>
                        <?=wp_editor($content,"content",array(
                            'media_buttons' => false,
                            'textarea_rows' => 8,
                            'tabindex' => 4,
                            'tinymce' => array(
                                'theme_advanced_buttons1' => 'bold, italic, ul, min_size, max_size',
                            ),
                        ))?>

                    </div>
                    <div class="form-group ">

                        <label>پیوست</label>
                        <div class="Img_selector">
                            <div class="loading">

                            </div>
                            <div class="thumb" style="background-image:url(<?=$attachment?>) "></div>
                            <div class="box_upload">
                                <?=file_get_contents(wp_get_attachment_url(97));?>
                                <span>برای بارگذاری تصویر (بکشید / کلیک کنید)</span>
                                <input class="imgUpload" type="file" >
                                <input class="source_image" type="hidden" id="attachment" value="<?=$attachment?>" >
                            </div>
                        </div>
                    </div>

                    <input type="submit" class="btn" value="<?=$btn;?>">

                </form>

            </div>
        </div>
    </section>
<?php
get_footer();

?>