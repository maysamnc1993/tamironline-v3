<?php
IFLoggedAndVerify("add_financial_internal");
// Template Name: add financial internal
get_header();

$title_section = 'افزودن فاکتور حسابداری داخلی';
$btn = 'افزودن حقوق پرداخت شده';
$action = 'add';
$uid = "";
$name = "";

$type_of_desc_acc = get_option("type_of_desc_acc");
$title_of_desc_acc = get_option("title_of_desc_acc");

?>

    <section class="DashboardSection">
        <?=NavDashboard();?>
        <div class="main">
            <div class="content" style="margin-top:50px;">
                <h1 class="title">افزودن پاداش / جریمه</h1>

                <form action="#" method="post" id="addFinancialrewardInternalAjax" >

                    <div class="form-group half">
                        <label>تکنسین</label>
                        <select class="form-control validate" data-validate="empty" id="technician_id">
                            <option value="">انتخاب تکنسین</option>
                            <?php
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
                                if($item->ID == get_current_user_id()){}else{

                                    $firstname_u = get_user_meta($item->ID,"first_name",true);

                                    echo '<option value="'.$item->ID.'">'.$firstname_u.'</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group half">
                        <label>انتخاب پاداش/جریمه</label>
                        <select class="form-control validate" data-validate="empty" id="rewardType">
                            <option value="">انتخاب کنید</option>
                            <option value="1">پاداش</option>
                            <option value="0">جریمه</option>
                        </select>
                    </div>

                    <div class="form-group half">
                        <label>مبلغ پاداش/جریمه</label>
                        <input type="text" class="form-control validate PriceFormat" data-validate="empty" id="rewardprice">
                    </div>
                    <div class="form-group half">
                        <label>مبلغ به حروف</label>
                        <div class="price_char">صفر تومان</div>
                    </div>
                    <div class="form-group">
                        <label>توضیحات</label>
                        <select class="form-control validate" data-validate="empty" id="rewardDesc">
                            <option value="">انتخاب کنید</option>
                            <?php
                            for($i = 0; $i < count($title_of_desc_acc); $i++ ){
                                if($title_of_desc_acc[$i] == ""){}else{

                                    echo '<option style="display:none" value="'.$title_of_desc_acc[$i].'" id="'.$type_of_desc_acc[$i].'">'.$title_of_desc_acc[$i].'</option>';

                                }
                            }
                            ?>

                        </select>
                    </div>

                    <input type="submit" class="btn" value="افزودن پاداش و جریمه">

                </form>

            </div>
            <div class="content" style="margin-top: 50px;" >

                <h1 class="title">حقوق پرداخت شده</h1>
                <form action="#" method="post" id="addFinancialInnerAjax" >
                    <div class="form-group half">
                        <label>تکنسین</label>
                        <select class="form-control validate" data-validate="empty" id="technician">
                            <option value="">انتخاب تکنسین</option>
                            <?php
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
                                if($item->ID == get_current_user_id()){}else{

                                    $firstname_u = get_user_meta($item->ID,"first_name",true);

                                    echo '<option value="'.$item->ID.'">'.$firstname_u.'</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group half">
                        <label>توضیحات</label>
                        <input type="text" class="form-control validate" data-validate="empty" id="description">
                    </div>

                    <div class="form-group half" >
                        <label>مبلغ حقوق</label>
                        <input type="text" class="form-control validate PriceFormat" data-validate="empty" id="price">
                    </div>
                    <div class="form-group half">
                        <label>مبلغ به حروف</label>
                        <div class="price_char">صفر تومان</div>
                    </div>


                    <input type="submit" class="btn" value="<?=$btn;?>">

                </form>

            </div>

        </div>
    </section>
<?php
get_footer();

?>