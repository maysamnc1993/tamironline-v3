<?php
function ListOFMenu(){

    $access = get_user_meta(get_current_user_id(),"access",true);


    $return = '<ul>';


    // Dashboard
    $return.= '
            <li class="">
                <a href="'.get_the_permalink(142).'">
                    '.file_get_contents(wp_get_attachment_url(144)).'
                    <span>داشبورد</span>
                </a>
            </li>
                ';
    // Operator List
    if(in_array("operator_list",$access)){
        $return.= '
            <li>
            
                <a href="'.get_the_permalink(88).'">
                    '.file_get_contents(wp_get_attachment_url(81)).'
                    <span>اپراتورها</span>
                </a>
                <ul class="sub_menu">
                ';

        // Add Operator
        if(in_array("add_operator",$access)){

            $return.='
                <li>
                    <a href="'.get_the_permalink(90).'">
                        '.file_get_contents(wp_get_attachment_url(96)).'
                        <span>افزودن اپراتور</span>
                    </a>
                </li>
                <li>
                <a href="'.get_the_permalink(88).'">
                    '.file_get_contents(wp_get_attachment_url(81)).'
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
    if(in_array("search_technician",$access)){
        $return.= '
            <li>
            
                <a href="#" id="SearchTech">
                    '.file_get_contents(wp_get_attachment_url(102)).'
                    <span>جستجوی تکنسین</span>
                </a>
                
                ';
    }
    // Technician list
    if(in_array("technician_list",$access)){
        $return.= '
            <li>
            
                <a href="'.get_the_permalink(98).'">
                    '.file_get_contents(wp_get_attachment_url(102)).'
                    <span>تکنسین ها</span>
                </a>
                <ul class="sub_menu">
                ';

        // Add Operator
        if(in_array("add_technician",$access)){

            $return.='
                <li>
                    <a href="'.get_the_permalink(100).'">
                        '.file_get_contents(wp_get_attachment_url(96)).'
                        <span>افزودن تکنسین</span>
                    </a>
                </li>
                <li>
                <a href="'.get_the_permalink(98).'">
                    '.file_get_contents(wp_get_attachment_url(102)).'
                    <span>لیست تکنسین ها</span>
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
                    '.file_get_contents(wp_get_attachment_url(107)).'
                    <span>مشتری ها</span>
                </a>
                <ul class="sub_menu">
                ';

        // Add Operator
        if(in_array("add_customer",$access)){

            $return.='
                <li>
                    <a href="'.get_the_permalink(105).'">
                        '.file_get_contents(wp_get_attachment_url(96)).'
                        <span>افزودن مشتری</span>
                    </a>
                </li>
                <li>
                <a href="'.get_the_permalink(103).'">
                    '.file_get_contents(wp_get_attachment_url(107)).'
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

    // Order list
    if(in_array("order_list",$access)){
        $return.= '
            <li>
            
                <a href="'.get_the_permalink(108).'?status_filter=0">
                    '.file_get_contents(wp_get_attachment_url(231)).'
                    <span>سفارش ها</span>
                </a>
                <ul class="sub_menu">
                ';

        // Add Operator
        if(in_array("add_order",$access)){

            $return.='
                <li>
                    <a href="'.get_the_permalink(110).'">
                        '.file_get_contents(wp_get_attachment_url(96)).'
                        <span>افزودن سفارش</span>
                    </a>
                </li>
                <li>
                <a href="'.get_the_permalink(108).'?status_filter=0">
                    '.file_get_contents(wp_get_attachment_url(231)).'
                    <span>لیست سفارش ها</span>
                </a>
</li>
            ';

        }

        $return.='
                </ul>
            </li>
        ';
    }

    // brand list
    if(in_array("brand_list",$access)){
        $return.= '
            <li>
            
                <a href="'.get_the_permalink(112).'">
                    '.file_get_contents(wp_get_attachment_url(116)).'
                    <span>برند ها</span>
                </a>
                <ul class="sub_menu">
                ';

        // Add Operator
        if(in_array("add_brand",$access)){

            $return.='
                <li>
                    <a href="'.get_the_permalink(114).'">
                        '.file_get_contents(wp_get_attachment_url(96)).'
                        <span>افزودن برند</span>
                    </a>
                </li>
                <li>
                <a href="'.get_the_permalink(112).'">
                    '.file_get_contents(wp_get_attachment_url(116)).'
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
                    '.file_get_contents(wp_get_attachment_url(122)).'
                    <span>دستگاه ها</span>
                </a>
                <ul class="sub_menu">
                ';

        // Add Operator
        if(in_array("add_device",$access)){

            $return.='
                <li>
                    <a href="'.get_the_permalink(120).'">
                        '.file_get_contents(wp_get_attachment_url(96)).'
                        <span>افزودن دستگاه</span>
                    </a>
                </li>
                <li>
                <a href="'.get_the_permalink(117).'">
                    '.file_get_contents(wp_get_attachment_url(122)).'
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
                    '.file_get_contents(wp_get_attachment_url(127)).'
                    <span>استان ها</span>
                </a>
                <ul class="sub_menu">
                ';

        // Add Operator
        if(in_array("add_state",$access)){

            $return.='
                <li>
                    <a href="'.get_the_permalink(125).'">
                        '.file_get_contents(wp_get_attachment_url(96)).'
                        <span>افزودن دستگاه</span>
                    </a>
                </li>
                <li>
                <a href="'.get_the_permalink(123).'">
                    '.file_get_contents(wp_get_attachment_url(127)).'
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
                    '.file_get_contents(wp_get_attachment_url(127)).'
                    <span>مناطق</span>
                </a>
                <ul class="sub_menu">
                ';

        // Add Operator
        if(in_array("add_city",$access)){

            $return.='
                <li>
                    <a href="'.get_the_permalink(131).'">
                        '.file_get_contents(wp_get_attachment_url(96)).'
                        <span>افزودن منطقه</span>
                    </a>
                </li>
                <li>
                <a href="'.get_the_permalink(128).'">
                    '.file_get_contents(wp_get_attachment_url(127)).'
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

    // Ticket list
    if(in_array("ticket_list",$access)){
        $return.= '
            <li>
                <a href="'.get_the_permalink(138).'">
                    '.file_get_contents(wp_get_attachment_url(234)).'
                    <span>تیکت ها</span>
                </a>
                <ul class="sub_menu">
                ';
        // Add Operator
        if(in_array("add_ticket",$access)){
            $return.='
                <li>
                    <a href="'.get_the_permalink(140).'">
                        '.file_get_contents(wp_get_attachment_url(96)).'
                        <span>افزودن تیکت</span>
                    </a>
                </li>
                <li>
                <a href="'.get_the_permalink(138).'">
                    '.file_get_contents(wp_get_attachment_url(234)).'
                    <span>لیست تیکت ها</span>
                </a>
</li>
            ';

        }

        $return.='
                </ul>
            </li>
        ';
    }

    // financial
    if(in_array("list_financial",$access)){
        $return.= '
            <li>
            
                <a href="'.get_the_permalink(206).'">
                    '.file_get_contents(wp_get_attachment_url(164)).'
                    <span>حسابداری</span>
                </a>
                <ul class="sub_menu">
                ';

        // Add Operator
        if(in_array("add_financial",$access)){

            $return.='
                <li>
                    <a href="'.get_the_permalink(208).'">
                        '.file_get_contents(wp_get_attachment_url(96)).'
                        <span>افزودن فاکتور حسابداری</span>
                    </a>
                </li>
                <li>
                <a href="'.get_the_permalink(206).'">
                    '.file_get_contents(wp_get_attachment_url(127)).'
                    <span>لیست حسابداری</span>
                </a>
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
                    '.file_get_contents(wp_get_attachment_url(192)).'
                    <span>تنظیمات</span>
                </a>
                <ul class="sub_menu">
                ';
        // Add Operator
        if(in_array("sms_setting",$access)){
            $return.='
                <li>
                    <a href="'.get_the_permalink(189).'">
                        '.file_get_contents(wp_get_attachment_url(191)).'
                        <span>تنظیمات پیامک</span>
                    </a>
                </li>
            ';

        }
        if(in_array("payment_setting",$access)){
            $return.='
                <li>
                    <a href="'.get_the_permalink(199).'">
                        '.file_get_contents(wp_get_attachment_url(191)).'
                        <span>تنظیمات پرداخت</span>
                    </a>
                </li>
            ';

        }
        if(in_array("accounting_setting",$access)){
            $return.='
                <li>
                    <a href="'.get_the_permalink(1009).'">
                        '.file_get_contents(wp_get_attachment_url(191)).'
                        <span>تنظیمات حسابداری</span>
                    </a>
                </li>
            ';

        }
        if(in_array("cancel_setting",$access)){
            $return.='
                <li>
                    <a href="'.get_the_permalink(257).'">
                        '.file_get_contents(wp_get_attachment_url(191)).'
                        <span>تنظیمات کنسلی</span>
                    </a>
                </li>
            ';

        }
        if(in_array("objection_setting",$access)){
            $return.='
                <li>
                    <a href="'.get_the_permalink(296).'">
                        '.file_get_contents(wp_get_attachment_url(191)).'
                        <span>تنظیمات ایرادات</span>
                    </a>
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
                    '.file_get_contents(wp_get_attachment_url(145)).'
                    <span>خروج</span>
                </a>
            </li>
                ';

    $return.= '</ul>';

    return $return;

}
?>