<?php
IFLoggedAndVerify("customer_list");
// Template Name: Customers List

get_header();
if(isset($_GET['page_no'])){
    $paged = $_GET['page_no'];
}else{
    $paged = 1;
}
//$count_operators = get_users( array(
//    'meta_key' => 'role',
//    'meta_value' => 'customer',
//    'meta_compare' => '=',
//    'number' => 10000,
//) );
$users = count_users();
$count = $users["total_users"];
$pagination =  ceil($count / 1000);


?>

    <section class="DashboardSection">

        <?=NavDashboard();?>

        <div class="main">

            <div class="content">

                <h1 class="title">لیست مشتری ها</h1>
                <table class="table ListTable">

                    <tr>
                        <th>#</th>
                        <th>نام و نام خانوادگی</th>
                        <th>شماره تماس</th>
                        <th>عملیات</th>
                    </tr>

                    <?php

                    $operators = get_users( array(
                        'meta_key' => 'role',
                        'meta_value' => 'customer',
                        'meta_compare' => '=',
                        'number' => 1000,
                        'offset' => $paged,

                    ) );
                    $i = 0;
                    foreach($operators as $item){
                        $i++;
                        $firstname = get_user_meta($item->ID,"first_name",true);
                        $mobile = get_user_meta($item->ID,"mobile",true);


                        echo '
                        <tr>
                            <td>'.$i.'</td>
                            <td>'.$firstname.'</td>
                            <td>'.$mobile.'</td>
                            ';
                        if(CheckAccess("edit_customer")){
                            echo'
                            <td><a href="'.get_the_permalink(105).'?uid='.$item->ID.'">ویرایش</a> </td>
                           ';
                        }
                        echo '
                        </tr>
                    ';

                    }

                    ?>



                </table>

                <ul class="paginition">

                    <?php

                    for($i = 1 ; $i<= $pagination ; $i++){


                        if(!isset($_GET['page_no']) && $i == 1){
                            $active = 'active';
                        }
                        else if($i == $_GET['page_no']){
                            $active = 'active';
                        }
                        else{
                            $active = '';
                        }

                        echo '
                                
                                    <li>
                                        
                                        <a class="'.$active.'" href="?page_no='.$i.'">'.$i.'</a>
                                    
                                    </li>
                                
                                ';

                    }

                    ?>

                </ul>

            </div>

        </div>
    </section>

<?php
get_footer();

?>