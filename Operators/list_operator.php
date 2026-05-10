<?php
IFLoggedAndVerify("operator_list");
// Template Name: Operator List

get_header();
?>
<section class="DashboardSection">

    <?=NavDashboard();?>

    <div class="main">

        <div class="content">

            <h1 class="title">لیست اپراتورها</h1>
            <table class="table ListTable">

                <tr>
                    <th>#</th>
                    <th>نام و نام خانوادگی</th>
                    <th>سمت</th>
                    <th>داخلی مورد نظر</th>
                    <th>تاریخ ورود</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>

                <?php

                $operators = get_users( array(
                    'meta_key' => 'role',
                    'meta_value' => 'operator',
                    'meta_compare' => '=' // exact match only
                ) );

                $i = 0;
                foreach($operators as $item){
                    $i++;
                    $firstname = get_user_meta($item->ID,"first_name",true);
                    $operator = get_user_meta($item->ID,"operator",true);
                    $call = get_user_meta($item->ID,"call",true);
                    echo '
                        <tr>
                            <td>'.$i.'</td>
                            <td>'.$firstname.'
                            
                            </td>
                            <td>'.$operator.'</td>
                            <td>'.$call.'</td>
                            <td>'.date("Y/m/d",strtotime($item->user_registered)).'</td>
                            <td>فعال</td>
                            <td>
                            ';
                            if(CheckAccess("edit_operator")){
                                echo'
                                    <a href="'.get_the_permalink(90).'?uid='.$item->ID.'">ویرایش</a> </td>
                                ';
                            }
                        echo'
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