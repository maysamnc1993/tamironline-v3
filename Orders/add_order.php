<?php

// Template Name: Add Order

get_header();
$objectionsList = get_option("objectionsList");
$introductionList = get_option("introductionList");
if(isset($_GET['uid'])){
    IFLoggedAndVerify("edit_order");
    $title_section = 'ویرایش سفارش';
    $btn = 'ویرایش سفارش';
    $action = 'update';
    $uid = $_GET['uid'];
    $customer = get_post_meta($uid,"customer",true);
    $first_name = get_user_meta($customer,"first_name",true);
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
    $introduction = get_post_meta($uid,"introduction",true);
    $technician = get_post_meta($uid,"technician",true);
    $order_type = get_post_meta($uid,"order_type",true);

}else{
    IFLoggedAndVerify("add_order");
    $title_section = 'افزودن سفارش';
    $btn = 'افزودن سفارش';
    $action = 'add';
    $customer = "new_customer";
    $subscription = "";
    $mobile = "";
    $phone = "";
    $state = "15";
    $city = '';
    $address = "";
    $brand = "";
    $device = "";
    $objection = "";
    $objection_description = "";
    $introduction = "";
    $technician = "";
    $order_type = "";
    $first_name = "";
}
?>

    <div class="modal fade" id="PopUpCustomerList" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
               <div class="customerList">
                   <h1>لیست مشتریان</h1>
                   <div class="List_of_customer">

                   </div>
               </div>
            </div>
        </div>
    </div>
    <section class="DashboardSection">
        <?=NavDashboard();?>
        <div class="main">
            <div class="content">
                <h1 class="title"><?=$title_section?></h1>

                <form action="#" method="post" id="OrderAjax" >
                    <input type="hidden" id="customer" value="<?=$customer?>">
                    <input type="hidden" id="type_action" value="<?=$action?>">
                    <input type="hidden" id="uid" value="<?php if(isset($uid)){echo $uid;}?>">
                    <div class="form-group half">
                        <label>نوع سفارش</label>
                            <select class="form-control validate" data-validate="empty" id="order_type">

                                <option value="">نوع سفارش را انتخاب نمایید.</option>
                                <option <?php if($order_type == "repair"){echo 'selected';}?> value="repair">تعمیرات</option>
<!--                                <option --><?php //if($order_type == "buy"){echo 'selected';}?><!--  value="buy">خرید</option>-->
                                <option <?php if($order_type == "service"){echo 'selected';}?>  value="service">نصب و سرویس</option>

                            </select>

                    </div>
                    <div class="form-group half">
                        <label>نحوه اشنایی</label>
                        <select class="form-control validate" data-validate="empty" id="introduction">
                            <option value="">نحوه آشنایی سفارش را مشخص نمایید.</option>
                            <?php

                            for($i = 0; $i < count($introductionList); $i++){
                                if($introductionList[$i] == ""){}else {
                                    if($introduction == ''){
                                        $selected = "";
                                    }else{
                                        if($introductionList[$i] == $introduction){
                                            $selected = "selected";
                                        }
                                        else{
                                            $selected = "";
                                        }
                                    }
                                    echo '<option '.$selected.' value="'.$introductionList[$i].'">'.$introductionList[$i].'</option>';
                                }
                            }

                            ?>
                        </select>

                    </div>
                    <div class="form-group half">
                        <label>اشتراک</label>
                        <input type="text" class="form-control"  data-validate="empty" id="subscription" value="<?=$subscription?>">
                    </div>
                    <div class="form-group half">
                        <label>نام و نام خانوادگی</label>
                        <input type="text" class="form-control validate"  data-validate="empty" id="first_name" value="<?=$first_name?>">
                    </div>
                    <div class="form-group half">
                        <label>شماره موبایل</label>
                        <input type="text" class="form-control validate isNumberic"  data-validate="mobile" id="mobile" maxlength="11" value="<?=$mobile?>">
                    </div>
                    <div class="form-group half">
                        <label>شماره ثابت</label>
                        <input type="text" class="form-control isNumberic" id="phone" value="<?=$phone?>">
                    </div>

                    <div class="form-group half">
                        <label>استان</label>
                        <select class="form-control validate" data-validate="empty" id="state">

                            <option value="">استان را انتخاب نمایید.</option>
                            <?php
                                $i = 0;
                                $state_list = get_terms(array("taxonomy" => "state" , "hide_empty" => false));
                                foreach($state_list as $item){
                                    $i++;
                                    if($item->term_id == $state){
                                        $selected = "selected";
                                    }
                                    elseif($i == 1){
                                        $selected = "selected";
                                    }
                                    else{
                                        $selected = "";
                                    }
                                    echo '<option '.$selected.' value="'.$item->term_id.'">'.$item->name.'</option>';

                                }
                            ?>

                        </select>
                    </div>
                    <div class="form-group half">
                        <label>مناطق</label>
                        <select class="form-control validate" data-validate="empty" id="city">

                            <option value="">منطقه را انتخاب نمایید.</option>
                            <?php
                            $city_list = get_terms(array("taxonomy" => "city" , "hide_empty" => false));

                            foreach($city_list as $item){
                                if($city == ''){
                                    $selected = "";
                                }else{
                                if($item->term_id == $city){
                                    $selected = "selected";
                                }else{
                                    $selected = "";
                                }
                                }
                                echo '<option '.$selected.' value="'.$item->term_id.'">'.$item->name.'</option>';

                            }
                            ?>

                        </select>
                    </div>
                    <div class="form-group">
                        <label>آدرس</label>
                        <textarea class="form-control validate" data-validate="empty" id="address"><?=$address?></textarea>
                    </div>
                    <div class="form-group third">
                        <label>نوع دستگاه</label>
                        <select class="form-control validate" data-validate="empty" id="device">

                            <option value="">نوع دستگاه را انتخاب نمایید.</option>
                            <?php
                            $device_list = get_terms(array("taxonomy" => "device" , "hide_empty" => false));

                            foreach($device_list as $item){
                                if($item->term_id == $device){
                                    $selected = "selected";
                                }else{
                                    $selected = "";
                                }
                                echo '<option '.$selected.' value="'.$item->term_id.'">'.$item->name.'</option>';

                            }
                            ?>

                        </select>
                    </div>
                    <div class="form-group third">
                        <label>برند</label>
                        <select class="form-control validate" data-validate="empty" id="brand">

                            <option value="">نوع برند را انتخاب نمایید.</option>
                            <?php
                            $brand_list = get_terms(array("taxonomy" => "brand" , "hide_empty" => false));

                            foreach($brand_list as $item){
                                if($item->term_id == $brand){
                                    $selected = "selected";
                                }else{
                                    $selected = "";
                                }
                                echo '<option '.$selected.' value="'.$item->term_id.'">'.$item->name.'</option>';

                            }
                            ?>

                        </select>
                    </div>
                    <div class="form-group third">
                        <label>ایراد دستگاه</label>
                        <select class="form-control" id="objection" multiple>
                            <option value="">ایراد دستگاه را مشخص نمایید.</option>
                            <?php

                                for($i = 0; $i < count($objectionsList); $i++){
                                    if($objectionsList[$i] == ""){}else {
                                        if($objection == ''){
                                            $selected = "";
                                        }else{
                                        if(in_array($objectionsList[$i],$objection)){
                                            $selected = "selected";
                                        }
                                        else{
                                            $selected = "";
                                        }
                                        }
                                        echo '<option '.$selected.' value="'.$objectionsList[$i].'">'.$objectionsList[$i].'</option>';
                                    }
                                }

                            ?>
                        </select>

                    </div>
                    <div class="form-group">
                        <label>توضیحات</label>
                        <textarea class="form-control" id="objection_description"><?=$objection_description?></textarea>
                    </div>
                    <?php if(CheckAccess("order_technician")){?>
                    <?php
                        if($technician == ""){
                            $deactive = '';
                        }
                        else{
                            $deactive = "disabled";
                        }
                    ?>

                    <?php

                        if(!isset($_GET["test"])){
?>
                            <div class="form-group">
                                <label>تکنسین</label>
                                <select class="form-control new_tech" id="technician" <?=$deactive?>>

                                    <option value="">انتخاب تکسین</option>
                                    <?php
                                    $customer_list = get_users( array(
                                        "meta_query" =>
                                            array(
                                                "relation" => "AND",
                                                array(
                                                    'key' => 'role',
                                                    'value' => 'technician',
                                                    'compare' => '='
                                                ),
                                                array(
                                                    "key" => "type_tech",
                                                    "value" => "external",
                                                    "compare" => "=",
                                                ),
                                            )
                                    ));
                                    foreach($customer_list as $item) {
                                        $status_tech = get_user_meta($item->ID, "status", true);
                                        if ($status_tech !== "0") {

                                            if ($technician == $item->ID) {
                                                $selected = "selected";
                                            } else {
                                                $selected = "";
                                            }
                                            $firstname_u = get_user_meta($item->ID, "first_name", true);

                                            $max_order = get_user_meta($item->ID, "max_order", true);
                                            $max_price = get_user_meta($item->ID, "max_price", true);

                                            $max_price = is_numeric($max_price) ? floatval($max_price) : 0;

                                            echo '<option ' . $selected . '
                                 data-max-order="' . $max_order . '"
                                 data-max-price="' . number_format($max_price) . '"
                                 value="' . $item->ID . '">' . $firstname_u . '
                                 </option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                    <?php
                        }else{

                    ?>

                    <div class="form-group">
                        <label>تکنسین</label>
                        <select class="form-control old" id="technician" <?=$deactive?>>

                            <option value="">انتخاب تکسین</option>
                            <?php
                            $customer_list = get_users( array(
                                    "meta_query" =>
                                        array(
                                            "relation" => "AND",
                                            array(
                                                'key' => 'role',
                                                'value' => 'technician',
                                                'compare' => '='
                                            ),
                                            array(
                                                "key" => "type_tech",
                                                "value" => "external",
                                                "compare" => "=",
                                            ),
                                        )
                                ));
                            foreach($customer_list as $item){

                                if($technician == $item->ID){
                                    $selected = "selected";
                                }
                                else{
                                    $selected = "";
                                }
                                $firstname_u = get_user_meta($item->ID,"first_name",true);

                                $max_order = get_user_meta($item->ID,"max_order",true);
                                $max_price = get_user_meta($item->ID,"max_price",true);

                                $order = count(get_posts( array(
                                    "post_type" => "orders",
                                    "posts_per_page" => -1,
                                    "post_status" => "any",
                                    "meta_query" => array(
                                        "relation" => "AND",
                                        array(
                                            "key" => "technician",
                                            "value" => $item->ID,
                                            "compare" => "LIKE"
                                        ),
                                        array(
                                            "key" => "status",
                                            "value" => array("0","1","2","3"),
                                            "compare" => "IN",
                                        )
                                    )
                                ) ));
                                $debt = loadAllDataFinَUser($item->ID);
                                if($debt < 1){
                                    $debt = $debt * -1;
                                }else{
                                    $debt = 0;
                                }
                                if($debt > $max_price || $order > $max_order){
                                    $message = '(تکنسین بیش از حد مجاز سفارش یا بدهی دارد. )';

                                    $error = 1;
                                }
                                else{
                                    $message = '';
                                    $error = 0;
                                }
                                echo '<option '.$selected.'
                                 data-max-order="'.$max_order.'"
                                 data-max-price="'.number_format($max_price).'"
                                 data-now-order="'.$order.'"
                                 data-now-debt="'.number_format($debt).'"
                                 data-error = "'.$error.'"
                                 value="'.$item->ID.'">
                                 '.$firstname_u.'  '.$message.'
                                 </option>';

                            }
                            ?>
                        </select>
                    </div>
                    <?php
                        }
                    ?>
                    <div class="dataOFTechnician">
                        <table >
                            <tr>
                                <th>سفارش های فعال تکنسین</th>
                                <td class="order_exists">0</td>
                                <th>تعداد سفارش مجاز</th>
                                <td class="order_limit">0</td>
                            </tr>
                            <tr>
                                <th>بدهی به شرکت</th>
                                <td class="debt_exists">0</td>
                                <th>سقف مجاز بدهی</th>
                                <td class="debt_limit">0</td>
                            </tr>
                        </table>
                    </div>
                    <div class="FourceChoseTec">ارسال اجباری به تکنسین</div>
                    <?php } ?>


                    <input type="submit" class="btn" value="<?=$btn;?>">

                </form>

            </div>
        </div>
    </section>
<?php
get_footer();

?>