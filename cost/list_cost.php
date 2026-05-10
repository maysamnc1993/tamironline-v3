<?php
IFLoggedAndVerify("list_cost");
// Template Name: Cost List

get_header();


    $post_per_page = 30;
    if(isset($_GET['page_no'])){
        $paged = $_GET['page_no'];
    }else{
        $paged = 1;
    }

$date_query = '';

    if(isset($_GET['timestamp']) && isset($_GET['timestamp_end'])){
    $d = date("d",($_GET['timestamp'] / 1000));
    $m = date("m",($_GET['timestamp'] / 1000));
    $y = date("Y",($_GET['timestamp'] / 1000));
    $time = "0";
    $d_end = date("d",($_GET['timestamp_end'] / 1000));
    $m_end = date("m",($_GET['timestamp_end'] / 1000));
    $y_end = date("Y",($_GET['timestamp_end'] / 1000));
    $time_end = "23";
    $start = array(
        'year'  => $y,
        'month' => $m,
        'day'   => $d,
        'hour' => $time
    );
    $end = array(
        'year'  => $y_end,
        'month' => $m_end,
        'day'   => $d_end,
        'hour' => $time_end
    );


}

   $cost_array = array(
        "post_type" => "cost",
        "posts_per_page" => $post_per_page,
        "paged" => $paged,
        "post_status" => "any",
    );
    if($_GET['date_year'] == "" && $_GET['date_year_end'] == ""){}else{
        $cost_array["date_query"] = array(
            array(
                'after'     => $start,
                'before'    => $end,
                'inclusive' => true,
            )
        );
    }
     $cost = get_posts($cost_array);
     $count = wp_count_posts('cost');
    $pagination = ceil($count->draft / 30);

?>

    <section class="DashboardSection">
<?=NavDashboard();?>


    <div class="main">

        <form action="#" method="get" id="searchFilter">
            <div class="filter_option_div costlist">
                <div class="form-group half">

                    <input type="text" name="date_year" placeholder="از تاریخ" id="date_year" class="form-control" <?php if(isset($_GET['date_year'])){echo'value="'.$_GET["date_year"].'"';}?> >
                    <input type="hidden" name="timestamp" id="timestamp" value="<?php if(isset($_GET['timestamp'])){echo $_GET["timestamp"];}?>">
                </div>
                <div class="form-group half">
                    <input type="text" name="date_year_end" placeholder="تا تاریخ" id="date_year_end" class="form-control" <?php if(isset($_GET['date_year_end'])){echo'value="'.$_GET["date_year_end"].'"';}?> >
                    <input type="hidden" name="timestamp_end" id="timestamp_end" value="<?php if(isset($_GET['timestamp_end'])){echo $_GET["timestamp_end"];}?>">
                </div>
                <input type="submit" class="btn" value="فیلتر">
            </div>
        </form>
        <div class="content">
            <h1 class="title">لیست هزینه ها</h1>

            <ul class="list_of_invoice header">
                <li>شماره سند</li>
                <li>نوع هزینه</li>
                <li>هزینه</li>
                <li>اسناد</li>
                <li>تاریخ</li>
            </ul>
            <?php
            $total_cost = 0;
            foreach($cost as $item){
                $cost_type = get_post_meta($item->ID,"cost_type",true);
                $cost_desc = get_post_meta($item->ID,"cost_desc",true);
                $cost_price = get_post_meta($item->ID,"cost_price",true);
                $total_cost = + $total_cost + $cost_price;
                $document = get_post_meta($item->ID,"document",true);
                $document_2 = get_post_meta($item->ID,"document_2",true);
                $doc_down = '';
                if($document =="" ){}else{
                    $doc_down.='<a href="'.$document.'" target="_blank" class="download">سند ۱</a>';
                }
                if($document_2 =="" ){}else{
                    $doc_down.='<a href="'.$document_2.'" target="_blank" class="download">سند ۲</a>';
                }
                echo '
                    
                        <ul class="list_of_invoice ">
                            <li>'.$item->ID.'</li>
                            <li>'.$cost_type.'</li>
                            <li>'.number_format($cost_price).' تومان</li>
                            <li>'.$doc_down.'</li>
                            <li>'.get_the_date("Y/m/d H:i" , $item->ID).'</li>
                        </ul>
                       <div class="description_invoice">'.$cost_desc.'</div>
                    ';
            }
            ?>
            <ul class="list_of_invoice total">
                <li>جمع کل هزینه ها : <span class="tt"></span> تومان </li>
            </ul>

            <ul class="paginition">

                <?php

                //                    for($i = 1 ; $i<= $pagination ; $i++){
                if(!isset($_GET['page_no'])){
                    $i = 1;
                }else{
                    $i = $_GET['page_no'];
                }

                if(!isset($_GET['page_no']) && $i == 1){
                    $active = 'active';

                }
                else if(isset($_GET['page_no']) && $i == $_GET['page_no']){
                    $active = 'active';
                }
                else{
                    $active = '';
                }

                $actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                    $link = $actual_link.'&page_no='.$i;
                }else{
                    $link = '?page_no='.$i;
                }

                if($i == 1 || !isset($_GET['page_no'])){}else{
                    $b = 1;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no=1';
                    }else{
                        $link = '?page_no=1';
                    }
                    echo '
                                
                                    <li id="one">
                                        
                                        <a  href="'.$link.'">1</a>
                                    
                                    </li>
                                
                                ';
                }
                if(($i - 4) > 1){
                    echo '<li class="sep_min">...</li>';
                }
                if(($i - 3) > 1){
                    $b = $i - 3;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no='.$b;
                    }else{
                        $link = '?page_no='.$b;
                    }
                    echo '
                                
                                    <li>
                                        
                                        <a href="'.$link.'">'.$b.'</a>
                                    
                                    </li>
                                
                                ';
                }
                if(($i - 2) > 1){
                    $b = $i - 2;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no='.$b;
                    }else{
                        $link = '?page_no='.$b;
                    }
                    echo '
                                
                                    <li>
                                        
                                        <a href="'.$link.'">'.$b.'</a>
                                    
                                    </li>
                                
                                ';
                }
                if(($i - 1) > 1){
                    $b = $i - 1;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no='.$b;
                    }else{
                        $link = '?page_no='.$b;
                    }
                    echo '
                                
                                    <li>
                                        
                                        <a href="'.$link.'">'.$b.'</a>
                                    
                                    </li>
                                
                                ';
                }
                echo '
                                
                                    <li>
                                        
                                        <a class="'.$active.'" href="'.$link.'">'.$i.'</a>
                                    
                                    </li>
                                
                                ';



                if(($i + 1) <$pagination){
                    $b = $i + 1;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no='.$b;
                    }else{
                        $link = '?page_no='.$b;
                    }
                    echo '
                                
                                    <li>
                                        
                                        <a href="'.$link.'">'.$b.'</a>
                                    
                                    </li>
                                
                                ';
                }
                if(($i + 2) <$pagination){
                    $b = $i + 2;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no='.$b;
                    }else{
                        $link = '?page_no='.$b;
                    }
                    echo '
                                
                                    <li>
                                        
                                        <a href="'.$link.'">'.$b.'</a>
                                    
                                    </li>
                                
                                ';
                }
                if(($i + 3) < $pagination){
                    $b = $i + 3;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no='.$b;
                    }else{
                        $link = '?page_no='.$b;
                    }

                    echo '
                                
                                    <li>
                                        
                                        <a  href="'.$link.'">'.$b.'</a>
                                    
                                    </li>
                                
                                ';
                }
                if(($i + 4) < $pagination ){
                    echo '<li  class="sep_min">...</li>';
                }
                if($pagination) {
                    $b = $pagination;
                    if(isset($_GET['searchTrue']) || isset($_GET["status_filter"])){
                        $link = $actual_link.'&page_no='.$pagination;
                    }else{
                        $link = '?page_no='.$pagination;
                    }
                    echo '
                                
                                    <li>
                                        
                                        <a class="" href="' . $link . '">' . $pagination . '</a>
                                    
                                    </li>
                                
                                ';
                }

                ?>

            </ul>



        </div>
    </div>






<?php
get_footer();

?>
        <script>
            $(window).on("load",function (){
                var start_date = "<?php if(isset($_GET['timestamp'])){echo $_GET['timestamp'];}else{'';}?>";
                var end_date =  "<?php if(isset($_GET['timestamp_end'])){echo $_GET['timestamp_end'];}else{'';}?>";
                LoadcostTotal(start_date,end_date);

            });
        </script>
