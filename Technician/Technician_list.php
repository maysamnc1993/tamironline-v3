<?php
IFLoggedAndVerify("technician_list");
// Template Name: Technician List

get_header();
?>

    <section class="DashboardSection">

        <?=NavDashboard();?>

        <div class="main">

            <div class="content">
                <h1 class="title">لیست تکنسین ها</h1>
                <input type="text" class="form-control" placeholder="نام تکنسین" id="searchTech">

                <table class="table ListTable">

                    <tr>
                        <th>#</th>
                        <th>نام و نام خانوادگی</th>
                        <th>شماره تماس</th>
                        <th>درصد تکنسین</th>
                        <th>نوع تکنسین</th>
                        <th>محل سکونت</th>
                        <th>عملیات</th>
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

                        echo '
                        <tr>
                            <td>'.$i.'</td>
                            <td>'.$dl.' . '.$firstname.'
                            
                            <span style="display: inline-block;width: 100%;background: #ffe8bc;text-align: center;font-size: 12px;padding: 2px;border-radius: 6px;">احراز هویت
                                <b>'.get_user_meta($item->ID,'active_verify',true).'</b>
                            </span>
                            </td>
                            <td>'.$mobile.'</td>
                            <td>'.$percent.'%</td>
                            <td>'.$t_type.'</td>
                            <td>'.$address.'</td>
                            
                            <td>
                            
                             ';
                        if(CheckAccess("edit_technician")){
                            echo'
                                    <a href="'.get_the_permalink(100).'?uid='.$item->ID.'">ویرایش</a> 
                                ';
                        }
                        echo'
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