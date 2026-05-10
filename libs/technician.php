<?php


// operator
add_action( 'wp_ajax_technician', 'technician' );

if( !function_exists('technician') ):

    function technician(){


        $type_action = $_POST['type_action'];
        $uid = $_POST['uid'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $firstname = $_POST['firstname'];
        $firstname_tech = $_POST['firstname_tech'];
        $technician_id = $_POST['technician_id'];
        $national_code = $_POST['national_code'];
        $mobile = $_POST['mobile'];
        $phone = $_POST['phone'];
        $phone_force = $_POST['phone_force'];
        $address = $_POST['address'];
        $description = $_POST['description'];
        $percent = $_POST['percent'];
        $max_order = $_POST['max_order'];
        $max_price = $_POST['max_price'];
        $status_tec = $_POST['status'];
        $type_tech = $_POST['type_tech'];
        $cart_img = $_POST['cart_img'];
        $province = $_POST['province'];
        $specialty = $_POST['specialty'];
        $ready_for_derliver = $_POST['ready_for_derliver'];
        $type_of_calc_tech = $_POST['type_of_calc_tech'];
        $tech_per_of_all = $_POST['tech_per_of_all'];


        if($type_action == "add"){
            if(username_exists( $username ) ) {
                $status = false;
                $message = "نام کاربری وارد شده قبلا ثبت شده است";
            }
            else {
                $user = wp_create_user($username, $password, '');
                $status = true;
                $message = "تکنسین با موفقیت ثبت گردید";
            }
        }
        else if($type_action == "update"){

            $user = $uid;
            if($password == ""){}else{
                $pass = wp_set_password($password,$uid);
            }
            $status = true;
            $message = "تکنسین با موفقیت به روز رسانی شد.";
        }
        if($user){
            $add = update_user_meta($user,"role","technician");
            $add = update_user_meta($user,"first_name",$firstname);
            $add = update_user_meta($user,"technician_id",$technician_id);
            $add = update_user_meta($user,"national_code",$national_code);
            $add = update_user_meta($user,"firstname_tech",$firstname_tech);
            $add = update_user_meta($user,"mobile",$mobile);
            $add = update_user_meta($user,"phone",$phone);
            $add = update_user_meta($user,"phone_force",$phone_force);
            $add = update_user_meta($user,"address",$address);
            $add = update_user_meta($user,"description",$description);
            $add = update_user_meta($user,"percent",$percent);
            $add = update_user_meta($user,"max_order",$max_order);
            $add = update_user_meta($user,"max_price",$max_price);
            $add = update_user_meta($user,"status",$status_tec);
            $add = update_user_meta($user,"type_tech",$type_tech);
            $add = update_user_meta($user,"cart_img",$cart_img);
            $add = update_user_meta($user,"province",$province);
            $add = update_user_meta($user,"specialty",$specialty);
            $add = update_user_meta($user,"ready_for_derliver",$ready_for_derliver);
            $add = update_user_meta($user,"type_of_calc_tech",$type_of_calc_tech);
            $add = update_user_meta($user,"tech_per_of_all",$tech_per_of_all);

        }
        else{
            $status = false;
            $message = "خطا سیستمی ! اطلاعات دریافتی نا معتبر است.";
        }

        print json_encode(array("status" => $status , "message" => $message));
        exit();

    }
endif;

// operator
add_action( 'wp_ajax_tech_img_personal', 'tech_img_personal' );

if( !function_exists('tech_img_personal') ):

    function tech_img_personal(){


         $img_Personal = $_POST['img_Personal'];

            $add = update_user_meta(get_current_user_id(),"img_Personal",$img_Personal);

            $status = true;
            $message = "تصویر با پرسنلی با موفقیت اضافه گردید";


        print json_encode(array("status" => $status , "message" => $message));
        exit();

    }
endif;

?>