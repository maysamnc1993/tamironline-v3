<?php


// operator
add_action( 'wp_ajax_operator', 'operator' );

if( !function_exists('operator') ):

    function operator(){


        $type_action = $_POST['type_action'];
        $uid = $_POST['uid'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $firstname = $_POST['firstname'];
        $operator_id = $_POST['operator_id'];
        $national_code = $_POST['national_code'];
        $mobile = $_POST['mobile'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $description = $_POST['description'];
        $operator = $_POST['operator'];
        $call = $_POST['call'];
        $price = $_POST['price'];
        $StartTimeWork = $_POST['StartTimeWork'];
        $endTimeWork = $_POST['endTimeWork'];
        $document = $_POST['document'];
        $img_profile = $_POST['img_profile'];
        $status = $_POST['status'];
        $access = $_POST['access'];

        if($type_action == "add"){
            if(username_exists( $username ) ) {
                $status = false;
                $message = "نام کاربری وارد شده قبلا ثبت شده است";
            }
            else {
                $user = wp_create_user($username, $password, '');
                $status = true;
                $message = "اپراتور با موفقیت ثبت گردید";
            }
        }
        else if($type_action == "update"){

            $user = $uid;
            if($password == ""){}else{
                $pass = wp_set_password($password,$uid);
            }
            $status = true;
            $message = "اپراتور با موفقیت به روز رسانی شد.";
        }
        if($user){
            $add = update_user_meta($user,"role","operator");
            $add = update_user_meta($user,"first_name",$firstname);
            $add = update_user_meta($user,"operator_id",$operator_id);
            $add = update_user_meta($user,"national_code",$national_code);
            $add = update_user_meta($user,"mobile",$mobile);
            $add = update_user_meta($user,"phone",$phone);
            $add = update_user_meta($user,"address",$address);
            $add = update_user_meta($user,"description",$description);
            $add = update_user_meta($user,"operator",$operator);
            $add = update_user_meta($user,"call",$call);
            $add = update_user_meta($user,"price",$price);
            $add = update_user_meta($user,"StartTimeWork",$StartTimeWork);
            $add = update_user_meta($user,"endTimeWork",$endTimeWork);
            $add = update_user_meta($user,"document",$document);
            $add = update_user_meta($user,"img_profile",$img_profile);
            $add = update_user_meta($user,"status",$status);
            $add = update_user_meta($user,"access",$access);


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