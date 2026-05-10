<?php
get_header();
while ( have_posts() ) : the_post();
    $id = get_the_ID();
?>
    <section class="page_inner_elementor">
        <?=the_content()?>
    </section>
<?php
endwhile;
get_footer(); ?>