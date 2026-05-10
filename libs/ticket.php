<?php
// operator
add_action( 'wp_ajax_ticket', 'ticket' );

if( !function_exists('ticket') ):

    function ticket(){

        $type_action = $_POST['type_action'];
        $uid = $_POST['uid'];
        $send_to = $_POST['send_to'];
        $operator = $_POST['operator'];
        $technician = $_POST['technician'];
        $order_ticket = $_POST['order_ticket'];
        $subject = $_POST['subject'];
        $content = $_POST['content'];
        $attachment = $_POST['attachment'];

        if($type_action == "add"){
            $ticket = wp_insert_post(array(
                'post_title'=> $subject,
                'post_type'=> 'ticket',
                'post_content' => $content
            ));
            $status = true;
            $message = "تیکت شما با موفقیت ثبت گردید.";
        }
        else if($type_action == "update"){

        }
        if($ticket){

            $add = update_post_meta($ticket,"send_to",$send_to);
            $add = update_post_meta($ticket,"sender",get_current_user_id());
            $add = update_post_meta($ticket,"operator",$operator);
            $add = update_post_meta($ticket,"technician",$technician);
            $add = update_post_meta($ticket,"order_ticket",$order_ticket);
            $add = update_post_meta($ticket,"attachment",$attachment);
            $add = update_post_meta($ticket,"ticket_status",0);

        }
        else{
            $status = false;
            $message = "خطا سیستمی ! اطلاعات دریافتی نا معتبر است.";
        }

        print json_encode(array("status" => $status , "message" => $message));
        exit();

    }
endif;add_action( 'wp_ajax_ticket', 'ticket' );

add_action( 'wp_ajax_reply_ticket', 'reply_ticket' );
if( !function_exists('reply_ticket') ):

    function reply_ticket(){


        $type_action = $_POST['type_action'];
        $uid = $_POST['uid'];
        $role = $_POST['role'];;
        $subject = $_POST['subject'];
        $content = $_POST['content'];
        $attachment = $_POST['attachment'];

            $replay = get_post_meta($uid,"reply",true);

            $replay_message = array(
                "subject" => $subject,
                "content" => $content,
                "attachment" => $attachment,
                "date" => date("Y/m/d h:s"),
                "sender" => get_current_user_id(),
                "role" => $role,

            );

            if($replay == ""){
                $array = array();
            }
            else{
                $array = $replay;
            }
            array_push($array,$replay_message);

            if($role == "operator"){
                $status = 2;
            }
            else if($role == "technician"){
                $status = 3;
            }

            $update = update_post_meta($uid,"reply",$array);
            $update = update_post_meta($uid,"ticket_status",$status);

        $status = true;
        $message = "تیکت شما با موفقیت ثبت گردید.";


        print json_encode(array("status" => $status , "message" => $message));
        exit();

    }
endif;



function Signature($uid){

    $first_name = get_user_meta($uid,"first_name",true);
    $role = get_user_meta($uid,"role",true);
    if($role == "operator"){
        $side = get_user_meta($uid,"operator",true);
    }else{
        $side = "تکنسین";
    }
    $return = '
        <span class="name">'.$first_name.'</span>
        <span class="side">'.$side.'</span>
    ';
    return $return;
}
?>