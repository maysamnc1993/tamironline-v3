<?php
add_action( 'init', 'Orders' );
add_action( 'init', 'Order_brand' );
add_action( 'init', 'Order_device' );
add_action( 'init', 'Order_state' );
add_action( 'init', 'Order_city' );
add_action( 'init', 'Tickets' );
add_action( 'init', 'financial' );


function Orders() {
    $labels = array(
        'name'               => "سفارشات",
        'singular_name'      => "سفاشات",
        'add_new'            => "افزودن",
        'add_new_item'       => "افزودن",
        'edit_item'          => "ویرایش",
        'new_item'           => "جدید",
        'all_items'          => "همه",
        'view_item'          => "مشاهده",
        'search_items'       => "جستجو",
        'not_found'          => "یافت نشد",
        'not_found_in_trash' => "یافت نشد",
        'parent_item_colon'  => "والد",
        'menu_name'          => 'سفارشات'
    );
    $args = array(
        'public' => true,
        'labels' => $labels,
        'description'   => 'محصولات',
        'menu_position' => 14,
        'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
        'has_archive' => true,
        'rewrite' => array('slug' => 'orders','with_front' => true),
        'show_ui' => true,
        'publicly_queryable' => true,
        'query_var' => true,
        'capability_type' => 'post',
    );
    register_post_type( 'orders', $args );
}
function Order_brand() {

    $labels = array(
        'name'          => 'برندها',
        'singular_name' => 'برندها',
        'edit_item'     => 'ویرایش',
        'update_item'   => 'ویرایش',
        'add_new_item'  => 'افزودن',
        'menu_name'     => 'برندها'
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'rewrite'           => array( 'slug' => 'brand' )
    );

    register_taxonomy( 'brand', array("orders"), $args );

}
function Order_device() {

    $labels = array(
        'name'          => 'دستگاه ها',
        'singular_name' => 'دستگاه ها',
        'edit_item'     => 'ویرایش',
        'update_item'   => 'ویرایش',
        'add_new_item'  => 'افزودن',
        'menu_name'     => 'دستگاه ها'
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'rewrite'           => array( 'slug' => 'device' )
    );

    register_taxonomy( 'device', array("orders"), $args );

}
function Order_state() {

    $labels = array(
        'name'          => 'شهرها',
        'singular_name' => 'شهرها',
        'edit_item'     => 'ویرایش',
        'update_item'   => 'ویرایش',
        'add_new_item'  => 'افزودن',
        'menu_name'     => 'شهرها'
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'rewrite'           => array( 'slug' => 'state' )
    );

    register_taxonomy( 'state', array("orders"), $args );

}
function Order_city() {

    $labels = array(
        'name'          => 'شهر',
        'singular_name' => 'شهر',
        'edit_item'     => 'ویرایش',
        'update_item'   => 'ویرایش',
        'add_new_item'  => 'افزودن',
        'menu_name'     => 'شهر'
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'rewrite'           => array( 'slug' => 'city' )
    );

    register_taxonomy( 'city', array("orders"), $args );

}

function Tickets() {
    $labels = array(
        'name'               => "تیکت ها",
        'singular_name'      => "تیکت ها",
        'add_new'            => "افزودن",
        'add_new_item'       => "افزودن",
        'edit_item'          => "ویرایش",
        'new_item'           => "جدید",
        'all_items'          => "همه",
        'view_item'          => "مشاهده",
        'search_items'       => "جستجو",
        'not_found'          => "یافت نشد",
        'not_found_in_trash' => "یافت نشد",
        'parent_item_colon'  => "والد",
        'menu_name'          => 'تیکت ها'
    );
    $args = array(
        'public' => true,
        'labels' => $labels,
        'description'   => 'محصولات',
        'menu_position' => 14,
        'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
        'has_archive' => true,
        'rewrite' => array('slug' => 'ticket','with_front' => true),
        'show_ui' => true,
        'publicly_queryable' => true,
        'query_var' => true,
        'capability_type' => 'post',
    );
    register_post_type( 'ticket', $args );
}
function financial() {
    $labels = array(
        'name'               => "مالی",
        'singular_name'      => "تیکت ها",
        'add_new'            => "افزودن",
        'add_new_item'       => "افزودن",
        'edit_item'          => "ویرایش",
        'new_item'           => "جدید",
        'all_items'          => "همه",
        'view_item'          => "مشاهده",
        'search_items'       => "جستجو",
        'not_found'          => "یافت نشد",
        'not_found_in_trash' => "یافت نشد",
        'parent_item_colon'  => "والد",
        'menu_name'          => 'مالی'
    );
    $args = array(
        'public' => true,
        'labels' => $labels,
        'description'   => 'محصولات',
        'menu_position' => 14,
        'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
        'has_archive' => true,
        'rewrite' => array('slug' => 'financial','with_front' => true),
        'show_ui' => true,
        'publicly_queryable' => true,
        'query_var' => true,
        'capability_type' => 'post',
    );
    register_post_type( 'financial', $args );
}


?>