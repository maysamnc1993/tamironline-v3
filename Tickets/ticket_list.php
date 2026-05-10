<?php
IFLoggedAndVerify("ticket_list");

// Template Name: Ticket List
get_header();
?>

    <section class="DashboardSection">

        <?=NavDashboard();?>

        <div class="main">

            <div class="content">

                <h1 class="title">لیست تیکت</h1>
                <table class="table ListTable">

                    <tr>
                        <th>#</th>
                        <th>شماره تیکت</th>
                        <th>ارسال کننده</th>
                        <th>سفارش</th>
                        <th>موضوع</th>
                        <th>تاریخ ارسال</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>

                    <?php

                    $ticket = get_posts( array(
                        "post_type" => "ticket",
                        "posts_per_page" => -1,
                        "post_status" => "any",
                        "meta_query" => array(
                                "relation" => "OR",
                                array(
                                    "key" => "sender",
                                    "value" => get_current_user_id(),
                                    "compare" => "LIKE"
                                ),
                                array(
                                    "key" => "operator",
                                    "value" => get_current_user_id(),
                                    "compare" => "LIKE"
                                ), array(
                                "key" => "operator",
                                "value" => "operator",
                                "compare" => "LIKE"
                            ),
                        ),
                        "order" => "DESC",
                        "orderby" => "date"
                    ) );
                    $i = 0;
                    foreach($ticket as $item){
                        $i++;
                        $sender = get_user_meta($item->post_author,"first_name","true");
                        $ticket_status = get_post_meta($item->ID,"ticket_status",true);
                        $order_ticket = get_post_meta($item->ID,"order_ticket",true);
                        if($order_ticket == ""){
                            $order = '-';
                        }else{
                            $order = '<a href="'.get_the_permalink(135).'?uid='.$order_ticket.'">سفارش '.$order_ticket.'</a>';
                        }
                        if($ticket_status == 0){
                            $statue = "جدید";
                        }
                        else if($ticket_status == 1){
                            $statue = "باز شده";
                        }
                        else if($ticket_status == 2){
                            $statue = "پاسخ اپراتور";
                        }
                        else if($ticket_status == 3){
                            $statue = "پاسخ تکنسین";
                        }
                        echo '
                        <tr>
                            <td>'.$i.'</td>
                            <td>'.$item->ID.'</td>
                            <td>'.$sender.'</td>
                            <td>'.$order.'</td>
                            <td>'.$item->post_title.'</td>
                            <td>'.get_the_date("Y/m/d",$item->ID).'</td>
                            <td>'.$statue.'</td>
                            <td><a href="'.get_the_permalink(167).'?uid='.$item->ID.'">مشاهده</a></td>
                           
                        </tr>
                    ';

                    }

                    ?>



                </table>

            </div>

        </div>
    </section>

<?php
get_footer();

?>