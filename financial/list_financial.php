<?php
IFLoggedAndVerify("list_financial");
// Template Name: financial List
get_header();
$access = get_user_meta(get_current_user_id(),"access",true);


if(isset($_GET['searchTrue'])) {
    echo '
        <style>.itemOrderBox {
        display: block;
}</style>
    ';

    $post_per_page = -1;
    $pagintion = 1;
}else{
    $post_per_page = 30;
    if(isset($_GET['page_no'])){
        $paged = $_GET['page_no'];
    }else{
        $paged = 1;
    }

    $count = wp_count_posts('financial');
    $pagintion = ceil($count->draft / 30);
}

$financial = get_posts( array(
    "post_type" => "financial",
    "posts_per_page" => $post_per_page,
    "paged" => $paged,
    "post_status" => "any",
) );
if (!isset($_GET['new_face'])) {
    $data = loadAllDataFinMonthly();
    $all_price = $data["all_price"];
    $all_income = $data["all_income"];
    $tech_Income = $data["tech_Income"];
    $reward = $data["reward"];
    $penalty = $data["penalty"];
    $wallet_charge_tech = $data["wallet_charge_tech"];
    $tech_total_debt =  (+$wallet_charge_tech + $reward) - $penalty;
    $total_tech_total_debt = +$tech_total_debt - $tech_Income;
    if($total_tech_total_debt > 0){
        $fin_tech= $total_tech_total_debt;
    }else{
        $total_tech_total_debt = $total_tech_total_debt * -1;
        $fin_tech= $total_tech_total_debt;
    }
    $tamir_from_tech = ($all_income - $tech_Income) + ($penalty - $reward);
    $tamir_income_notpure = ($all_income - $tech_Income);
    $per_co = ($tamir_from_tech / $all_income) * 100;
    $per_co_all = ($tamir_from_tech / $all_price) * 100;

    $post_per_page = 30;
    if(isset($_GET['page_no'])){
        $paged = $_GET['page_no'];
    }else{
        $paged = 1;
    }


    $date_query = '';

    if(isset($_GET['timestamp']) && isset($_GET['timestamp_end'])){
        $d = date("d",($_GET['timestamp'] / 1000));
        $m = date("m",($_GET['timestamp'] / 1000));
        $y = date("Y",($_GET['timestamp'] / 1000));
        $time = "0";
        $d_end = date("d",($_GET['timestamp_end'] / 1000));
        $m_end = date("m",($_GET['timestamp_end'] / 1000));
        $y_end = date("Y",($_GET['timestamp_end'] / 1000));
        $time_end = "23";
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

    $tech_search = '';
    if(isset($_GET['technician'])){
        $tech_search = $_GET['technician'];
    }

    $meta_query_status = array();
    if(isset($_GET['status_filter']) && $_GET['status_filter'] == "invoice"){
        $meta_query_status = array(
            "relation" => "AND",
            array(
                    "key" => "reward_type",
                    "compare" => "NOT EXISTS"
            ),
            array(
                "key" => "wallet",
                "compare" => "NOT EXISTS"
            ),
        );
    }
    if(isset($_GET['status_filter']) && $_GET['status_filter'] == "wallet"){
        $meta_query_status = array(
            array(
                "key" => "wallet",
                "compare" => "EXISTS"
            ),
        );
    }
    if(isset($_GET['status_filter']) && $_GET['status_filter'] == "reward"){
        $meta_query_status = array(
            array(
                "key" => "reward_type",
                "value" => "1",
                "compare" => "="
            ),
        );
    }
    if(isset($_GET['status_filter']) && $_GET['status_filter'] == "penalty"){
        $meta_query_status = array(
            array(
                "key" => "reward_type",
                "value" => "0",
                "compare" => "="
            ),
        );
    }

    $financial_array = array(
        "author" => $tech_search,
        "post_type" => "financial",
        "posts_per_page" => $post_per_page,
        "paged" => $paged,
        "post_status" => "any",
        "meta_query" => $meta_query_status,
    );


    if($_GET['date_year'] == "" && $_GET['date_year_end'] == ""){}else{
        $financial_array["date_query"] = array(
            array(
                'after'     => $start,
                'before'    => $end,
                'inclusive' => true,
            )
        );
    }
    $financial = get_posts($financial_array);
    if(isset($_GET['technician'])){
        $count_arg = array(
            "post_type" => "financial",
            "author" => $tech_search,
            "posts_per_page" => -1,
            "post_status" => "any",
            "meta_query" => $meta_query_status,
        );
        if($_GET['date_year'] == "" && $_GET['date_year_end'] == ""){}else{
            $count_arg["date_query"] = array(
                array(
                    'after'     => $start,
                    'before'    => $end,
                    'inclusive' => true,
                )
            );
        }
        $count = get_posts($count_arg);
        $pagination = ceil(count($count) / 30);
    }else{
    $count = wp_count_posts('financial');
    $pagination = ceil($count->draft / 30);
    }


}
?>

<div class="modal fade" id="EditFinancialPopup" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="PlaceOForm"></div>
        </div>
    </div>
</div>

<section class="DashboardSection">
    <?=NavDashboard();?>


<?php if(!isset($_GET['new_face'])){?>

    <div class="main">

        <div class="Short_view">

            <div class="filter-section">
                <select id="date_filter">
                    <option value="all">همه</option>
                    <option value="today">امروز</option>
                    <option value="week">هفته جاری</option>
                    <option value="month">ماه جاری</option>
                    <option value="custom_date">تاریخ دلخواه</option>
                </select>
                <input type="date" id="start_date" placeholder="تاریخ شروع" />
                <input type="date" id="end_date" placeholder="تاریخ پایان" />
            </div>

           <ul class="Short_view_option ">
<!--               <li>-->
<!--                   <i>--><?//=number_format($all_price);?><!--</i>-->
<!--                   <span>هزینه کل(تومان)</span>-->
<!--               </li>-->
<!--               <li>-->
<!--                   <i>--><?//=number_format($all_income);?><!--</i>-->
<!--                   <span>درآمد کلی(تومان)</span>-->
<!--               </li>-->
<!--               <li>-->
<!--                   <i>--><?//=number_format($tech_Income);?><!--</i>-->
<!--                   <span>درآمد تکنسین ها(تومان)</span>-->
<!--               </li>-->
               <li>
                   <i><?=number_format($tamir_income_notpure);?></i>
                   <span>سود ناخالص (تومان)</span>
               </li>
               <li class="first" >
                   <i><?=number_format($reward);?> </i>
                   <span>بستانکاری ایجادشده برای تعمیرکاران(تومان)</span>
               </li>
               <li class="fine">
                   <i><?=number_format($penalty);?></i>
                   <span>بدهی ایجادشده برای تعمیرکاران(تومان)</span>
               </li>


           </ul>
           <ul class="Short_view_option second">


               <li>
                   <i><?=number_format($tamir_from_tech);?></i>
                   <span>سود خالص (تومان)</span>
               </li>
               <li>
                   <i><?=number_format($wallet_charge_tech);?></i>
                   <span>شارژ کیف پول(تومان)</span>
               </li>
               <li>
                   <i id="LoadDebtTechFinExternal">در حال محاسبه</i>
                   <span>مانده بدهی تعمیرکاران(تومان)</span>
               </li>
<!--               <li>-->
<!--                   <i>--><?//=round($per_co);?><!-- %</i>-->
<!--                   <span>درصد سود مجموعه به درامد</span>-->
<!--               </li>-->
<!--               <li>-->
<!--                   <i>--><?//=round($per_co_all);?><!-- %</i>-->
<!--                   <span>درصد سود مجموعه نسبت به هزینه کل</span>-->
<!--               </li>-->

           </ul>
        </div>


        <form <?php if(isset($_GET['searchTrue'])) { echo 'class="active"'; } ?> action="#" method="get" id="SearchList">
            <input type="hidden" name="searchTrue" value="1">
        <div class="filter_option_div">
            <div class="form-group half">
                <label>وضعیت</label>
                <select class="form-control" name="status_filter">
                    <option <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "all"){echo 'selected';}?> value="all">همه</option>
                    <option <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "invoice"){echo 'selected';}?> value="invoice">فاکتورها</option>
                    <option <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "wallet"){echo 'selected';}?> value="wallet">شارژ کیف پول</option>
                    <option <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "reward"){echo 'selected';}?> value="reward">پاداش</option>
                    <option <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "penalty"){echo 'selected';}?> value="penalty">جریمه</option>
                </select>
            </div>
            <div class="form-group half">
                <label>شماره سند</label>
                <input class="form-control" name="document_number" id="DocumentNumberSearch"
                       value="<?php echo isset($_GET["document_number"]) ? htmlspecialchars($_GET["document_number"]) : ''; ?>">
            </div>

            <div class="form-group half">
                <label>شماره موبایل</label>
                <input class="form-control" name="mobile_number" id="MobileNumberSearch"
                       value="<?php echo isset($_GET["mobile_number"]) ? htmlspecialchars($_GET["mobile_number"]) : ''; ?>">
            </div>
            <div class="form-group half">
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

                        if(isset($_GET['technician'])){
                            if($item->ID == $_GET['technician']){
                                $selected = "selected";
                            }
                            else{
                                $selected = "";
                            }
                        }
                        else{
                            $selected = "";
                        }
                        echo '<option '.$selected.' value="'.$item->ID.'">'.$mobile.' - '.$s_first_name.' </option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group half multiple_element">
                <label>درصد سود رسانی</label>
                <select class="form-control" name="sort_profit">
                    <option <?php if(isset($_GET['sort_profit']) && $_GET['sort_profit'] == "desc"){echo 'selected';}?> value="desc">بالا تر از</option>
                     <option <?php if(isset($_GET['sort_profit']) && $_GET['sort_profit'] == "asc"){echo 'selected';}?> value="asc">پایین تر از</option>
                </select>
                <input type="text" class="form-control" name="profit_number">
            </div>
            <div class="form-group half">
                <label>از تاریخ</label>
                <input type="text" name="date_year" placeholder="از تاریخ" id="date_year" class="form-control" <?php if(isset($_GET['date_year'])){echo'value="'.$_GET["date_year"].'"';}?> >
                <input type="hidden" name="timestamp" id="timestamp" value="<?php if(isset($_GET['timestamp'])){echo $_GET["timestamp"];}?>">
            </div>
            <div class="form-group half">
                <label>تا تاریخ</label>
                <input type="text" name="date_year_end" placeholder="تا تاریخ" id="date_year_end" class="form-control" <?php if(isset($_GET['date_year_end'])){echo'value="'.$_GET["date_year_end"].'"';}?> >
                <input type="hidden" name="timestamp_end" id="timestamp_end" value="<?php if(isset($_GET['timestamp_end'])){echo $_GET["timestamp_end"];}?>">
            </div>

            <input type="submit" class="btn" value="فیلتر">

        </div>
        </form>


        <div class="content">
            <h1 class="title">لیست حسابداری</h1>
            <?php
            if(!isset($_GET["test"])){

            // نمایش عنوان‌ها قبل از شروع حلقه
            echo '
    <ul class="list_of_invoice header fin">
        <li>عملیات</li>
        <li>ردیف</li>
        <li>شماره سند</li>
        <li>اشتراک</li>
        <li>تکنسین</li>
        <li>پایان کار</li>
        <li>کل صورت حساب</li>
        <li>هزینه ها</li>
        <li>سهم شرکت</li>
            <li>وضعیت</li>
  <!-- <li>درآمد کل</li>-->
        <li>درصد سود سفارش</li>
        ';
        if (in_array("edit_financial", $access) || in_array("delete_financial", $access)) {
            echo '<li > عملیات</li >';
        }
        echo '
    </ul>


';
            $i = 0;
            $all_cost_price_sina = 0;
            foreach($financial as $item){
                $i++;
                $page_count =  + $paged - 1;
                $number_count = $i + ($page_count * 30);
                $uid = get_post_meta($item->ID,"order_id",true);
                $customer_id = get_post_meta($item->ID,"customer_id",true);
                $price_customer = get_post_meta($item->ID,"price_customer",true);
                $wallet = get_post_meta($item->ID,"wallet",true);
                $wallet_pay = get_post_meta($item->ID,"wallet_pay",true);
                $refid = get_post_meta($item->ID,"refid",true);
                $cost_price = get_post_meta($item->ID,"cost_price",true);
                $total_invoice = get_post_meta($item->ID,"total_invoice",true);
                $order_id = get_post_meta($item->ID,"order_id",true);
                $type_fin = get_post_meta($item->ID,"type_fin",true);
                if($total_invoice == ''){
                    $total_invoice = 0;
                }
                if($cost_price == ''){
                    $cost_price = 0;
                }
                $all_cost_price_sina = $all_cost_price_sina + $cost_price;
                $payment_status = get_post_meta($item->ID,"payment_status",true);
                $reward_type = get_post_meta($item->ID,"reward_type",true);
                $rewardDesc = get_post_meta($item->ID,"rewardDesc",true);
                $customer = get_post_meta($uid,"customer",true);
                $customer_first_name = get_user_meta($customer,"first_name",true);
                $sub_customer = + $customer + 10000;
                $author = $item->post_author;
                $percent = get_user_meta($author,"percent",true);
                $type_of_calc_tech = get_user_meta($author,"type_of_calc_tech",true);
                $tech_per_of_all = get_user_meta($author,"tech_per_of_all",true);
                $status = get_post_meta($uid,"status",true);
                if($status == 10){
                    $percent= 100;
                }
                $action_status = '';
                $tech_firstname = get_user_meta($item->post_author,"first_name",true);
                $customer_filter = true;
                $tech_filter = true;
                $date_filter = true;
                $invoice_format_filter = true;
                $wallet_in_filter = true;
                $increase_filter = true;
                $decerese_filter = true;
                if($payment_status == 3){
                    $allow_invoice = '';
                    $class = "dont_allow";
                }
                else{
                    $allow_invoice = '';
                    $class = '';
                }
                if($reward_type == 1){
                    $total_invoice = convert2english($total_invoice);
                    if($total_invoice == '' || empty($total_invoice)){$total_invoice = 0;}
                    $status_payment = '<span class="plus">+ '.number_format($total_invoice).' تومان</span>';
                    $type_doc_desc = '<a href="#">ایجاد بستانکاری ('.$rewardDesc.')</a>';
                    $action_status = '<span class="action_status R">ایجاد بستانکاری</span>';
                    $total_invoice_co = 0;
                    $total_invoice_customer = $total_invoice;
                    $type_doc = '-';
                    $total_price = $total_invoice;
                    $class_s = "GreenBold";
                    $edit_id = $item->ID;
                }
                if($reward_type == "0"){
                    $total_invoice = convert2english($total_invoice);
                    $status_payment = '<span class="minus"> - '.number_format($total_invoice).' تومان</span>';
                    $type_doc_desc = '<a href="#">ایجاد بدهکاری ('.$rewardDesc.')</a>';
                    $action_status = '<span class="action_status P">ایجاد بدهکاری</span>';
                    $total_invoice_co = $total_invoice;
                    $total_invoice_customer = - $total_invoice;
                    $type_doc = '-';
                    $class_s = "";
                    $edit_id = $item->ID;
                }
                if($wallet == 1){
                    $action_status = '<span class="action_status D">کیف پول</span>';
                    $status_payment = '<span class="plus">+ '.number_format($wallet_pay).' تومان</span>';
                    $type_doc_desc = '<a href="#">شارژ کیف پول</a><br><span class="plus">'.$refid.'</span>';
                    $total_invoice_co = $total_price;
                    $type_doc = '-';
                    $total_price = '-';
                    $class_s = "GreenBold";
                    $edit_id = $item->ID;
                }
                if($reward_type == "" && $wallet == ""){
                    $edit_id = $order_id;
                    $action_status = '<span class="action_status O"><a target="_blank" href="'.get_the_permalink(120266).'?oid='.$order_id.'">فاکتور</a></span>';

                    $type_doc = $customer_first_name .' -  کد اشتراک '.$sub_customer .'</br><a target="_blank" href="'.get_the_permalink(135).'?uid='.$order_id.'">مشاهده سفارش</a>';
                    $type_doc_desc = '-';

                    if($type_of_calc_tech == 1){

                        $tech_per = 1 - ($tech_per_of_all / 100);
                        $all_price_calc = $total_invoice + $cost_price;
                        $total_invoice_customer = $all_price_calc * $tech_per;
                        $total_invoice_co = $all_price_calc - $total_invoice_customer;
                    }else{
                        $total_invoice_customer = ($total_invoice / 100) * $percent;
                        $total_invoice_co = $total_invoice - $total_invoice_customer;
                    }


                    $total_price = + $total_invoice + $cost_price;

                    $per_order = ($total_invoice / $total_price) * 100;
                    if($type_fin == "internal"){
                        $status_payment = '<span class="minus green">+ ' . number_format($total_invoice_co) . ' تومان</span>';
                    }else {
                        $status_payment = '<span class="minus yellow">- ' . number_format($total_invoice_co) . ' تومان</span>';
                    }
                    $all_income = +$all_income + $total_invoice;
                    $all_price = + $all_price + $total_invoice + $cost_price;
                    $class_s = "OrangeBold";
                }
                echo '
              <ul class="list_of_invoice header fin hide_in_desktop">
                    <li>عملیات</li>
                    <li>ردیف</li>
                    <li>شماره سند</li>
                    <li>اشتراک</li>
                    <li>تکنسین</li>
                    <li>پایان کار</li>
                     <li>کل صورت حساب</li> 
                    <li>هزینه ها</li>
                    <li>سهم شرکت</li>
                    <li>وضعیت</li>
                    <li>درصد سود سفارش</li>
                     ';
                        if (in_array("edit_financial", $access) || in_array("delete_financial", $access)) {
                            echo '<li > عملیات</li >';
                        }
                    echo '
                </ul>
                 <ul class="list_of_invoice show_horizental '.$class.' '.$class_s.'">
                        <li>'.$action_status.'</li>
                        <li> '.$number_count.'</li>
                        <li>'.$item->ID.'</li>
                        <li>'.$type_doc.'</li>
                        <li>'.$tech_firstname.'</li>
                        <li>'.get_the_date("Y/m/d H:i", $item->ID) .'</li>
                        <li>'.@number_format($total_price).'</li>
                        <li>'.@number_format($cost_price).'</li>
                        <li>'.@number_format($total_invoice_co).'</li>
                        <li>'.$status_payment.'</li>
                        <li class="'.$class.'" ><b>'.ceil($per_order).'%</b></li>
                       
                           ';
                            if (in_array("edit_financial", $access) || in_array("delete_financial", $access)) {
                                echo '<li class="actionLi" >
                                ';
                                if (in_array("edit_financial", $access)){
                                    echo '<div class="edit_financialBTN" id="'.$edit_id.'"><i class="las la-pen"></i></div>';
                                }
                                if (in_array("delete_financial", $access)){
                                    echo '<div class="delete_financialBTN" id="'.$item->ID.'"><i class="las la-trash"></i></div>';
                                }
                                echo '
                                </li >';
                            }
                            echo '
                        
                </ul>
                <div class="SepratorList"></div>
                ';

                }

            ?>


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
                if(isset($_GET['technician'])){
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
                    if(isset($_GET['technician'])){
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
                    if(isset($_GET['technician'])){
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
                    if(isset($_GET['technician'])){
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
                    if(isset($_GET['technician'])){
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
                    if(isset($_GET['technician'])){
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
                    if(isset($_GET['technician'])){
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
                    if(isset($_GET['technician'])){
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



                }
            else{



                //old version
            $i = 0;
            foreach($financial as $item){
                $i++;
                $page_count =  + $paged - 1;
                $number_count = $i + ($page_count * 30);
                $uid = get_post_meta($item->ID,"order_id",true);
                $customer_id = get_post_meta($item->ID,"customer_id",true);
                $price_customer = get_post_meta($item->ID,"price_customer",true);
                $wallet = get_post_meta($item->ID,"wallet",true);
                $wallet_pay = get_post_meta($item->ID,"wallet_pay",true);
                $refid = get_post_meta($item->ID,"refid",true);
                $cost_price = get_post_meta($item->ID,"cost_price",true);
                $total_invoice = get_post_meta($item->ID,"total_invoice",true);
                $order_id = get_post_meta($item->ID,"order_id",true);
                $type_fin = get_post_meta($item->ID,"type_fin",true);
                if($total_invoice == ''){
                    $total_invoice = 0;
                }
                if($cost_price == ''){
                    $cost_price = 0;
                }
                $payment_status = get_post_meta($item->ID,"payment_status",true);
                $reward_type = get_post_meta($item->ID,"reward_type",true);
                $rewardDesc = get_post_meta($item->ID,"rewardDesc",true);
                $customer = get_post_meta($uid,"customer",true);
                $customer_first_name = get_user_meta($customer,"first_name",true);
                $sub_customer = + $customer + 10000;
                $author = $item->post_author;
                $percent = get_user_meta($author,"percent",true);

                $tech_firstname = get_user_meta($item->post_author,"first_name",true);
                $customer_filter = true;
                $tech_filter = true;
                $date_filter = true;
                $invoice_format_filter = true;
                $wallet_in_filter = true;
                $increase_filter = true;
                $decerese_filter = true;
                if($payment_status == 3){
                    $allow_invoice = '';
                    $class = "dont_allow";
                }
                else{
                    $allow_invoice = '';
                    $class = '';
                }
                if($reward_type == 1){
                    $total_invoice = convert2english($total_invoice);
                    if($total_invoice == '' || empty($total_invoice)){$total_invoice = 0;}
                    $status_payment = '<span class="plus">+ '.number_format($total_invoice).' تومان</span>';
                    $type_doc_desc = '<a href="#">پاداش ('.$rewardDesc.')</a>';
                    $total_invoice_co = 0;
                    $total_invoice_customer = $total_invoice;
                    $type_doc = '-';
                    $total_price = $total_invoice;
                    $class_s = "GreenBold";
                }
                if($reward_type == "0"){
                    $total_invoice = convert2english($total_invoice);
                    $status_payment = '<span class="minus"> - '.number_format($total_invoice).' تومان</span>';
                    $type_doc_desc = '<a href="#">جریمه ('.$rewardDesc.')</a>';
                    $total_invoice_co = $total_invoice;
                    $total_invoice_customer = - $total_invoice;
                    $type_doc = '-';
                    $class_s = "";

                }
                if($wallet == 1){
                    $status_payment = '<span class="plus">+ '.number_format($wallet_pay).' تومان</span>';
                    $type_doc_desc = '<a href="#">شارژ کیف پول</a><br><span class="plus">'.$refid.'</span>';
                    $total_invoice_co = $total_price;
                    $type_doc = '-';
                    $total_price = '-';
                    $class_s = "GreenBold";
                }
                if($reward_type == "" && $wallet == ""){
                    $type_doc = $customer_first_name .' -  کد اشتراک '.$sub_customer .'</br><a target="_blank" href="'.get_the_permalink(135).'?uid='.$order_id.'">مشاهده سفارش</a>';
                    $type_doc_desc = '-';
                    $total_invoice_customer = ($total_invoice / 100) * $percent;
                    $total_invoice_co = $total_invoice - $total_invoice_customer;
                    $total_price = + $total_invoice + $cost_price;

                    $per_order = ($total_invoice / $total_price) * 100;
                    if($type_fin == "internal"){
                        $status_payment = '<span class="minus green">+ ' . number_format($total_invoice_co) . ' تومان</span>';
                    }else {
                        $status_payment = '<span class="minus yellow">- ' . number_format($total_invoice_co) . ' تومان</span>';
                    }
                    $all_income = +$all_income + $total_invoice;
                    $all_price = + $all_price + $total_invoice + $cost_price;
                    $class_s = "OrangeBold";
                }
                echo '
                    
                        <ul class="list_of_invoice header fin">
                            
                            <li>شماره سند</li>
                            <li>اشتراک</li>
                            <li>تکنسین</li>
                            <li>هزینه کل</li>
                            <li>هزینه ها</li>
                            <li>سهم شرکت</li>
                            <li>وضعیت</li>
                            <li>ارسال کار</li>
                            <li>پایان کار</li>
                        </ul>
                        <div class="count_fin">'.$number_count.'</div>
                        <ul class="list_of_invoice '.$class.' '.$class_s.'">
                           
                            <li>'.$item->ID.'</li>
                            <li>'.$type_doc.'</li>
                            <li>'.$tech_firstname.'</li>
                            <li>'.@number_format($total_price).'</li>
                            <li>'.@number_format($cost_price).'</li>
                        
                            <li>'.@number_format($total_invoice_co).'</li>
                            <li>'.$status_payment.'</li>
                            <li>'.get_the_date("Y/m/d",$uid).'</li>
                            <li>'.get_the_date("Y/m/d",$item->ID).'</li>
                        </ul>
                        <div class="description_invoice  '.$class_s.'">
                        ';
                if($reward_type == "" && $wallet == ""){
                    $class = "";
                    if($per_order > 90 || $per_order < 40){
                        $class="warning";
                    }
                    echo '
                                <ul class="order_desc">
                                    <li><span>کل صورتحساب</span><b>'.number_format($total_price).'تومان</b></li>
                                    <li><span>هزینه ها</span><b>'.number_format($cost_price).'تومان</b></li>
                                    <li><span>درآمد کل</span><b>'.number_format($total_invoice).'تومان</b></li>
                                    <li class="'.$class.'" ><span>درصد سود سفارش</span><b>'.ceil($per_order).'%</b></li>
                                </ul>
                            ';
                }
                echo $type_doc_desc.'</div>';
            }
            ?>


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
                if(isset($_GET['technician'])){
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
                    if(isset($_GET['technician'])){
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
                    if(isset($_GET['technician'])){
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
                    if(isset($_GET['technician'])){
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
                    if(isset($_GET['technician'])){
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
                    if(isset($_GET['technician'])){
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
                    if(isset($_GET['technician'])){
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
                    if(isset($_GET['technician'])){
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
            }


                ?>

            </ul>
        </div>
        <?php

        $all_price = 0;
        $all_income = 0;
        $tech_Income = 0;
        $all_cost_price = 0;
        $cost_price = 0;
        $reward = 0;
        $penalty = 0;
        $wallet_charge_tech = 0;

        $cost_prcie_calc = 0;
        foreach($count as $item){
            $i++;
            $uid = get_post_meta($item->ID,"order_id",true);
            $status = get_post_meta($uid,"status",true);
            $customer_id = get_post_meta($item->ID,"customer_id",true);
            $price_customer = get_post_meta($item->ID,"price_customer",true);
            $wallet = get_post_meta($item->ID,"wallet",true);
            $wallet_pay = get_post_meta($item->ID,"wallet_pay",true);
            $refid = get_post_meta($item->ID,"refid",true);
            $cost_price = get_post_meta($item->ID,"cost_price",true);
            $total_invoice = get_post_meta($item->ID,"total_invoice",true);
            $order_id = get_post_meta($item->ID,"order_id",true);
            $reward_type = get_post_meta($item->ID,"reward_type",true);
            if($total_invoice == ''){
                $total_invoice = 0;
            }
            if($cost_price == ''){
                $cost_price = 0;
            }
            $percent = get_user_meta($author,"percent",true);
            if($reward_type == 1){
                $total_invoice = convert2english($total_invoice);
                if($total_invoice == '' || empty($total_invoice)){$total_invoice = 0;}

                $reward = +$reward + $total_invoice;
            }
            if($reward_type == "0"){
                $total_invoice = convert2english($total_invoice);
                $penalty = + $penalty + $total_invoice;
            }
            if($wallet == 1){
                $wallet_charge_tech = +$wallet_charge_tech + $wallet_pay;
            }
            if($reward_type == "" && $wallet == ""){
                $cost_prcie_calc+= $cost_prcie_calc + $cost_price;
                $all_income = +$all_income + $total_invoice;
                $all_price = + $all_price + $total_invoice + $cost_price;
                $percent = get_user_meta($item->post_author,"percent",true);
                $type_of_calc_tech = get_user_meta($item->post_author,"type_of_calc_tech",true);
                $tech_per_of_all = get_user_meta($item->post_author,"tech_per_of_all",true);



                if($type_of_calc_tech == 1){

//                        $tech_per = 1 - ($tech_per_of_all / 100);
//                        $all_price_calc = $total_invoice + $cost_price;
//                        $total_invoice_co = $all_price_calc * $tech_per;
//                        $total_invoice_customer = $all_price_calc - $total_invoice_customer;
//                        $tech_Income = + $total_invoice_co;

                        $tech_per = 1 - ($tech_per_of_all / 100);
                        $all_price_calc = $total_invoice + $cost_price;
                        $total_invoice_co = $all_price_calc - ($all_price_calc * $tech_per);
                        $tech_Income = + $tech_Income + $total_invoice_co;
                    }else {

                        if ($status == 10) {
                            $percent = 100;
                        }
                        $total_invoice_customer = ($total_invoice / 100) * $percent;
                        $total_invoice_co = $total_invoice - $total_invoice_customer;
                        $tech_Income = + $tech_Income + $total_invoice_co;
                    }


            }
        }
        $pure_sallary_pure = ($tech_Income + $reward) - $penalty;
        $tech_per_co = ($tech_Income / $all_price) * 100;
        $co_pure_benefit = $all_income - $pure_sallary_pure;

        $tech_total_debt =  (+$wallet_charge_tech + $reward) - $penalty;
        $total_tech_total_debt = +$tech_total_debt - $tech_Income;
        if($total_tech_total_debt > 0){
            $fin_tech= number_format($total_tech_total_debt) . ' تومان (بستانکار)';
        }else{
            $total_tech_total_debt = $total_tech_total_debt * -1;
            $fin_tech= number_format($total_tech_total_debt) . ' تومان (بدهکار)';
        }
        if($type_of_calc_tech == 1){
            $tamir_from_tech = ($all_price  - $tech_Income) + ($penalty - $reward);
        }else {
            $tamir_from_tech = ($all_income  - $tech_Income) + ($penalty - $reward);
        }
        ?>
        <div class="List_of_detail">

            <ul class="list_of_invoice header">
                    <li>جمع کل فاکتورها</li>
                    <li>سهم تعمیرآنلاین از سفارشات</li>
                    <li>سهم تعمیرکار از سفارشات</li>
                    <li>ایجاد بستانکاری</li>
                    <li>ایجاد بدهکاری</li>
                    <li>درصد سودآوری</li>
                    <li>هزینه ها</li>
                </ul>
                <ul class="list_of_invoice">
                    <li><?=number_format($all_price)?> تومان</li>
                    <li><?=number_format($tech_Income)?> تومان</li>
                    <li><?=number_format($tamir_from_tech)?> تومان</li>
                    <li><?=number_format($reward)?> تومان</li>
                    <li><?=number_format($penalty)?> تومان</li>
                    <li><?=round($tech_per_co)?> %</li>
                    <li><?=number_format($all_cost_price_sina)?> تومان</li>
            </ul>

            <ul class="list_of_invoice header">
                <li>شارز کیف پول انجام شده</li>
                    <li>وضعیت حساب تعمیرکار</li>

            </ul>
            <ul class="list_of_invoice">

                <li><?=number_format($wallet_charge_tech)?> تومان</li>
                <li><?=$fin_tech?></li>

            </ul>

        </div>
    </div>



<?php }else{?>
    <div class="main">
        <div class="content">
            <h1 class="title">لیست مالی</h1>
            <div class="SearchBox">
                <span class="titleBox">جستجو پیشرفته</span>
                <form <?php if(isset($_GET['searchTrue'])){echo'class="active"';}?> action="#" method="get" id="SearchList">
                    <input type="hidden" name="searchTrue" value="1">
                    <div class="form-group half">
                        <label>کد اشتراک</label>
                        <input class="form-control" name="customer" id="CustomerSearch" value="<?php if(isset($_GET["customer"])){echo $_GET["customer"];} ?>">
                    </div>
                    <div class="form-group half">
                        <label>تکنسین</label>
                        <select class="form-control" name="technician" id="technicianSearch">
                            <option value="">تکنسین را انتخاب نمایید.</option>
                            <?php
                            $customer_list = get_users( array(
                                'meta_key' => 'role',
                                'meta_value' => 'technician',
                                'meta_compare' => '=' // exact match only
                            ) );
                            foreach($customer_list as $item){
                                $s_first_name = get_user_meta($item->ID,"first_name",true);
                                $mobile = get_user_meta($item->ID,"mobile",true);

                                if(isset($_GET['technician'])){
                                    if($item->ID == $_GET['technician']){
                                        $selected = "selected";
                                    }
                                    else{
                                        $selected = "";
                                    }
                                }
                                else{
                                    $selected = "";
                                }
                                echo '<option '.$selected.' value="'.$item->ID.'">'.$mobile.' - '.$s_first_name.' </option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group half">
                        <label>از تاریخ</label>
                        <input type="text" name="date_year" id="date_year" class="form-control" <?php if(isset($_GET['date_year'])){echo'value="'.$_GET["date_year"].'"';}?> >
                        <input type="hidden" name="timestamp" id="timestamp" value="<?php if(isset($_GET['timestamp'])){echo $_GET["timestamp"];}?>">
                    </div>
                    <div class="form-group half">
                        <label>تا تاریخ</label>
                        <input type="text" name="date_year_end" id="date_year_end" class="form-control" <?php if(isset($_GET['date_year_end'])){echo'value="'.$_GET["date_year_end"].'"';}?> >
                        <input type="hidden" name="timestamp_end" id="timestamp_end" value="<?php if(isset($_GET['timestamp_end'])){echo $_GET["timestamp_end"];}?>">
                    </div>

                    <ul class="list_of_status">
                        <li>
                            <input type="checkbox" name="invoice_format" <?php if(isset($_GET['invoice_format']) && $_GET['invoice_format'] == 1){echo 'checked';}?> value="1">
                            <span>فاکتور</span>

                        </li>
                       <li>
                           <input type="checkbox" name="wallet_in"   <?php if(isset($_GET['wallet_in']) && $_GET['wallet_in'] == 1){echo 'checked';}?> value="1">
                           <span>شارژ کیف پول</span>
                       </li>

                        <li>
                            <input type="checkbox" name="increase" <?php if(isset($_GET['increase']) && $_GET['increase'] == 1){echo 'checked';}?> value="1">
                            <span>پاداش</span>
                        </li>

                        <li>
                            <input type="checkbox" name="decerese" <?php if(isset($_GET['decerese']) && $_GET['decerese'] == 1){echo 'checked';}?>  value="1">
                            <span>جریمه</span>
                        </li>

                    </ul>

                    <input type="submit" class="btn" value="جستجو پیشرفته">
                </form>
            </div>
            <table class="table ListTable">
                <tr>
                    <th width="10%">شماره سند</th>
                    <th width="15%">اشتراک</th>
                    <th width="15%">توضیحات</th>
                    <th>تکنسین</th>
                    <th>هزینه کل</th>
                    <th>سهم شرکت</th>
                    <th>وضعیت</th>
                    <th>تاریخ ارسال کار</th>
                    <th>تاریخ پایان کار</th>
                </tr>
                <?php

                $all_price = 0;
                $all_income = 0;
                $tech_Income = 0;
                $reward = 0;
                $penalty = 0;
                $wallet_charge_tech = 0;


                $i = 0;
                foreach($financial as $item){
                    $i++;
                    $uid = get_post_meta($item->ID,"order_id",true);
                    $status = get_post_meta($uid,"status",true);
                    $customer_id = get_post_meta($item->ID,"customer_id",true);
                    $price_customer = get_post_meta($item->ID,"price_customer",true);
                    $wallet = get_post_meta($item->ID,"wallet",true);
                    $wallet_pay = get_post_meta($item->ID,"wallet_pay",true);
                    $refid = get_post_meta($item->ID,"refid",true);
                    $cost_price = get_post_meta($item->ID,"cost_price",true);
                    $total_invoice = get_post_meta($item->ID,"total_invoice",true);
                    $order_id = get_post_meta($item->ID,"order_id",true);

                    if($total_invoice == ''){
                        $total_invoice = 0;
                    }
                    if($cost_price == ''){
                        $cost_price = 0;
                    }

                    $payment_status = get_post_meta($item->ID,"payment_status",true);
                    $reward_type = get_post_meta($item->ID,"reward_type",true);
                    $rewardDesc = get_post_meta($item->ID,"rewardDesc",true);
                    $customer = get_post_meta($uid,"customer",true);
                    $customer_first_name = get_user_meta($customer,"first_name",true);
                    $sub_customer = + $customer + 10000;
                    $author = $item->post_author;
                    $percent = get_user_meta($author,"percent",true);
                    if($status == 10){$percent == 100;}

                    $tech_firstname = get_user_meta($item->post_author,"first_name",true);
                    if(isset($_GET['searchTrue'])){
                        
                        if($_GET['customer'] == ""){
                            $customer_filter = true;

                        }else{
                            if($_GET['customer'] == $sub_customer){
                                $customer_filter = true;
                            }
                            else{
                                $customer_filter = false;
                            }
                        }

                        if($_GET['technician'] == ""){
                            $tech_filter = true;
                        }else{

                            if($_GET['technician'] == $item->post_author){
                                $tech_filter = true;
                            }
                            else{
                                $tech_filter = false;
                            }
                        }
                        if($_GET['technician'] == ""){
                            $tech_filter = true;
                        }else{

                            if($_GET['technician'] == $item->post_author){
                                $tech_filter = true;
                            }
                            else{
                                $tech_filter = false;
                            }
                        }
                        if($_GET['timestamp'] == "" && $_GET['timestamp_end'] == ''){
                            $date_filter = true;
                        }else{
                            $start = ($_GET['timestamp'] / 1000 ) - (24*60*60);
                            $end = $_GET['timestamp_end'] / 1000;
                            $time = get_post_timestamp($item->ID);
                            if($start <= $time && $end >= $time){
                                $date_filter = true;
                            }else{
                                $date_filter = false;
                            }
                        }

                        if($_GET['invoice_format'] == 1){
                            if($reward_type == "" && $wallet == ""){
                                $invoice_format_filter = true;
                            }else{
                                $invoice_format_filter = false;
                            }
                        }else{
                            $invoice_format_filter = 'true';
                        }
                        if($_GET['wallet_in'] == 1){
                            if($wallet == 1){
                                $wallet_in_filter = true;
                            }else{
                                $wallet_in_filter = false;
                            }
                        }else{
                            $wallet_in_filter = true;
                        }
                        if($_GET['increase'] == 1){
                            if($reward_type == 1){
                                $increase_filter = true;
                            }else{
                                $increase_filter = false;
                            }
                        }else{
                            $increase_filter = true;
                        }
                        if($_GET['decerese'] == 1){
                            if($reward_type == 0){
                                $decerese_filter = true;
                            }else{
                                $decerese_filter = false;
                            }
                        }else{
                            $decerese_filter = true;
                        }


                        if($_GET['decerese'] == 1 && $_GET['increase'] == 1 && $_GET['wallet_in'] == 1 && $_GET['invoice_format'] == 1){
                            $invoice_format_filter = true;
                            $wallet_in_filter = true;
                            $increase_filter = true;
                            $decerese_filter = true;
                        }

                    }else{
                        $customer_filter = true;
                        $tech_filter = true;
                        $date_filter = true;
                        $invoice_format_filter = true;
                        $wallet_in_filter = true;
                        $increase_filter = true;
                        $decerese_filter = true;

                    }

                    if($payment_status == 3){
                        $allow_invoice = '<tr class="dont_allow"><td colspan="8" ><span class="dontAllow">این فاکتور توسط اپراتور تایید نشده است</span></td> </tr>';
                        $class = "dont_allow";
                    }
                    else{
                        $allow_invoice = '';
                        $class = '';
                    }
                    print_r(' ');
                    if($customer_filter && $tech_filter && $date_filter && $invoice_format_filter && $wallet_in_filter &&  $increase_filter && $decerese_filter){
                        $all_cost_price_sina = $all_cost_price_sina + $cost_price;
                    if($reward_type == 1){
                        $total_invoice = convert2english($total_invoice);
                        if($total_invoice == '' || empty($total_invoice)){$total_invoice = 0;}
                        $status_payment = '<span class="plus">+ '.number_format($total_invoice).' تومان</span>';
                        $type_doc_desc = '<a href="#">پاداش ('.$rewardDesc.')</a>';
                        $total_invoice_co = 0;
                        $total_invoice_customer = $total_invoice;
                        $type_doc = '-';
                        $total_price = $total_invoice;
//                        if(isset($_GET['searchTrue'])) {
//                            if($_GET['technician'] == $item->post_author){
                                $reward = +$reward + $total_invoice;
//                            }
//                        }
                    }
                    if($reward_type == "0"){
                        $total_invoice = convert2english($total_invoice);
                        $status_payment = '<span class="minus"> - '.number_format($total_invoice).' تومان</span>';
                        $type_doc_desc = '<a href="#">جریمه ('.$rewardDesc.')</a>';
                        $total_invoice_co = $total_invoice;
                        $total_invoice_customer = - $total_invoice;
                        $type_doc = '-';
                        $total_price = $total_invoice;
//
//                        if(isset($_GET['searchTrue'])) {
//                            if($_GET['technician'] == $item->post_author){
                                $penalty = + $penalty + $total_invoice;
//                            }
//                        }

                    }
                    if($wallet == 1){
                        $status_payment = '<span class="plus">+ '.number_format($wallet_pay).' تومان</span>';
                        $type_doc_desc = '<a href="#">شارژ کیف پول</a><br><span class="plus">'.$refid.'</span>';
                        $total_invoice_co = $wallet_pay;
                        $type_doc = '-';
                        $total_price = $wallet_pay;
//
//                        if(isset($_GET['searchTrue'])) {
//                            if($_GET['technician'] == $item->post_author){
                                $wallet_charge_tech = +$wallet_charge_tech + $wallet_pay;
//                            }
//                        }

                    }
                    if($reward_type == "" && $wallet == ""){
//
                        $type_doc = $customer_first_name .' -  کد اشتراک '.$sub_customer .'</br><a target="_blank" href="'.get_the_permalink(135).'?uid='.$order_id.'">مشاهده سفارش</a>';
                        $type_doc_desc = '-';
                        $total_invoice_customer = ($total_invoice / 100) * $percent;
                        $total_invoice_co = $total_invoice - $total_invoice_customer;
                        $total_price = + $total_invoice + $cost_price;
                        $status_payment =  '<span class="minus yellow">- '.number_format($total_invoice_co).' تومان</span>';
//                        if(isset($_GET['searchTrue'])) {
//                            if($_GET['technician'] == $item->post_author){
//                                if($payment_status == 3){}else{

                                    $all_income = +$all_income + $total_invoice;
                                    $all_price = + $all_price + $total_invoice + $cost_price;

                                    $tech_Income = +$tech_Income + $total_invoice_co;

//                                }
//                            }
//                        }

                    }
                    echo '
                        '.$allow_invoice.'
                        <tr class="'.$class.'">
                            
                            <td width="10%">'.$item->ID.'</td>
                           <td  width="15%">'.$type_doc.'</td>
                            <td>'.$type_doc_desc.'</td>
                            <td><a href="#">'.$tech_firstname.'</a></td>
                            <td>'.@number_format($total_price).' تومان</td>
                            <td>'.@number_format($total_invoice_co).' تومان</td>
                            <td>'.$status_payment.'</td>
                            <td>'.get_the_date("Y/m/d",$uid).'</td>
                            <td>'.get_the_date("Y/m/d",$item->ID).'</td>
                        </tr>
                    ';
                    }
                }

                ?>
            </table>
            <ul class="paginition">

                <?php

                for($i = 1 ; $i<= $pagintion ; $i++){

                    $actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                    if(isset($_GET['technician'])){
                        $link = $actual_link.'&page_no='.$i;
                    }else{
                        $link = '?page_no='.$i;
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

                    echo '
                                
                                    <li>
                                        
                                        <a class="'.$active.'" href="'.$link.'">'.$i.'</a>
                                    
                                    </li>
                                
                                ';
                }

                ?>

            </ul>

            <?php
                if(isset($_GET['searchTrue'])){}else{
                    $data = loadAllDataFin();
                    $all_price = $data["all_price"];
                    $all_income = $data["all_income"];
                    $tech_Income = $data["tech_Income"];
                    $reward = $data["reward"];
                    $penalty = $data["penalty"];
                    $wallet_charge_tech = $data["wallet_charge_tech"];


                }
            ?>
            <?php
                $pure_tech_sallary = ($tech_Income + $reward) - $penalty;
                $tech_per_co = ceil($tech_Income / $all_price);
                $co_pure_benefit = $all_income - $pure_tech_sallary;
            ?>
            <table class="table ListTable">

                <tr>
<!--                    <th>هزینه کل</th>-->
<!--                    <th>درامد کلی</th>-->
                    <th>درامد ناخالص تعمیرکار</th>
                    <th>ایجاد بستانکاری</th>
                    <th>ایجاد بدهکاری</th>
                    <th>درامد خالی تعمیرکار</th>
                    <th>درصد سود اوری</th>
                    <th>سود ناخالص مجموعه</th>
                    <th>شارز کیف پول</th>
                </tr>

                <tr>
<!--                    <td>--><?//=number_format($all_price)?><!-- تومان</td>-->
<!--                    <td>--><?//=number_format($all_income)?><!-- تومان</td>-->
                    <td><?=number_format($tech_Income)?> تومان</td>
                    <td><?=number_format($reward)?> تومان</td>
                    <td><?=number_format($penalty)?> تومان</td>
                    <td><?=number_format($pure_tech_sallary)?> تومان</td>
                    <td><?=$tech_per_co?> %</td>
                    <td><?=number_format($co_pure_benefit)?> تومان</td>
                    <td><?=number_format($wallet_charge_tech)?> تومان</td>
                </tr>

            </table>
            <?php
                $tech_total_debt =  (+$wallet_charge_tech + $reward) - $penalty;
                $total_tech_total_debt = +$tech_total_debt - $tech_Income;
                if($total_tech_total_debt > 0){
                    $fin_tech= number_format($total_tech_total_debt) . ' تومان (بستانکار)';
                }else{
                    $total_tech_total_debt = $total_tech_total_debt * -1;
                    $fin_tech= number_format($total_tech_total_debt) . ' تومان (بدهکار)';
                }
                $tamir_from_tech = ($all_income - $tech_Income) + ($penalty - $reward);

            ?>
            <table class="table ListTable">
                <tr>
                    <th>وضعیت مالی تکنسین به مجموعه</th>
                    <th>درآمد تعمیر آنلاین از تکنسین</th>
                    <th>مجموع هزینه ا</th>
                </tr>
                <tr>
                    <td><?=$fin_tech?></td>
                    <td><?=number_format($tamir_from_tech)?> تومان</td>
                    <td><?=number_format($all_cost_price_sina)?> تومان</td>
                </tr>
            </table>


        </div>

    </div>
</section>
<?php } ?>

<?php
get_footer();
?>

<script>
    jQuery(window).on("load",function (){
        LoadDebtTechFinExternal();
    })
</script>