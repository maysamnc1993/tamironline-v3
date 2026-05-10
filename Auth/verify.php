<?php
IFLoggedAndVerify("Verify");

// Template Name: Verify Page
get_header();
?>


    <section class="LoginSection">

        <div class="container">

            <div class="LoginBox">

                <div class="row">
                    <div class="col-md-12">
                        <div class="box_Login_Intro">

                            <img src="<?=wp_get_attachment_url(117729)?>" id="LoginLog">
                            <p>برای تایید ورود کد احراز هویت را وارد نمایید.</p>
                            <?=PL_Verify();?>
                        </div>
                    </div>

                </div>

            </div>


        </div>

    </section>

<?php
get_footer();
?>