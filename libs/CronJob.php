<?php

// Cron Job Technician List
function LoadTechDetailForOrder(){
    $tech_list_status = '';
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
        $status_tech = get_user_meta($item->ID, "status", true);
        if ($status_tech !== "0") {
            $firstname_u = get_user_meta($item->ID, "first_name", true);

            $max_order = get_user_meta($item->ID, "max_order", true);
            $max_price = get_user_meta($item->ID, "max_price", true);

            $order = count(get_posts(array(
                "post_type" => "orders",
                "posts_per_page" => -1,
                "post_status" => "any",
                "meta_query" => array(
                    "relation" => "AND",
                    array(
                        "key" => "technician",
                        "value" => $item->ID,
                        "compare" => "LIKE"
                    ),
                    array(
                        "key" => "status",
                        "value" => array("0", "1", "2", "3"),
                        "compare" => "IN",
                    )
                )
            )));
            $debt = loadAllDataFinَUser($item->ID);
            if ($debt < 1) {
                $debt = $debt * -1;
            } else {
                $debt = 0;
            }
            if ($debt > $max_price || $order > $max_order) {
                $message = '(تکنسین بیش از حد مجاز سفارش یا بدهی دارد. )';

                $error = 1;
            } else {
                $message = '';
                $error = 0;
            }
            $tech_list_status.='<option
                                 data-max-order="' . $max_order . '"
                                 data-max-price="' . number_format($max_price) . '"
                                 data-now-order="' . $order . '"
                                 data-now-debt="' . number_format($debt) . '"
                                 data-error = "' . $error . '"
                                 value="' . $item->ID . '">
                                 ' . $firstname_u . '  ' . $message . '
                                 </option>';
        }
    }

    $update = update_option('tech_list_status',$tech_list_status);
}


function run_LoadTechDetailForOrder() {
    LoadTechDetailForOrder();
}

function LoadTechDetailForOrder_CronJobs($schedules) {
    $schedules['one_day'] = array(
        'interval' => 86400, // 300 seconds = 5 minutes
        'display'  => esc_html__('Every one Day'),
    );
    return $schedules;
}

add_filter('cron_schedules', 'LoadTechDetailForOrder_CronJobs');

if (!wp_next_scheduled('LoadTechDetailForOrder_run_cron_job')) {
    wp_schedule_event(time(), 'one_day', 'LoadTechDetailForOrder_run_cron_job');
}
add_action('LoadTechDetailForOrder_run_cron_job', 'run_LoadTechDetailForOrder');



// Corn Job For Technician Order Count

function LoadTechnicianOrderCount()
{
    $count_tech_order = array();
    $new_count_count = get_posts(array(
        "post_type" => "orders",
        "posts_per_page" => -1,
        "post_status" => "any",
        "meta_query" => array(
            array(
                'key' => 'status',
                'value' => array(0,1,2,3),
                'compare' => 'IN'

            )

        ),
    ));

    foreach($new_count_count as $item){

        $tech = get_post_meta($item->ID,'technician',true);
        $status = get_post_meta($item->ID,'status',true);
        if($count_tech_order[$tech] == ''){
            $count_tech_order[$tech] = array();
            if($count_tech_order[$tech]['new'] == ''){
                $count_tech_order[$tech]['new']  = 0;
            }
            if($count_tech_order[$tech]['sync'] == ''){
                $count_tech_order[$tech]['sync']  = 0;
            }
            if($count_tech_order[$tech]['open'] == ''){
                $count_tech_order[$tech]['open']  = 0;
            }
            if($count_tech_order[$tech]['pend'] == ''){
                $count_tech_order[$tech]['pend']  = 0;
            }
        }
        if($status == 0){
            $count_tech_order[$tech]['new'] = $count_tech_order[$tech]['new'] + 1;
        }
        if($status == 1){
            $count_tech_order[$tech]['sync'] = $count_tech_order[$tech]['sync'] + 1;
        }
        if($status == 2){
            $count_tech_order[$tech]['open'] = $count_tech_order[$tech]['open'] + 1;
        }
        if($status == 3){
            $count_tech_order[$tech]['pend'] = $count_tech_order[$tech]['pend'] + 1;
        }


    }
    $update = update_option('count_tech_order',$count_tech_order);

}

function run_LoadTechnicianOrderCount() {
    LoadTechnicianOrderCount();
}

function LoadTechnicianOrderCount_CronJobs($schedules) {
    $schedules['one_hour'] = array(
        'interval' => 3600, // 300 seconds = 5 minutes
        'display'  => esc_html__('Every one hour'),
    );
    return $schedules;
}
add_filter('cron_schedules', 'LoadTechnicianOrderCount_CronJobs');

if (!wp_next_scheduled('LoadTechnicianOrderCount_run_cron_job')) {
    wp_schedule_event(time(), 'one_hour', 'LoadTechnicianOrderCount_run_cron_job');
}
add_action('LoadTechnicianOrderCount_run_cron_job', 'run_LoadTechnicianOrderCount');


?>