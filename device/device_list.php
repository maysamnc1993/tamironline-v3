<?php
IFLoggedAndVerify("device_list");
// Template Name: Device List

get_header();
?>

    <section class="DashboardSection">

        <?=NavDashboard();?>

        <div class="main">

            <div class="content">

                <h1 class="title">لیست دستگاه ها</h1>
                <table class="table ListTable">

                    <tr>
                        <th>#</th>
                        <th>نام دستگاه</th>
                        <th>عملیات</th>
                    </tr>

                    <?php

                    $brand = get_terms(array("taxonomy" => "device" , "hide_empty" => false));

                    $i = 0;
                    foreach($brand as $item){
                        $i++;
                        echo '
                        <tr>
                            <td>'.$i.'</td>
                            <td>'.$item->name.'</td>
                            <td>
                            
                             ';
                        if(CheckAccess("edit_device")){
                            echo'
                                    <a href="'.get_the_permalink(120).'?uid='.$item->term_id.'">ویرایش</a> 
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