 <?php
/**
 * Assets Loading - Optimized for Performance
 */

// Enhanced CSS Loading
function walkbyme_load_css() {
    $theme_version = wp_get_theme()->get('Version');
    
    // Main theme stylesheet
    wp_enqueue_style(
        'walkbyme-main-style', 
        get_template_directory_uri() . '/assets/public/css/mystyle.css',
        array(),
        $theme_version
    );
    
    // Font Awesome (CDN)
    wp_enqueue_style(
        'font-awesome', 
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
        array(),
        '6.4.0'
    );
}
add_action('wp_enqueue_scripts', 'walkbyme_load_css');

// Admin Styles
function custom_admin_styles() {
    if (file_exists(get_template_directory() . '/assets/public/css/admin-style.css')) {
        wp_enqueue_style( 'custom-admin-style', get_template_directory_uri() . '/assets/public/css/admin-style.css' );
    }
}
add_action( 'admin_enqueue_scripts', 'custom_admin_styles' );

// Enhanced JS Loading
function walkbyme_load_js() {
    $theme_version = wp_get_theme()->get('Version');
    
    // Conditional Load: Slick Slider
    // Loads only on Front Page, Products, or pages with the [slider] shortcode
    if (is_front_page() || is_product() || (is_page() && has_shortcode(get_post()->post_content, 'slider'))) {
        
        // JS
        wp_enqueue_script(
            'slick-carousel',
            'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js',
            array('jquery'),
            '1.8.1',
            true // Load in footer
        );
        
        // CSS (Check if not already enqueued to avoid duplicates)
        if (!wp_style_is('slick-carousel-css')) {
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
    }
    
    // Main Theme JS
    wp_enqueue_script(
        'walkbyme-main-js',
        get_template_directory_uri() . '/assets/js/myjs.js',
        array('jquery'), // Depends on jQuery
        $theme_version,
        true // Load in footer
    );
    
    // Parallax JS (FIXED SYNTAX ERROR HERE)
    wp_enqueue_script(
        'walkbyme-parallax-js',
        get_template_directory_uri() . '/assets/js/parallax.js',
        array(), // Empty array for dependencies was missing
        $theme_version,
        true // Load in footer
    );

    // Localize script for AJAX
    wp_localize_script('walkbyme-main-js', 'walkbyme_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('walkbyme_nonce'),
        'theme_url'=> get_template_directory_uri(),
        'is_mobile'=> wp_is_mobile() ? 'true' : 'false'
    ));
}
add_action('wp_enqueue_scripts', 'walkbyme_load_js');

// Remove Default WooCommerce Styles
// Note: Ensure your 'mystyle.css' covers all necessary Woo elements
function walkbyme_woocommerce_styles() {
    add_filter('woocommerce_enqueue_styles', '__return_false');
}
add_action('init', 'walkbyme_woocommerce_styles');

// Preload Critical Resources
function walkbyme_preload_resources() {
    // Preloading the main CSS is handled by browser usually, but this helps prioritize
    echo '<link rel="preload" href="' . esc_url(get_template_directory_uri() . '/assets/public/css/mystyle.css') . '" as="style">';
    
    // Preload Font Awesome DNS to speed up connection
    echo '<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>';
}
add_action('wp_head', 'walkbyme_preload_resources', 1);