<?php
/**
 * Register widget areas for side nav
 *
 */
function side_nav_widgets_init() {

    register_sidebar( array(
        'name'          => 'Side Nav Top',
        'id'            => 'side_nav_top',
        'before_widget' => '<div>',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="rounded">',
        'after_title'   => '</h2>',
    ) );

    register_sidebar( array(
        'name'          => 'Side Nav Bottom',
        'id'            => 'side_nav_bottom',
        'before_widget' => '<div>',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="rounded">',
        'after_title'   => '</h2>',
    ) );
    register_sidebar( array(
        'name'          => 'Main Menu CTA Area',
        'id'            => 'main_menu_cta_area',
        'before_widget' => '',
        'after_widget'  => '',
        'before_title'  => '<h2 style="display:none;">',
        'after_title'   => '</h2>',
    ) );

}
add_action( 'widgets_init', 'side_nav_widgets_init' );
?>