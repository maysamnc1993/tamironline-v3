<?php
function IFLoggedAndVerify($exp){

    if(is_user_logged_in()){

        $active_temp = get_user_meta(get_current_user_id(),"active_temp",true);
        $role = get_user_meta(get_current_user_id(),"role",true);
        $cart_img = get_user_meta($current_user_id, "cart_img", true);
        if($role == "operator"){
            $href = get_the_permalink(142);
            $access = get_user_meta(get_current_user_id(),"access",true);
            array_push($access,"dashboard");
        }
        else{
            $href =  get_the_permalink(146);
            $access = array('dashboard_tech','tech_order_list');
        }

        if($exp == "Login"){

            echo '<script>window.location.href = "'.$href.'"</script>';
        }
        else if($active_temp == 0 && $exp !== "Verify"){
            echo '<script>window.location.href = "/TamirOnline/verify"</script>';
            
        }
        else if(!in_array($exp,$access) && $exp !== "Verify" ){
            echo '<script>window.location.href = "'.$href.'"</script>';
        }
        else{
        }
    }
    else{
        if($exp == "Login"){}else{
            echo '<script>window.location.href = "/"</script>';

        }
    }

}
function CheckAccess($ac){
    $access = get_user_meta(get_current_user_id(),"access",true);
    if(in_array($ac,$access)){
        return true;
    }
    else{
        return false;
    }
}
function NavDashboard(){

    $role = get_user_meta(get_current_user_id(),"role",true);
    if($role == "operator"){
        $menu = ListOFMenu();
        $contact_list = '
        <div class="contactListTechnician">
            <div class="ContactList">
       
                <input type="text" class="form-control" placeholder="نام تکسین" id="searchTech">
                <ul class="tech_list">
                   ';
        $operators = get_users( array(
            'meta_key' => 'role',
            'meta_value' => 'technician',
            'meta_compare' => '=' // exact match only
        ) );
        $i = 0;
        foreach($operators as $item) {
            $i++;
            $firstname = get_user_meta($item->ID, "first_name", true);
            $mobile = get_user_meta($item->ID, "mobile", true);
            $contact_list.= '<li>
                                <span>'.$firstname.'</span>
                                <b>'.convert2english($mobile).'</b>
                            </li>';
        }
        $contact_list.='
                </ul>
            </div>
        </div>
        ';
    }
    else{
        $menu = Tech_List_of_menu();
        $contact_list = ' ';
    }

    $return = '
        '.$contact_list.'
        <nav>
       <div class="DashboardLogosite"><img src="'.wp_get_attachment_url(117719).'" id="DashboardLogo"> </div>
            '.ShowUserDetail().'
                <div class="MobileMenu"> فهرست  <img src="'.wp_get_attachment_url(118117).'" id="DashboardLogo"></div>
            <div class="List_Of_Menu">
                '.$menu.'
                
            </div>
        </nav>
    ';

    return $return;

}

function ShowUserDetail(){
    $current_user_id = get_current_user_id();
    $role = get_user_meta(get_current_user_id(),"role",true);
    $firstname_tech = get_user_meta(get_current_user_id(),"firstname_tech",true);
    $cart_img = get_user_meta($current_user_id, "cart_img", true);
    if($role == "operator"){
        $side = get_user_meta(get_current_user_id(),"operator",true);
    }
    else{
        $side = 'تکنسین';
    }
    $return = '
    <div class="detail_user">
        <div class="user-right">
            <img src="' . $cart_img . '"> </div>
        <div class="user-left"> 
            <span class="name">' . $firstname_tech . '</span>
            <!-- <span class="side">' . $side . '</span> -->
        </div>
    </div>
';

    return $return;
}

function ListOFMenu(){

    $access = get_user_meta(get_current_user_id(),"access",true);


    $return = '<ul>';


    // Dashboard
    $return.= '
            <li class="">
                <a href="'.get_the_permalink(142).'">
                      <i class="las la-tachometer-alt"></i>
                    <span>داشبورد</span>
                    
                </a>
            </li>
            ';

    if(in_array("search_technician",$access)){
        $return.= '
            <li>
            
                <a href="#" id="SearchTech">
                    <i class="las la-user-tie"></i>
                    <span>جستجوی تکنسین</span>
                </a>
                
                ';
    }
    // Order list
    if(in_array("order_list",$access)){
//        $return.= '
//            <li>
//
//                <a href="'.get_the_permalink(108).'?status_filter=0">
//                  <i class="las la-tools"></i>
//                    <span>سفارش های خارجی</span>
//                    <i class="las la-angle-down"></i>
//                </a>
//                <ul class="sub_menu">
//                ';

        // Add Operator
        if(in_array("add_order",$access)){

            $return.='
                <li>
                    <a href="'.get_the_permalink(110).'">
                        <i class="las la-plus"></i>
                        <span>افزودن سفارش جدید</span>
                    </a>
                </li>
                <li>
                <a href="'.get_the_permalink(108).'?status_filter=0">
                    <i class="las la-list-alt"></i>
                    <span>لیست سفارش ها</span>
                </a>
</li>
            ';

        }

//        $return.='
//                </ul>
//            </li>
//        ';
    }


//    if(in_array("show_happyCall",$access)){
//        $return.= '
//            <li>
//
//                <a href="#">
//                    <i class="las la-smile"></i>
//                    <span>هپی کال</span>
//                    <i class="las la-angle-down"></i>
//                </a>
//                <ul class="sub_menu">
//                ';
//
//        // Add Operator
//        if(in_array("show_happyCall_external",$access)){
//
//            $return.='
//                <li>
//                    <a href="'.get_the_permalink(38786).'?status_filter=unarchive">
//                        <i class="las la-smile-beam"></i>
//                        <span>هپی کال سفارشات خارجی</span>
//                    </a>
//                </li>
//                <li>
//            ';
//
//        }
//        if(in_array("show_happyCall_internal",$access)){
//
//            $return.='
//                <li>
//                    <a href="'.get_the_permalink(38788).'?status_filter=5">
//                        <i class="las la-smile-beam"></i>
//                        <span>هپی کال سفارشات داخلی</span>
//                    </a>
//                </li>
//                <li>
//            ';
//
//        }
//
//        $return.='
//                </ul>
//            </li>
//        ';
//    }
//
//
//    // Ticket list
//    if(in_array("ticket_list",$access)){
//        $return.= '
//            <li>
//                <a href="'.get_the_permalink(138).'">
//                    <i class="las la-comments"></i>
//                    <span>تیکت ها</span>
//                    <i class="las la-angle-down"></i>
//                </a>
//                <ul class="sub_menu">
//                ';
//        // Add Operator
//        if(in_array("add_ticket",$access)){
//            $return.='
//                <li>
//                    <a href="'.get_the_permalink(140).'">
//                        <i class="las la-plus"></i>
//                        <span>افزودن تیکت</span>
//                    </a>
//                </li>
//                <li>
//                <a href="'.get_the_permalink(138).'">
//                   <i class="las la-comments"></i>
//                    <span>لیست تیکت ها</span>
//                </a>
//</li>
//            ';
//
//        }
//
//        $return.='
//                </ul>
//            </li>
//        ';
//    }

//    // financial
//    if(in_array("list_cost",$access)) {
//        $return .= '
//            <li>
//
//                <a href="' . get_the_permalink(7318) . '">
//                    <i class="las la-money-bill"></i>
//                    <span>هزینه ها</span>
//                    <i class="las la-angle-down"></i>
//                </a>
//                <ul class="sub_menu">
//                ';
//
//        if (in_array("add_cost", $access)) {
//
//            $return .= '
//                <li>
//                    <a href="' . get_the_permalink(7315) . '">
//                        <i class="las la-plus"></i>
//                        <span>افزودن هزینه</span>
//                    </a>
//                </li>
//                <li>
//                <a href="' . get_the_permalink(7318) . '">
//                    <i class="las la-list-alt"></i>
//                    <span>لیست هزینه</span>
//                </a>
//</li>
//            ';
//
//        }
//
//        $return .= '
//                </ul>
//            </li>
//        ';
//    }
    // financial
    if(in_array("list_financial",$access)){
        $return.= '
            <li>
            
                <a href="'.get_the_permalink(206).'">
                    <i class="las la-calculator"></i>
                    <span>امور مالی و حسابداری</span>
                    <i class="las la-angle-down"></i>
                </a>
                <ul class="sub_menu">
                ';

        // Add Operator
        if(in_array("add_financial",$access)){

            $return.='
                <li>
                    <a href="'.get_the_permalink(208).'">
                        <i class="las la-plus"></i>
                        <span>افزودن فاکتور حسابداری</span>
                    </a>
                </li>
                <li>
                <a href="'.get_the_permalink(206).'">
                    <i class="las la-list-alt"></i>
                    <span>لیست حسابداری</span>
                </a>
</li>
            ';

        }        // Add Operator
        if(in_array("tech_debt",$access)){

            $return.='
                <li>
                    <a href="'.get_the_permalink(26843).'">
                        <i class="las la-list-alt"></i>
                        <span>لیست بدهی تکنسین ها</span>
                    </a>
                </li>
            ';

        }

        $return.='
                </ul>
            </li>
        ';
    }




    if(in_array("hr_list",$access)){
        $return.='
            <li class="">
                <a href="'.get_the_permalink(142).'">
                    <i class="las la-users"></i>
                    <span>منابع انسانی</span>
                    <i class="las la-angle-down"></i>
                </a>
                <ul class="sub_menu zero_sub">
            
                ';
        // Operator List
        if(in_array("operator_list",$access)){
            $return.= '
            <li>
            
                <a href="'.get_the_permalink(88).'">
                   
                    <span>اپراتورها</span>
                </a>
                <ul class="sub_menu">
                ';

            // Add Operator
            if(in_array("add_operator",$access)){

                $return.='
                <li>
                    <a href="'.get_the_permalink(90).'">
                       <i class="las la-plus"></i>
                        <span>افزودن اپراتور</span>
                    </a>
                </li>
                <li>
                <a href="'.get_the_permalink(88).'">
                   <i class="las la-list-alt"></i>
                    <span>لیست اپراتورها</span>
                </a>
</li>
            ';

            }

            $return.='
                </ul>
            </li>
        ';
        }
        // Search Technician

        // Technician list
        if(in_array("technician_list",$access)){
            $return.= '
            <li>
            
                <a href="'.get_the_permalink(98).'">
                    
                    <span>تکنسین ها</span>
                </a>
                <ul class="sub_menu">
                ';

            // Add Operator
            if(in_array("add_technician",$access)){

                $return.='
                <li>
                    <a href="'.get_the_permalink(100).'">
                        <i class="las la-plus"></i>
                        <span>افزودن تکنسین</span>
                    </a>
                </li>
                <li>
                <a href="'.get_the_permalink(98).'">
                    <i class="las la-plus"></i>
                    <span>لیست تکنسین ها</span>
                </a>
</li>
            ';

            }

            if(in_array("point_technician",$access)){

                $return.='
                <li>
                    <a href="'.get_the_permalink(38790).'">
                        
                        <span>امتیازات تکنسین</span>
                    </a>
                </li>

            ';

            }

            $return.='
                </ul>
            </li>
        ';
        }

        // Customer list
        if(in_array("customer_list",$access)){
            $return.= '
            <li>
            
                <a href="'.get_the_permalink(103).'">
                    
                    <span>مشتری ها</span>
                </a>
                <ul class="sub_menu">
                ';

            // Add Operator
            if(in_array("add_customer",$access)){

                $return.='
                <li>
                    <a href="'.get_the_permalink(105).'">
                        <i class="las la-plus"></i>
                        <span>افزودن مشتری</span>
                    </a>
                </li>
                <li>
                <a href="'.get_the_permalink(103).'">
                   <i class="las la-plus"></i>
                    <span>لیست مشتری ها</span>
                </a>
</li>
            ';

            }

            $return.='
                </ul>
            </li>
        ';
        }

        $return.='
        </ul>
        </li>
    ';
    }
    // Setting
    if(in_array("setting",$access)){
        $return.= '
            <li>
                <a href="'.get_the_permalink(186).'">
                   <i class="las la-bars"></i>
                    <span>تنظیمات</span>
                    <i class="las la-angle-down"></i>
                </a>
                <ul class="sub_menu">
                ';
        // Add Operator
        if(in_array("sms_setting",$access)){
            $return.='
                <li>
                    <a href="'.get_the_permalink(189).'">
                        <i class="las la-bars"></i>
                        <span>تنظیمات پیامک</span>
                    </a>
                </li>
            ';

        }
        if(in_array("payment_setting",$access)){
            $return.='
                <li>
                    <a href="'.get_the_permalink(199).'">
                        <i class="las la-bars"></i>
                        <span>تنظیمات پرداخت</span>
                    </a>
                </li>
            ';

        }
        if(in_array("accounting_setting",$access)){
            $return.='
                <li>
                    <a href="'.get_the_permalink(1009).'">
                        <i class="las la-bars"></i>
                        <span>تنظیمات حسابداری</span>
                    </a>
                </li>
            ';

        }
        if(in_array("cancel_setting",$access)){
            $return.='
                <li>
                    <a href="'.get_the_permalink(257).'">
                       <i class="las la-bars"></i>
                        <span>تنظیمات کنسلی</span>
                    </a>
                </li>
            ';

        }
        if(in_array("objection_setting",$access)){
            $return.='
                <li>
                    <a href="'.get_the_permalink(296).'">
                        <i class="las la-bars"></i>
                        <span>تنظیمات ایرادات</span>
                    </a>
                </li>
            ';

        }
        if(in_array("introduction_setting",$access)){
            $return.='
                <li>
                    <a href="'.get_the_permalink(7055).'">
                        <i class="las la-bars"></i>
                        <span>تنظیمات نحوه آشنایی</span>
                    </a>
                </li>
            ';


        }
        if(in_array("invoice_setting",$access)){
            $return.='
                <li>
                    <a href="'.get_the_permalink(13407).'">
                        <i class="las la-bars"></i>
                        <span>تنظیمات فاکتور</span>
                    </a>
                </li>
            ';
        }
        if(in_array("setting_cost",$access)){
            $return.='
                <li>
                    <a href="'.get_the_permalink(7313).'">
                        <i class="las la-bars"></i>
                        <span>تنظیمات هزینه ها </span>
                    </a>
                </li>
            ';

        }
//        if(in_array("shipping_setting",$access)){
//            $return.='
//                <li>
//                    <a href="'.get_the_permalink(14246).'">
//                        <i class="las la-bars"></i>
//                        <span>تنظیمات نحوه ارسال </span>
//                    </a>
//                </li>
//            ';
//
//        }
//        if(in_array("happycall_tech_setting",$access)){
//            $return.='
//                <li>
//                    <a href="'.get_the_permalink(37774).'">
//                        <i class="las la-bars"></i>
//                        <span>تنظیمات هپی کال تکنسین </span>
//                    </a>
//                </li>
//            ';
//
//        }
//
//        if(in_array("happycall_customer_setting",$access)){
//            $return.='
//                <li>
//                    <a href="'.get_the_permalink(37772).'">
//                        <i class="las la-bars"></i>
//                        <span>تنظیمات هپی کال مشتریان </span>
//                    </a>
//                </li>
//            ';
//
//        }



    if(in_array("order_setting",$access)){
        $return.='
    <li>
        <a href="'.get_the_permalink(112).'">
        <i class="las la-sliders-h"></i>
            <span>تنظیمات سفارش</span>
            <i class="las la-angle-down"></i>
        </a>
        <ul class="sub_menu zero_sub">
    ';

        // brand list
        if(in_array("brand_list",$access)){
            $return.= '
            <li>
            
                <a href="'.get_the_permalink(112).'">
                    
                    <span>برند ها</span>
                </a>
                <ul class="sub_menu">
                ';

            // Add Operator
            if(in_array("add_brand",$access)){

                $return.='
                <li>
                    <a href="'.get_the_permalink(114).'">
                       <i class="las la-plus"></i>
                        <span>افزودن برند</span>
                    </a>
                </li>
                <li>
                <a href="'.get_the_permalink(112).'">
                    <i class="las la-list-alt"></i>
                    <span>لیست برند ها</span>
                </a>
</li>
            ';

            }

            $return.='
                </ul>
            </li>
        ';
        }

        // device list
        if(in_array("device_list",$access)){
            $return.= '
            <li>
            
                <a href="'.get_the_permalink(117).'">
                    
                    <span>دستگاه ها</span>
                </a>
                <ul class="sub_menu">
                ';

            // Add Operator
            if(in_array("add_device",$access)){

                $return.='
                <li>
                    <a href="'.get_the_permalink(120).'">
                       <i class="las la-plus"></i>
                        <span>افزودن دستگاه</span>
                    </a>
                </li>
                <li>
                <a href="'.get_the_permalink(117).'">
                    <i class="las la-list-alt"></i>
                    <span>لیست دستگاه ها</span>
                </a>
</li>
            ';

            }

            $return.='
                </ul>
            </li>
        ';
        }

        // state list
        if(in_array("state_list",$access)){
            $return.= '
            <li>
            
                <a href="'.get_the_permalink(123).'">
                    
                    <span>استان ها</span>
                </a>
                <ul class="sub_menu">
                ';

            // Add Operator
            if(in_array("add_state",$access)){

                $return.='
                <li>
                    <a href="'.get_the_permalink(125).'">
                        <i class="las la-plus"></i>
                        <span>افزودن دستگاه</span>
                    </a>
                </li>
                <li>
                <a href="'.get_the_permalink(123).'">
                    <i class="las la-list-alt"></i>
                    <span>لیست استان ها</span>
                </a>
</li>
            ';

            }

            $return.='
                </ul>
            </li>
        ';
        }

        // City list
        if(in_array("city_list",$access)){
            $return.= '
            <li>
            
                <a href="'.get_the_permalink(128).'">
                    
                    <span>مناطق</span>
                </a>
                <ul class="sub_menu">
                ';

            // Add Operator
            if(in_array("add_city",$access)){

                $return.='
                <li>
                    <a href="'.get_the_permalink(131).'">
                        <i class="las la-plus"></i>
                        <span>افزودن منطقه</span>
                    </a>
                </li>
                <li>
                <a href="'.get_the_permalink(128).'">
                    <i class="las la-list-alt"></i>
                    <span>لیست منطقه ها</span>
                </a>
</li>
            ';

            }

            $return.='
                </ul>
            </li>
        ';
        }
        $return.='</ul></li>';
    }
        $return.='
                </ul>
            </li>
        ';
    }

    if(in_array("order_list_internal",$access)){
        $return.= '
            <li>
                <a href="#">
                    <i class="las la-wrench"></i>
                    <span>پنل سفارشات داخلی</span>
                    <i class="las la-angle-down"></i>
                </a>
            
            <ul class="sub_menu">
            <li>
            
                <a href="'.get_the_permalink(14233).'?status_filter=0">
                   <i class="las la-wrench"></i>
                    <span>سفارش های داخلی</span>
                    <i class="las la-angle-down"></i>
                </a>
                <ul class="sub_menu">
                ';

        // Add Operator
        if(in_array("add_order_internal",$access)){

            $return.='
                <li>
                    <a href="'.get_the_permalink(14235).'">
                    <i class="las la-plus"></i>
                        <span>افزودن سفارش</span>
                    </a>
                </li>
                <li>
                <a href="'.get_the_permalink(14233).'?status_filter=0">
                <i class="las la-list-alt"></i>
                    <span>لیست سفارش ها</span>
                </a>
</li>
            ';

        }

        $return.='
                </ul>
            </li>
        ';

    // financial
    if(in_array("list_financial_internal",$access)){
        $return.= '
            <li>
            
                <a href="'.get_the_permalink(15892).'">
                    <i class="las la-calculator"></i>
                    <span> حسابداری داخلی </span>
                    <i class="las la-angle-down"></i>
                </a>
                <ul class="sub_menu">
                ';

        // Add Operator
        if(in_array("add_financial_internal",$access)){

            $return.='
                <li>
                    <a href="'.get_the_permalink(15894).'">
                        <i class="las la-plus"></i>
                        <span>افزودن فاکتور حسابداری</span>
                    </a>
                </li>
                            ';

        }
        if(in_array("list_financial_internal",$access)){

            $return.='
                <li>
                <a href="'.get_the_permalink(15892).'">
                    <i class="las la-list-alt"></i>
                    <span>لیست حسابداری داخلی</span>
                </a>
</li>
            ';

        }

        $return.='
                </ul>
            </li>
        ';
    }
    $return.='
                </ul>
            </li>
        ';
    }
    // Logout
    $return.= '
            <li>
                <a href="#" id="Exit">
                    <i class="las la-sign-out-alt"></i>
                    <span>خروج</span>
                </a>
            </li>
                ';

    $return.= '</ul>';

    return $return;

}

function Tech_List_of_menu(){
    $access_save = array('dashboard_tech','tech_order_list');
    $update = update_user_meta(get_current_user_id(),"access",$access_save);
    $type_of_tech = get_user_meta(get_current_user_id(),"type_tech",true);
    $return = '<ul>';

    // Dashboard
    $return.= '
            <li>
                <a href="'.get_the_permalink(146).'">
                    <i class="las la-tachometer-alt"></i>
                    <span>داشبورد</span>
                </a>
            </li>
            ';


    if($type_of_tech == "external"){
        // Order List
        $return.= '
            <li>
                <a href="'.get_the_permalink(149).'">
                    <i class="las la-tools"></i>
                    <span>سفارشات</span>
                </a>
            </li>
            ';
    }
    else if($type_of_tech == "internal"){
        $return.= '
            <li>
                <a href="'.get_the_permalink(14274).'">
                     <i class="las la-tools"></i>
                    <span>سفارشات</span>
                </a>
            </li>
            ';
    }
    if($type_of_tech == "external"){
    // Order payment
    $return.= '
            <li>
                <a href="'.get_the_permalink(157).'">
                     <i class="las la-money-bill"></i>
                    <span>پرداخت آنلاین</span>
                </a>
            </li>
            ';
    // Order financial
    $return.= '
            <li>
                <a href="'.get_the_permalink(161).'">
                    <i class="las la-wallet"></i>
                    <span>مالی</span>
                </a>
            </li>
            ';
        }
    // Ticket list
//    $return.= '
//            <li>
//                <a href="'.get_the_permalink(170).'">
//
//                    <span>تیکت ها</span>
//                </a>
//                <ul class="sub_menu">
//                <li>
//                    <a href="'.get_the_permalink(172).'">
//
//                        <span>افزودن تیکت</span>
//                    </a>
//                </li>
//                <li>
//                <a href="'.get_the_permalink(170).'">
//
//                    <span>لیست تیکت ها</span>
//                </a>
//        </li>
//
//                </ul>
//            </li>
//        ';

    // Cart_Technician
    $return.= '
            <li>
               <a href="'.get_the_permalink(119580).'">
                    <i class="las la-address-card"></i>
                    <span>کارت تکنسین</span>
                </a>
            </li>
                ';


    // Logout
    $return.= '
            <li>
                <a href="#" id="Exit">
                    <i class="las la-sign-out-alt"></i>
                    <span>خروج</span>
                </a>
            </li>
                ';
    $return.= '</ul>';

    return $return;
}
add_action( 'wp_ajax_file_upload', 'file_upload' );
if( !function_exists('file_upload') ):

    function file_upload(){
        $arr_img_ext = array('image/png', 'image/jpeg', 'image/jpg', 'image/gif' , 'application/pdf');
        for($i = 0; $i < count($_FILES['file']['name']); $i++) {
            if (in_array($_FILES['file']['type'][$i], $arr_img_ext)) {
                $rand = md5(rand(5000,50000));
                if($_FILES['file']['type'][$i] == "image/png"){
                    $mime = ".png";
                }
                elseif($_FILES['file']['type'][$i] == "image/jpeg"){
                    $mime = ".jpeg";
                }
                elseif($_FILES['file']['type'][$i] == "image/jpg"){
                    $mime = ".jpg";
                }
                elseif($_FILES['file']['type'][$i] == "image/gif"){
                    $mime = ".gif";
                }
                elseif($_FILES['file']['type'][$i] == "application/pdf"){
                    $mime = ".pdf";
                }
                $name = $rand.$mime;
                $upload = wp_upload_bits($name, null, file_get_contents($_FILES['file']['tmp_name'][$i]));
                $url = false;
                foreach($upload as $key => $value){
                    if($key == "url") {
                        $url = $value;
                    }
                }
                print json_encode(array("status" => true , "url" => $url));
                exit();
            }
            else{
                print json_encode(array("status" => false , "message" => "خطا! فرمت فایل ارسال مجاز نمی باشد."));
            }
        }
        wp_die();
    }

endif;

function convert2english($string) {
    $newNumbers = range(0, 9);
    // 1. Persian HTML decimal
    $persianDecimal = array('&#1776;', '&#1777;', '&#1778;', '&#1779;', '&#1780;', '&#1781;', '&#1782;', '&#1783;', '&#1784;', '&#1785;');
    // 2. Arabic HTML decimal
    $arabicDecimal = array('&#1632;', '&#1633;', '&#1634;', '&#1635;', '&#1636;', '&#1637;', '&#1638;', '&#1639;', '&#1640;', '&#1641;');
    // 3. Arabic Numeric
    $arabic = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
    // 4. Persian Numeric
    $persian = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');

    $string =  str_replace($persianDecimal, $newNumbers, $string);
    $string =  str_replace($arabicDecimal, $newNumbers, $string);
    $string =  str_replace($arabic, $newNumbers, $string);
    return str_replace($persian, $newNumbers, $string);
}