<?php 
add_action( 'wp_enqueue_scripts', function(){
     $parent_style = 'parent-style';

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/_build/web/assets/styles/modern.css',
        array( $parent_style )
        );
    
    // register DrawerView
    wp_register_script( 'DrawerView', get_stylesheet_directory_uri() . '/_build/web/assets/scripts/views/DrawerView.js', array('jquery'), '', true );

    // register shim
    wp_register_script( 'shim', get_stylesheet_directory_uri() . '/_build/web/assets/scripts/shim.js', array('jquery', 'modernizr'), '', true );

    // register Modernizr
    wp_register_script( 'modernizr','https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js', array('jquery'), '', false );

    // register App
    //    wp_register_script( 'App', get_stylesheet_directory_uri() . '/_build/web/assets/scripts/App.js', array('jquery', 'DrawerView', 'ROIView', 'MagnificPopupView', 'FunnelView', 'MegaMenuView'), '', true );
    wp_register_script( 'App', get_stylesheet_directory_uri() . '/_build/web/assets/scripts/App.js', array('jquery', 'DrawerView'), '', true );

    // register main
    wp_register_script( 'main', get_stylesheet_directory_uri() . '/_build/web/assets/scripts/main.js', array('shim','App'), '', true );
    wp_enqueue_script( 'main' );
});


add_action( 'after_setup_theme', function(){
    register_nav_menus( array('slide_out_nav' => 'Slide Out Navigation Menu') );
    require_once 'inc/register-side-nav-widgets.php';
}, 0 );

/**
 * Return the appropriate phone number based on a user's geo location or a logical default.
 *
 * @return mixed|null|string|void
 */
function gps_insight_get_geo_phone_number() {
    // Figure out a default phone number that makes sense.
    return 'REQUEST A DEMO';
    $defaultPhoneNumber = '480-663-9454';

    if (function_exists('get_field')) {
        // Let's see if there was a default phone number defined on the ACF options page.
        $acfDefaultPhoneNumber = get_field('default_phone_number', 'option');

        if ($acfDefaultPhoneNumber) {
            $defaultPhoneNumber = $acfDefaultPhoneNumber;
        }
    }

    // If the geo phone number plugin isn't enabled, return the default phone number
    if (!class_exists('NerderyGeoPhoneNumber')) {
        return $defaultPhoneNumber;
    }

    // The plugin is enabled, so return the value determined by the plugin.
    $nerderyGeoPhoneNumber = new NerderyGeoPhoneNumber();
    return $nerderyGeoPhoneNumber->getPhoneNumberForCurrentUserLocation();
}