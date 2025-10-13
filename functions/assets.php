<?php
/**
 * Assets Loading - Optimized for Performance
 * Enhanced version with better performance and security
 */

// Enhanced CSS Loading with versioning and conditional loading
function walkbyme_load_css() {
    // Theme version for cache busting
    $theme_version = wp_get_theme()->get('Version');
    
    // Main theme stylesheet with proper versioning
    wp_enqueue_style(
        'walkbyme-main-style', 
        get_template_directory_uri() . '/assets/public/css/mystyle.css',
        array(),
        $theme_version
    );
    
    // Font Awesome - Use local version or updated CDN
    wp_enqueue_style(
        'font-awesome', 
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
        array(),
        '6.4.0'
    );

    
    

}
add_action('wp_enqueue_scripts', 'walkbyme_load_css');


function custom_admin_styles() {
    wp_enqueue_style( 'custom-admin-style', get_template_directory_uri() . '/assets/public/css/admin-style.css' ); // Αντικατέστεσε το 'admin-style.css' με το όνομα του αρχείου CSS που θέλεις να χρησιμοποιήσεις
}
add_action( 'admin_enqueue_scripts', 'custom_admin_styles' );

// Enhanced JS Loading with proper dependencies and optimization
function walkbyme_load_js() {
    $theme_version = wp_get_theme()->get('Version');
    
    // Don't load jQuery from CDN - use WordPress built-in version
    // WordPress includes jQuery by default and deregistering can cause conflicts
    
    // Only load Slick on pages that need it
    if (is_front_page() || is_product() || (is_page() && has_shortcode(get_post()->post_content, 'slider'))) {
        wp_enqueue_script(
            'slick-carousel',
            'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js',
            array('jquery'),
            '1.8.1',
            true
        );
        
        // Slick CSS (only when needed)
        wp_enqueue_style(
            'slick-carousel-css',
            'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css',
            array(),
            '1.8.1'
        );
        
        wp_enqueue_style(
            'slick-theme-css',
            'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css',
            array('slick-carousel-css'),
            '1.8.1'
        );
    }
    
    // Main theme JS - load in footer for better performance
    wp_enqueue_script(
        'walkbyme-main-js',
        get_template_directory_uri() . '/assets/js/myjs.js',
        array('jquery'),
        $theme_version,
        true
    );
    
    wp_enqueue_script(
        'walkbyme-parallax-js',
        get_template_directory_uri() . '/assets/js/parallax.js',
        $theme_version,
        true
    );

    // Localize script for AJAX and theme data
    wp_localize_script('walkbyme-main-js', 'walkbyme_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('walkbyme_nonce'),
        'theme_url' => get_template_directory_uri(),
        'is_mobile' => wp_is_mobile() ? 'true' : 'false'
    ));
}
add_action('wp_enqueue_scripts', 'walkbyme_load_js');

// Enhanced WooCommerce styles control
function walkbyme_woocommerce_styles() {
    // Remove all default WooCommerce styles
    add_filter('woocommerce_enqueue_styles', '__return_false');
    
    // Optionally keep only specific WooCommerce styles if needed
    // wp_enqueue_style('woocommerce-layout', WC()->plugin_url() . '/assets/css/woocommerce-layout.css');
}
add_action('init', 'walkbyme_woocommerce_styles');

// Preload critical resources for better performance
function walkbyme_preload_resources() {
    echo '<link rel="preload" href="' . get_template_directory_uri() . '/assets/public/css/mystyle.css" as="style">';
    
    // Preload Google Fonts if used
    // echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
    // echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
    
    // Preload Font Awesome
    echo '<link rel="preconnect" href="https://cdnjs.cloudflare.com">';
}
add_action('wp_head', 'walkbyme_preload_resources', 1);