<!DOCTYPE html>
<html lang="en">
<head>

    <?php wp_head();?>
    <meta charset="UTF-8">
    <title>

        <?php
        if(!is_home()){
            if(is_tax()){
                echo single_cat_title(). ' - ';
            }
            else if(is_category())
            {
                echo single_cat_title() .' - ';
            }
            else if(is_archive()){
                echo str_replace("Archive","",get_the_archive_title()).' - ';
            }
            else {
                echo get_the_title() . ' - ';
            }
        }
        echo bloginfo( 'name' );

        ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="https://tamironline.com/wp-content/uploads/2021/02/cropped-favicon-2.png">
    <meta property="og:image" content="https://tamironline.com/wp-content/uploads/2021/02/cropped-favicon-2.png" />
    <link rel="stylesheet" href="<?=get_template_directory_uri()?>/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?=get_template_directory_uri()?>/assets/css/style.css?var=<?=rand(100,1000)?>">
    <link rel="stylesheet" href="<?=get_template_directory_uri()?>/assets/css/responsive.css?var=<?=rand(100,1000)?>">
    <link rel="stylesheet" href="<?=get_template_directory_uri()?>/assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="<?=get_template_directory_uri()?>/assets/css/animate.min.css">
    <!--<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">-->
    <link rel="stylesheet" href="<?=get_template_directory_uri()?>/assets/css/select2.min.css">
    <link rel="stylesheet" href="<?=get_template_directory_uri()?>/assets/css/persian-datepicker.min.css">
    <!--<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">-->
    <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fontisto@v3.0.4/css/fontisto/fontisto.min.css">-->

</head>
<body <?=body_class()?>>


<div class="modal fade" id="ExitPanel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <h1>آیا میخواهید از سیستم خارج شوید</h1>
            <ul class="ExitSistem">
                <li><a href="<?=wp_logout_url('/TamirOnline/Login')?>" class="yes">بلی</a> </li>
                <li><a href="#" class="no">خیر</a> </li>
            </ul>
        </div>
    </div>
</div>


<?php



?>




