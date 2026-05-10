<?php
IFLoggedAndVerify("order_list_internal");
// Template Name: Order List Internal

if(!isset($_GET['test'])){

    if(isset($_GET['page_no'])){
        $paged = $_GET['page_no'];
    }else{
        $paged = 1;
    }

    if(!isset($_GET['searchTrue'])){

    }else{
    }


    $all_count = 0;
    $new_count= 0;
    $sync_count= 0;
    $open_count= 0;
    $pend_count= 0;
    $cancel_count= 0;
    $down_count= 0;
    $archive= 0;
    $unarchive= 0;
    $back_count = 0;
    $payment_pend_count = 0;
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
        if($_GET['status_send_recive'] == ""){}else{

            $status_send_recive = array(
                "key" => "status_send_recive",
                "value" => $_GET['status_send_recive'],
                "compare" => "="
            );
            array_push($meta_query,$status_send_recive);
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
//    if($_GET['status'] == ""){}else{
//        $status = array(
//            "key" => "status",
//            "value" => $_GET['status'],
//            "compare" => "LIKE"
//        );
//        array_push($meta_query,$status);
//    }

        if($_GET['status'] == "0" ){
            $status = array(
                "relation" => "OR",
                array(
                    "key" => "technician",
                    "value" => "",
                    "compare" => "=",
                ),
                array(
                    "key" => "status_internal_order",
                    "value" => array("4","5"),
                    "compare" => "IN",
                ),
            );
            array_push($meta_query,$status);

        }
        else if( $_GET['status'] == "all" ){
            $status = array();
            array_push($meta_query,$status);
        }
        else if( $_GET['status'] == "1" ){
            $status = array(
                "relation" => "AND",
                array(
                    "key" => "technician",
                    "value" => "",
                    "compare" => "!=",
                ),
                array(
                    "key" => "status",
                    "value" => array("0","sent","receive"),
                    "compare" => "IN"
                )
            );
            array_push($meta_query,$status);
        }
        else if( $_GET['status'] == "sent" ){
            $status = array(
                array(
                    "key" => "status_internal_order",
                    "value" => "sent",
                    "compare" => "=",
                )
            );
            array_push($meta_query,$status);
        }
        else if( $_GET['status'] == "payment_pend" ){
            $status = array(
                array(
                    "key" => "status_internal_order",
                    "value" => "payment_pend",
                    "compare" => "=",
                )
            );
            array_push($meta_query,$status);
        }
        else if( $_GET['status'] == "receive" ){
            $status = array(
                array(
                    "key" => "status_internal_order",
                    "value" => "receive",
                    "compare" => "=",
                )
            );
            array_push($meta_query,$status);
        }
        else if( $_GET['status'] == "qc" ){
            $status = array(
                "relation" => "OR",
                array(
                    "key" => "status_internal_order",
                    "value" => "qc",
                    "compare" => "=",
                ),
                array(
                    "key" => "status",
                    "value" => "qc",
                    "compare" => "=",
                ),
            );
            array_push($meta_query,$status);
        }

        else if( $_GET['status_filter'] == "4" ){
            $status = array(
                "relation" => "AND",
                array(
                    "key" => "status_internal_order",
                    "value" => "1000",
                    "compare" => "=",
                ),
                array(
                    "key" => "status",
                    "value" => "4",
                    "compare" => "=",
                )
            );
            array_push($meta_query,$status);
        }

        else if( $_GET['status_filter'] == "5" ){
            $status = array(
                "relation" => "AND",
                array(
                    "key" => "status_internal_order",
                    "value" => "1000",
                    "compare" => "=",
                ),
                array(
                    "key" => "status",
                    "value" => "5",
                    "compare" => "=",
                )

            );
            array_push($meta_query,$status);
        }

        else{
            $meta_query_order = array(
                array(
                    "key" => "status",
                    "value" => $_GET['status_filter'],
                    "compare" => "LIKE",
                )
            );

        }



        if($_GET['return_order'] == ""){}else{
            $return = array(
                "key" => "return_type",
                "compare" => "EXIST",
            );
            array_push($meta_query,$return);
        }
        if(isset($_GET['archive']) && $_GET['archive'] == "1"){

            $archive = array(
                "key" => "happyCall",
                "value" => "1",
                "compare" => "="
            );
            array_push($meta_query,$archive);
        }
        if(isset($_GET['archive']) && $_GET['archive'] == "0"){

            $unarchive = array(
                "key" => "happyCall",
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
        if($_GET["order_id_search"] == ""){}else{
            array_push($id_search,$_GET["order_id_search"]);
        }
        $filter =  array(
            "post__in" => $id_search,
            "post_type" => "internal_orders",
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
                "relation" => "OR",
                array(
                    "key" => "technician",
                    "value" => "",
                    "compare" => "=",
                ),
                array(
                    "key" => "status_internal_order",
                    "value" => array("","4","5"),
                    "compare" => "IN",
                ),


            );
        }
        else if( $_GET['status_filter'] == "all" ){
            $meta_query_order = array();
        }
        else if( $_GET['status_filter'] == "1" ){
            $meta_query_order = array(
                "relation" => "AND",
                array(
                    "key" => "technician",
                    "value" => "",
                    "compare" => "!=",
                ),
                array(
                    "key" => "status",
                    "value" => array("0","sent","receive"),
                    "compare" => "IN"
                )
            );
        }
        else if( $_GET['status_filter'] == "sent" ){
            $meta_query_order = array(
                array(
                    "key" => "status_internal_order",
                    "value" => "sent",
                    "compare" => "=",
                )
            );
        }
        else if( $_GET['status_filter'] == "payment_pend" ){
            $meta_query_order = array(
                array(
                    "key" => "status_internal_order",
                    "value" => "payment_pend",
                    "compare" => "=",
                )
            );
        }
        else if( $_GET['status_filter'] == "receive" ){
            $meta_query_order = array(
                array(
                    "key" => "status_internal_order",
                    "value" => "receive",
                    "compare" => "=",
                )
            );
        }

        else if( $_GET['status_filter'] == "qc" ){
            $meta_query_order = array(
                array(
                    "key" => "status_internal_order",
                    "value" => "qc",
                    "compare" => "=",
                )
            );
        }

        else if( $_GET['status_filter'] == "4" ){
            $meta_query_order = array(
                "relation" => "AND",
                array(
                    "key" => "status_internal_order",
                    "value" => "1000",
                    "compare" => "=",
                ),
                array(
                    "key" => "status",
                    "value" => "4",
                    "compare" => "=",
                )
            );
        }

        else if( $_GET['status_filter'] == "5" ){
            $meta_query_order = array(
                "relation" => "AND",
                array(
                    "key" => "status_internal_order",
                    "value" => "1000",
                    "compare" => "=",
                ),
                array(
                    "key" => "status",
                    "value" => "5",
                    "compare" => "=",
                )
            );
        }

        else{
            $meta_query_order = array(
                array(
                    "key" => "status",
                    "value" => $_GET['status_filter'],
                    "compare" => "LIKE",
                )
            );

        }
        $operators = get_posts( array(
            "post_type" => "internal_orders",
            "posts_per_page" => -1,
            "post_status" => "any",
            "meta_query" => $meta_query_order,
        ) );

        $count = count($operators);
        $pagination =  ceil($count / 10);
        $array_filter = array(
            "post_type" => "internal_orders",

            "post_status" => "any",
            "posts_per_page" => 10,
            "paged" => $paged,
            "meta_query" => $meta_query_order,
        ) ;

    }
    else if(isset($_GET['searchTrue']) && isset($_GET['status_filter'])){
        echo '
        <style>.itemOrderBox {
        display: block;
}</style>
    ';
        $meta_query = array();
        $tax_query = array();
        $id_search = array();

        if($_GET['subscription'] == ""){}else{

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
        if($_GET['status_filter'] == ""){}else{
            if( $_GET['status_filter'] == "no_tech" ){
                $meta_query_order = array(
                    "key" => "technician",
                    "value" => '',
                    "compare" => "=",
                );

            }
            if( $_GET['status_filter'] == "all" ){
                $meta_query_order = array();
            }
            else{
                $meta_query_order = array(
                    "key" => "status",
                    "value" => $_GET['status_filter'],
                    "compare" => "LIKE",
                );
            }

            array_push($meta_query,$meta_query_order);

        }
        if($_GET['objection'] == ""){}else{

            $objection = array(
                "key" => "objection",
                "value" => $_GET['objection'],
                "compare" => "LIKE"
            );
            array_push($meta_query,$objection);
        }
        if($_GET['order_type'] == ""){}else{

            $order_type = array(
                "key" => "order_type",
                "value" => $_GET['order_type'],
                "compare" => "LIKE"
            );
            array_push($meta_query,$order_type);
        }
        if(isset($_GET['timestamp'])){
            $d = date("d",($_GET['timestamp'] / 1000));
            $m = date("m",($_GET['timestamp'] / 1000));
            $y = date("Y",($_GET['timestamp'] / 1000));
            $date = array(
                'year'  => $y,
                'month' => $m,
                'day'   => $d,
            );
        }


        $filter =  array(
            "post__in" => $id_search,
            "post_type" => "internal_orders",
            "posts_per_page" => -1,
            "post_status" => "any",
            "meta_query" => array(
                "relation" => "AND",
                $meta_query
            ),
        );



        if($_GET['date_year'] == ""){}else{
            $filter["date_query"] = $date;
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


    $operator_list = get_posts($array_filter);

    if(isset($_GET['ConvertToEcxel'])){
        Export2Excel($array_filter);
    }
    get_header();
    ?>
    <div class="modal fade" id="SearchModalPopUp" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form <?php if(isset($_GET['searchTrue'])){echo'class="active"';}?> action="#" method="get" id="SearchList">

                    <h2 class="search_title"><span>جستجو بر اساس مشتری و تکنسین</span></h2>

                    <div class="box_of_search">
                        <input type="hidden" name="searchTrue" value="1">
                        <div class="form-group fived">
                            <label>شماره سفارش</label>
                            <input type="text" name="order_id_search" class="form-control" <?php if(isset($_GET['searchTrue'])){echo'value="'.$_GET["order_id_search"].'"';}?> >
                        </div>
                        <div class="form-group fived">
                            <label>کد اشتراک</label>
                            <input type="text" name="subscription" class="form-control" <?php if(isset($_GET['searchTrue'])){echo'value="'.$_GET["subscription"].'"';}?> >
                        </div>
                        <div class="form-group fived">
                            <label>شماره موبایل</label>
                            <input type="text" name="order_id" class="form-control" <?php if(isset($_GET['searchTrue'])){echo'value="'.$_GET["order_id"].'"';}?>>
                        </div>
                        <div class="form-group fived">
                            <label>شماره ثابت</label>
                            <input type="text" name="phone" class="form-control" <?php if(isset($_GET['searchTrue'])){echo'value="'.$_GET["phone"].'"';}?>>
                        </div>
                        <div class="form-group fived">
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
                                                "value" => "internal",
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
                                    }elseif($item->term_id == 15){
                                        $selected = "selected";
                                    }else{
                                        $selected = '';
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
                                    <span>نامشخص</span>
                                </label>
                            </li>
                            <li>
                                <label class="<?php if(isset($_GET['status'])){if($_GET['status'] == "1"){echo 'active';}}?>">
                                    <input value="1" class="<?php if(isset($_GET['status'])){if($_GET['status'] == "1"){echo 'checked';}}?>" type="radio" name="status" >
                                    <span>در حال تعمیرات</span>
                                </label>
                            </li>
                            <li>
                                <label class="<?php if(isset($_GET['status'])){if($_GET['status'] == "payment_pend"){echo 'active';}}?>">
                                    <input value="payment_pend" class="<?php if(isset($_GET['status'])){if($_GET['status'] == "payment_pend"){echo 'checked';}}?>" type="radio" name="status" >
                                    <span>منتظر پرداخت</span>
                                </label>
                            </li>
                            <li>
                                <label class="<?php if(isset($_GET['status'])){if($_GET['status'] == "sent"){echo 'active';}}?>">
                                    <input value="sent" class="<?php if(isset($_GET['status'])){if($_GET['status'] == "sent"){echo 'checked';}}?>" type="radio" name="status" >
                                    <span>ارسال بشود</span>
                                </label>
                            </li>
                            <li>
                                <label class="<?php if(isset($_GET['status'])){if($_GET['status'] == "receive"){echo 'active';}}?>">
                                    <input value="receive" class="<?php if(isset($_GET['status'])){if($_GET['status'] == "receive"){echo 'checked';}}?>" type="radio" name="status" >
                                    <span>دریافت بشود</span>
                                </label>
                            </li>
                            <li>
                                <label class="<?php if(isset($_GET['status'])){if($_GET['status'] == "qc"){echo 'active';}}?>">
                                    <input value="qc" class="<?php if(isset($_GET['status'])){if($_GET['status'] == "qc"){echo 'checked';}}?>" type="radio" name="status" >
                                    <span>واحد کنترل</span>
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

                    <h2 class="search_title"><span>جستجو بر تگ</span></h2>
                    <div class="box_of_search">
                        <ul class="Status_search" id="status_filter">


                            <li>
                                <label class="<?php if(isset($_GET['status_send_recive'])){if($_GET['status_send_recive'] == "sent"){echo 'active';}}?>">
                                    <input value="sent" class="<?php if(isset($_GET['status_send_recive'])){if($_GET['status_send_recive'] == "sent"){echo 'checked';}}?>" type="radio" name="status_send_recive" >
                                    <span>ارسال شد</span>
                                </label>
                            </li>

                            <li>
                                <label class="<?php if(isset($_GET['status_send_recive'])){if($_GET['status_send_recive'] == "receive"){echo 'active';}}?>">
                                    <input value="receive" class="<?php if(isset($_GET['status_send_recive'])){if($_GET['status_send_recive'] == "receive"){echo 'checked';}}?>" type="radio" name="status_send_recive" >
                                    <span>دریافت شد</span>
                                </label>
                            </li>
                            <li>
                                <label class="<?php if(isset($_GET['status'])){if($_GET['status_send_recive'] == "4"){echo 'active';}}?>">
                                    <input value="4" class="<?php if(isset($_GET['status_send_recive'])){if($_GET['status_send_recive'] == "4"){echo 'checked';}}?>" type="radio" name="status_send_recive" >
                                    <span>کنسل شده</span>
                                </label>
                            </li>
                            <li>
                                <label class="<?php if(isset($_GET['status_send_recive'])){if($_GET['status_send_recive'] == "5"){echo 'active';}}?>">
                                    <input value="5" class="<?php if(isset($_GET['status_send_recive'])){if($_GET['status_send_recive'] == "5"){echo 'checked';}}?>" type="radio" name="status_send_recive" >
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
                                    <span>پرداخت شده </span>
                                </label>
                            </li>
                            <li>
                                <label class="<?php if(isset($_GET['archive'])){if($_GET['archive'] == "0"){echo 'active';}}?>">
                                    <input value="0" class="<?php if(isset($_GET['archive'])){if($_GET['archive'] == "0"){echo 'checked';}}?>" type="radio" name="archive" >
                                    <span>منتظر پرداخت</span>
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
                // Fix for the query string separator handling
                $sep = '?';
                if (!empty($_SERVER['QUERY_STRING'])) {
                    if (isset($_GET['searchTrue'])) {
                        $sep = '?' . htmlspecialchars($_SERVER['QUERY_STRING']) . '&';
                    } else {
                        $sep = '?' . htmlspecialchars($_SERVER['QUERY_STRING']);
                    }
                }
                ?>

                <div class="content">
                    <h1 class="title">لیست سفارشات</h1>

                    <ul class="filter <?= htmlspecialchars($disabled_edit) ?>">
                        <li class="<?= (isset($_GET['status_filter']) && $_GET['status_filter'] == "all") ? 'active' : '' ?>">
                            <a href="<?= $sep ?>status_filter=all">
                                همه <i class="all_count"><?= htmlspecialchars($all_count) ?></i>
                            </a>
                        </li>
                        <li class="<?= (!isset($_GET['status_filter']) || $_GET['status_filter'] == "0") ? 'active' : '' ?>">
                            <a href="<?= $sep ?>status_filter=0">نامشخص<i class="new_count"><?= htmlspecialchars($new_count) ?></i></a>
                        </li>
                        <li class="<?= (isset($_GET['status_filter']) && $_GET['status_filter'] == "payment_pend") ? 'active' : '' ?>">
                            <a href="<?= $sep ?>status_filter=payment_pend">منتظرپرداخت<i class="payment_pend_count"><?= htmlspecialchars($payment_pend_count) ?></i></a>
                        </li>
                        <li class="<?= (isset($_GET['status_filter']) && $_GET['status_filter'] == "1") ? 'active' : '' ?>">
                            <a href="<?= $sep ?>status_filter=1">در حال تعمیرات<i class="sync_count"><?= htmlspecialchars($sync_count) ?></i></a>
                        </li>

                        <?php if (CheckAccess("send_order_internal")) { ?>
                            <li class="<?= (isset($_GET['status_filter']) && $_GET['status_filter'] == "sent") ? 'active' : '' ?>">
                                <a href="<?= $sep ?>status_filter=sent">ارسال بشود<i class="send_count"><?= htmlspecialchars($sync_count) ?></i></a>
                            </li>
                        <?php } ?>

                        <!-- Continue with other conditions and filters -->
                    </ul>

                    <div class="Box_filter_order">
                        <div id="SearchBoxBTN">جستجو پیشرفته</div>
                        <?php if (CheckAccess("order_export")) { ?>
                            <a href="<?= $sep ?>ConvertToEcxel" id="ConvertToEcxel">خروجی اکسل</a>
                        <?php } ?>
                    </div>

                    <ul class="list_of_data head show_horizental">
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
                    foreach ($operator_list as $item) {
                        $i++;
                        $subscription = get_post_meta($item->ID, "subscription", true);
                        $customer = get_post_meta($item->ID, "customer", true);
                        $first_name = get_user_meta($customer, "first_name", true);
                        $address = get_post_meta($item->ID, "address", true);
                        $device = wp_get_post_terms($item->ID, "device");
                        $brand = wp_get_post_terms($item->ID, "brand");
                        $technician = get_post_meta($item->ID, "technician", true);
                        $first_name_technician = get_user_meta($technician, "first_name", true);
                        $status = get_post_meta($item->ID, "status", true);

                        $status_title = "نامشخص";
                        $active = ($status == 0 || $status == "") ? "active" : '';
                        if ($status == 1) {
                            $status_title = "در حال تعمیر";
                        } else if ($status == "sent") {
                            $status_title = "ارسال بشود";
                        } else if ($status == "receive") {
                            $status_title = "دریافت بشود";
                        } else if ($status == "qc") {
                            $status_title = "واحد کنترل";
                        } else if ($status == 4) {
                            $status_title = "کنسل شده";
                        } else if ($status == 5) {
                            $status_title = "انجام کار";
                        }
                        ?>
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
                            <li><?= $status_title ?></li>
                            <li><?= htmlspecialchars($subscription) ?></li>
                            <li><?= htmlspecialchars($first_name) ?></li>
                            <li><?= htmlspecialchars($address) ?></li>
                            <li><?= htmlspecialchars($device[0]->name) ?></li>
                            <li><?= htmlspecialchars($brand[0]->name) ?></li>
                            <li><?= htmlspecialchars($first_name_technician) ?></li>
                            <li><?= get_the_date("Y/m/d H:i", $item->ID) ?></li>

                            <li>
                                <div class="order_internal_style">
                                    <?php if (CheckAccess("edit_order_internal")) { ?>
                                        <a class="edit" href="<?= get_the_permalink(14235) . '?uid=' . $item->ID ?>">ویرایش</a>
                                    <?php } ?>
                                    <a class="view" href="<?= get_the_permalink(14285) . '?uid=' . $item->ID ?>">مشاهده</a>
                                    <a class="print" target="_blank" href="<?= get_the_permalink(14267) . '?oid=' . $item->ID ?>">پرینت رسید</a>
                                </div>
                            </li>
                        </ul>

                    <?php } ?>
                </div>
            </div>


            <ul class="paginition">

                <?php

                //                    for($i = 1 ; $i<= $pagination ; $i++){
                if(!isset($_GET['page_no'])){
                    $i = 1;
                }else{
                    $i = $_GET['page_no'];
                }

                if(!isset($_GET['page_no']) && $i == 1){
                    $active = 'active';

                }
                else if(isset($_GET['page_no']) && $i == $_GET['page_no']){
                    $active = 'active';
                }
                else{
                    $active = '';
                }

                $actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                    $link = $actual_link.'&page_no='.$i;
                }else{
                    $link = '?page_no='.$i;
                }

                if($i == 1 || !isset($_GET['page_no'])){}else{
                    $b = 1;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no=1';
                    }else{
                        $link = '?page_no=1';
                    }
                    echo '
                                
                                    <li id="one">
                                        
                                        <a  href="'.$link.'">1</a>
                                    
                                    </li>
                                
                                ';
                }
                if(($i - 4) > 1){
                    echo '<li class="sep_min">...</li>';
                }
                if(($i - 3) > 1){
                    $b = $i - 3;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no='.$b;
                    }else{
                        $link = '?page_no='.$b;
                    }
                    echo '
                                
                                    <li>
                                        
                                        <a href="'.$link.'">'.$b.'</a>
                                    
                                    </li>
                                
                                ';
                }
                if(($i - 2) > 1){
                    $b = $i - 2;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no='.$b;
                    }else{
                        $link = '?page_no='.$b;
                    }
                    echo '
                                
                                    <li>
                                        
                                        <a href="'.$link.'">'.$b.'</a>
                                    
                                    </li>
                                
                                ';
                }
                if(($i - 1) > 1){
                    $b = $i - 1;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no='.$b;
                    }else{
                        $link = '?page_no='.$b;
                    }
                    echo '
                                
                                    <li>
                                        
                                        <a href="'.$link.'">'.$b.'</a>
                                    
                                    </li>
                                
                                ';
                }
                echo '
                                
                                    <li>
                                        
                                        <a class="'.$active.'" href="'.$link.'">'.$i.'</a>
                                    
                                    </li>
                                
                                ';



                if(($i + 1) <$pagination){
                    $b = $i + 1;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no='.$b;
                    }else{
                        $link = '?page_no='.$b;
                    }
                    echo '
                                
                                    <li>
                                        
                                        <a href="'.$link.'">'.$b.'</a>
                                    
                                    </li>
                                
                                ';
                }
                if(($i + 2) <$pagination){
                    $b = $i + 2;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no='.$b;
                    }else{
                        $link = '?page_no='.$b;
                    }
                    echo '
                                
                                    <li>
                                        
                                        <a href="'.$link.'">'.$b.'</a>
                                    
                                    </li>
                                
                                ';
                }
                if(($i + 3) < $pagination){
                    $b = $i + 3;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no='.$b;
                    }else{
                        $link = '?page_no='.$b;
                    }

                    echo '
                                
                                    <li>
                                        
                                        <a  href="'.$link.'">'.$b.'</a>
                                    
                                    </li>
                                
                                ';
                }
                if(($i + 4) < $pagination ){
                    echo '<li  class="sep_min">...</li>';
                }
                if($pagination) {
                    $b = $pagination;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no='.$pagination;
                    }else{
                        $link = '?page_no='.$pagination;
                    }
                    echo '
                                
                                    <li>
                                        
                                        <a class="" href="' . $link . '">' . $pagination . '</a>
                                    
                                    </li>
                                
                                ';
                }

                ?>

            </ul>


        </div>

        </div>




    </section>

    <?php
    get_footer();



    if(isset($_GET['searchTrue'])){
        $array_filter["posts_per_page"] = -1;
        $count_filter = get_posts($array_filter);

        $all_count = 0;
        $unknown_count = 0;
        $repairing_count = 0;
        $sending_count = 0;
        $reciving_count = 0;
        $qc_count = 0;
        $cancel_count = 0;
        $down_count = 0;
        $payment_pend_count = 0;
        foreach($count_filter as $item){
            $all_count++;
            $technician = get_post_meta($item->ID,"technician",true);
            $status_internal_order = get_post_meta($item->ID,"status_internal_order",true);
            $status = get_post_meta($item->ID,"status",true);
//        $ = get_post_meta($item->ID,"",true);


            if($status_internal_order == 4 || $status_internal_order == 5 || $technician == ""){
                $unknown_count++;
            }

            if($technician == ""){}else{
                if($status == "sent" || $status == "receive" || $status == "0"){
                    $repairing_count++;
                }
            }
            if($status_internal_order == "receive"){
                $reciving_count++;
            }
            if($status_internal_order == "sent"){
                $sending_count++;
            }
            if($status_internal_order == "qc"){
                $qc_count++;
            }
            if($status_internal_order == "payment_pend"){
                $payment_pend_count++;
            }
            if($status_internal_order == 1000 && $status == 4){
                $cancel_count++;
            }
            if($status_internal_order == 1000 && $status == 5){
                $down_count++;
            }

        }

        ?>
        <script>
            $(document).ready(function (){
                $(".filter li i.all_count").html(<?=$all_count?>);
                $(".filter li i.new_count").html(<?=$unknown_count?>);
                $(".filter li i.sync_count").html(<?=$repairing_count?>);
                $(".filter li i.send_count").html(<?=$sending_count?>);
                $(".filter li i.recive_count").html(<?=$reciving_count?>);
                $(".filter li i.qc_count").html(<?=$qc_count?>);
                $(".filter li i.cancel_count").html(<?=$cancel_count?>);
                $(".filter li i.down_count").html(<?=$down_count?>);
                $(".filter li i.payment_pend_count").html(<?=$payment_pend_count?>);
            });
        </script>
    <?php }
    else{?>
        <script>
            $(window).on("load",function (){
                LoadCountOrderInternal();
            });
        </script>
        <?php
    }



}


else{
//    old version



if(isset($_GET['page_no'])){
    $paged = $_GET['page_no'];
}else{
    $paged = 1;
}

if(!isset($_GET['searchTrue'])){

}else{
}


$all_count = 0;
$new_count= 0;
$sync_count= 0;
$open_count= 0;
$pend_count= 0;
$cancel_count= 0;
$down_count= 0;
$archive= 0;
$unarchive= 0;
$back_count = 0;
$payment_pend_count = 0;

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
    if($_GET['status_send_recive'] == ""){}else{

        $status_send_recive = array(
            "key" => "status_send_recive",
            "value" => $_GET['status_send_recive'],
            "compare" => "="
        );
        array_push($meta_query,$status_send_recive);
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
//    if($_GET['status'] == ""){}else{
//        $status = array(
//            "key" => "status",
//            "value" => $_GET['status'],
//            "compare" => "LIKE"
//        );
//        array_push($meta_query,$status);
//    }

    if($_GET['status'] == "0" ){
        $status = array(
            "relation" => "OR",
            array(
                "key" => "technician",
                "value" => "",
                "compare" => "=",
            ),
            array(
                "key" => "status_internal_order",
                "value" => array("4","5"),
                "compare" => "IN",
            ),
        );
        array_push($meta_query,$status);

    }
    else if( $_GET['status'] == "all" ){
        $status = array();
        array_push($meta_query,$status);
    }
    else if( $_GET['status'] == "1" ){
        $status = array(
            "relation" => "AND",
            array(
                "key" => "technician",
                "value" => "",
                "compare" => "!=",
            ),
            array(
                "key" => "status",
                "value" => array("0","sent","receive"),
                "compare" => "IN"
            )
        );
        array_push($meta_query,$status);
    }
    else if( $_GET['status'] == "sent" ){
        $status = array(
            array(
                "key" => "status_internal_order",
                "value" => "sent",
                "compare" => "=",
            )
        );
        array_push($meta_query,$status);
    }
    else if( $_GET['status'] == "payment_pend" ){
        $status = array(
            array(
                "key" => "status_internal_order",
                "value" => "payment_pend",
                "compare" => "=",
            )
        );
        array_push($meta_query,$status);
    }
    else if( $_GET['status'] == "receive" ){
        $status = array(
            array(
                "key" => "status_internal_order",
                "value" => "receive",
                "compare" => "=",
            )
        );
        array_push($meta_query,$status);
    }
    else if( $_GET['status'] == "qc" ){
        $status = array(
            "relation" => "OR",
            array(
                "key" => "status_internal_order",
                "value" => "qc",
                "compare" => "=",
            ),
            array(
                "key" => "status",
                "value" => "qc",
                "compare" => "=",
            ),
        );
        array_push($meta_query,$status);
    }

    else if( $_GET['status_filter'] == "4" ){
        $status = array(
            "relation" => "AND",
            array(
                "key" => "status_internal_order",
                "value" => "1000",
                "compare" => "=",
            ),
            array(
                "key" => "status",
                "value" => "4",
                "compare" => "=",
            )
        );
        array_push($meta_query,$status);
    }

    else if( $_GET['status_filter'] == "5" ){
        $status = array(
            "relation" => "AND",
            array(
                "key" => "status_internal_order",
                "value" => "1000",
                "compare" => "=",
            ),
            array(
                "key" => "status",
                "value" => "5",
                "compare" => "=",
            )

        );
        array_push($meta_query,$status);
    }

    else{
        $meta_query_order = array(
            array(
                "key" => "status",
                "value" => $_GET['status_filter'],
                "compare" => "LIKE",
            )
        );

    }



    if($_GET['return_order'] == ""){}else{
        $return = array(
            "key" => "return_type",
            "compare" => "EXIST",
        );
        array_push($meta_query,$return);
    }
    if(isset($_GET['archive']) && $_GET['archive'] == "1"){

        $archive = array(
            "key" => "happyCall",
            "value" => "1",
            "compare" => "="
        );
        array_push($meta_query,$archive);
    }
    if(isset($_GET['archive']) && $_GET['archive'] == "0"){

        $unarchive = array(
            "key" => "happyCall",
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
    if($_GET["order_id_search"] == ""){}else{
        array_push($id_search,$_GET["order_id_search"]);
    }
    $filter =  array(
        "post__in" => $id_search,
        "post_type" => "internal_orders",
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
            "relation" => "OR",
            array(
                "key" => "technician",
                "value" => "",
                "compare" => "=",
            ),
            array(
                "key" => "status_internal_order",
                "value" => array("","4","5"),
                "compare" => "IN",
            ),


        );
    }
    else if( $_GET['status_filter'] == "all" ){
        $meta_query_order = array();
    }
    else if( $_GET['status_filter'] == "1" ){
        $meta_query_order = array(
            "relation" => "AND",
            array(
                "key" => "technician",
                "value" => "",
                "compare" => "!=",
            ),
            array(
                "key" => "status",
                "value" => array("0","sent","receive"),
                "compare" => "IN"
            )
        );
    }
    else if( $_GET['status_filter'] == "sent" ){
        $meta_query_order = array(
            array(
                "key" => "status_internal_order",
                "value" => "sent",
                "compare" => "=",
            )
        );
    }
    else if( $_GET['status_filter'] == "payment_pend" ){
        $meta_query_order = array(
            array(
                "key" => "status_internal_order",
                "value" => "payment_pend",
                "compare" => "=",
            )
        );
    }
    else if( $_GET['status_filter'] == "receive" ){
        $meta_query_order = array(
            array(
                "key" => "status_internal_order",
                "value" => "receive",
                "compare" => "=",
            )
        );
    }

    else if( $_GET['status_filter'] == "qc" ){
        $meta_query_order = array(
            array(
                "key" => "status_internal_order",
                "value" => "qc",
                "compare" => "=",
            )
        );
    }

    else if( $_GET['status_filter'] == "4" ){
        $meta_query_order = array(
            "relation" => "AND",
            array(
                "key" => "status_internal_order",
                "value" => "1000",
                "compare" => "=",
            ),
            array(
                "key" => "status",
                "value" => "4",
                "compare" => "=",
            )
        );
    }

    else if( $_GET['status_filter'] == "5" ){
        $meta_query_order = array(
            "relation" => "AND",
            array(
                "key" => "status_internal_order",
                "value" => "1000",
                "compare" => "=",
            ),
            array(
                "key" => "status",
                "value" => "5",
                "compare" => "=",
            )
        );
    }

    else{
        $meta_query_order = array(
            array(
                "key" => "status",
                "value" => $_GET['status_filter'],
                "compare" => "LIKE",
            )
        );

    }
    $operators = get_posts( array(
        "post_type" => "internal_orders",
        "posts_per_page" => -1,
        "post_status" => "any",
        "meta_query" => $meta_query_order,
    ) );

    $count = count($operators);
    $pagination =  ceil($count / 10);
    $array_filter = array(
        "post_type" => "internal_orders",

        "post_status" => "any",
        "posts_per_page" => 10,
        "paged" => $paged,
        "meta_query" => $meta_query_order,
    ) ;

}
else if(isset($_GET['searchTrue']) && isset($_GET['status_filter'])){
    echo '
        <style>.itemOrderBox {
        display: block;
}</style>
    ';
    $meta_query = array();
    $tax_query = array();
    $id_search = array();

    if($_GET['subscription'] == ""){}else{

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
    if($_GET['status_filter'] == ""){}else{
        if( $_GET['status_filter'] == "no_tech" ){
            $meta_query_order = array(
                "key" => "technician",
                "value" => '',
                "compare" => "=",
            );

        }
        if( $_GET['status_filter'] == "all" ){
            $meta_query_order = array();
        }
        else{
            $meta_query_order = array(
                "key" => "status",
                "value" => $_GET['status_filter'],
                "compare" => "LIKE",
            );
        }

        array_push($meta_query,$meta_query_order);

    }
    if($_GET['objection'] == ""){}else{

        $objection = array(
            "key" => "objection",
            "value" => $_GET['objection'],
            "compare" => "LIKE"
        );
        array_push($meta_query,$objection);
    }
    if($_GET['order_type'] == ""){}else{

        $order_type = array(
            "key" => "order_type",
            "value" => $_GET['order_type'],
            "compare" => "LIKE"
        );
        array_push($meta_query,$order_type);
    }
    if(isset($_GET['timestamp'])){
        $d = date("d",($_GET['timestamp'] / 1000));
        $m = date("m",($_GET['timestamp'] / 1000));
        $y = date("Y",($_GET['timestamp'] / 1000));
        $date = array(
            'year'  => $y,
            'month' => $m,
            'day'   => $d,
        );
    }


    $filter =  array(
        "post__in" => $id_search,
        "post_type" => "internal_orders",
        "posts_per_page" => -1,
        "post_status" => "any",
        "meta_query" => array(
            "relation" => "AND",
            $meta_query
        ),
    );



    if($_GET['date_year'] == ""){}else{
        $filter["date_query"] = $date;
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


$operator_list = get_posts($array_filter);

if(isset($_GET['ConvertToEcxel'])){
    Export2Excel($array_filter);
}
get_header();
?>
<div class="modal fade" id="SearchModalPopUp" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form <?php if(isset($_GET['searchTrue'])){echo'class="active"';}?> action="#" method="get" id="SearchList">

                <h2 class="search_title"><span>جستجو بر اساس مشتری و تکنسین</span></h2>

                <div class="box_of_search">
                    <input type="hidden" name="searchTrue" value="1">
                    <div class="form-group fived">
                        <label>شماره سفارش</label>
                        <input type="text" name="order_id_search" class="form-control" <?php if(isset($_GET['searchTrue'])){echo'value="'.$_GET["order_id_search"].'"';}?> >
                    </div>
                    <div class="form-group fived">
                        <label>کد اشتراک</label>
                        <input type="text" name="subscription" class="form-control" <?php if(isset($_GET['searchTrue'])){echo'value="'.$_GET["subscription"].'"';}?> >
                    </div>
                    <div class="form-group fived">
                        <label>شماره موبایل</label>
                        <input type="text" name="order_id" class="form-control" <?php if(isset($_GET['searchTrue'])){echo'value="'.$_GET["order_id"].'"';}?>>
                    </div>
                    <div class="form-group fived">
                        <label>شماره ثابت</label>
                        <input type="text" name="phone" class="form-control" <?php if(isset($_GET['searchTrue'])){echo'value="'.$_GET["phone"].'"';}?>>
                    </div>
                    <div class="form-group fived">
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
                                            "value" => "internal",
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
                                }elseif($item->term_id == 15){
                                    $selected = "selected";
                                }else{
                                    $selected = '';
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
                                <span>نامشخص</span>
                            </label>
                        </li>
                        <li>
                            <label class="<?php if(isset($_GET['status'])){if($_GET['status'] == "1"){echo 'active';}}?>">
                                <input value="1" class="<?php if(isset($_GET['status'])){if($_GET['status'] == "1"){echo 'checked';}}?>" type="radio" name="status" >
                                <span>در حال تعمیرات</span>
                            </label>
                        </li>
                        <li>
                            <label class="<?php if(isset($_GET['status'])){if($_GET['status'] == "payment_pend"){echo 'active';}}?>">
                                <input value="payment_pend" class="<?php if(isset($_GET['status'])){if($_GET['status'] == "payment_pend"){echo 'checked';}}?>" type="radio" name="status" >
                                <span>منتظر پرداخت</span>
                            </label>
                        </li>
                        <li>
                            <label class="<?php if(isset($_GET['status'])){if($_GET['status'] == "sent"){echo 'active';}}?>">
                                <input value="sent" class="<?php if(isset($_GET['status'])){if($_GET['status'] == "sent"){echo 'checked';}}?>" type="radio" name="status" >
                                <span>ارسال بشود</span>
                            </label>
                        </li>
                        <li>
                            <label class="<?php if(isset($_GET['status'])){if($_GET['status'] == "receive"){echo 'active';}}?>">
                                <input value="receive" class="<?php if(isset($_GET['status'])){if($_GET['status'] == "receive"){echo 'checked';}}?>" type="radio" name="status" >
                                <span>دریافت بشود</span>
                            </label>
                        </li>
                        <li>
                            <label class="<?php if(isset($_GET['status'])){if($_GET['status'] == "qc"){echo 'active';}}?>">
                                <input value="qc" class="<?php if(isset($_GET['status'])){if($_GET['status'] == "qc"){echo 'checked';}}?>" type="radio" name="status" >
                                <span>واحد کنترل</span>
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

                <h2 class="search_title"><span>جستجو بر تگ</span></h2>
                <div class="box_of_search">
                    <ul class="Status_search" id="status_filter">


                        <li>
                            <label class="<?php if(isset($_GET['status_send_recive'])){if($_GET['status_send_recive'] == "sent"){echo 'active';}}?>">
                                <input value="sent" class="<?php if(isset($_GET['status_send_recive'])){if($_GET['status_send_recive'] == "sent"){echo 'checked';}}?>" type="radio" name="status_send_recive" >
                                <span>ارسال شد</span>
                            </label>
                        </li>

                        <li>
                            <label class="<?php if(isset($_GET['status_send_recive'])){if($_GET['status_send_recive'] == "receive"){echo 'active';}}?>">
                                <input value="receive" class="<?php if(isset($_GET['status_send_recive'])){if($_GET['status_send_recive'] == "receive"){echo 'checked';}}?>" type="radio" name="status_send_recive" >
                                <span>دریافت شد</span>
                            </label>
                        </li>
                        <li>
                            <label class="<?php if(isset($_GET['status'])){if($_GET['status_send_recive'] == "4"){echo 'active';}}?>">
                                <input value="4" class="<?php if(isset($_GET['status_send_recive'])){if($_GET['status_send_recive'] == "4"){echo 'checked';}}?>" type="radio" name="status_send_recive" >
                                <span>کنسل شده</span>
                            </label>
                        </li>
                        <li>
                            <label class="<?php if(isset($_GET['status_send_recive'])){if($_GET['status_send_recive'] == "5"){echo 'active';}}?>">
                                <input value="5" class="<?php if(isset($_GET['status_send_recive'])){if($_GET['status_send_recive'] == "5"){echo 'checked';}}?>" type="radio" name="status_send_recive" >
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
                                <span>پرداخت شده </span>
                            </label>
                        </li>
                        <li>
                            <label class="<?php if(isset($_GET['archive'])){if($_GET['archive'] == "0"){echo 'active';}}?>">
                                <input value="0" class="<?php if(isset($_GET['archive'])){if($_GET['archive'] == "0"){echo 'checked';}}?>" type="radio" name="archive" >
                                <span>منتظر پرداخت</span>
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
        if(isset($_GET['searchTrue'])){
            $sep = '?'.$_SERVER['QUERY_STRING'].'&';
        }else{
            $sep = '?';
        }
        ?>

        <div class="content">
            <h1 class="title">لیست سفارشات</h1>

            <ul class="filter <?=$disabled_edit?>">
                <li <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "all"){echo 'class="active"';}?>>
                    <a href="<?=$sep?>status_filter=all">همه <i class="all_count"><?=$all_count?></i></a>
                </li>
                <li <?php if(!isset($_GET['status_filter']) || $_GET['status_filter'] == "0"){echo 'class="active"';}?>>
                    <a href="<?=$sep?>status_filter=0">نامشخص<i class="new_count"><?=$new_count?></i></a>
                </li>
                <li  <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "payment_pend"){echo 'class="active"';}?>>
                    <a href="<?=$sep?>status_filter=payment_pend">منتظرپرداخت<i class="payment_pend_count"><?=$payment_pend_count?></i></a>
                </li>
                <li  <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "1"){echo 'class="active"';}?>>
                    <a href="<?=$sep?>status_filter=1">در حال تعمیرات<i class="sync_count"><?=$sync_count?></i></a>
                </li>

                <?php if(CheckAccess("send_order_internal")){ ?>
                    <li <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "sent"){echo 'class="active"';}?>>
                        <a href="<?=$sep?>status_filter=sent">ارسال بشود<i class="send_count"><?=$sync_count?></i></a>
                    </li>
                <?php } ?>

                <?php if(CheckAccess("receive_order_internal")){ ?>
                    <li <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "receive"){echo 'class="active"';}?>>
                        <a href="<?=$sep?>status_filter=receive">دریافت بشود<i class="receive_count"><?=$sync_count?></i></a>
                    </li>
                <?php } ?>

                <?php if(CheckAccess("qc_order_internal")){ ?>
                    <li <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "qc"){echo 'class="active"';}?>>
                        <a href="<?=$sep?>status_filter=qc">واحد کنترل<i class="qc_count"><?=$sync_count?></i></a>
                    </li>
                <?php } ?>

                <?php if(CheckAccess("cancel_order_internal")){ ?>
                    <li <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "4"){echo 'class="active"';}?>>
                        <a href="<?=$sep?>status_filter=4">کنسل شده <i class="cancel_count"><?=$sync_count?></i></a>
                    </li>
                <?php } ?>

                <?php if(CheckAccess("done_order_internal")){ ?>
                    <li <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "5"){echo 'class="active"';}?>>
                        <a href="<?=$sep?>status_filter=5">انجام شده <i class="down_count"><?=$sync_count?></i></a>
                    </li>
                <?php } ?>
            </ul>

            <div class="Box_filter_order">
                <div id="SearchBoxBTN">جستجو پیشرفته</div>
                <?php if(CheckAccess("order_export")){ ?>
                    <a href="<?=$sep?>ConvertToEcxel" id="ConvertToEcxel">خروجی اکسل</a>
                <?php } ?>
            </div>

            <?php
            $i = 0;
            foreach ($operator_list as $item) {
                $i++;
                $subscription = get_post_meta($item->ID, "subscription", true);
                $introduction = get_post_meta($item->ID, "introduction", true);
                $customer = get_post_meta($item->ID, "customer", true);
                $first_name = get_user_meta($customer, "first_name", true);
                $address = get_post_meta($item->ID, "address", true);
                $send_technician = get_post_meta($item->ID, "send_technician", true);
                $device = wp_get_post_terms($item->ID, "device");
                $brand = wp_get_post_terms($item->ID, "brand");
                $technician = get_post_meta($item->ID, "technician", true);
                $shipping = get_post_meta($item->ID, "shipping", true);
                $status_internal_order = get_post_meta($item->ID, "status_internal_order", true);
                $status_send_recive = get_post_meta($item->ID, "status_send_recive", true);
                $first_name_technician = get_user_meta($technician, "first_name", true);
                $status = get_post_meta($item->ID, "status", true);
                $finish_order_sh = get_post_meta($item->ID, "finish_order_sh", true);
                $negative_invoice = get_post_meta($item->ID, "negative_invoice", true);
                $SaveToShayganStatus = get_post_meta($item->ID, "happyCall", true);
                $return_type = get_post_meta($item->ID, "return_type", true);
                $order_type = get_post_meta($item->ID, "order_type", true);
                $city_terms = wp_get_post_terms($item->ID, "city");

                // بررسی وجود city
                $city = !empty($city_terms) ? $city_terms[0]->name : "نامشخص";

                // بررسی SaveToShayganStatus
                if ($SaveToShayganStatus == 1) {
                    $save_soroshan = '<span class="sync">پرداخت شده</span>';
                } else {
                    $save_soroshan = '<span class="unsync orange">منتظر پرداخت</span>';
                }

                // بررسی نوع سفارش
                if ($order_type == "repair") {
                    $type_order = '<span class="to repair">تعمیر</span>';
                } elseif ($order_type == "buy") {
                    $type_order = '<span class="to buy">خرید</span>';
                } else {
                    $type_order = '<span class="to unknown">نامشخص</span>';
                }

                // وضعیت سفارش
                $status_title = "نامشخص";
                $active = '';
                $status_show = '';
                if ($status == 0 || $status == "") {
                    $active = "active";
                } elseif ($status == 1) {
                    $status_title = "در حال تعمیر";
                } elseif ($status == "sent") {
                    $status_title = "ارسال بشود";
                } elseif ($status == "receive") {
                    $status_title = "دریافت بشود";
                } elseif ($status == "qc") {
                    $status_title = "واحد کنترل";
                } elseif ($status == 4) {
                    $status_title = "کنسل شده";
                    $status_show = '<span class="unsync">کنسل شد</span>';
                } elseif ($status == 5) {
                    $status_title = "انجام کار";
                    $status_show = '<span class="sync">انجام شد</span>';
                }

                // مدیریت ارسال و دریافت
                $sent_show = '';
                if ($status_internal_order == "sent") {
                    $sent_show = '<span class="unsync">ارسال بشود</span>';
                }

                $receive_show = '';
                if ($status_internal_order == "receive") {
                    $receive_show = '<span class="unsync">دریافت شد</span>';
                }

                if ($status_send_recive == "sent") {
                    $sent_show = '<span class="sync">ارسال شد</span>';
                }

                if ($status_send_recive == "receive") {
                    $receive_show = '<span class="sync">دریافت شد</span>';
                }

                // مدیریت تکنسین
                if (empty($technician)) {
                    $first_name_technician = "بدون تکنسین";
                }

                // مدیریت فاکتور منفی
                if ($negative_invoice == 1) {
                    $n_inv = '<span class="n_invoice">فاکتور منفی (نیازمند تایید اپراتور)</span>';
                } else {
                    $n_inv = "";
                }

                // سفارش برگشتی
                $return_type_text = '';
                if ($return_type == 1) {
                    $return_type_text = ' 
                <li class="backOrder">
                    <b><span class="back">سفارش برگشتی</span></b>
                </li>';
                }

                // پایان کار
                if (!empty($finish_order_sh)) {
                    $finish_or = '<li class="sub">
                <span>پایان کار</span>
                <b>'.$finish_order_sh.'</b>
            </li>';
                } else {
                    $finish_or = '';
                }

                // نحوه ارسال
                if (empty($shipping)) {
                    $shipping_method = '';
                } else {
                    $shipping_method = '<li class="oid">
                <span>نحوه ارسال </span>
                <b>'.$shipping.'</b>
            </li>';
                }

                // امتیاز هپی کال مشتری
                $hc_customer = get_post_meta($item->ID, "hc_customer", true);
                if (!empty($hc_customer)) {
                    $hc_customer_star = generate_happy_call_star($hc_customer, 'مشتری');
                } else {
                    $hc_customer_star = '';
                }

                // امتیاز هپی کال تکنسین
                $hc_tech = get_post_meta($item->ID, "hc_tech", true);
                if (!empty($hc_tech)) {
                    $hc_tech_star = generate_happy_call_star($hc_tech, 'تکنسین');
                } else {
                    $hc_tech_star = '';
                }

                // HTML نهایی
                echo '
            <div class="itemOrderBox s_'.$status.' '.$active.'">
                '.$n_inv.'
                <ul class="header_order">
                    <li class="sub">
                        '.$type_order.'
                        <span>کد اشتراک</span>
                        <b>'.$subscription.'</b>
                    </li>
                    <li class="sub">
                        <span>شماره سفارش</span>
                        <b>'.$item->ID.'</b>
                    </li>
                    <li class="oid">
                        <span>نام مشتری</span>
                        <b>'.$first_name.'  '.SelectTypeUserName($customer).'</b>
                    </li>  
                    <li class="oid">
                        <span>نحوه آشنایی </span>
                        <b>'.$introduction.'</b>
                    </li>
                    '.$shipping_method.'
                    <li class="device">
                        <span>وضعیت</span>
                        <b>'.$status_title.'</b>
                    </li>
                    <li class="device">
                        <span>تاریخ سفارش</span>
                        <b>'.get_the_date("Y/m/d H:i", $item->ID).'</b>
                    </li>
                    <li class="device">
                        <span>ارسال تکنسین</span>
                        <b>'.$send_technician.'</b>
                    </li>
                    '.$finish_or.'
                    <li class="device">
                        <span>دستگاه</span>
                        <b>'.(isset($device[0]) ? $device[0]->name : "نامشخص").'</b>
                    </li>
                    <li class="device">
                        <span>برند</span>
                        <b>'.(isset($brand[0]) ? $brand[0]->name : "نامشخص").'</b>
                    </li> 
                    <li class="device">
                        <span>تکنسین</span>
                        <b>'.$first_name_technician.'</b>
                    </li>
                    <li class="address">
                        <span>آدرس</span>
                        <b>'.$address.'</b>
                    </li>   
                    <li class="address">
                        <span>منطقه</span>
                        <b>'.$city.'</b>
                    </li>
                    <li class="soroshan">
                        <span>'.$SaveToShayganStatus.'</span>
                        <b>'.$save_soroshan.'</b>
                    </li>
                    <li>
                        <span>'.$status_show.'</span>
                    </li>
                    <li>
                        <span>'.$sent_show.'</span>
                        <span>'.$receive_show.'</span>
                    </li>
                    '.$return_type_text.'
                    <li>'.$hc_customer_star.'</li>
                    <li>'.$hc_tech_star.'</li>
                </ul>
            </div>';

                if (CheckAccess("edit_order_internal")) {
                    echo '
            <div class="order_internal_style">
                <a class="edit" href="'.get_the_permalink(14235).'?uid='.$item->ID.'">ویرایش</a>';
                }

                if ($status_internal_order == "qc") {
                    if (CheckAccess("qc_order_internal")) {
                        $link_qc = '<a class="final" target="_blank" href="'.get_the_permalink(14285).'?uid='.$item->ID.'">نهایی سازی سفارش</a>';
                    }
                } else {
                    $link_qc = '';
                }

                echo '
            <a class="view" href="'.get_the_permalink(14285).'?uid='.$item->ID.'">مشاهده</a>
            <a class="print" target="_blank" href="'.get_the_permalink(14267).'?oid='.$item->ID.'">پرینت رسید</a>
            '.$link_qc.'
            </div>
        ';
            }
            ?>
        </div>



        <ul class="paginition">

                <?php

                //                    for($i = 1 ; $i<= $pagination ; $i++){
                if(!isset($_GET['page_no'])){
                    $i = 1;
                }else{
                    $i = $_GET['page_no'];
                }

                if(!isset($_GET['page_no']) && $i == 1){
                    $active = 'active';

                }
                else if(isset($_GET['page_no']) && $i == $_GET['page_no']){
                    $active = 'active';
                }
                else{
                    $active = '';
                }

                $actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                    $link = $actual_link.'&page_no='.$i;
                }else{
                    $link = '?page_no='.$i;
                }

                if($i == 1 || !isset($_GET['page_no'])){}else{
                    $b = 1;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no=1';
                    }else{
                        $link = '?page_no=1';
                    }
                    echo '
                                
                                    <li id="one">
                                        
                                        <a  href="'.$link.'">1</a>
                                    
                                    </li>
                                
                                ';
                }
                if(($i - 4) > 1){
                    echo '<li class="sep_min">...</li>';
                }
                if(($i - 3) > 1){
                    $b = $i - 3;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no='.$b;
                    }else{
                        $link = '?page_no='.$b;
                    }
                    echo '
                                
                                    <li>
                                        
                                        <a href="'.$link.'">'.$b.'</a>
                                    
                                    </li>
                                
                                ';
                }
                if(($i - 2) > 1){
                    $b = $i - 2;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no='.$b;
                    }else{
                        $link = '?page_no='.$b;
                    }
                    echo '
                                
                                    <li>
                                        
                                        <a href="'.$link.'">'.$b.'</a>
                                    
                                    </li>
                                
                                ';
                }
                if(($i - 1) > 1){
                    $b = $i - 1;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no='.$b;
                    }else{
                        $link = '?page_no='.$b;
                    }
                    echo '
                                
                                    <li>
                                        
                                        <a href="'.$link.'">'.$b.'</a>
                                    
                                    </li>
                                
                                ';
                }
                echo '
                                
                                    <li>
                                        
                                        <a class="'.$active.'" href="'.$link.'">'.$i.'</a>
                                    
                                    </li>
                                
                                ';



                if(($i + 1) <$pagination){
                    $b = $i + 1;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no='.$b;
                    }else{
                        $link = '?page_no='.$b;
                    }
                    echo '
                                
                                    <li>
                                        
                                        <a href="'.$link.'">'.$b.'</a>
                                    
                                    </li>
                                
                                ';
                }
                if(($i + 2) <$pagination){
                    $b = $i + 2;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no='.$b;
                    }else{
                        $link = '?page_no='.$b;
                    }
                    echo '
                                
                                    <li>
                                        
                                        <a href="'.$link.'">'.$b.'</a>
                                    
                                    </li>
                                
                                ';
                }
                if(($i + 3) < $pagination){
                    $b = $i + 3;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no='.$b;
                    }else{
                        $link = '?page_no='.$b;
                    }

                    echo '
                                
                                    <li>
                                        
                                        <a  href="'.$link.'">'.$b.'</a>
                                    
                                    </li>
                                
                                ';
                }
                if(($i + 4) < $pagination ){
                    echo '<li  class="sep_min">...</li>';
                }
                if($pagination) {
                    $b = $pagination;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no='.$pagination;
                    }else{
                        $link = '?page_no='.$pagination;
                    }
                    echo '
                                
                                    <li>
                                        
                                        <a class="" href="' . $link . '">' . $pagination . '</a>
                                    
                                    </li>
                                
                                ';
                }

                ?>

            </ul>


        </div>

    </div>
</section>

<?php
get_footer();



if(isset($_GET['searchTrue'])){
    $array_filter["posts_per_page"] = -1;
    $count_filter = get_posts($array_filter);

    $all_count = 0;
    $unknown_count = 0;
    $repairing_count = 0;
    $sending_count = 0;
    $reciving_count = 0;
    $qc_count = 0;
    $cancel_count = 0;
    $down_count = 0;
    $payment_pend_count = 0;
    foreach($count_filter as $item){
        $all_count++;
        $technician = get_post_meta($item->ID,"technician",true);
        $status_internal_order = get_post_meta($item->ID,"status_internal_order",true);
        $status = get_post_meta($item->ID,"status",true);
//        $ = get_post_meta($item->ID,"",true);


        if($status_internal_order == 4 || $status_internal_order == 5 || $technician == ""){
            $unknown_count++;
        }

        if($technician == ""){}else{
            if($status == "sent" || $status == "receive" || $status == "0"){
                $repairing_count++;
            }
        }
        if($status_internal_order == "receive"){
            $reciving_count++;
        }
        if($status_internal_order == "sent"){
            $sending_count++;
        }
        if($status_internal_order == "qc"){
            $qc_count++;
        }
        if($status_internal_order == "payment_pend"){
            $payment_pend_count++;
        }
        if($status_internal_order == 1000 && $status == 4){
            $cancel_count++;
        }
        if($status_internal_order == 1000 && $status == 5){
            $down_count++;
        }

    }

    ?>
    <script>
        $(document).ready(function (){
            $(".filter li i.all_count").html(<?=$all_count?>);
            $(".filter li i.new_count").html(<?=$unknown_count?>);
            $(".filter li i.sync_count").html(<?=$repairing_count?>);
            $(".filter li i.send_count").html(<?=$sending_count?>);
            $(".filter li i.recive_count").html(<?=$reciving_count?>);
            $(".filter li i.qc_count").html(<?=$qc_count?>);
            $(".filter li i.cancel_count").html(<?=$cancel_count?>);
            $(".filter li i.down_count").html(<?=$down_count?>);
            $(".filter li i.payment_pend_count").html(<?=$payment_pend_count?>);
        });
    </script>
<?php }
else{?>
    <script>
        $(window).on("load",function (){
            LoadCountOrderInternal();
        });
    </script>
    <?php
}
}
?>


