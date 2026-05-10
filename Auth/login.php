<?php

// Template Name: Login Page

if(isset($_GET['LogoutAllUser'])){
    $logged = update_user_meta(get_current_user_id(),"active_user",0);
    $sessions = WP_Session_Tokens::get_instance(get_current_user_id());
    $sessions->destroy_all();
}
IFLoggedAndVerify("Login");
get_header();
?>

<section class="LoginSection">

    <div class="container">

        <div class="LoginBox">

            <div class="row">
                <div class="col-md-12">
                    <div class="box_Login_Intro">

                        <img src="<?=wp_get_attachment_url(117729)?>" id="LoginLog">
                        <?=PL_LoginPaged();?>
                    </div>
                </div>
            </div>

        </div>


    </div>

</section>

<?php
get_footer();
?>