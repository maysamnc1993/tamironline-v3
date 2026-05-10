



<?php
IFLoggedAndVerify("add_financial");
// Template Name: add financial
get_header();

$title_section = 'افزودن فاکتور حسابداری';
$btn = 'افزودن شارژ کیف پول';
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

                <div class="row">
                    <div class="col-md-6">
                        <h1 class="title">افزودن بستانکاری / بدهکاری</h1>

                        <form action="#" method="post" id="addFinancialrewardAjax" >

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
                                                    "value" => "external",
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
                                <label>انتخاب </label>
                                <select class="form-control validate" data-validate="empty" id="rewardType">
                                    <option value="">انتخاب کنید</option>
                                    <option value="1">ایجاد بستانکاری</option>
                                    <option value="0">ایجاد بدهکاری</option>
                                </select>
                            </div>

                            <div class="form-group half">
                                <label>مبلغ </label>
                                <input type="text" class="form-control validate PriceFormat" data-validate="empty" id="rewardprice">
                            </div>

                            <div class="form-group half">
                                <label>مبلغ به حروف</label>
                                <div class="price_char">صفر تومان</div>
                            </div>

                            <div class="form-group">
                                <label>توضیحات</label>
                                <input style="height: 54px;" list="rewardDesc" class="form-control validate" id="rewardDesc" name="rewardDesc" data-validate="empty" placeholder="علت ایجاد بدهکاری /بستانکاری را بنویسید">


                            </div>

                            <input type="submit" class="btn" value="افزودن سند بستانکاری / بدهکاری">

                        </form>
                    </div>
                    <div class="col-md-6">
                        <h1 class="title">شارژ کیف پول</h1>
                        <form action="#" method="post" id="addFinancialAjax" >
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
                                                    "value" => "external",
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
                                <label>مبلغ شارز کیف پول</label>

                                <input type="text" class="form-control validate PriceFormat" data-validate="empty" id="price">
                            </div>
                            <div class="form-group half">
                                <label>مبلغ به حروف</label>
                                <div class="price_char">صفر تومان</div>
                            </div>
                            <div class="form-group">
                                <ul class="default_price">
                                    <li id="1000000">1.000.000 </li>
                                    <li id="1500000">1.500.000 </li>
                                    <li id="2000000">2.000.000 </li>
                                    <li id="2500000">2.500.000 </li>
                                    <li id="3000000">3.000.000 </li>
                                    <li id="3500000">3.500.000 </li>
                                    <li id="4000000">4.000.000 </li>
                                    <li id="4500000">4.500.000 </li>
                                    <li id="5000000">5.000.000 </li>
                                    <li id="10000000">10.000.000 </li>
                                </ul>
                            </div>
                            <input type="submit" class="btn" value="<?=$btn;?>">

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
get_footer();

?>