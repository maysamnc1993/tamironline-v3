<?php
IFLoggedAndVerify("tech_order_list");
// Template Name: Technician Order List
get_header();

$count_tech_order = get_option('count_tech_order')[get_current_user_id()];

$total_count = $count_tech_order['new'] + $count_tech_order['sync'] + $count_tech_order['open'] + $count_tech_order['pend'];

$meta_query_order = array();
if(isset($_GET['subscription'])){
$meta_query_order = array(
    "relation" => "AND",
    array(
        "key" => "subscription",
        "value" => $_GET['subscription'],
        "compare" => "=",
    ),
    array(
        "key" => "technician",
        "value" => get_current_user_id(),
        "compare" => "LIKE"
    )
);

if (!empty($_GET['mobile'])) {
    $meta_query_order = array(
        "relation" => "AND",
        array(
            "key" => "mobile",
            "value" => $_GET['mobile'],
            "compare" => "LIKE",
        ),
        array(
            "key" => "technician",
            "value" => get_current_user_id(),
            "compare" => "LIKE"
        )
    );
}

if (!empty($_GET['address'])) {
    $meta_query_order = array(
        "relation" => "AND",
        array(
            "key" => "address",
            "value" => $_GET['address'],
            "compare" => "LIKE",
        ),
        array(
            "key" => "technician",
            "value" => get_current_user_id(),
            "compare" => "LIKE"
        )
    );


}
}






else if(!isset($_GET['status_filter']) || $_GET['status_filter'] == "0" ){
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
        )
        ,array(
            "key" => "technician",
            "value" => get_current_user_id(),
            "compare" => "LIKE"
        )
    );
}
else if( $_GET['status_filter'] == "all" ){
    $meta_query_order = array(
            "relation" => "AND",
            array(
                "key" => "technician",
                "value" => get_current_user_id(),
                "compare" => "LIKE"
            ),
            array(
                "key" => "status",
                "value" => array("4","5"),
                "compare" => "NOT IN"
            )
    );
}
else if( $_GET['status_filter'] == "6" ){
    $meta_query_order = array(
        array(
            "key" => "save_as_draft",
            "value" => 1,
            "compare" => "="
        ) ,array(
            "key" => "technician",
            "value" => get_current_user_id(),
            "compare" => "LIKE"
        )
    );
}
else{
    $meta_query_order = array(
        array(
            "key" => "status",
            "value" => $_GET['status_filter'],
            "compare" => "=",
        ) ,array(
            "key" => "technician",
            "value" => get_current_user_id(),
            "compare" => "="
        )
    );
}
//$operators = get_posts( array(
//    "post_type" => "orders",
//    "posts_per_page" => -1,
//    "post_status" => "any",
//    "meta_query" => $meta_query_order,
//) );

$operators = new WP_Query( array(
    "post_type" => "orders",
    "posts_per_page" => 200,
    "post_status" => "any",
    "meta_query" => $meta_query_order,
) );




?>

    <section class="DashboardSection">

        <?=NavDashboard();?>

        <div class="main">

            <div class="content">

                <h1 class="title">لیست سفارشات</h1>
                <ul class="filter tech_filter">

                    <li <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "all"){echo 'class="active"';}?>>
                        <a href="?status_filter=all">همه <i class="all_count"><?=$total_count?></i></a>
                    </li>

                    <li <?php if(!isset($_GET['status_filter']) || $_GET['status_filter'] == "0"){echo 'class="active"';}?>>
                        <a href="?status_filter=0">جدید <i class="new_count"><?=$count_tech_order['new']?></i></a>
                    </li>

                    <li  <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "1"){echo 'class="active"';}?>>
                        <a href="?status_filter=1">هماهنگ شده <i class="sync_count"><?=$count_tech_order['sync']?></i></a>
                    </li>

                    <li  <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "2"){echo 'class="active"';}?>>
                        <a href="?status_filter=2">باز شده<i class="open_count"><?=$count_tech_order['open']?></i></a>
                    </li>

                    <li  <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "3"){echo 'class="active"';}?>>
                        <a href="?status_filter=3">نامشخص<i class="pend_count"><?=$count_tech_order['pend']?></i></a>
                    </li>
                    <li  <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "6"){echo 'class="active"';}?>>
                        <a href="?status_filter=6">پیش نویس</a>
                    </li>
                 
                    <!--
    <li  <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "4"){echo 'class="active"';}?>>
        <a href="?status_filter=4">کنسل شده<i class="cancel_count"><?=$count_tech_order['cancel']?></i></a>
    </li>

    <li  <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "5"){echo 'class="active"';}?>>
        <a href="?status_filter=5">انجام شده<i class="done_count"><?=$count_tech_order['done']?></i></a>
    </li>
    -->

                </ul>


                <form action="#" method="get" id="tech_search_filter">
                    <input value="<?php if(isset($_GET['subscription'])){echo $_GET['subscription'];}?>" type="text" class="form-control" placeholder="کد اشتراک" name="subscription">
                    <input value="<?php if(isset($_GET['mobile'])){echo $_GET['mobile'];}?>" type="text" class="form-control" placeholder="شماره موبایل" name="mobile">
                    <input value="<?php if(isset($_GET['address'])){echo $_GET['address'];}?>" type="text" class="form-control" placeholder="آدرس" name="address">
                    <input type="submit" class="btn" value="جستجو">
                </form>

<!--                <table class="table ListTable filter_tbl">-->
<!---->
<!--                    <tr class="s_0 s_1 s_2 s_3 s_4 s_5 active ">-->
<!--                        <th>کد اشتراک</th>-->
<!--                        <th>کد سفارش</th>-->
<!--                        <th>نام و نام خانوادگی</th>-->
<!--                        <th>آدرس</th>-->
<!--                        <th>نوع دستگاه</th>-->
<!--                        <th>برند</th>-->
<!--                        <th>وضعیت</th>-->
<!--                        <th>تاریخ سفارش</th>-->
<!--                        <th>عملیات</th>-->
<!--                    </tr>-->







                <?php
                    $i = 0;
                    if ( $operators->have_posts() ) {
                        $i = 0;
                        while ($operators->have_posts()) {
                            $operators->the_post();
                            $i++;
                            $subscription = get_post_meta(get_the_ID(), "subscription", true);
                            $customer = get_post_meta(get_the_ID(), "customer", true);
                            $first_name = get_user_meta($customer, "first_name", true);
                            $address = get_post_meta(get_the_ID(), "address", true);
                            $device = wp_get_post_terms(get_the_ID(), "device");
                            $brand = wp_get_post_terms(get_the_ID(), "brand");
                            $technician = get_post_meta(get_the_ID(), "technician", true);
                            $status = get_post_meta(get_the_ID(), "status", true);
                            $first_name_technician = get_user_meta($technician, "first_name", true);
                            $return_type = get_post_meta(get_the_ID(), "return_type", true);
                            $send_technician = get_post_meta(get_the_ID(), "send_technician", true);
                            $status_title = "جدید";
                            $active = '';
                            if ($status == 0 || $status == "") {
                                $active = "active";
                            }
                            if ($status == 1) {
                                $status_title = "هماهنگ شده";

                            } else if ($status == 2) {
                                $status_title = "باز شده";
                            } else if ($status == 3) {
                                $status_title = "معلق";
                            } else if ($status == 4) {
                                $status_title = "کنسل شده";
                            } else if ($status == 5) {
                                $status_title = "انجام کار";
                            }else if ($status == 6) {
                                $status_title = "پیش نویس";
                            }
                            else if ($status == 10) {
                                $status_title = "ایاب و ذهاب";
                            }
                            $return_type_text = '';
                            if ($return_type == 1) {
                                $return_type_text = ' 
                                    <li class="backOrder">
                                        <b><span class="back">سفارش برگشتی</span> </b>
                                    </li>';
                            }


                            echo '
                                    
                            
                            <div class="' . $status . ' ' . $active . '">
                            
                                <ul class=" list_of_data list_of_data_tech show_horizental">
                                 <li class="Description_Order_Order">
                                        <span>' . $status_title . '</span>
                                    
                                     
                                        <span>' . $subscription . '</span>
                                   
                                        <span>' . $first_name . '</span>
                                        </li>
                                
                                        <li class="Description_Address">   آدرس:  ' . $address . '</li>
                                  
                                      
                                    
                                        <li class="Date_Order_tech"> تاریخ سفارش:' . $send_technician . '</li> 
                                 <li class="Brand_name_Order">
                                        <span>دستگاه:' . $device[0]->name . '</span>
                                 
                                        <span> برند:' . $brand[0]->name . '</span>
                                  </li>
                                    ' . $return_type_text . '
                              
                        ';


                            echo '
                                <a class="view View-Tech" href="' . get_the_permalink(153) . '?uid=' . get_the_ID() . '">مشاهده سفارش</a>
 
                            </div>
                             </ul>
                        ';

//                        echo '
//
//
//                        <tr class="s_'.$status.' '.$active.'">
//
//                            <td>'.$subscription.'</td>
//                            <td>'.$item->ID.'</td>
//                            <td>'.$first_name.'</td>
//                            <td>'.$address.'</td>
//                            <td>'.$device[0]->name.'</td>
//                            <td>'.$brand[0]->name.'</td>
//                            <td>'.$status_title.'</td>
//                            <td>'.get_the_date("Y/m/d" , $item->ID).'</td>
//                            <td>
//                            <a href="'.get_the_permalink(153).'?uid='.$item->ID.'">مشاهده</a>
//                            </td>
//                        </tr>
//                    ';

                        }
                    }
                    ?>



                </table>

            </div>

        </div>
    </section>

<?php
get_footer();





?>
