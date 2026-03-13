<?php
/**
 * Main Functions File
 * * Handles the loading of all separate functionality modules.
 */

// Define theme directory constant for performance (avoids repeated function calls)
if ( ! defined( 'WALKBYME_DIR' ) ) {
    define( 'WALKBYME_DIR', get_template_directory() );
}

// List of all files to include
$includes = [
    // Core & Setup
    'functions/theme-setup.php',  // Theme support (title-tag, logo, etc)
    'functions/core.php',         // WordPress core customizations
    'functions/assets.php',       // Scripts and styles enqueuing
    
    // Functionality
    'inc/cart-slide.php',
    'inc/product-filters.php',
    'inc/details-product.php',          // Product specific details
    'inc/product-tabs.php',
    'popup-builder/popup-builder.php',  // Custom popup builder
    'functions/analytics.php',          // Analytics tracking
    'functions/woocommerce.php',        // WooCommerce hooks & filters
    'functions/seo.php',                // Custom SEO functions
    'functions/sitemaps.php',           // XML Sitemap generation
    'functions/meta-boxes.php',         // Backend meta boxes
    'functions/custom-post-types.php',  // CPT registration (Gallery etc)
    'functions/badges.php'              // Product badges system
];

// Loop through and safely require files
foreach ( $includes as $file ) {
    $filepath = WALKBYME_DIR . '/' . $file;
    if ( file_exists( $filepath ) ) {
        require_once $filepath;
    } else {
        // Silent fail on production, but useful to know structure works
        // error_log( 'File not found: ' . $filepath ); 
    }
}

// Initialize Badges System safely
add_action('after_setup_theme', function() {
    if ( class_exists( 'WC_Product_Badges_System' ) ) {
        WC_Product_Badges_System::get_instance();
    }
});



add_action('admin_enqueue_scripts', function($hook) {
    if ($hook === 'woocommerce_page_welcome-popup') wp_enqueue_media();
});






function get_product_handmade_note() {
    global $product;
    
    // Όλα τα jewelry slugs (κύριες + ανδρικές υποκατηγορίες)
    $jewelry_slugs = array(
        'vraxiolia',
        'daxtilidia', 
        'kolie',
        'skoularikia',
        'andrika-vraxiolia',
        'andrika-daxtilidia',
        'andrika-kolie'
    );
    
    // Καπέλα
    $hats_slugs = array(
        'hats',
    );
    
    if (has_term($jewelry_slugs, 'product_cat', $product->get_id())) {
        return 'Κάθε κόσμημα κατασκευάζεται στο χέρι αποκλειστικά για εσάς. Ευχαριστούμε που στηρίζετε το <b>ελληνικό χειροποίητο κόσμημα</b>.';
    }
    
    if (has_term($hats_slugs, 'product_cat', $product->get_id())) {
        return 'Κάθε καπέλο επιλέγεται με προσοχή για την ποιότητα και την αισθητική του. Ευχαριστούμε που στηρίζετε την <b>ελληνική επιχειρηματικότητα</b>.';
    }
    
    return 'Ευχαριστούμε για την εμπιστοσύνη σας.';
}


function get_availability_label() {
    global $product;
    
    // Όλα τα jewelry slugs (κύριες + ανδρικές υποκατηγορίες)
    $jewelry_slugs = array(
        'vraxiolia',
        'daxtilidia', 
        'kolie',
        'skoularikia',
        'andrika-vraxiolia',
        'andrika-daxtilidia',
        'andrika-kolie'
    );
    
    if (has_term($jewelry_slugs, 'product_cat', $product->get_id())) {
        return 'Χρόνος Ετοιμασίας';
    }
    
    return 'Διαθεσιμότητα';
}