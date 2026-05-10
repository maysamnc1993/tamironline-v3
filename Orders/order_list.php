<?php
IFLoggedAndVerify("order_list");
// Template Name: Order List

if(!isset($_GET['test'])){


    if(!isset($_GET['searchTrue'])){

    }else{
    }
    
    $all_count = 0;
    $new_count= 0;
    $decline_count= 0;
    $sync_count= 0;
    $open_count= 0;
    $pend_count= 0;
    $cancel_count= 0;
    $down_count= 0;
    $archive= 0;
    $unarchive= 0;
    $back_count = 0;
    $back_open_count = 0;

    $disabled_edit = '';

    if(isset($_GET['searchTrue']) && !isset($_GET['status_filter'])){
        $disabled_edit = 'disabled_edit';
        echo '
        <style>.itemOrderBox {
        display: block;
}</style>
    ';
        $meta_query = array();
        $tax_query = array();
        $id_search = array();

        if($_GET['subscription'] == ""){}else{

            $sub_id = + $_GET['subscription'] - 10000;
            $subscription = array(
                "key" => "subscription",
                "value" => $_GET['subscription'],
                "compare" => "LIKE"
            );
            array_push($meta_query,$subscription);
        }

        if($_GET['order_id'] == ""){}else{
            $mobile = array(
                "key" => "mobile",
                "value" => $_GET['order_id'],
                "compare" => "LIKE"
            );
            array_push($meta_query,$mobile);
        }
        if($_GET['phone'] == ""){}else{
            $mobile = array(
                "key" => "phone",
                "value" => $_GET['phone'],
                "compare" => "LIKE"
            );
            array_push($meta_query,$mobile);
        }
        if($_GET['customer'] == ""){}else{

            $customer = array(
                "key" => "customer",
                "value" => $_GET['customer'],
                "compare" => "LIKE"
            );
            array_push($meta_query,$customer);
        }
        if($_GET['device'] == ""){}else{

            $brand = array(
                "taxonomy" => "device",
                "field" => "term_id",
                "terms" => $_GET['device'],
            );
            array_push($tax_query,$brand);
        }
        if($_GET['brand'] == ""){}else{

            $brand = array(
                "taxonomy" => "brand",
                "field" => "term_id",
                "terms" => $_GET['brand'],
            );
            array_push($tax_query,$brand);
        }
        if($_GET['technician'] == ""){}else{

            $customer = array(
                "key" => "technician",
                "value" => $_GET['technician'],
                "compare" => "LIKE"
            );
            array_push($meta_query,$customer);
        }
        if($_GET['state'] == ""){}else{

            $state = array(
                "taxonomy" => "state",
                "field" => "term_id",
                "terms" => $_GET['state'],
            );
            array_push($tax_query,$state);
        }
        if($_GET['city'] == ""){}else{

            $city = array(
                "taxonomy" => "city",
                "field" => "term_id",
                "terms" => $_GET['city'],
            );
            array_push($tax_query,$city);
        }
        if($_GET['status'] == ""){}else{
            $status = array(
                "key" => "status",
                "value" => $_GET['status'],
                "compare" => "LIKE"
            );
            array_push($meta_query,$status);
        }
        if($_GET['return_order'] == ""){}else if($_GET["return_order"] == 1){
            $return = array(
                "key" => "return_type",
                "compare" => "EXIST",
            );
            array_push($meta_query,$return);
        }
        else if($_GET["return_order"] == 2){
            $return = array(
                "key" => "return_type",
                "compare" => "EXIST",
            );
            array_push($meta_query,$return);
            $status = array(
                "key" => "status",
                "value" => 0,
                "compare" => "LIKE"
            );
            array_push($meta_query,$status);
        }
        if(isset($_GET['archive']) && $_GET['archive'] == "1"){

            $archive = array(
                "key" => "SaveToShayganStatus",
                "value" => "1",
                "compare" => "="
            );
            array_push($meta_query,$archive);
        }
        if(isset($_GET['archive']) && $_GET['archive'] == "0"){

            $unarchive = array(
                "key" => "SaveToShayganStatus",
                "value" => "1",
                "compare" => "NOT EXISTS"
            );
            array_push($meta_query,$unarchive);
        }
        if($_GET['objection'] == ""){}else{

            $objection = array(
                "key" => "objection",
                "value" => $_GET['objection'],
                "compare" => "LIKE"
            );
            array_push($meta_query,$objection);
        }
        if($_GET['introduction'] == ""){}else{

            $introduction = array(
                "key" => "introduction",
                "value" => $_GET['introduction'],
                "compare" => "LIKE"
            );
            array_push($meta_query,$introduction);
        }
        if($_GET['order_type'] == ""){}else{

            $order_type = array(
                "key" => "order_type",
                "value" => $_GET['order_type'],
                "compare" => "LIKE"
            );
            array_push($meta_query,$order_type);
        }
        if($_GET['no_tech_search'] == ""){}else{
            $no_tech = array(
                "key" => "technician",
                "value" => '',
                "compare" => "="
            );
            array_push($meta_query,$no_tech);
        }
        if($_GET['timestamp_end_order'] == ""){}else{
            $date = date("Y/m/d",($_GET['timestamp_end_order'] / 1000));
            $finish_order = array(
                "key" => "finish_order",
                "value" => $date,
                "compare" => "=",
            );
            array_push($meta_query,$finish_order);
        }
        if(isset($_GET['timestamp']) && isset($_GET['timestamp_end'])){
            $d = date("d",($_GET['timestamp'] / 1000));
            $m = date("m",($_GET['timestamp'] / 1000));
            $y = date("Y",($_GET['timestamp'] / 1000));
            if($_GET['start_order_time'] == ""){
                $time = "0";
            }else{
                $time = $_GET['start_order_time'];
            }
            $d_end = date("d",($_GET['timestamp_end'] / 1000));
            $m_end = date("m",($_GET['timestamp_end'] / 1000));
            $y_end = date("Y",($_GET['timestamp_end'] / 1000));
            if($_GET['end_order_time'] == ""){
                $time_end = "23";
            }else{
                $time_end = $_GET['end_order_time'];
            }
            $start = array(
                'year'  => $y,
                'month' => $m,
                'day'   => $d,
                'hour' => $time
            );
            $end = array(
                'year'  => $y_end,
                'month' => $m_end,
                'day'   => $d_end,
                'hour' => $time_end
            );

        }

        $filter =  array(
            "post__in" => $id_search,
            "post_type" => "orders",
            "posts_per_page" => -1,
            "author" => $_GET['operator'],
            "post_status" => "any",
            "meta_query" => array(
                "relation" => "AND",
                $meta_query
            ),
        );

//    if($_GET['operator'] == ""){}else{
//        $filter["author"] == $_GET['operator'];
//    }

        if($_GET['date_year'] == "" && $_GET['date_year_end'] == ""){}else{
            $filter["date_query"] = array(
                array(
                    'after'     => $start,
                    'before'    => $end,
                    'inclusive' => true,
                )
            );
        }
        if(count($tax_query) > 0 ){
            $filter['tax_query'] = array(
                "relation" => "OR",
                $tax_query
            ) ;
        }

        $operators = get_posts($filter);

        $count = count($operators);
        $pagination =  ceil($count / 10);

        $filter["posts_per_page"] = 10 ;
        $filter['paged'] = $paged;

        $array_filter = $filter;

    }
    else if(!isset($_GET['searchTrue']) && isset($_GET['status_filter'])){
        $meta_query_order = array();
        if(!isset($_GET['status_filter']) || $_GET['status_filter'] == "0" ){
            $meta_query_order = array(
                "relation" => "AND",
                array(
                    "key" => "status",
                    "value" => "0",
                    "compare" => "=",
                ),
                array(
                    "key" => "status",
                    "value" => "",
                    "compare" => "LIKE",
                ),
                array(
                    "key" => "return_type",
                    "compare" => "NOT EXISTS",
                )
            );
        }
        else if( $_GET['status_filter'] == "all" ){
            $meta_query_order = array();
        }
        else if( $_GET['status_filter'] == "back_open" ){
            $meta_query_order = array(
                "relation" => "AND",
                array(
                    "key" => "status",
                    "value" => "0",
                    "compare" => "=",
                ),
                array(
                    "key" => "return_type",
                    "value" => "",
                    "compare" => "!=",
                )
            );
        }
        else if( $_GET['status_filter'] == "back" ){
            $meta_query_order = array(
                array(
                    "key" => "return_type",
                    "compare" => "EXIST",
                )
            );
        }
        else if( $_GET['status_filter'] == "archive" ){
            $meta_query_order = array(
                array(
                    "key" => "happyCall",
                    "value" => 1,
                    "compare" => "=",
                )
            );
        }
        else if( $_GET['status_filter'] == "unarchive" ){
            $meta_query_order = array(
                "relation" => "AND",
                array(
                    "key" => "happyCall",
                    "compare" => "NOT EXISTS",
                ),
                array(
                    "key" => "status",
                    "value" => array("4","5"),
                    "compare" => "iN"
                )
            );
        }

        else if( $_GET['status_filter'] == "no_tech" ){

            $meta_query_order = array(
                array(
                    'key'     => 'technician',
                    'value'   => '',
                    'compare' => '=',
                ),
            );
        }
        else{
            $meta_query_order = array(
                array(
                    "key" => "status",
                    "value" => $_GET['status_filter'],
                    "compare" => "=",
                )
            );

        }
//    $operators = get_posts( array(
//        "post_type" => "orders",
//        "posts_per_page" => -1,
//        "post_status" => "any",
//        "meta_query" => $meta_query_order,
//    ) );
//
//    $count = count($operators);
        $array_filter = array(
            "post_type" => "orders",
            "post_status" => "any",
            "posts_per_page" => 10,
            "meta_query" => $meta_query_order,
        ) ;

    }

    $array_filter["paged"] = get_query_var( 'paged' ) ;
    $operator_list = new WP_Query($array_filter);

    if(isset($_GET['ConvertToEcxel'])){
        Export2Excel($array_filter);
    }
    get_header();
    ?>
<div class="modal fade" id="show_decline_report" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="list_of_show_decline_report">
            <?php
                $report_decline_user = get_option('report_decline');
                foreach($report_decline_user as $key => $value){
                    echo '<div class="report_monthly">
                        <div class="month_show">'.$key.'</div>
                        <table>
                            <tr>
                                <th>تکنسین</th>
                                <th>تعداد رد سفارش</th>
                            </tr>
                        
                    ';

                    foreach($value as $key => $value){
                        echo '
                            <tr>
                                <td>'.get_user_meta($key,'first_name',true).'</td>
                                <td>'.$value.'</td>
                            </tr>
                        ';
                    }
                    echo '</table></div>';
                }
            ?>
            </div>
        </div>
    </div>
</div>


    <div class="modal fade" id="SearchModalPopUp" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form <?php if(isset($_GET['searchTrue'])){echo'class="active"';}?> action="#" method="get" id="SearchList">

                    <h2 class="search_title"><span>جستجو بر اساس مشتری و تکنسین</span></h2>

                    <div class="box_of_search">
                        <input type="hidden" name="searchTrue" value="1">
                        <div class="form-group third">
                            <label>کد اشتراک</label>
                            <input type="text" name="subscription" class="form-control" <?php if(isset($_GET['searchTrue'])){echo'value="'.$_GET["subscription"].'"';}?> >
                        </div>
                        <div class="form-group third">
                            <label>شماره موبایل</label>
                            <input type="text" name="order_id" class="form-control" <?php if(isset($_GET['searchTrue'])){echo'value="'.$_GET["order_id"].'"';}?>>
                        </div>
                        <div class="form-group third">
                            <label>شماره ثابت</label>
                            <input type="text" name="phone" class="form-control" <?php if(isset($_GET['searchTrue'])){echo'value="'.$_GET["phone"].'"';}?>>
                        </div>
                        <div class="form-group third">
                            <label>تکنسین</label>
                            <select class="form-control" name="technician" id="technicianSearch">
                                <option value="">تکنسین را انتخاب نمایید.</option>
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
                                    $s_first_name = get_user_meta($item->ID,"first_name",true);
                                    $mobile = get_user_meta($item->ID,"mobile",true);
                                    $status_tech = get_user_meta($item->ID,"status",true);
                                    if($status_tech !== "0"){
                                        if (isset($_GET['technician'])) {
                                            if ($item->ID == $_GET['technician']) {
                                                $selected = "selected";
                                            } else {
                                                $selected = "";
                                            }
                                        } else {
                                            $selected = "";
                                        }
                                        echo '<option ' . $selected . ' value="' . $item->ID . '">'.$status_tech. $mobile . ' - ' . $s_first_name . ' </option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group third">
                            <label>اپراتور</label>
                            <select class="form-control" name="operator" id="operatorSearch">
                                <option value="">اپراتور را انتخاب نمایید.</option>
                                <?php
                                $customer_list = get_users( array(
                                    'meta_key' => 'role',
                                    'meta_value' => 'operator',
                                    'meta_compare' => '=' // exact match only
                                ) );
                                foreach($customer_list as $item){
                                    $s_first_name = get_user_meta($item->ID,"first_name",true);
                                    $mobile = get_user_meta($item->ID,"mobile",true);
                                    $status = get_user_meta($item->ID,"status",true);
                                    if($status !== 0){
                                        if(isset($_GET['operator'])){
                                            if($item->ID == $_GET['operator']){
                                                $selected = "selected";
                                            }
                                            else{
                                                $selected = "";
                                            }
                                        }
                                        else{
                                            $selected = "";
                                        }
                                        echo '<option '.$selected.' value="'.$item->ID.'">'.$s_first_name.' </option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group third">
                            <label>نحوه آشنایی</label>
                            <select class="form-control" id="Search_intro" name="introduction">
                                <option value="">نحوه آشنایی را مشخص نمایید.</option>
                                <?php
                                $introductionList = get_option("introductionList");
                                for($i = 0; $i < count($introductionList); $i++){
                                    if ($introductionList[$i] == "") {
                                    } else {
                                        if(isset($_GET['introduction'])) {
                                            if ($introductionList[$i] == $_GET['introduction']) {
                                                $selected = "selected";
                                            } else {
                                                $selected = "";
                                            }
                                        }
                                        else{
                                            $selected = "";
                                        }
                                        echo '<option ' . $selected . ' value="' . $introductionList[$i] . '">' . $introductionList[$i] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                    </div>

                    <h2 class="search_title"><span>جستجو بر اساس نوع دستگاه</span></h2>
                    <div class="box_of_search">
                        <div class="form-group fived">
                            <label>نوع دستگاه</label>
                            <select class="form-control validate" data-validate="empty" name="device" id="Searchdevice">

                                <option value="">نوع دستگاه را انتخاب نمایید.</option>
                                <?php
                                $device_list = get_terms(array("taxonomy" => "device" , "hide_empty" => false));

                                foreach($device_list as $item){
                                    if(isset($_GET['device'])){
                                        if($item->term_id == $_GET['device']){
                                            $selected = "selected";
                                        }else{
                                            $selected = "";
                                        }
                                    }else{
                                        $selected = "";
                                    }
                                    echo '<option '.$selected.' value="'.$item->term_id.'">'.$item->name.'</option>';

                                }
                                ?>

                            </select>
                        </div>
                        <div class="form-group fived">
                            <label>برند</label>
                            <select class="form-control validate" data-validate="empty" name="brand" id="search_brand">

                                <option value="">نوع برند را انتخاب نمایید.</option>
                                <?php
                                $brand_list = get_terms(array("taxonomy" => "brand" , "hide_empty" => false));

                                foreach($brand_list as $item){
                                    if(isset($_GET['brand'])){
                                        if($item->term_id == $_GET['brand']){
                                            $selected = "selected";
                                        }else{
                                            $selected = "";
                                        }
                                    }
                                    else{
                                        $selected = "";
                                    }
                                    echo '<option '.$selected.' value="'.$item->term_id.'">'.$item->name.'</option>';

                                }
                                ?>

                            </select>
                        </div>
                        <div class="form-group fived">
                            <label>ایراد دستگاه</label>
                            <select class="form-control" id="Search_objection" name="objection">
                                <option value="">ایراد دستگاه را مشخص نمایید.</option>
                                <?php
                                $objectionsList = get_option("objectionsList");
                                for($i = 0; $i < count($objectionsList); $i++){
                                    if ($objectionsList[$i] == "") {
                                    } else {
                                        if(isset($_GET['objection'])) {
                                            if ($objectionsList[$i] == $_GET['objection']) {
                                                $selected = "selected";
                                            } else {
                                                $selected = "";
                                            }
                                        }
                                        else{
                                            $selected = "";
                                        }
                                        echo '<option ' . $selected . ' value="' . $objectionsList[$i] . '">' . $objectionsList[$i] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group fived">
                            <label>نوع سفارش</label>
                            <select class="form-control validate" data-validate="empty" id="order_type" name="order_type">
                                <option value="">نوع سفارش را انتخاب نمایید.</option>
                                <option <?php  if(isset($_GET['order_type'])){if($_GET['order_type'] == "repair"){echo 'selected';}}?> value="repair">تعمیر</option>
                                <option <?php  if(isset($_GET['order_type'])){if($_GET['order_type'] == "selected"){echo 'selected';}}?>  value="buy">خرید</option>
                                <option <?php  if(isset($_GET['order_type'])){if($_GET['order_type'] == "selected"){echo 'selected';}}?>  value="service">سرویس در مجموعه</option>

                            </select>

                        </div>
                    </div>
                    <h2 class="search_title"><span>جستجو بر اساس آدرس</span></h2>
                    <div class="box_of_search">
                        <div class="form-group half">
                            <label>استان</label>
                            <select class="form-control validate" data-validate="empty" name="state" id="search_state">
                                <option value="">استان را انتخاب نمایید.</option>
                                <?php
                                $i = 0;
                                $state_list = get_terms(array("taxonomy" => "state" , "hide_empty" => false));
                                foreach($state_list as $item){
                                    $i++;
                                    if(isset($_GET['state'])){
                                        if($item->term_id == $_GET['state']){
                                            $selected = "selected";
                                        }else{
                                            $selected = "";
                                        }
                                    }
                                    elseif($item->term_id == 15){
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
                            <select class="form-control validate" data-validate="empty" name="city" id="search_city">

                                <option value="">منطقه را انتخاب نمایید.</option>
                                <?php
                                $city_list = get_terms(array("taxonomy" => "city" , "hide_empty" => false));

                                foreach($city_list as $item){
                                    if(isset($_GET['city'])) {
                                        if ($item->term_id == $_GET['city']) {
                                            $selected = "selected";
                                        } else {
                                            $selected = "";
                                        }
                                    }else{
                                        $selected = "";
                                    }
                                    echo '<option '.$selected.' value="'.$item->term_id.'">'.$item->name.'</option>';

                                }
                                ?>

                            </select>
                        </div>
                    </div>
                    <h2 class="search_title"><span>جستجو بر اساس وضعیت</span></h2>
                    <div class="box_of_search">
                        <ul class="Status_search" id="status_filter">

                            <li>
                                <label class="<?php if(isset($_GET['status'])){if($_GET['status'] == ""){echo 'active';}}?>">
                                    <input value="" class="<?php if(isset($_GET['status'])){if($_GET['status'] == ""){echo 'checked';}}?>" type="radio" name="status" >
                                    <span>همه</span>
                                </label>
                            </li>
                            <li>
                                <label class="<?php if(isset($_GET['status'])){if($_GET['status'] == "0"){echo 'active';}}?>">
                                    <input value="0" class="<?php if(isset($_GET['status'])){if($_GET['status'] == "0"){echo 'checked';}}?>" type="radio" name="status" >
                                    <span>جدید</span>
                                </label>
                            </li>
                            <li>
                                <label class="<?php if(isset($_GET['status'])){if($_GET['status'] == "100"){echo 'active';}}?>">
                                    <input value="100" class="<?php if(isset($_GET['status'])){if($_GET['status'] == "100"){echo 'checked';}}?>" type="radio" name="status" >
                                    <span>رد شده</span>
                                </label>
                            </li>
                            <li>
                                <label class="<?php if(isset($_GET['status'])){if($_GET['status'] == "1"){echo 'active';}}?>">
                                    <input value="1" class="<?php if(isset($_GET['status'])){if($_GET['status'] == "1"){echo 'checked';}}?>" type="radio" name="status" >
                                    <span>هماهنگ شده</span>
                                </label>
                            </li>
                            <li>
                                <label class="<?php if(isset($_GET['status'])){if($_GET['status'] == "2"){echo 'active';}}?>">
                                    <input value="2" class="<?php if(isset($_GET['status'])){if($_GET['status'] == "2"){echo 'checked';}}?>" type="radio" name="status" >
                                    <span>باز شده</span>
                                </label>
                            </li>
                            <li>
                                <label class="<?php if(isset($_GET['status'])){if($_GET['status'] == "3"){echo 'active';}}?>">
                                    <input value="3" class="<?php if(isset($_GET['status'])){if($_GET['status'] == "3"){echo 'checked';}}?>" type="radio" name="status" >
                                    <span>معلق</span>
                                </label>
                            </li>
                            <li>
                                <label class="<?php if(isset($_GET['status'])){if($_GET['status'] == "4"){echo 'active';}}?>">
                                    <input value="4" class="<?php if(isset($_GET['status'])){if($_GET['status'] == "4"){echo 'checked';}}?>" type="radio" name="status" >
                                    <span>کنسل شده</span>
                                </label>
                            </li>
                            <li>
                                <label class="<?php if(isset($_GET['status'])){if($_GET['status'] == "5"){echo 'active';}}?>">
                                    <input value="5" class="<?php if(isset($_GET['status'])){if($_GET['status'] == "5"){echo 'checked';}}?>" type="radio" name="status" >
                                    <span>انجام کار</span>
                                </label>
                            </li>
                               

                        </ul>
                    </div>
                    <h2 class="search_title"><span>جستجو بر اساس امکانات</span></h2>
                    <div class="box_of_search">
                        <ul class="Status_search" id="archive_filter">

                            <li>
                                <label class="<?php if(isset($_GET['archive'])){if($_GET['archive'] == "1"){echo 'active';}}?>">
                                    <input value="1" class="<?php if(isset($_GET['archive'])){if($_GET['archive'] == "1"){echo 'checked';}}?>" type="radio" name="archive" >
                                    <span>هپی کال شده </span>
                                </label>
                            </li>
                            <li>
                                <label class="<?php if(isset($_GET['archive'])){if($_GET['archive'] == "0"){echo 'active';}}?>">
                                    <input value="0" class="<?php if(isset($_GET['archive'])){if($_GET['archive'] == "0"){echo 'checked';}}?>" type="radio" name="archive" >
                                    <span>هپی کال نشده</span>
                                </label>
                            </li>


                        </ul>
                        <ul class="Status_search auto" id="return_search">

                            <li>
                                <label class="<?php if(isset($_GET['return_order'])){if($_GET['return_order'] == "1"){echo 'active';}}?>">
                                    <input value="1" class="<?php if(isset($_GET['return_order'])){if($_GET['return_order'] == "1"){echo 'checked';}}?>" type="checkbox" name="return_order" >
                                    <span>سفارش بازگشتی</span>
                                </label>
                            </li>
                            <li>
                                <label class="<?php if(isset($_GET['return_order'])){if($_GET['return_order'] == "2"){echo 'active';}}?>">
                                    <input value="2" class="<?php if(isset($_GET['return_order'])){if($_GET['return_order'] == "2"){echo 'checked';}}?>" type="checkbox" name="return_order" >
                                    <span>بازگشتی باز</span>
                                </label>
                            </li>

                        </ul>
                        <ul class="Status_search auto" id="no_tech">

                            <li>
                                <label class="<?php if(isset($_GET['no_tech_search'])){if($_GET['no_tech_search'] == "1"){echo 'active';}}?>">
                                    <input value="1" class="<?php if(isset($_GET['no_tech_search'])){if($_GET['no_tech_search'] == "1"){echo 'checked';}}?>" type="checkbox" name="no_tech_search" >
                                    <span>بدون تکنسین</span>
                                </label>
                            </li>

                        </ul>
                    </div>

                    <h2 class="search_title"><span>جستجو بر اساس تاریخ</span></h2>
                    <div class="box_of_search">
                        <div class="form-group third">
                            <div class="diviader main">
                                <label>تاریخ شروع سفارش</label>
                                <input type="text" name="date_year" id="date_year" class="form-control" <?php if(isset($_GET['date_year'])){echo'value="'.$_GET["date_year"].'"';}?> >
                                <input type="hidden" name="timestamp" id="timestamp" value="">
                            </div>
                            <div class="diviader">
                                <label>ساعت شروع سفارش</label>
                                <select class="form-control" name="start_order_time">
                                    <option value="">ساعت شروع</option>
                                    <?php
                                    for($i = 0 ; $i < 24; $i++){
                                        if(isset($_GET['start_order_time'])){
                                            if($_GET['start_order_time'] == $i){
                                                $selected = 'selected';
                                            }else{
                                                $selected = '';
                                            }
                                        }
                                        $h = $i + 1;
                                        echo '<option '.$selected.' value="'.$i.'">'.$h.'</option>';
                                    }
                                    ?>

                                </select>
                            </div>
                        </div>
                        <div class="form-group third">
                            <div class="diviader main">
                                <label>تاریخ پایان سفارش</label>
                                <input type="text" name="date_year_end" id="date_year_end" class="form-control" <?php if(isset($_GET['date_year_end'])){echo'value="'.$_GET["date_year_end"].'"';}?> >
                                <input type="hidden" name="timestamp_end" id="timestamp_end" value="">
                            </div>

                            <div class="diviader">
                                <label>ساعت پایان سفارش</label>
                                <select class="form-control" name="end_order_time">
                                    <option value="">ساعت پایان</option>
                                    <?php
                                    for($i = 0 ; $i < 24; $i++){
                                        if(isset($_GET['end_order_time'])){
                                            if($_GET['end_order_time'] == $i){
                                                $selected = 'selected';
                                            }else{
                                                $selected = '';
                                            }
                                        }
                                        $h = $i + 1;
                                        echo '<option '.$selected.' value="'.$i.'">'.$h.'</option>';
                                    }
                                    ?>

                                </select>
                            </div>
                        </div>
                        <div class="form-group third">
                            <label>تاریخ اتمام سفارش</label>
                            <input type="text" name="date_year_end_order" id="date_year_end_order" class="form-control" <?php if(isset($_GET['date_year_end_order'])){echo'value="'.$_GET["date_year_end_order"].'"';}?> >
                            <input type="hidden" name="timestamp_end_order" id="timestamp_end_order" value="">
                        </div>
                    </div>
                    <input type="submit" class="btn" value="جستجو پیشرفته">
                </form>
            </div>
        </div>
    </div>


    <section class="DashboardSection">

        <?=NavDashboard();?>

        <div class="main">
            <?php
            if(isset($_GET['searchTrue']) || isset($_GET['status_filter'])){
                $sep = '?'.$_SERVER['QUERY_STRING'].'&';
            }else{
                $sep = '?';
            }
            ?>

            <div class="content">

                <h1 class="title">لیست سفارشات</h1>

                <ul class="filter <?=$disabled_edit?>">

                    <li <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "all"){echo 'class="active"';}?>>
                        <a href="<?=$sep?>status_filter=all">
                            همه <i class="all_count"><?=$all_count?></i>
                        </a>
                    </li>
                    <li <?php if(!isset($_GET['status_filter']) || $_GET['status_filter'] == "0"){echo 'class="active"';}?>>
                        <a href="<?=$sep?>status_filter=0">جدید <i class="new_count"><?=$new_count?></i></a>
                    </li>
                    <li <?php if(!isset($_GET['status_filter']) || $_GET['status_filter'] == "100"){echo 'class="active"';}?>>
                        <a href="<?=$sep?>status_filter=100"> رد سفارش<i class="decline_count"><?=$decline_count?></i></a>
                    </li>
                    <li  <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "1"){echo 'class="active"';}?>>
                        <a href="<?=$sep?>status_filter=1">هماهنگ شده <i class="sync_count"><?=$sync_count?></i></a>
                    </li>
                    <li  <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "2"){echo 'class="active"';}?>>
                        <a href="<?=$sep?>status_filter=2">باز شده<i class="open_count"><?=$open_count?></i></a>
                    </li>
                    <li  <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "no_tech"){echo 'class="active"';}?>>
                        <a href="<?=$sep?>status_filter=no_tech">بدون تکنسین <i class="notech_count"><?=$notech_count?></i></a>
                    </li>
                    <li  <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "3"){echo 'class="active"';}?>>
                        <a href="<?=$sep?>status_filter=3">معلق<i class="pend_count"><?=$pend_count?></i></a>
                    </li>
                    <li  <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "4"){echo 'class="active"';}?>>
                        <a href="<?=$sep?>status_filter=4">کنسل شده<i class="cancel_count"><?=$cancel_count?></i></a>
                    </li>
                    <li  <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "5"){echo 'class="active"';}?>>
                        <a href="<?=$sep?>status_filter=5">انجام شده <i class="down_count"><?=$down_count?></i></a>
                    </li>
                    
                     <li  <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "10"){echo 'class="active"';}?>>
                        <a href="<?=$sep?>status_filter=10">
                            ایاب و ذهاب و تشخیص عیب
                             <!--<i class="down_count"><?=$down_count?></i>-->
                             </a>
                    </li>
                    <li <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "back"){echo 'class="active"';}?>>
                        <a href="<?=$sep?>status_filter=back">سفارش برگشتی<i class="back_count"><?=$back_count?></i></a>
                    </li>
                    <li <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "back_open"){echo 'class="active"';}?>>
                        <a href="<?=$sep?>status_filter=back_open">برگشتی باز<i class="back_open_count"><?=$back_open_count?></i></a>
                    </li>

                </ul>



                <div class="Box_filter_order">
                    <div id="SearchBoxBTN">جستجو پیشرفته</div>
                    <?php if(CheckAccess("order_export")){ ?>
                        <a href="<?=$sep?>ConvertToEcxel" id="ConvertToEcxel">خروجی اکسل</a>
                    <?php }?>

                    <div class="show_report_dec">گزارش رد سفارش ها</div>
                    <?php
                    if(isset($_GET['searchTrue'])){
                        ?>
                        <ul class="filter happy_call_filter_style <?=$disabled_edit?>">

                            <li <?php if(isset($_GET['archive_status']) && $_GET['archive_status'] == "archive"){echo 'class="active"';}?>>
                                <a href="<?=$sep?>archive_status=archive">
                                    هپی کال شده <i class="archive"><?=$archive?></i>
                                </a>
                            </li>
                            <li <?php if(isset($_GET['archive_status']) && $_GET['archive_status'] == "unarchive"){echo 'class="active"';}?>>
                                <a href="<?=$sep?>archive_status=unarchive">
                                    هپی کال نشده <i class="unarchive"><?=$unarchive?></i>
                                </a>
                            </li>


                        </ul>
                        <?php
                    }else{
                        ?>

                        <ul class="filter">

                            <li <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "archive"){echo 'class="active"';}?>>
                                <a href="<?=$sep?>status_filter=archive">
                                    هپی کال شده <i class="archive"><?=$archive?></i>
                                </a>
                            </li>
                            <li <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "unarchive"){echo 'class="active"';}?>>
                                <a href="<?=$sep?>status_filter=unarchive">
                                    هپی کال نشده <i class="unarchive"><?=$unarchive?></i>
                                </a>
                            </li>


                        </ul>
                    <?php } ?>
                </div>


                <ul class="list_of_data head">
                    <li>وضعیت</li>
                    <li>کد اشتراک</li>
                    <li>نام مشتری</li>
                    <li>آدرس</li>
                    <li>دستگاه</li>
                    <li>برند</li>
                    <li>تکنسین</li>
                    <li>تاریخ سفارش</li>
                    <li>عملیات</li>
                </ul>

                <?php

                $i = 0;
                if ( $operator_list->have_posts() ) {
                    while ($operator_list->have_posts()) {
                        $operator_list->the_post();
                        $i++;
                        $subscription = get_post_meta(get_the_ID(), "subscription", true);
                        $introduction = get_post_meta(get_the_ID(), "introduction", true);
                        $customer = get_post_meta(get_the_ID(), "customer", true);
                        $first_name = get_user_meta($customer, "first_name", true);
                        $address = get_post_meta(get_the_ID(), "address", true);
                        $send_technician = get_post_meta(get_the_ID(), "send_technician", true);
                        $device = wp_get_post_terms(get_the_ID(), "device");
                        $brand = wp_get_post_terms(get_the_ID(), "brand");
                        $technician = get_post_meta(get_the_ID(), "technician", true);
                        $first_name_technician = get_user_meta($technician, "first_name", true);
                        $status = get_post_meta(get_the_ID(), "status", true);
                        $finish_order_sh = get_post_meta(get_the_ID(), "finish_order_sh", true);
                        $negative_invoice = get_post_meta(get_the_ID(), "negative_invoice", true);
                        $SaveToShayganStatus = get_post_meta(get_the_ID(), "happyCall", true);
                        $return_type = get_post_meta(get_the_ID(), "return_type", true);
                        $hc_customer = get_post_meta(get_the_ID(), "hc_customer", true);
                        $hc_tech = get_post_meta(get_the_ID(), "hc_tech", true);
                        if ($hc_customer == "") {
                            $hc_customer_star = '';
                        } else {

                            $s_1 = '';
                            $s_2 = '';
                            $s_3 = '';
                            $s_4 = '';
                            $s_5 = '';
                            if ($hc_customer > 1) {
                                $s_1 = "active";
                            }
                            if ($hc_customer > 19) {
                                $s_2 = "active";
                            }
                            if ($hc_customer > 39) {
                                $s_3 = "active";
                            }
                            if ($hc_customer > 59) {
                                $s_4 = "active";
                            }
                            if ($hc_customer > 89) {
                                $s_5 = "active";
                            }
                            $hc_customer_star = '
                        <span class="box_star_hc_customer">
                            هپی کال مشتریان
                            <span class="star">
                            <b class="' . $s_1 . '">*</b>
                            <b class="' . $s_2 . '">*</b>
                            <b class="' . $s_3 . '">*</b>
                            <b class="' . $s_4 . '">*</b>
                            <b class="' . $s_5 . '">*</b>
                            </span>
                        </span>
                    ';
                        }
                        if ($hc_tech == "") {
                            $hc_tech_star = '';
                        } else {

                            $st_1 = '';
                            $st_2 = '';
                            $st_3 = '';
                            $st_4 = '';
                            $st_5 = '';
                            if ($hc_tech > 1) {
                                $st_1 = "active";
                            }
                            if ($hc_tech > 19) {
                                $st_2 = "active";
                            }
                            if ($hc_tech > 39) {
                                $st_3 = "active";
                            }
                            if ($hc_tech > 59) {
                                $st_4 = "active";
                            }
                            if ($hc_tech > 89) {
                                $st_5 = "active";
                            }
                            $hc_tech_star = '
                        <span class="box_star_hc_customer">
                            هپی کال تکنسین
                            <span class="star">
                            <b class="' . $st_1 . '">*</b>
                            <b class="' . $st_2 . '">*</b>
                            <b class="' . $st_3 . '">*</b>
                            <b class="' . $st_4 . '">*</b>
                            <b class="' . $st_5 . '">*</b>
                            </span>
                        </span>
                    ';
                        }
                        $city = wp_get_post_terms($item->ID, "city")[0]->name;
                        if ($SaveToShayganStatus == 1) {
                            $save_soroshan = '<span class="sync">هپی کال شده</span>';
                        } else {
                            $save_soroshan = '<span class="unsync">هپی کال نشده</span>';
                        }

                        $status_title = '<span class="status new">جدید</span>';
                        $active = '';
                        if ($status == 0 || $status == "") {
                            $active = "active";

                        }
                        if ($status == 1) {
                            $status_title = '<span class="status sync">هماهنگ شده</span>';

                        } else if ($status == 2) {
                            $status_title = '<span class="status opened">باز شده</span>';

                        } else if ($status == 3) {
                            $status_title = '<span class="status pend">معلق</span>';

                        } else if ($status == 4) {
                            $status_title = '<span class="status cancel">کنسل</span>';

                        } else if ($status == 5) {
                            $status_title = '<span class="status done">انجام شده</span>';

                        }
                        else if ($status == 10) {
                            $status_title = '<span class="status done">
                            ایاب و ذهاب
                            
                            </span>';

                        }
                        else if ($status == 100) {
                            $status_title = '<span class="status cancel">رد شده</span>';

                        }
                        if ($technician == "") {

                        } else {
                            $tech_Status = "";

                        }

                        if ($negative_invoice == 1) {
                            $n_inv = '<span class="n_invoice">فاکتور منفی (نیازمند تایید اپراتور)</span>';
                        } else {
                            $n_inv = "";
                        }
                        $return_type_text = '';
                        if ($return_type == 1) {
                            $return_type_text = ' 
                                    <li class="backOrder">
                                        <b><span class="back">سفارش برگشتی</span> </b>
                                    </li>';
                        }

                        if ($finish_order_sh == '') {
                        } else {
                            $finish_or = '<li class="sub">
                                        <span>پایان کار</span>
                                        <b>' . $finish_order_sh . '</b>
                                    </li>';
                        }

                        echo '
                        
                            <ul class="list_of_data head hide_in_desktop ">
                                 <li>وضعیت</li>
                                <li>کد اشتراک</li>
                                <li>نام مشتری</li>
                                <li>آدرس</li>
                                <li>دستگاه</li>
                                <li>برند</li>
                                <li>تکنسین</li>
                                <li>تاریخ سفارش</li>
                                <li>عملیات</li>
                            </ul>
                                   
                            <ul class="list_of_data show_horizental">
                                <li>'.$status_title.'</li>
                                <li>'.$subscription.'</li>
                                <li>' . $first_name . ' ' . SelectTypeUserName($customer) . '</li>
                                <li class="Address"> ' . $address . ' </li>   
                                <li>' . $device[0]->name . '</li>
                                <li>' . $brand[0]->name . '</li>
                                <li>' . $first_name_technician . '</li>
                                <li>' . get_the_date("Y/m/d H:i", $item->ID) . '</li>
                                <li>
                               
                                ';

                                if (CheckAccess("edit_order")) {
                                    echo '<a class=" edit" href="' . get_the_permalink(110) . '?uid=' . get_the_ID() . '">ویرایش</a>';
                                }
                                echo '
                                    <a class=" view" href="' . get_the_permalink(135) . '?uid=' . get_the_ID() . '">مشاهده</a>
                                </li>
                            </ul>
                       ';
                        

                    }
                }
                ?>


                <div class="pagination">
                    <?php
                    echo paginate_links( array(
                        'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                        'total'        => $operator_list->max_num_pages,
                        'current'      => max( 1, get_query_var( 'paged' ) ),
                        'format'       => '?paged=%#%',
                        'show_all'     => false,
                        'type'         => 'plain',
                        'end_size'     => 2,
                        'mid_size'     => 1,
                        'prev_next'    => true,
                        'add_args'     => false,
                        'add_fragment' => '',
                    ) );
                    ?>
                </div>


            </div>

        </div>
    </section>

    <?php
    get_footer();



    if(isset($_GET['searchTrue'])){
        $array_filter["posts_per_page"] = -1;
        $count_filter = get_posts($array_filter);

        $all_count = 0;
        $new_count = 0;
        $decline_count = 0;
        $sync_count = 0;
        $open_count = 0;
        $notech_count = 0;
        $pend_count = 0;
        $cancel_count = 0;
        $down_count = 0;
        $back_count = 0;
        $archive = 0;
        $unarchive = 0;
        $back_open_count = 0;
        foreach($count_filter as $item){
//
            $status = get_post_meta($item->ID,"status",true);
            $SaveToShayganStatus = get_post_meta($item->ID,"happyCall",true);
            $negative_invoice = get_post_meta($item->ID,"return_type",true);
            $no_tech_count = get_post_meta($item->ID,"technician",true);
            if($no_tech_count == "" && $status == 0){
                $notech_count++;
            }
            if($negative_invoice == ''){}else{
                $back_count++;
            }

            $all_count++;
            if($status == 0 || $status == ""){
                $new_count++;
            }
            if($status == 1){
                $sync_count++;
            }
            if($status == 100){
                $decline_count++;
            }
            else if($status == 2){
                $open_count++;
            }
            else if($status == 3){
                $pend_count++;
            }
            else if($status == 4){
                if($SaveToShayganStatus == 1){
                    $archive++;
                }else{
                    $unarchive++;
                }
                $cancel_count++;
            }
            else if($status == 5){
                if($SaveToShayganStatus == 1){
                    $archive++;
                }else{
                    $unarchive++;
                }
                $down_count++;
            }

        }

        ?>
        <script>
            $(document).ready(function (){
                $(".filter li i.all_count").html("<?=$all_count?>");
                $(".filter li i.new_count").html("<?=$new_count?>");
                $(".filter li i.sync_count").html("<?=$sync_count?>");
                $(".filter li i.open_count").html("<?=$open_count?>");
                $(".filter li i.notech_count").html("<?=$notech_count?>");
                $(".filter li i.pend_count").html("<?=$pend_count?>");
                $(".filter li i.cancel_count").html("<?=$cancel_count?>");
                $(".filter li i.down_count").html("<?=$down_count?>");
                $(".filter li i.back_count").html("<?=$back_count?>");
                $(".filter li i.archive").html("<?=$archive?>");
                $(".filter li i.unarchive").html("<?=$unarchive?>");
                $(".filter li i.back_open").html("<?=$back_open_count?>");
            });
        </script>
    <?php }
    else{?>
        <script>
            $(window).on("load",function (){
                LoadCountOrder();
            });
        </script>
        <?php
    }



}



?>

