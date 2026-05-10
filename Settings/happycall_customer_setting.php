<?php

// Template Name: HappCall Customer Setting
IFLoggedAndVerify("happycall_customer_setting");
get_header();
$HappyCallCustomer = get_option("HappyCallCustomer");
$count_hc_customer = get_option("HappyCallCustomer_Count");
if(isset($_GET["hid"])){
    $key = $_GET["hid"];
    $value = $HappyCallCustomer[$key];
    $btn = "ویرایش";
}else{
    $key = '';
    $value = '';
    $btn = "افزودن";
}

?>
<section class="DashboardSection">
    <?=NavDashboard();?>
    <div class="main">
        <div class="content">
            <div class="row">

                <div class="col-md-4">
                    <h5 class="title">افزودن هپی کال مشتری</h5>
                    <form action="#" method="post" id="addHappyCallCustomerAjax">
                        <div class="form-group">
                            <label>عنوان</label>
                            <input type="hidden" id="key_of_happycall" value="<?=$key?>">
                            <input type="text" class="form-control validate" value="<?=$value?>" data-validate="empty" id="title_of_happycall">
                        </div>
                        <input type="submit" class="btn" value="<?=$btn?>">
                    </form>
                </div>
                <div class="col-md-8">

                    <h5 class="title">لیست هپی کال مشتری</h5>
                    <table class="table ListTable">

                        <tr>
                            <th>#</th>
                            <th>عنوان</th>
                            <th>عملیات</th>
                        </tr>

                        <?php
                        $i = 0;
                        foreach($HappyCallCustomer as $key => $value){

                            $per = array_sum($count_hc_customer[$key]["result"]) / count($count_hc_customer[$key]["result"]);
                            $s_1 = '';
                            $s_2 = '';
                            $s_3 = '';
                            $s_4 = '';
                            $s_5 = '';
                            if($per > 1){
                                $s_1 = "active";
                            }
                            if($per > 19){
                                $s_2 = "active";
                            }
                            if($per > 39){
                                $s_3 = "active";
                            }
                            if($per > 59){
                                $s_4 = "active";
                            }
                            if($per > 89){
                                $s_5 = "active";
                            }
                            $count = $count_hc_customer[$key]["count"];

                            $i++;
                            echo '
                                    <tr>
                                    <td>'.$i.'</td>
                                    <td>'.$value.'</td>
                                    <td>
                                        
                                        <span class="AvgPer">
                                            <b class="'.$s_1.'">*</b>
                                            <b class="'.$s_2.'">*</b>
                                            <b class="'.$s_3.'">*</b>
                                            <b class="'.$s_4.'">*</b>
                                            <b class="'.$s_5.'">*</b>
                                            
                                        </span>
                                        <span class="AvgPer">'.$per.'٪</span>
                                        <span class="AvgPer">'.$count.' رای ثبت شده</span>
                                        <a href="'.get_the_permalink(37772).'?hid='.$key.'" class="edit">ویرایش</a>
                                    
                                    </td>
                                    </tr>
                                ';
                        }
                        ?>
                    </table>
                </div>
            </div>


        </div>
    </div>
</section>
<?php
get_footer();

?>
