<?php
IFLoggedAndVerify("tech_order_list");
// Template Name: Internal Technician Order List
get_header();


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
}
else if(!isset($_GET['status_filter']) || $_GET['status_filter'] == "0" ){
    $meta_query_order = array(
     "relation" => "AND",
      array(
            "key" => "technician",
            "value" => get_current_user_id(),
            "compare" => "LIKE"
        ),
        array(
                "key" => "status",
                "value" => array("4","5","qc","payment_pend"),
                "compare" => "NOT IN"
        ),
    );
}
else if( $_GET['status_filter'] == "all" ){
    $meta_query_order = array(
        array(
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
            "compare" => "LIKE",
        ) ,array(
            "key" => "technician",
            "value" => get_current_user_id(),
            "compare" => "LIKE"
        )
    );
}
$operators = get_posts( array(
    "post_type" => "internal_orders",
    "posts_per_page" => -1,
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
                    <a href="?status_filter=all">
                        همه
                    </a>
                </li>
                <li <?php if(!isset($_GET['status_filter']) || $_GET['status_filter'] == "0"){echo 'class="active"';}?>>
                    <a href="?status_filter=0">در حال تعمیرات<i class="new_count"></i></a>

                <li  <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "4"){echo 'class="active"';}?>>
                    <a href="?status_filter=4">کنسل شده</a>
                </li>
                <li  <?php if(isset($_GET['status_filter']) && $_GET['status_filter'] == "5"){echo 'class="active"';}?>>
                    <a href="?status_filter=5">انجام شده </a>
                </li>

            </ul>

            <form action="#" method="get" id="tech_search">
                <input value="<?php if(isset($_GET['subscription'])){echo $_GET['subscription'];}?>" type="text" class="form-control" placeholder="کد اشتراک" name="subscription">
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

            //                    $operators = get_posts( array(
            //                        "post_type" => "orders",
            //                        "posts_per_page" => -1,
            //                        "post_status" => "any",
            //                        "meta_query" => array(
            //                            array(
            //                                "key" => "technician",
            //                                "value" => get_current_user_id(),
            //                                "compare" => "LIKE"
            //                            )
            //                        )
            //                    ) );

            $i = 0;


            foreach($operators as $item){
                $i++;
                $subscription = get_post_meta($item->ID,"subscription",true);
                $customer = get_post_meta($item->ID,"customer",true);
                $first_name = get_user_meta($customer,"first_name",true);
                $address = get_post_meta($item->ID,"address",true);
                $device = wp_get_post_terms($item->ID,"device");
                $brand = wp_get_post_terms($item->ID,"brand");
                $technician = get_post_meta($item->ID,"technician",true);
                $status = get_post_meta($item->ID,"status",true);
                $first_name_technician = get_user_meta($technician,"first_name",true);
                $return_type = get_post_meta($item->ID,"return_type",true);
                $send_technician = get_post_meta($item->ID,"send_technician",true);
                $status_title = "جدید";
                $active = '';
                if($status == 0 || $status == ""){
                    $active = "active";
                    $status_show = '';
                }
                if($status == 1){
                    $status_title = "هماهنگ شده";
                    $status_show = '';

                }
                else if($status == 2){
                    $status_title = "باز شده";
                    $status_show = '';
                }
                else if($status == 3){
                    $status_title = "نامشخص";
                    $status_show = '';
                }
                else if($status == 4){
                    $status_title = "کنسل شده";
                    $status_show = '<span class="unsync">کنسل شد</span>';

                }
                else if($status == 5){
                    $status_title = "انجام کار";
                    $status_show = '<span class="sync">انجام شد</span>';

                }

                $return_type_text = '';
                if($return_type == 1){
                    $return_type_text = ' 
                                    <li class="backOrder">
                                        <b><span class="back">سفارش برگشتی</span> </b>
                                    </li>';
                }


                echo '
                            <div class="itemOrderBox s_'.$status.' '.$active.'">
                            
                                <ul class="header_order">
                                    <li class="sub">
                                        <span>کد اشتراک</span>
                                        <b>'.$subscription.'</b>
                                    </li>
                                    <li class="oid">
                                        <span>نام مشتری</span>
                                        <b>'.$first_name.'</b>
                                    </li>
                                     <li class="device">
                                        <span>وضعیت</span>
                                        <b>'.$status_title.'</b>
                                    </li>
                                     <li class="device">
                                        <span>تاریخ سفارش</span>
                                        <b>'.get_the_date("Y/m/d" , $item->ID).'</b>
                                    </li>
                                    <li class="device">
                                        <span>تاریخ ارسال سفارش</span>
                                        <b>'.$send_technician.'</b>
                                    </li>
                                    <li class="device">
                                        <span>دستگاه</span>
                                        <b>'.$device[0]->name.'</b>
                                    </li>
                                    <li class="device">
                                        <span>برند</span>
                                        <b>'.$brand[0]->name.'</b>
                                    </li>
                                    <li class="address">
                                        <span>آدرس</span>
                                        <b>'.$address.'</b>
                                    </li>
                                    <li><b>'.$status_show.'</b></li>
                                    '.$return_type_text.'
                                </ul>
                        ';
                echo '
                                <a class="view" href="'.get_the_permalink(14278).'?uid='.$item->ID.'">مشاهده</a>
 
                            </div>
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

            ?>



            </table>

        </div>

    </div>
</section>

<?php
get_footer();


$new_count_count = new WP_Query(array(
    "post_type" => "internal_orders",
    "posts_per_page" => -1,
    "post_status" => "any",
    "meta_query" => array(
        "relation" => "AND",
        array(
            "key" => "status",
            "value" => "0",
            "compare" => "LIKE",
        ),
        array(
            "key" => "technician",
            "value" => get_current_user_id(),
            "compare" => "LIKE"
        )
    ),
));
$new_count = $new_count_count->found_posts;


$sync_count_count = new WP_Query(array(
    "post_type" => "internal_orders",
    "posts_per_page" => -1,
    "post_status" => "any",
    "meta_query" => array(
        "relation" => "AND",
        array(
            "key" => "status",
            "value" => "1",
            "compare" => "LIKE",
        ),
        array(
            "key" => "technician",
            "value" => get_current_user_id(),
            "compare" => "LIKE"
        )
    ),
));
$sync_count = $sync_count_count->found_posts;

$open_count_count = new WP_Query(array(
    "post_type" => "internal_orders",
    "posts_per_page" => -1,
    "post_status" => "any",
    "meta_query" => array(
        "relation" => "AND",
        array(
            "key" => "status",
            "value" => "2",
            "compare" => "LIKE",
        ),
        array(
            "key" => "technician",
            "value" => get_current_user_id(),
            "compare" => "LIKE"
        )
    ),
));
$open_count = $open_count_count->found_posts;


$pend_count_count = new WP_Query(array(
    "post_type" => "internal_orders",
    "posts_per_page" => -1,
    "post_status" => "any",
    "meta_query" => array(
        "relation" => "AND",
        array(
            "key" => "status",
            "value" => "3",
            "compare" => "LIKE",
        ),
        array(
            "key" => "technician",
            "value" => get_current_user_id(),
            "compare" => "LIKE"
        )
    ),
));
$pend_count = $pend_count_count->found_posts;


?>
<script>

    $(document).ready(function (){
        $(".filter li i.new_count").html("<?=$new_count?>");
        $(".filter li i.sync_count").html("<?=$sync_count?>");
        $(".filter li i.open_count").html("<?=$open_count?>");
        $(".filter li i.pend_count").html("<?=$pend_count?>");
    })

</script>
