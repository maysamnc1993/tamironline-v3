<?php

// Add ParrentCategory
function heading_category() {

    $labels = array(
        'name'          => 'سرفصل ها',
        'singular_name' => 'سرفصل ها',
        'edit_item'     => 'ویرایش',
        'update_item'   => 'ویرایش',
        'add_new_item'  => 'افزودن',
        'menu_name'     => 'سرفصل ها'
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => array( 'slug' => 'heading' )
    );

    register_taxonomy( 'heading', array("post"), $args );

}
add_action( 'init', 'heading_category' );