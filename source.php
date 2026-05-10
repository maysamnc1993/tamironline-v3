<div class="SearchBox">

    <span class="titleBox">جستجو پیشرفته</span>

    <form <?php if(isset($_GET['searchTrue'])){echo'class="active"';}?> action="#" method="get" id="SearchList">
        <input type="hidden" name="searchTrue" value="1">
        <div class="form-group third">
            <label>کد اشتراک</label>
            <input type="text" name="subscription" class="form-control" <?php if(isset($_GET['searchTrue'])){echo'value="'.$_GET["subscription"].'"';}?> >
        </div>
        <div class="form-group third">
            <label>شماره موبایل</label>
            <input type="text" name="order_id" class="form-control" <?php if(isset($_GET['searchTrue'])){echo'value="'.$_GET["order_id"].'"';}?>>
        </div>

        <div class="form-group third">
            <label>مشتری</label>
            <select class="form-control" name="customer" id="CustomerSearch">
                <option value="">مشتری را انتخاب نمایید.</option>
                <?php
                $customer_list = get_users( array(
                    'meta_key' => 'role',
                    'meta_value' => 'customer',
                    'meta_compare' => '=' // exact match only
                ) );
                foreach($customer_list as $item){
                    $s_sub_id = +$item->ID + 10000;
                    $s_first_name = get_user_meta($item->ID,"first_name",true);
                    $mobile = get_user_meta($item->ID,"mobile",true);
                    if(isset($_GET['customer'])){
                        if($item->ID == $_GET['customer']){
                            $selected = "selected";
                        }
                        else{
                            $selected = "";
                        }
                    }
                    else{
                        $selected = "";
                    }
                    echo '<option '.$selected.' value="'.$item->ID.'">'.$s_sub_id.' - '.$mobile.' - '.$s_first_name.' </option>';
                }
                ?>
            </select>
        </div>
        <div class="form-group third">
            <label>نوع دستگاه</label>
            <select class="form-control validate" data-validate="empty" name="device" id="Searchdevice">

                <option value="">نوع دستگاه را انتخاب نمایید.</option>
                <?php
                $device_list = get_terms(array("taxonomy" => "device" , "hide_empty" => false));

                foreach($device_list as $item){
                    if(isset($_GET['device'])){
                        if($item->term_id == $_GET['device']){
                            $selected = "selected";
                        }else{
                            $selected = "";
                        }
                    }else{
                        $selected = "";
                    }
                    echo '<option '.$selected.' value="'.$item->term_id.'">'.$item->name.'</option>';

                }
                ?>

            </select>
        </div>
        <div class="form-group third">
            <label>برند</label>
            <select class="form-control validate" data-validate="empty" name="brand" id="search_brand">

                <option value="">نوع برند را انتخاب نمایید.</option>
                <?php
                $brand_list = get_terms(array("taxonomy" => "brand" , "hide_empty" => false));

                foreach($brand_list as $item){
                    if(isset($_GET['brand'])){
                        if($item->term_id == $_GET['brand']){
                            $selected = "selected";
                        }else{
                            $selected = "";
                        }
                    }
                    else{
                        $selected = "";
                    }
                    echo '<option '.$selected.' value="'.$item->term_id.'">'.$item->name.'</option>';

                }
                ?>

            </select>
        </div>
        <div class="form-group third">
            <label>تکنسین</label>
            <select class="form-control" name="technician" id="technicianSearch">
                <option value="">تکنسین را انتخاب نمایید.</option>
                <?php
                $customer_list = get_users( array(
                    'meta_key' => 'role',
                    'meta_value' => 'technician',
                    'meta_compare' => '=' // exact match only
                ) );
                foreach($customer_list as $item){
                    $s_first_name = get_user_meta($item->ID,"first_name",true);
                    $mobile = get_user_meta($item->ID,"mobile",true);

                    if(isset($_GET['technician'])){
                        if($item->ID == $_GET['technician']){
                            $selected = "selected";
                        }
                        else{
                            $selected = "";
                        }
                    }
                    else{
                        $selected = "";
                    }
                    echo '<option '.$selected.' value="'.$item->ID.'">'.$mobile.' - '.$s_first_name.' </option>';
                }
                ?>
            </select>
        </div>

        <div class="form-group third">
            <label>استان</label>
            <select class="form-control validate" data-validate="empty" name="state" id="search_state">
                <option value="">استان را انتخاب نمایید.</option>
                <?php
                $i = 0;
                $state_list = get_terms(array("taxonomy" => "state" , "hide_empty" => false));
                foreach($state_list as $item){
                    $i++;
                    if(isset($_GET['state'])){
                        if($item->term_id == $_GET['state']){
                            $selected = "selected";
                        }else{
                            $selected = "";
                        }
                    }
                    elseif($i == 1){
                        $selected = "selected";
                    }
                    else{
                        $selected = "";
                    }
                    echo '<option '.$selected.' value="'.$item->term_id.'">'.$item->name.'</option>';

                }
                ?>

            </select>
        </div>
        <div class="form-group third">
            <label>مناطق</label>
            <select class="form-control validate" data-validate="empty" name="city" id="search_city">

                <option value="">منطقه را انتخاب نمایید.</option>
                <?php
                $city_list = get_terms(array("taxonomy" => "city" , "hide_empty" => false));

                foreach($city_list as $item){
                    if(isset($_GET['city'])) {
                        if ($item->term_id == $_GET['city']) {
                            $selected = "selected";
                        } else {
                            $selected = "";
                        }
                    }else{
                        $selected = "";
                    }
                    echo '<option '.$selected.' value="'.$item->term_id.'">'.$item->name.'</option>';

                }
                ?>

            </select>
        </div>
        <div class="form-group third">
            <label>وضعیت</label>
            <select class="form-control" name="status" id="CustomerStatus">

                <option <?php if(isset($_GET['status'])){if($_GET['status'] == "100"){echo 'selected';}}?>  value="100">همه</option>
                <option <?php if(isset($_GET['status'])){if($_GET['status'] == 0){echo 'selected';}}?> value="0">جدید</option>
                <option <?php if(isset($_GET['status'])){if($_GET['status'] == 1){echo 'selected';}}?> value="1">هماهنگ شده</option>
                <option <?php if(isset($_GET['status'])){if($_GET['status'] == 2){echo 'selected';}}?> value="2">بازشده</option>
                <option <?php if(isset($_GET['status'])){if($_GET['status'] == 3){echo 'selected';}}?> value="3">معلق</option>
                <option <?php if(isset($_GET['status'])){if($_GET['status'] == 4){echo 'selected';}}?> value="4">کنسل شده</option>
                <option <?php if(isset($_GET['status'])){if($_GET['status'] == 5){echo 'selected';}}?> value="5">انجام کار</option>
                <option <?php if(isset($_GET['status'])){if($_GET['status'] == 6){echo 'selected';}}?> value="6">بازگشتی</option>
            </select>
        </div>
        <div class="form-group third">
            <label>تاریخ سفارش</label>
            <input type="text" name="date_year" id="date_year" class="form-control" <?php if(isset($_GET['date_year'])){echo'value="'.$_GET["date_year"].'"';}?> >
            <input type="hidden" name="timestamp" id="timestamp" value="">
        </div>
        <div class="form-group third">
            <label>ایراد دستگاه</label>
            <select class="form-control" id="Search_objection" name="objection">
                <option value="">ایراد دستگاه را مشخص نمایید.</option>
                <?php
                $objectionsList = get_option("objectionsList");
                for($i = 0; $i < count($objectionsList); $i++){
                    if ($objectionsList[$i] == "") {
                    } else {
                        if(isset($_GET['objection'])) {
                            if ($objectionsList[$i] == $_GET['objection']) {
                                $selected = "selected";
                            } else {
                                $selected = "";
                            }
                        }
                        else{
                            $selected = "";
                        }
                        echo '<option ' . $selected . ' value="' . $objectionsList[$i] . '">' . $objectionsList[$i] . '</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div class="form-group third">
            <label>نوع سفارش</label>
            <select class="form-control validate" data-validate="empty" id="order_type" name="order_type">
                <option value="">نوع سفارش را انتخاب نمایید.</option>
                <option <?php  if(isset($_GET['order_type'])){if($_GET['order_type'] == "repair"){echo 'selected';}}?> value="repair">تعمیر</option>
                <option <?php  if(isset($_GET['order_type'])){if($_GET['order_type'] == "selected"){echo 'selected';}}?>  value="buy">خرید</option>
                <option <?php  if(isset($_GET['order_type'])){if($_GET['order_type'] == "selected"){echo 'selected';}}?>  value="service">سرویس در مجموعه</option>

            </select>

        </div>
        <input type="submit" class="btn" value="جستجو پیشرفته">
    </form>
</div>