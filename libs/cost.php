<?php
// operator
add_action( 'wp_ajax_addCostAjax', 'addCostAjax' );
if( !function_exists('addCostAjax') ):
    function addCostAjax(){

        $cost_type = $_POST['cost_type'];
        $cost_desc = $_POST['cost_desc'];
        $cost_price = $_POST['cost_price'];
        $document = $_POST['document'];
        $document_2 = $_POST['document_2'];

        $post_id = wp_insert_post(array(
            'post_title'=>'هزینه جدید',
            'post_type'=>'cost',
        ));
        if($post_id){

            $add = update_post_meta($post_id,"cost_type",$cost_type);
            $add = update_post_meta($post_id,"cost_desc",$cost_desc);
            $add = update_post_meta($post_id,"cost_price",$cost_price);
            $add = update_post_meta($post_id,"document",$document);
            $add = update_post_meta($post_id,"document_2",$document_2);

        }

        $status = true;
        $message = "هزینه با موفقیت ثبت گردید";
        print json_encode(array("status" => $status , "message" => $message));
        exit();

    }
endif;

add_action( 'wp_ajax_LoadcostTotal', 'LoadcostTotal' );
if( !function_exists('LoadcostTotal') ):
    function LoadcostTotal(){

        $start = $_POST['start'];
        $end = $_POST['end'];


        $cost_array = array(
            "post_type" => "cost",
            "posts_per_page" => -1,
            "post_status" => "any",
        );

        if($start == "" && $end == ''){}else{

            $d = date("d",($start / 1000));
            $m = date("m",($start / 1000));
            $y = date("Y",($start/ 1000));
            $time = "0";

            $d_end = date("d",($end / 1000));
            $m_end = date("m",($end / 1000));
            $y_end = date("Y",($end / 1000));
            $time_end = "23";
            $start_d = array(
                'year'  => $y,
                'month' => $m,
                'day'   => $d,
                'hour' => $time
            );
            $end_d = array(
                'year'  => $y_end,
                'month' => $m_end,
                'day'   => $d_end,
                'hour' => $time_end
            );

            $cost_array["date_query"] = array(
                array(
                    'after'     => $start_d,
                    'before'    => $end_d,
                    'inclusive' => true,
                )
            );

        }
        $cost = get_posts($cost_array);
        $total_price = 0;
        foreach($cost as $item){
            $cost_price = get_post_meta($item->ID,"cost_price",true);
            $total_price = +$total_price + $cost_price;
        }
        print json_encode(array("status" => true, "price" => number_format($total_price)));
        exit();
    }
endif;

?>