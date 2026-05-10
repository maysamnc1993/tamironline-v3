<?php
IFLoggedAndVerify("point_technician");
// Template Name: Point Tech List

get_header();
?>
    <div class="modal fade ShowCUstomerOrder" id="ShowPopUpPointTech" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="customerList">
                    <h1>مشاهده امتیازات</h1>
                    <div class="List_of_point">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="DashboardSection">

        <?=NavDashboard();?>

        <div class="main">

            <div class="content">

                <h1 class="title">امتیازات تکنسین ها</h1>
                <table class="table ListTable">

                    <tr>
                        <th>#</th>
                        <th>نام و نام خانوادگی</th>
                        <th>شماره تماس</th>
                        <th>امتیازات</th>
                    </tr>

                    <?php

                    $operators = get_users( array(
                        'meta_key' => 'role',
                        'meta_value' => 'technician',
                        'meta_compare' => '=' // exact match only
                    ) );

                    $i = 0;
                    foreach($operators as $item){
                        $i++;
                        $firstname = get_user_meta($item->ID,"first_name",true);
                        $mobile = get_user_meta($item->ID,"mobile",true);
                        $percent = get_user_meta($item->ID,"percent",true);
                        $address = get_user_meta($item->ID,"address",true);
                        $img_Personal = get_user_meta($item->ID,"img_Personal",true);
                        $type_tech = get_user_meta($item->ID,"type_tech",true);

                        if($type_tech == "internal"){
                            $t_type = "تکنسین داخلی";
                        }else{
                            $t_type = "تکنسین خارجی";
                        }

                        if($img_Personal == ''){
                            $dl = '';
                        }else{
                            $dl = '<a href="'.$img_Personal.'" target="_blank" class="download">تصویر</a>';
                        }
                        $tech_point = get_user_meta($item->ID,"tech_point",true);

                        $per = array_sum($tech_point["point"]) / count($tech_point["point"]);
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
                        $count = count($tech_point["point"]);

                        echo '
                        <tr>
                            <td>'.$i.'</td>
                            <td>'.$firstname.'</td>
                            <td>'.$mobile.'</td>
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
                                        <span class="AvgPer ShowDetail" id="'.$item->ID.'">مشاهده</span>
                            
                            </td>
                          
                        </tr>
                    ';

                    }

                    ?>



                </table>

            </div>

        </div>
    </section>

<?php
get_footer();

?>