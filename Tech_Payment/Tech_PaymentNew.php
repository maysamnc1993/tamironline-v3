<?php
IFLoggedAndVerify("tech_order_list");
// Template Name: Technician Order Peyment
get_header();

if (isset($_GET['verify'])) {
    $verify = VerifyNow();
} else {
    $verify = false;
}

$Debt = loadAllDataFinَUser(get_current_user_id());

if ($Debt < 0) {
    $debt_value = $Debt * -1;
    $wallet = 0;
} else {
    $debt_value = 0;
    $wallet = $Debt;
}

$payment = get_option("payment");

?>

<section class="DashboardSection">

    <?= NavDashboard(); ?>

    <div class="main">

        <div class="content">

            <?php
            if ($verify) {

                if ($verify['status'] == true) {

                    echo '
                                    <div class="Payment success">
                                    
                                        <h3>' . $verify['message'] . '</h3>
                                        <span class="description">شماره پیگیری پرداخت : ' . $verify["refid"] . '</span>
                                    
                                    </div>
                                ';

                } else {
                    echo '
                                    <div class="Payment error">
                                    
                                        <h3 >' . $verify['message'] . '</h3>
                                        <span class="description">علت خطا بانک : ' . $verify["error_message"] . '</span>
                                    </div>
                                ';
                }

            }
            ?>

            <h1 class="title">پرداخت آنلاین</h1>

            <div class="Wallet_Detail">

                <div class="Your_wallet">
                    <span>موجود کیف پول شما</span>
                    <i><?= number_format($wallet) ?></i><b>تومان</b>
                </div>

                <div class="Your_Debt">
                    <span>بدهی</span>
                    <i><?= number_format($debt_value) ?></i><b>تومان</b>
                </div>

                <input data-debt="--><?//=$debt_value ?><!--" id="PaymentWalletNow" type="submit" class="btn"
                    value="پرداخت آنلاین">

            </div>

        </div>




        <div class="content" style="margin: 30px 0;">

            <h1 class="title">شارژ کیف پول</h1>

            <ul class="Charge_Wallet">
                <?php
                if ($payment['wallet1'] == "") {
                } else {
                    echo '
                   <li>
                      <a href="#" id="ChargeWallet" data-charge="' . $payment['wallet1'] . '" data-charge-real="' . $payment['wallet1_charge'] . '">
                          ' . number_format($payment['wallet1']) . ' تومان
                      </a>
                  </li>
              ';
                }
                if ($payment['wallet2'] == "") {
                } else {
                    echo '
                   <li>
                      <a href="#" id="ChargeWallet" data-charge="' . $payment['wallet2'] . '" data-charge-real="' . $payment['wallet2_charge'] . '">
                          ' . number_format($payment['wallet2']) . ' تومان
                      </a>
                  </li>
              ';
                }
                if ($payment['wallet3'] == "") {
                } else {
                    echo '
                   <li>
                      <a href="#" id="ChargeWallet" data-charge="' . $payment['wallet3'] . '" data-charge-real="' . $payment['wallet3_charge'] . '">
                          ' . number_format($payment['wallet3']) . ' تومان
                      </a>
                  </li>
              ';
                }
                if ($payment['wallet4'] == "") {
                } else {
                    echo '
                   <li>
                      <a href="#" id="ChargeWallet" data-charge="' . $payment['wallet4'] . '" data-charge-real="' . $payment['wallet4_charge'] . '">
                          ' . number_format($payment['wallet4']) . ' تومان
                      </a>
                  </li>
              ';
                }
                if ($payment['wallet5'] == "") {
                } else {
                    echo '
                   <li>
                      <a href="#" id="ChargeWallet" data-charge="' . $payment['wallet5'] . '" data-charge-real="' . $payment['wallet5_charge'] . '">
                          ' . number_format($payment['wallet5']) . ' تومان
                      </a>
                  </li>
              ';
                }
                ?>


            </ul>

        </div>

        <div class="content">

            <h2 class="title">پرداختی های شما</h2>
            <table class="table ListTable">
                <tr>
                    <th>#</th>
                    <th>مبلغ</th>
                    <th>تاریخ</th>
                </tr>
                <?php

                $financial = get_posts(array(
                    "post_type" => "financial",
                    "posts_per_page" => -1,
                    "post_status" => "any",
                    "author" => get_current_user_id(),
                    "meta_query" => array(
                        array(
                            "key" => "payment_status",
                            "value" => "3",
                            "compare" => "!="
                        )
                    )
                ));
                $i = 0;
                foreach ($financial as $item) {

                    $wallet = get_post_meta($item->ID, "wallet", true);
                    $wallet_pay = get_post_meta($item->ID, "wallet_pay", true);
                    if ($wallet == 1) {
                        $i++;
                        $status_payment = '<span class="plus">+ ' . number_format($wallet_pay) . ' تومان</span>';
                        echo '<tr>
                
                                    <td>' . $i . '</td>
                                    <td>' . $status_payment . '</td>
                                    <td>' . get_the_date("Y/m/d", $item->ID) . '</td>

                                </tr>';

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