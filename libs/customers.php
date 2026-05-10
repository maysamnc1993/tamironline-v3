<?php


// operator
add_action( 'wp_ajax_customers', 'customers' );

if( !function_exists('customers') ):

    function customers(){


        $type_action = $_POST['type_action'];
        $uid = $_POST['uid'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $firstname = $_POST['firstname'];
        $mobile = $_POST['mobile'];
        $phone = $_POST['phone'];

        if($type_action == "add"){
            if(username_exists( $username ) ) {
                $status = false;
                $message = "نام کاربری وارد شده قبلا ثبت شده است";
            }
            else {
                $user = wp_create_user($username, $password, '');
                $status = true;
                $message = "مشتری با موفقیت ثبت گردید";
            }
        }
        else if($type_action == "update"){

            $user = $uid;
            if($password == ""){}else{
                $pass = wp_set_password($password,$uid);
            }
            $status = true;
            $message = "مشتری با موفقیت به روز رسانی شد.";
        }
        if($user){
            $add = update_user_meta($user,"role","customer");
            $add = update_user_meta($user,"first_name",$firstname);
            $add = update_user_meta($user,"mobile",$mobile);
            $add = update_user_meta($user,"phone",$phone);


        }
        else{
            $status = false;
            $message = "خطا سیستمی ! اطلاعات دریافتی نا معتبر است.";
        }

        print json_encode(array("status" => $status , "message" => $message));
        exit();

    }
endif;

?>