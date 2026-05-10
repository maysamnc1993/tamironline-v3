<?php
IFLoggedAndVerify("dashboard");
// Template Name: Operator Dashboard

get_header();
?>
    <section class="DashboardSection">
        <?=NavDashboard();?>

        <div class="main">

            <div class="content">

                <h1 class="title">دسترسی سریع</h1>

                <div class="MenuListDashboard">

                    <?=ListOFMenu();?>

                </div>

            </div>

        </div>
    </section>

<?php
get_footer();

?>