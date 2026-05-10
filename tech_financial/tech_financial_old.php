<?php
IFLoggedAndVerify("tech_order_list");
// Template Name: Technician financial List_old

get_header();

$type_tech = get_user_meta(get_current_user_id(), "type_tech", true);
$post_type = ($type_tech == "internal") ? "financial_internal" : "financial";
?>


<section class="DashboardSection">

    <?= NavDashboard(); ?>

    <div class="main">

        <div class="content">

            <h1 class="title">لیست مالی</h1>

            <!-- فیلتر انتخاب بازه زمانی -->
            <form method="get" action="#" id="form-tech-financial-list">
                <div class="form-group half">
                    <label>از تاریخ</label>
                    <input type="text" value="<?php if(isset($_GET['date_year'])){echo $_GET['date_year'];}?>" name="date_year" placeholder="از تاریخ" id="date_year" class="form-control">
                    <input type="hidden" value="<?php if(isset($_GET['timestamp'])){echo $_GET['timestamp'];}?>" name="timestamp" id="timestamp">
                </div>
                <div class="form-group half">
                    <label>تا تاریخ</label>
                    <input value="<?php if(isset($_GET['date_year_end'])){echo $_GET['date_year_end'];}?>" type="text" name="date_year_end" placeholder="تا تاریخ" id="date_year_end" class="form-control">
                    <input value="<?php if(isset($_GET['timestamp_end'])){echo $_GET['timestamp_end'];}?>" type="hidden" name="timestamp_end" id="timestamp_end">
                </div>

                <input type="submit" class="btn" value="فیلتر">
            </form>

            <div class="style-design-tech-order">

                <div class="Row-reverce-list-order1">

                    <ul class=" list_of_data_orders head">
                        <!--  <li>#</li> -->
                        <li>وضعیت سفارش</li>
                        <li>توضیحات</li>
                        <li>هزینه کل</li>
                        <!--   <li>سهم شرکت</li>-->
                        <li>سهم تکسین</li>
                        <li>وضعیت</li>
                        <!--     <li>تاریخ ارسال کار</li> -->
                        <li>تاریخ تکمیل کار</li>
                    </ul>

                    <?php

                    // اعمال فیلتر تاریخ

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

                    $financial = get_posts(array(
                        "post_type" => $post_type,
                        "posts_per_page" => -1,
                        "post_status" => "any",
                        "author" => get_current_user_id(),
                        "date_query" => array(
                            array(
                                'after'     => $start,
                                'before'    => $end,
                                'inclusive' => true,
                            )
                        ), // افزودن فیلتر تاریخ
                        "meta_query" => array(
                            array(
                                "key" => "payment_status",
                                "value" => "3",
                                "compare" => "!=",
                            )
                        )
                    ));
                    ?>
                    <?php
                    $percent = get_user_meta(get_current_user_id(), "percent", true);
                    $i = 0;
                    $all_income = 0;
                    $all_price = 0;
                    $tech_Income = 0;
                    $reward = 0;
                    $penalty = 0;
                    $wallet_charge_tech = 0;
                    $tech_total_debt=0;

                    foreach ($financial as $item) {
                        $i++;
                        $uid = get_post_meta($item->ID, "order_id", true);
                        $price_customer = get_post_meta($item->ID, "price_customer", true);
                        $wallet = get_post_meta($item->ID, "wallet", true);
                        $wallet_pay = get_post_meta($item->ID, "wallet_pay", true);
                        $cost_price = get_post_meta($item->ID, "cost_price", true);
                        $total_invoice = get_post_meta($item->ID, "total_invoice", true);
                        $reward_type = get_post_meta($item->ID, "reward_type", true);
                        $rewardDesc = get_post_meta($item->ID, "rewardDesc", true);
                        $send_technician = get_post_meta($uid, "send_technician", true);
                        $customer = get_post_meta($uid, "customer", true);
                        $customer_first_name = get_user_meta($customer, "first_name", true);

                        if (empty($total_invoice)) {
                            $total_invoice = 0;
                        }
                        if (empty($cost_price)) {
                            $cost_price = 0;
                        }
                        $total_price = $total_invoice + $cost_price;

                        // محاسبه پاداش و جریمه
                        if ($reward_type == 1) {
                            $total_invoice = convert2english($total_invoice);
                            $reward += $total_invoice;
                            $status_payment = '<span class="plus">+ ' . number_format($total_invoice) . ' تومان</span>';
                            $type_doc_desc = '<a href="#">پاداش (' . $rewardDesc . ')</a>';
                            $total_invoice_co = 0;
                            $total_invoice_customer = $total_invoice;
                            $type_doc = '-';
                            $total_invoice_tech = $total_invoice;
                            $total_price = $total_invoice;
                            $action_status = '<span class="action_status R">ایجاد بستانکاری</span>';
                        } elseif ($reward_type == "0") {
                            $total_invoice = convert2english($total_invoice);
                            $penalty += $total_invoice;
                            $status_payment = '<span class="minus">- ' . number_format($total_invoice) . ' تومان</span>';
                            $type_doc_desc = '<a href="#">جریمه (' . $rewardDesc . ')</a>';
                            $total_invoice_customer = -$total_invoice;
                            $action_status = '<span class="action_status P">ایجاد بدهکاری</span>';
                            $total_invoice_tech = $total_invoice;
                            $type_doc = '-';
                            $total_price = $total_invoice;
                        }

                        // محاسبه شارژ کیف پول تکنسین
                        if ($wallet == 1) {
                            $wallet_charge_tech += $wallet_pay;
                            $action_status = '<span class="action_status D">کیف پول</span>';
                            $status_payment = '<span class="plus">+ ' . number_format($wallet_pay) . ' تومان</span>';
                            $type_doc_desc = '<a href="#">شارژ کیف پول</a>';
                            $total_invoice_co = $wallet_pay;
                            $type_doc = '-';
                            $total_price = $wallet_pay;
                        }

                        if ($reward_type == "" && $wallet == "") {
                            $all_income += $total_invoice;
                            $all_price += $total_invoice + $cost_price;
                            $total_invoice_customer = ($total_invoice / 100) * $percent;
                            $total_invoice_co = $total_invoice - $total_invoice_customer;
                            $tech_Income += $total_invoice_co;
                            $status_payment = '<span class="minus yellow">- ' . number_format($total_invoice_co) . ' تومان</span>';
                            $action_status = 'فاکتور ';
                            $type_doc = $customer_first_name . ' -  کد اشتراک ' . ($customer + 10000);
                            $type_doc_desc = '-';
                            $type_name_doc = $customer_first_name;
                            $type_Subscription_code = $customer + 10000;
                            $total_price = $total_invoice + $cost_price;
                            $total_invoice_tech = ($total_invoice / 100) * $percent;
                            if ($tech_total_debt > 0) {
                                $status_payment = '<span class="plus">بستانکار - ' . number_format($tech_total_debt) . ' تومان</span>';
                            } else {
                                $status_payment = '<span class="minus">بدهکار - ' . number_format(abs($tech_total_debt)) . ' تومان</span>';
                            }
                        }

                        $pure_salary_pure = ($tech_Income + $reward) - $penalty;
                        $tech_per_co = ($tech_Income / ($all_price ?: 1)) * 100;
                        $tamir_from_tech = ($all_income - $tech_Income) + ($penalty - $reward);
                        $tech_total_debt = ($wallet_charge_tech + $reward) - ($penalty + $tech_Income);
                        if ($tech_total_debt > 0) {
                            $fin_tech = number_format($tech_total_debt) . ' تومان (بستانکار)';
                            $fin_class = 'credit'; // کلاس برای بستانکار
                        } else {
                            $tech_total_debt = abs($tech_total_debt);
                            $fin_tech = number_format($tech_total_debt) . ' تومان (بدهکار)';
                            $fin_class = 'debt'; // کلاس برای بدهکار
                        }


                        echo '
                            <!--  <ul class="list_of_data head hide_in_desktop">
                                               <li>#</li>
                                                <li>وضعیت سفارش</li>
                                                <li>توضیحات</li>
                                                <li>هزینه کل</li>
                                               <li>سهم شرکت</li>
                                                <li>سهم تکسین</li>
                                                <li>وضعیت</li>
                                                 <li>تاریخ ارسال کار</li>
                                                <li>تاریخ تکمیل کار</li>
                                            </ul> -->

                             <ul class="list_of_data show_horizental show_in_mobile_techfin">

                                <span>' . $action_status . '</span>
                                  <ul>
                                     <li>نام مشتری <br>
                                     ' . $type_name_doc . '</li>
                                     <li>کد اشتراک <br>
                                     ' . $type_Subscription_code . '</li>
                                     <li>تاریخ اتمام سفارش <br>
                                    ' . get_the_date("Y/m/d", $item->ID) . '</li>
                                   </ul>

                                    <ul>
                                        <li>جمع کل صورتحساب<br>
                                         ' . number_format($all_price) . ' تومان</li>
                                        <li>هزینه ی قطعات<br>
                                         ' . number_format($cost_price) . ' تومان</li>
                                        <li>مانده کل<br>
                                         ' . number_format($total_invoice) . ' تومان</li>
                                    </ul>

                                    <ul>
                                        <li>سهم شما از سفارش<br>
                                         ' . number_format($total_invoice_tech) . ' تومان</li>
                                        <li>سهم تعمیر آنلاین<br>
                                         ' . number_format($total_invoice_co ) . ' تومان</li>
                                        <li>وضعیت<br>
                                         ' . $status_payment . '</li>

                                    </ul>



                              </ul>



                                 <ul class="list_of_data show_horizental show_in_desktop_techfin">
                                 <!--   <li>' . $i . '-' . $uid . '</li> -->
                                    <li>' . $action_status . '</li>
                                    <li>' . $type_doc . '</li>
                                    <li> ' . number_format($total_price) . ' تومان</li>
                                  <!--   <li>' . number_format($total_invoice_co) . ' تومان</li>  -->
                                    <li> ' . number_format($total_invoice_tech) . ' تومان</li>
                                    <li> ' . $status_payment . '</li>
                                  <!-- <li>' . $send_technician . '</li> -->
                                    <li> ' . get_the_date("Y/m/d", $item->ID) . '</li>
                                </ul>
                            ';
                    }

                    ?>

                    <ul class="list_tech_financial show_in_desktop_techfin">
                        <ul class="list_of_invoice header">
                            <li>مبلغ کل فاکتورها</li>
                            <li>درآمد خالص تعمیرآنلاین</li>
                            <li>درصد سودآوری</li>
                            <li>درآمد خالص تعمیرکار</li>

                        </ul>
                        <ul class="list_of_invoice price_list_style">
                            <li><?= number_format($all_price); ?> تومان</li>
                            <li><?= number_format($pure_salary_pure); ?> تومان</li>
                            <li><?= round($tech_per_co); ?> %</li>
                            <li><?=number_format($tamir_from_tech)?> تومان</li>

                        </ul>

                        <ul class="list_of_invoice header">
                            <li>شارژ کیف پول</li>
                            <li>وضعیت مالی تکنسین به مجموعه</li>
                            <li>موجودی کیف پول</li>
                        </ul>
                        <ul class="list_of_invoice">
                            <li><?= number_format($wallet_charge_tech); ?> تومان</li>
                            <li><?= number_format(($wallet_charge_tech + $reward) - ($penalty + $tech_Income)); ?> تومان</li>
                            <li><?= number_format($wallet_charge_tech); ?> تومان</li>
                        </ul>
                    </ul>
                </div>

                <div class="Row-reverce-list-order2">
                    <ul class="list_tech_financial show_in_mobile_techfin">

                        <ul class="list_of_invoice price_list_style">
                            <li><span>جمع کل فاکتورها:</span><p><?= number_format($all_price); ?> تومان</p>
                            </li>
                            <li><span>سهم تعمیرکار از سفارشات:</span><p><?= number_format($tech_Income); ?> تومان</p>
                            </li>
                            <li><span>ایجاد بدهکاری:</span><p><?= number_format($penalty); ?> تومان</p>
                            </li>
                            <li><span>ایجاد بستانکاری:</span><p><?= number_format($reward); ?> تومان</p>
                            </li>
                            <li><span>درآمد خالص تعمیرکار:</span><p><?= number_format($tamir_from_tech); ?> تومان</p>
                            </li>
                            <li><span>درصد سودآوری:</span><p><?= round($tech_per_co); ?> %</p>
                            </li>
                            <li><span>شارژ کیف پول انجام شده: </span><p><?= number_format($wallet_charge_tech); ?> تومان</p>
                            </li>
                            <li class="Reward_style">
                                <span>وضعیت حساب تعمیرکار:</span>
                                <p class="Reward_style_color <?= $fin_class; ?>"><?= $fin_tech; ?></p>
                            </li>


                            <!--                    <li>موجودی کیف پول:--><?php //= number_format($wallet_charge_tech); ?><!-- تومان</li>-->
                        </ul>
                    </ul>
                </div>
            </div>


        </div>
    </div>


</section>

<?php
get_footer();
?>
