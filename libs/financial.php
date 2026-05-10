<?php

/**
 * Financial Ajax/Helpers
 * PHP 8 safe version - original logic preserved.
 */

if (!function_exists('clearComma')) {
    function clearComma($val) {
        $value = (string) $val;
        $value = str_replace([',', '،', ' '], '', $value);
        return $value;
    }
}

if (!function_exists('fin_number')) {
    function fin_number($value) {
        $value = clearComma($value);

        $persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        $arabic  = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
        $english = ['0','1','2','3','4','5','6','7','8','9'];

        $value = str_replace($persian, $english, $value);
        $value = str_replace($arabic, $english, $value);

        if ($value === '' || !is_numeric($value)) {
            return 0;
        }

        return (float) $value;
    }
}

if (!function_exists('fin_post')) {
    function fin_post($key, $default = '') {
        return isset($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : $default;
    }
}

if (!function_exists('fin_post_raw')) {
    function fin_post_raw($key, $default = '') {
        return isset($_POST[$key]) ? wp_unslash($_POST[$key]) : $default;
    }
}

// operator
add_action('wp_ajax_addFinancialAjax', 'addFinancialAjax');
if (!function_exists('addFinancialAjax')):
    function addFinancialAjax() {

        $technician = absint(fin_post('technician'));
        $price = fin_number(fin_post('price'));
        $description = fin_post('description');

        $post_id = wp_insert_post(array(
            'post_title'  => 'پرداخت بدهی',
            'post_type'   => 'financial',
            'post_author' => $technician,
        ));

        if ($post_id && !is_wp_error($post_id)) {
            $amount = $price;

            update_post_meta($post_id, 'payment_status', 1);
            update_post_meta($post_id, 'wallet', 1);
            update_post_meta($post_id, 'wallet_pay', $price);
            update_post_meta($post_id, 'refid', $description);
            update_post_meta($post_id, 'description', $description);

            $mobile = get_user_meta($technician, 'mobile', true);

            $sms_options = get_option('sms');
            $template = (is_array($sms_options) && isset($sms_options['tech_wallet_charge']))
                ? $sms_options['tech_wallet_charge']
                : '';

            $token = get_user_meta($technician, 'first_name', true);
            $token2 = number_format($amount) . 'تومان';

            if (function_exists('SendSMS')) {
                SendSMS($mobile, $template, $token, $token2);
            }

            wp_send_json(array(
                'status'  => true,
                'message' => 'سفارش با موفقیت ویرایش گردید',
            ));
        }

        wp_send_json(array(
            'status'  => false,
            'message' => 'خطا لطفا دوباره تلاش نمایید.',
        ));
    }
endif;


// operator
add_action('wp_ajax_addFinancialInternalAjax', 'addFinancialInternalAjax');
if (!function_exists('addFinancialInternalAjax')):
    function addFinancialInternalAjax() {

        $technician = absint(fin_post('technician'));
        $price = fin_number(fin_post('price'));
        $description = fin_post('description');

        $post_id = wp_insert_post(array(
            'post_title'  => 'پرداخت بدهی',
            'post_type'   => 'financial_internal',
            'post_author' => $technician,
        ));

        if ($post_id && !is_wp_error($post_id)) {
            $amount = $price;

            update_post_meta($post_id, 'payment_status', 1);
            update_post_meta($post_id, 'wallet', 1);
            update_post_meta($post_id, 'wallet_pay', $price);
            update_post_meta($post_id, 'refid', $description);
            update_post_meta($post_id, 'description', $description);

            $mobile = get_user_meta($technician, 'mobile', true);

            $sms_options = get_option('sms');
            $template = (is_array($sms_options) && isset($sms_options['tech_wallet_charge']))
                ? $sms_options['tech_wallet_charge']
                : '';

            $token = get_user_meta($technician, 'first_name', true);
            $token2 = number_format($amount) . 'تومان';

            if (function_exists('SendSMS')) {
                SendSMS($mobile, $template, $token, $token2);
            }

            wp_send_json(array(
                'status'  => true,
                'message' => 'حقوق با موفقیت اضافه گردید',
            ));
        }

        wp_send_json(array(
            'status'  => false,
            'message' => 'خطا لطفا دوباره تلاش نمایید.',
        ));
    }
endif;


// operator
add_action('wp_ajax_addFinancialRewardAjax', 'addFinancialRewardAjax');
if (!function_exists('addFinancialRewardAjax')):
    function addFinancialRewardAjax() {

        $technician = absint(fin_post('technician'));
        $rewardType = fin_post('rewardType');
        $rewardprice = fin_number(fin_post('rewardprice'));
        $rewardDesc = fin_post('rewardDesc');

        $post_id = wp_insert_post(array(
            'post_title'  => 'فاکتور جدید',
            'post_type'   => 'financial',
            'post_author' => $technician,
        ));

        if ($post_id && !is_wp_error($post_id)) {
            update_post_meta($post_id, 'reward_type', $rewardType);
            update_post_meta($post_id, 'price_customer', 0);
            update_post_meta($post_id, 'cost_price', 0);
            update_post_meta($post_id, 'total_invoice', $rewardprice);
            update_post_meta($post_id, 'rewardDesc', $rewardDesc);
            update_post_meta($post_id, 'payment_status', 1);
        }

        wp_send_json(array(
            'status'  => true,
            'message' => 'پاداش / جریمه با موفقیت ویرایش گردید',
        ));
    }
endif;


// TECHNESIAN
add_action('wp_ajax_addInvoiceAjax', 'addInvoiceAjax');
if (!function_exists('addInvoiceAjax')):
    function addInvoiceAjax() {

        $technician = absint(fin_post('technician'));
        $price_customer = fin_number(fin_post('totalInvoice'));

        $post_id = wp_insert_post(array(
            'post_title'  => 'فاکتور جدید',
            'post_type'   => 'invoice',
            'post_author' => $technician,
        ));

        if ($post_id && !is_wp_error($post_id)) {
            update_post_meta($post_id, 'price_customer', $price_customer);
        }

        wp_send_json(array(
            'status'  => true,
            'message' => 'فاکتور با موفقیت ذخیره شد.',
        ));
    }
endif;


// operator
add_action('wp_ajax_addFinancialRewardInternalAjax', 'addFinancialRewardInternalAjax');
if (!function_exists('addFinancialRewardInternalAjax')):
    function addFinancialRewardInternalAjax() {

        $technician = absint(fin_post('technician'));
        $rewardType = fin_post('rewardType');
        $rewardprice = fin_number(fin_post('rewardprice'));
        $rewardDesc = fin_post('rewardDesc');

        $post_id = wp_insert_post(array(
            'post_title'  => 'فاکتور جدید',
            'post_type'   => 'financial_internal',
            'post_author' => $technician,
        ));

        if ($post_id && !is_wp_error($post_id)) {
            update_post_meta($post_id, 'reward_type', $rewardType);
            update_post_meta($post_id, 'price_customer', 0);
            update_post_meta($post_id, 'cost_price', 0);
            update_post_meta($post_id, 'total_invoice', $rewardprice);
            update_post_meta($post_id, 'rewardDesc', $rewardDesc);
            update_post_meta($post_id, 'payment_status', 1);
        }

        wp_send_json(array(
            'status'  => true,
            'message' => 'پاداش / جریمه با موفقیت ویرایش گردید',
        ));
    }
endif;


function loadAllDataFin() {

    $financial = get_posts(array(
        'post_type'      => 'financial',
        'posts_per_page' => -1,
        'post_status'    => 'any',
    ));

    $all_price = 0;
    $all_income = 0;
    $tech_Income = 0;
    $reward = 0;
    $penalty = 0;
    $wallet_charge_tech = 0;

    foreach ($financial as $item) {
        $uid = absint(get_post_meta($item->ID, 'order_id', true));
        $status = get_post_meta($uid, 'status', true);

        $wallet = get_post_meta($item->ID, 'wallet', true);
        $wallet_pay = fin_number(get_post_meta($item->ID, 'wallet_pay', true));
        $cost_price = fin_number(get_post_meta($item->ID, 'cost_price', true));
        $total_invoice = fin_number(get_post_meta($item->ID, 'total_invoice', true));

        $reward_type = get_post_meta($item->ID, 'reward_type', true);

        $customer = get_post_meta($uid, 'customer', true);
        $sub_customer = fin_number($customer) + 10000;

        $author = $item->post_author;
        $percent = fin_number(get_user_meta($author, 'percent', true));

        if ($status == 10) {
            $percent = 100;
        }

        if ($reward_type == 1) {
            if ($total_invoice == '' || empty($total_invoice)) {
                $total_invoice = 0;
            }

            $reward = +$reward + $total_invoice;
        }

        if ($reward_type == '0') {
            $penalty = +$penalty + $total_invoice;
        }

        if ($wallet == 1) {
            $wallet_charge_tech = +$wallet_charge_tech + $wallet_pay;
        }

        if ($reward_type == '' && $wallet == '') {
            $total_invoice_customer = ($total_invoice / 100) * $percent;
            $total_invoice_co = $total_invoice - $total_invoice_customer;

            $all_income = +$all_income + $total_invoice;
            $all_price = +$all_price + $total_invoice + $cost_price;
            $tech_Income = +$tech_Income + $total_invoice_co;
        }
    }

    return array(
        'all_price'          => $all_price,
        'all_income'         => $all_income,
        'tech_Income'        => $tech_Income,
        'reward'             => $reward,
        'penalty'            => $penalty,
        'wallet_charge_tech' => $wallet_charge_tech,
    );
}


function loadAllDataFinMonthly() {

    $financial = get_posts(array(
        'post_type'      => 'financial',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'date_query'     => array(
            array(
                'after' => '-1 month',
            ),
        ),
    ));

    $all_price = 0;
    $all_income = 0;
    $tech_Income = 0;
    $reward = 0;
    $penalty = 0;
    $wallet_charge_tech = 0;

    foreach ($financial as $item) {
        $uid = absint(get_post_meta($item->ID, 'order_id', true));

        $wallet = get_post_meta($item->ID, 'wallet', true);
        $wallet_pay = fin_number(get_post_meta($item->ID, 'wallet_pay', true));
        $cost_price = fin_number(get_post_meta($item->ID, 'cost_price', true));
        $total_invoice = fin_number(get_post_meta($item->ID, 'total_invoice', true));

        $reward_type = get_post_meta($item->ID, 'reward_type', true);

        $customer = get_post_meta($uid, 'customer', true);
        $sub_customer = fin_number($customer) + 10000;

        $author = $item->post_author;
        $percent = fin_number(get_user_meta($author, 'percent', true));

        if ($reward_type == 1) {
            if ($total_invoice == '' || empty($total_invoice)) {
                $total_invoice = 0;
            }

            $reward = +$reward + $total_invoice;
        }

        if ($reward_type == '0') {
            $penalty = +$penalty + $total_invoice;
        }

        if ($wallet == 1) {
            $wallet_charge_tech = +$wallet_charge_tech + $wallet_pay;
        }

        if ($reward_type == '' && $wallet == '') {
            $total_invoice_customer = ($total_invoice / 100) * $percent;
            $total_invoice_co = $total_invoice - $total_invoice_customer;

            $all_income = +$all_income + $total_invoice;
            $all_price = +$all_price + $total_invoice + $cost_price;
            $tech_Income = +$tech_Income + $total_invoice_co;
        }
    }

    return array(
        'all_price'          => $all_price,
        'all_income'         => $all_income,
        'tech_Income'        => $tech_Income,
        'reward'             => $reward,
        'penalty'            => $penalty,
        'wallet_charge_tech' => $wallet_charge_tech,
    );
}


function loadAllDataFinَUser($uid) {

    $financial = get_posts(array(
        'post_type'      => 'financial',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'author'         => $uid,
    ));

    $temp_per = fin_number(get_user_meta($uid, 'percent', true));
    $percent = fin_number(get_user_meta($uid, 'percent', true));
    $type_of_calc_tech = get_user_meta($uid, 'type_of_calc_tech', true);
    $tech_per_of_all = fin_number(get_user_meta($uid, 'tech_per_of_all', true));

    $all_price = 0;
    $all_income = 0;
    $tech_Income = 0;
    $reward = 0;
    $penalty = 0;
    $wallet_charge_tech = 0;

    foreach ($financial as $item) {
        $order_uid = absint(get_post_meta($item->ID, 'order_id', true));
        $status = get_post_meta($order_uid, 'status', true);

        if ($status == 10) {
            $percent = 100;
        } else {
            $percent = $temp_per;
        }

        $wallet = get_post_meta($item->ID, 'wallet', true);
        $wallet_pay = fin_number(get_post_meta($item->ID, 'wallet_pay', true));
        $cost_price = fin_number(get_post_meta($item->ID, 'cost_price', true));
        $total_invoice = fin_number(get_post_meta($item->ID, 'total_invoice', true));

        $reward_type = get_post_meta($item->ID, 'reward_type', true);

        if ($reward_type == 1) {
            if ($total_invoice == '' || empty($total_invoice)) {
                $total_invoice = 0;
            }

            $reward = +$reward + $total_invoice;
        }

        if ($reward_type == '0') {
            $penalty = +$penalty + $total_invoice;
        }

        if ($wallet == 1) {
            $wallet_charge_tech = +$wallet_charge_tech + $wallet_pay;
        }

        if ($reward_type == '' && $wallet == '') {

            if ($type_of_calc_tech == 1) {
                $tech_per = 1 - ($tech_per_of_all / 100);

                $all_income = +$all_income + $total_invoice;
                $all_price = +$all_price + $total_invoice + $cost_price;

                $total_invoice_co = ($total_invoice + $cost_price) - (($total_invoice + $cost_price) * $tech_per);
                $tech_Income = +$tech_Income + $total_invoice_co;
            } else {
                $total_invoice_customer = ($total_invoice / 100) * $percent;
                $total_invoice_co = $total_invoice - $total_invoice_customer;

                $all_income = +$all_income + $total_invoice;
                $all_price = +$all_price + $total_invoice + $cost_price;
                $tech_Income = +$tech_Income + $total_invoice_co;
            }
        }
    }

    $tech_total_debt = (+$wallet_charge_tech + $reward) - $penalty;
    $total_tech_total_debt = +$tech_total_debt - $tech_Income;

    return $total_tech_total_debt;
}


function loadAllDataFinInternal() {

    $financial = get_posts(array(
        'post_type'      => 'financial_internal',
        'posts_per_page' => -1,
        'post_status'    => 'any',
    ));

    $all_price = 0;
    $all_income = 0;
    $tech_Income = 0;
    $reward = 0;
    $penalty = 0;
    $wallet_charge_tech = 0;

    foreach ($financial as $item) {
        $uid = absint(get_post_meta($item->ID, 'order_id', true));

        $wallet = get_post_meta($item->ID, 'wallet', true);
        $wallet_pay = fin_number(get_post_meta($item->ID, 'wallet_pay', true));
        $cost_price = fin_number(get_post_meta($item->ID, 'cost_price', true));
        $total_invoice = fin_number(get_post_meta($item->ID, 'total_invoice', true));

        $reward_type = get_post_meta($item->ID, 'reward_type', true);

        $customer = get_post_meta($uid, 'customer', true);
        $sub_customer = fin_number($customer) + 10000;

        $author = $item->post_author;
        $percent = fin_number(get_user_meta($author, 'percent', true));

        if ($reward_type == 1) {
            if ($total_invoice == '' || empty($total_invoice)) {
                $total_invoice = 0;
            }

            $reward = +$reward + $total_invoice;
        }

        if ($reward_type == '0') {
            $penalty = +$penalty + $total_invoice;
        }

        if ($wallet == 1) {
            $wallet_charge_tech = +$wallet_charge_tech + $wallet_pay;
        }

        if ($reward_type == '' && $wallet == '') {
            $total_invoice_customer = ($total_invoice / 100) * $percent;
            $total_invoice_co = $total_invoice - $total_invoice_customer;

            $all_income = +$all_income + $total_invoice;
            $all_price = +$all_price + $total_invoice + $cost_price;
            $tech_Income = +$tech_Income + $total_invoice_customer;
        }
    }

    return array(
        'all_price'          => $all_price,
        'all_income'         => $all_income,
        'tech_Income'        => $tech_Income,
        'reward'             => $reward,
        'penalty'            => $penalty,
        'wallet_charge_tech' => $wallet_charge_tech,
    );
}


function loadAllDataFinInternalMontly() {

    $financial = get_posts(array(
        'post_type'      => 'financial_internal',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'date_query'     => array(
            array(
                'after' => '-1 month',
            ),
        ),
    ));

    $all_price = 0;
    $all_income = 0;
    $tech_Income = 0;
    $reward = 0;
    $penalty = 0;
    $wallet_charge_tech = 0;

    foreach ($financial as $item) {
        $uid = absint(get_post_meta($item->ID, 'order_id', true));

        $wallet = get_post_meta($item->ID, 'wallet', true);
        $wallet_pay = fin_number(get_post_meta($item->ID, 'wallet_pay', true));
        $cost_price = fin_number(get_post_meta($item->ID, 'cost_price', true));
        $total_invoice = fin_number(get_post_meta($item->ID, 'total_invoice', true));

        $reward_type = get_post_meta($item->ID, 'reward_type', true);

        $customer = get_post_meta($uid, 'customer', true);
        $sub_customer = fin_number($customer) + 10000;

        $author = $item->post_author;
        $percent = fin_number(get_user_meta($author, 'percent', true));

        if ($reward_type == 1) {
            if ($total_invoice == '' || empty($total_invoice)) {
                $total_invoice = 0;
            }

            $reward = +$reward + $total_invoice;
        }

        if ($reward_type == '0') {
            $penalty = +$penalty + $total_invoice;
        }

        if ($wallet == 1) {
            $wallet_charge_tech = +$wallet_charge_tech + $wallet_pay;
        }

        if ($reward_type == '' && $wallet == '') {
            $total_invoice_customer = ($total_invoice / 100) * $percent;
            $total_invoice_co = $total_invoice - $total_invoice_customer;

            $all_income = +$all_income + $total_invoice;
            $all_price = +$all_price + $total_invoice + $cost_price;
            $tech_Income = +$tech_Income + $total_invoice_customer;
        }
    }

    return array(
        'all_price'          => $all_price,
        'all_income'         => $all_income,
        'tech_Income'        => $tech_Income,
        'reward'             => $reward,
        'penalty'            => $penalty,
        'wallet_charge_tech' => $wallet_charge_tech,
    );
}


function loadAllDataFinَUserinternal($uid) {

    $financial = get_posts(array(
        'post_type'      => 'financial_internal',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'author'         => $uid,
    ));

    $percent = fin_number(get_user_meta($uid, 'percent', true));

    $all_price = 0;
    $all_income = 0;
    $tech_Income = 0;
    $reward = 0;
    $penalty = 0;
    $wallet_charge_tech = 0;

    foreach ($financial as $item) {
        $order_uid = absint(get_post_meta($item->ID, 'order_id', true));

        $wallet = get_post_meta($item->ID, 'wallet', true);
        $wallet_pay = fin_number(get_post_meta($item->ID, 'wallet_pay', true));
        $cost_price = fin_number(get_post_meta($item->ID, 'cost_price', true));
        $total_invoice = fin_number(get_post_meta($item->ID, 'total_invoice', true));

        $reward_type = get_post_meta($item->ID, 'reward_type', true);

        if ($reward_type == 1) {
            if ($total_invoice == '' || empty($total_invoice)) {
                $total_invoice = 0;
            }

            $reward = +$reward + $total_invoice;
        }

        if ($reward_type == '0') {
            $penalty = +$penalty + $total_invoice;
        }

        if ($wallet == 1) {
            $wallet_charge_tech = +$wallet_charge_tech + $wallet_pay;
        }

        if ($reward_type == '' && $wallet == '') {
            $total_invoice_customer = ($total_invoice / 100) * $percent;
            $total_invoice_co = $total_invoice - $total_invoice_customer;

            $all_income = +$all_income + $total_invoice;
            $all_price = +$all_price + $total_invoice + $cost_price;
            $tech_Income = +$tech_Income + $total_invoice_co;
        }
    }

    $tech_total_debt = (+$wallet_charge_tech + $reward) - $penalty;
    $total_tech_total_debt = +$tech_total_debt - $tech_Income;

    return $total_tech_total_debt;
}


// LoadDebtTechFinExternal
add_action('wp_ajax_LoadDebtTechFinExternal', 'LoadDebtTechFinExternal');
if (!function_exists('LoadDebtTechFinExternal')):
    function LoadDebtTechFinExternal() {

        $data = loadAllDataFin();

        $tech_Income = $data['tech_Income'];
        $reward = $data['reward'];
        $penalty = $data['penalty'];
        $wallet_charge_tech = $data['wallet_charge_tech'];

        $tech_total_debt = (+$wallet_charge_tech + $reward) - $penalty;
        $total_tech_total_debt = +$tech_total_debt - $tech_Income;

        if ($total_tech_total_debt > 0) {
            $fin_tech = $total_tech_total_debt;
        } else {
            $total_tech_total_debt = $total_tech_total_debt * -1;
            $fin_tech = $total_tech_total_debt;
        }

        wp_send_json(array(
            'status'  => true,
            'message' => number_format($fin_tech),
        ));
    }
endif;


// LoadDebtTechFinInternal
add_action('wp_ajax_LoadDebtTechFinInternal', 'LoadDebtTechFinInternal');
if (!function_exists('LoadDebtTechFinInternal')):
    function LoadDebtTechFinInternal() {

        $data = loadAllDataFinInternal();

        $tech_Income = $data['tech_Income'];
        $reward = $data['reward'];
        $penalty = $data['penalty'];
        $wallet_charge_tech = $data['wallet_charge_tech'];

        $tech_total_debt = (+$wallet_charge_tech + $reward) - $penalty;
        $total_tech_total_debt = +$tech_total_debt - $tech_Income;

        if ($total_tech_total_debt > 0) {
            $fin_tech = $total_tech_total_debt;
        } else {
            $total_tech_total_debt = $total_tech_total_debt * -1;
            $fin_tech = $total_tech_total_debt;
        }

        wp_send_json(array(
            'status'  => true,
            'message' => number_format($fin_tech),
        ));
    }
endif;


add_action('wp_ajax_MoveTOTrash', 'MoveTOTrash');
if (!function_exists('MoveTOTrash')):
    function MoveTOTrash() {

        $id = absint(fin_post('id'));
        $order_id = get_post_meta($id, 'order_id', true);

        $post = get_post($id);

        if (!$post) {
            wp_send_json(array(
                'status'  => false,
                'message' => 'سند یافت نشد.',
            ));
        }

        $post_type = $post->post_type . '_trash';

        wp_update_post(array(
            'ID'        => $id,
            'post_type' => $post_type,
        ));

        $post_order = get_post($id);
        $post_order_type = $post_order ? $post_order->post_type . '_trash' : $post_type . '_trash';

        $content = 'سند حسابداری حذف شد';
        $subject = 'حذف سند حسابداری';
        $status_title = 'حذف سند حسابداری';

        if (function_exists('addStatusDescription') && function_exists('jdate')) {
            addStatusDescription(
                $order_id,
                $subject,
                $content,
                get_current_user_id(),
                jdate('Y/m/d H:i'),
                $status_title
            );
        }

        wp_update_post(array(
            'ID'        => $id,
            'post_type' => $post_order_type,
        ));

        wp_send_json(array(
            'status'  => true,
            'message' => 'انتقال به زباله دان با موفقیت انجام شد',
        ));
    }
endif;


add_action('wp_ajax_editFinancialInvoiceAjax', 'editFinancialInvoiceAjax');
if (!function_exists('editFinancialInvoiceAjax')):
    function editFinancialInvoiceAjax() {

        $oid = absint(fin_post('oid'));
        $price_customer = fin_number(fin_post('price_customer'));
        $cost_price = fin_number(fin_post('cost_price'));
        $total_invoice = fin_number(fin_post('total_invoice'));
        $invoice_descripotion = fin_post('invoice_descripotion');

        if ($invoice_descripotion == '') {
            $order_id = get_post_meta($oid, 'order_id', true);

            update_post_meta($oid, 'cost_price', $cost_price);
            update_post_meta($oid, 'total_invoice', $total_invoice);

            update_post_meta($order_id, 'cost_price', $cost_price);
            update_post_meta($order_id, 'total_invoice', $total_invoice);
        } else {
            update_post_meta($oid, 'invoice_descripotion', $invoice_descripotion);
        }

        wp_send_json(array(
            'status'  => true,
            'message' => 'ویرایش با موفقیت انجام شد',
        ));
    }
endif;


add_action('wp_ajax_editFinanciaDescriptionAjax', 'editFinanciaDescriptionAjax');
if (!function_exists('editFinanciaDescriptionAjax')):
    function editFinanciaDescriptionAjax() {

        $oid = absint(fin_post('oid'));
        $invoice_descripotion = fin_post('invoice_descripotion');

        update_post_meta($oid, 'invoice_descripotion', $invoice_descripotion);

        wp_send_json(array(
            'status'  => true,
            'message' => 'ویرایش با موفقیت انجام شد',
        ));
    }
endif;


add_action('wp_ajax_editFinancialWalletAjax', 'editFinancialWalletAjax');
if (!function_exists('editFinancialWalletAjax')):
    function editFinancialWalletAjax() {

        $oid = absint(fin_post('oid'));
        $wallet_pay = fin_number(fin_post('wallet_pay'));

        update_post_meta($oid, 'wallet_pay', $wallet_pay);

        wp_send_json(array(
            'status'  => true,
            'message' => 'ویرایش با موفقیت انجام شد',
        ));
    }
endif;


add_action('wp_ajax_EditFinancialAjax', 'EditFinancialAjax');
if (!function_exists('EditFinancialAjax')):
    function EditFinancialAjax() {

        $id = absint(fin_post('id'));

        $reward_type = get_post_meta($id, 'reward_type', true);
        $wallet = get_post_meta($id, 'wallet', true);
        $wallet_pay = get_post_meta($id, 'wallet_pay', true);
        $invoice_descripotion = get_post_meta($id, 'invoice_descripotion', true);

        $return = '';

        if ($reward_type == 1) {
            // Original logic had no output for this state.
        }

        if ($reward_type == '0') {
            // Original logic had no output for this state.
        }

        if ($wallet == 1) {
            $return = '
                <form action="#" id="FinancialWalletEdit">
                    <input type="hidden" id="oid" value="' . esc_attr($id) . '">
                    <h2>ویرایش شارژ کیف پول</h2>
                    <div class="form-group">
                        <label>کل صورتحساب</label>
                        <input type="text" class="form-control" id="wallet_pay" value="' . esc_attr($wallet_pay) . '">
                    </div>

                    <input type="submit" class="btn" value="ذخیره">
                </form>
            ';
        }

        if ($reward_type == '' && $wallet == '') {
            $return = '
                <form action="#" id="FinancialInvoiceEdit">
                    <input type="hidden" id="oid" value="' . esc_attr($id) . '">
                    <h2>ویرایش فاکتور</h2>
                    <div class="form-group">
                        <label> ویرایش متن فاکتور ارسالی برای مشتری </label>
                        <textarea class="form-control" id="invoice_descripotion">' . esc_textarea($invoice_descripotion) . '</textarea>
                    </div>
                    <input type="submit" class="btn" value="ارسال مجدد فاکتور">
                </form>
            ';
        }

        wp_send_json(array(
            'status'  => true,
            'message' => $return,
        ));
    }
endif;


// filter_financial_list
add_action('wp_ajax_filter_financial_list', 'filter_financial_list_callback');
add_action('wp_ajax_nopriv_filter_financial_list', 'filter_financial_list_callback');

function filter_financial_list_callback() {

    $start_date = fin_post('start_date');
    $end_date = fin_post('end_date');

    $start_timestamp = strtotime($start_date);
    $end_timestamp = strtotime($end_date);

    if (!$start_timestamp || !$end_timestamp) {
        echo '<p>تاریخ نامعتبر است.</p>';
        wp_die();
    }

    $args = array(
        'post_type'      => 'financial',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'author'         => get_current_user_id(),
        'date_query'     => array(
            array(
                'after'     => date('Y-m-d', $start_timestamp),
                'before'    => date('Y-m-d', $end_timestamp),
                'inclusive' => true,
            ),
        ),
        'meta_query'     => array(
            array(
                'key'     => 'payment_status',
                'value'   => '3',
                'compare' => '!=',
            ),
        ),
    );

    $financial_posts = get_posts($args);

    if ($financial_posts) {
        foreach ($financial_posts as $item) {
            $uid = get_post_meta($item->ID, 'order_id', true);
            $action_status = '<span class="action_status">وضعیت سفارش</span>';

            echo '
                <ul class="list_of_data show_horizental">
                    <li>' . esc_html($uid) . '</li>
                    <li>' . $action_status . '</li>
                    <!-- باقی فیلدهای مالی و اطلاعات دیگر اینجا -->
                </ul>
            ';
        }
    } else {
        echo '<p>هیچ نتیجه‌ای یافت نشد.</p>';
    }

    wp_die();
}


add_action('wp_ajax_filter_financial_data', 'filter_financial_data_callback');
add_action('wp_ajax_nopriv_filter_financial_data', 'filter_financial_data_callback');

function filter_financial_data_callback() {

    $filter = fin_post('filter', 'all');
    $start_date = fin_post('start_date');
    $end_date = fin_post('end_date');

    $args = array(
        'post_type'      => 'financial',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
    );

    if ($filter === 'today') {
        $args['date_query'] = array(
            array(
                'year'  => date('Y'),
                'month' => date('m'),
                'day'   => date('d'),
            ),
        );
    } elseif ($filter === 'week') {
        $args['date_query'] = array(
            array(
                'after'     => date('Y-m-d', strtotime('monday this week')),
                'before'    => date('Y-m-d', strtotime('sunday this week')),
                'inclusive' => true,
            ),
        );
    } elseif ($filter === 'month') {
        $args['date_query'] = array(
            array(
                'year'  => date('Y'),
                'month' => date('m'),
            ),
        );
    } elseif ($filter === 'custom_date' && !empty($start_date) && !empty($end_date)) {
        $args['date_query'] = array(
            array(
                'after'     => $start_date,
                'before'    => $end_date,
                'inclusive' => true,
            ),
        );
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        ob_start();

        while ($query->have_posts()) {
            $query->the_post();

            echo '<div class="financial-item">';
            echo '<h3>' . esc_html(get_the_title()) . '</h3>';
            echo '<p>تاریخ: ' . esc_html(get_the_date('Y/m/d')) . '</p>';
            echo '</div>';
        }

        wp_reset_postdata();

        wp_send_json(ob_get_clean());
    } else {
        wp_send_json('<p>هیچ سفارشی یافت نشد.</p>');
    }

    wp_die();
}