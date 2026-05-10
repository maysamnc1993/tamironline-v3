<?php

// Template Name: Add Order Internal

get_header();
$objectionsList = get_option("objectionsList");
$shipping_list = get_option("shipping_list");
$introductionList = get_option("introductionList");
if(isset($_GET['uid'])){
    IFLoggedAndVerify("edit_order_internal");
    $title_section = 'ویرایش سفارش داخل مجموعه';
    $btn = 'ویرایش سفارش داخل مجموعه';
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
    $shipping = get_post_meta($uid,"shipping",true);
    $objection_description = get_post_meta($uid,"objection_description",true);
    $introduction = get_post_meta($uid,"introduction",true);
    $technician = get_post_meta($uid,"technician",true);
    $order_type = get_post_meta($uid,"order_type",true);
    $status_internal_order = get_post_meta($uid,"status_internal_order",true);

}else{
    IFLoggedAndVerify("add_order_internal");
    $title_section = 'افزودن سفارش داخل مجموعه';
    $btn = 'افزودن سفارش داخل مجموعه';
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
    $shipping = "";
    $objection = "";
    $objection_description = "";
    $introduction = "";
    $technician = "";
    $order_type = "";
    $first_name = "";
    $status_internal_order = "";
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

                <form action="#" method="post" id="OrderInternalAjax" >
                    <input type="hidden" id="customer" value="<?=$customer?>">
                    <input type="hidden" id="type_action" value="<?=$action?>">
                    <input type="hidden" id="uid" value="<?php if(isset($uid)){echo $uid;}?>">
                    <div class="form-group half">
                        <label>نوع سفارش</label>
                        <select class="form-control validate" data-validate="empty" id="order_type">

                            <option value="">نوع سفارش را انتخاب نمایید.</option>
                            <option <?php if($order_type == "repair"){echo 'selected';}?> value="repair">تعمیر</option>
                            <option <?php if($order_type == "buy"){echo 'selected';}?>  value="buy">خرید</option>

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
                    <?php if(CheckAccess("add_shipping_method_internal")){?>

                        <div class="form-group">
                            <label>نحوه ارسال کار</label>
                            <select class="form-control" id="shipping">
                                <option value="">نحوه ارسال را مشخص نمایید.</option>
                                <?php

                                for($i = 0; $i < count($shipping_list); $i++){
                                    if($shipping_list[$i] == ""){}else {
                                        if($objection == ''){
                                            $selected = "";
                                        }else{
                                            if($shipping_list[$i] == $shipping){
                                                $selected = "selected";
                                            }
                                            else{
                                                $selected = "";
                                            }
                                        }
                                        echo '<option '.$selected.' value="'.$shipping_list[$i].'">'.$shipping_list[$i].'</option>';
                                    }
                                }

                                ?>
                            </select>

                        </div>

                    <?php } ?>
<!--                    --><?php //if(CheckAccess("add_shipping_method_internal")){?>
<!---->
<!--                        <div class="form-group">-->
<!--                            <label>تغییر وضعیت</label>-->
<!--                            <select class="form-control" id="status_internal_order">-->
<!--                                <option value="">وضعیت را مشخص نمایید.</option>-->
<!--                                <option --><?php //if($status_internal_order  == "sent"){echo 'selected';}?><!-- value="sent">ارسال شود</option>-->
<!--                                <option --><?php //if($status_internal_order  == "receive"){echo 'selected';}?><!-- value="receive">دریافت شود</option>-->
<!--                                <option --><?php //if($status_internal_order  == "qc"){echo 'selected';}?><!-- value="qc">واحد کنترل</option>-->
<!--                            </select>-->
<!---->
<!--                        </div>-->
<!---->
<!--                    --><?php //} ?>
                    <?php
                        if(CheckAccess("add_tech_order_internal")){
                            $style = "";
                        }else{
                            $style = 'style="display:none;"';
                        }

                        ?>
                        <?php
                        if($technician == ""){
                            $deactive = '';
                        }
                        else{
                            $deactive = "disabled";
                        }
                        ?>
                        <div class="form-group" <?=$style?>>
                            <label>انتخاب تکسین داخل مجموعه</label>
                            <select class="form-control" id="technician" <?=$deactive?>>

                                <option value="">انتخاب</option>
                                <?php
                                if(isset($_GET['uid'])){

                                    echo '<option selected value="'.$technician.'">'.get_user_meta($technician,"first_name",true).'</option>';

                                }else{
                                echo get_option('tech_list_status');
                                }

                                ?>
                            </select>
                        </div>

                        <div <?=$style?> class="dataOFTechnician">
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
                        <div <?=$style?> class="FourceChoseTec">ارسال اجباری به تکنسین</div>


                    <input type="submit" class="btn" value="<?=$btn;?>">

                </form>

            </div>
        </div>
    </section>
<?php
get_footer();

?>