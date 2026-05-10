<?php
IFLoggedAndVerify("tech_debt");
// Template Name: Debt Tech List
get_header();
?>

<section class="DashboardSection">

    <?=NavDashboard();?>

    <div class="main">

        <div class="content">

            <h1 class="title">لیست بدهی تکنسین ها</h1>
            <table class="table ListTable">

                <tr>
                    <th>#</th>
                    <th>نام و نام خانوادگی</th>
                    <th>وضعیت</th>
                    <th>(تومان)بستانکار/بدهکار</th>
                </tr>

                <?php
                $operators = get_users(array(
                    'meta_key' => 'role',
                    'meta_value' => 'technician',
                    'meta_compare' => '=' // exact match only
                ));

                $i = 0;
                $totalDebt = 0; // Variable to keep the sum of debts

                foreach ($operators as $item) {
                    $i++;
                    $firstname = get_user_meta($item->ID, "first_name", true);
                    $mobile = get_user_meta($item->ID, "mobile", true);
                    $percent = get_user_meta($item->ID, "percent", true);
                    $address = get_user_meta($item->ID, "address", true);
                    $img_Personal = get_user_meta($item->ID, "img_Personal", true);
                    $type_tech = get_user_meta($item->ID, "type_tech", true);
                    $debt = loadAllDataFinَUser($item->ID);

                    $totalDebt += $debt; // Add the current debt to the total

                    if ($debt < 1) {
                        $class = 'red';
                        $status = 'بدهکار';
                    } else {
                        $class = 'green';
                        $status = 'بستانکار';
                    }

                    if ($type_tech == "internal") {
                        $t_type = "تکنسین داخلی";
                    } else {
                        $t_type = "تکنسین خارجی";
                    }

                    if ($img_Personal == '') {
                        $dl = '';
                    } else {
                        $dl = '<a href="' . $img_Personal . '" target="_blank" class="download">تصویر</a>';
                    }

                    echo '
                    <tr class="' . $class . '">
                        <td>' . $i . '</td>
                        <td>' . $firstname . '</td>
                        <td>' . $status . '</td>
                        <td>' . number_format($debt) . '</td>
                    </tr>
                    ';
                }
                ?>

                <!-- Add a row for the total sum -->
                <tr class="total">
                    <td colspan="3"><strong>مجموع</strong></td>
                    <td><strong><?= number_format($totalDebt) ?></strong></td>
                </tr>

            </table>

        </div>

    </div>
</section>

<?php
get_footer();
?>
