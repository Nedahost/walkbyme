<?php

require_once (get_template_directory() . '/inc/details-product.php');

// Load all required files
$required_files = [
    'functions/core.php',         // WordPress core customizations
    'functions/assets.php',       // Scripts and styles
    'functions/theme-setup.php',  // Theme setup
    'functions/woocommerce.php',  // WooCommerce functions
    'functions/seo.php',          // SEO functions
    'functions/sitemaps.php',     // Sitemap generation
    'functions/meta-boxes.php',   // Custom meta boxes
    'functions/custom-post-types.php' // Custom post types
];

foreach ($required_files as $file) {
    if (file_exists(get_template_directory() . '/' . $file)) {
        require_once get_template_directory() . '/' . $file;
    }
}







/**
 * Προσθήκη metabox για την εξαίρεση προϊόντων από το XML feed του Facebook
 */

// 1. Προσθήκη του metabox στη σελίδα επεξεργασίας προϊόντος
function add_facebook_feed_exclude_metabox() {
    add_meta_box(
        'facebook_feed_exclude_metabox',           // ID του metabox
        'Facebook XML Feed',                       // Τίτλος που εμφανίζεται
        'facebook_feed_exclude_metabox_callback',  // Callback συνάρτηση
        'product',                                 // Τύπος post (product για WooCommerce)
        'side',                                    // Θέση (side = πλαϊνή στήλη)
        'default'                                  // Προτεραιότητα
    );
}
add_action('add_meta_boxes', 'add_facebook_feed_exclude_metabox');

// 2. Περιεχόμενο του metabox
function facebook_feed_exclude_metabox_callback($post) {
    // Προσθήκη nonce για ασφάλεια
    wp_nonce_field('facebook_feed_exclude_metabox', 'facebook_feed_exclude_nonce');
    
    // Ανάκτηση της τρέχουσας τιμής, αν υπάρχει
    $exclude_from_facebook = get_post_meta($post->ID, '_exclude_from_facebook_feed', true);
    
    // Εμφάνιση του checkbox
    ?>
    <p>
        <label for="exclude_from_facebook_feed">
            <input type="checkbox" id="exclude_from_facebook_feed" name="exclude_from_facebook_feed" <?php checked($exclude_from_facebook, 'yes'); ?> />
            Εξαίρεση από το Facebook XML Feed
        </label>
    </p>
    <p class="description">
        Επιλέξτε αυτό το πεδίο για να μην συμπεριληφθεί το προϊόν στο XML feed του Facebook.
    </p>
    <?php
}

// 3. Αποθήκευση των δεδομένων όταν γίνεται αποθήκευση του προϊόντος
function save_facebook_feed_exclude_meta($post_id) {
    // Έλεγχος ασφαλείας με nonce
    if (!isset($_POST['facebook_feed_exclude_nonce']) || 
        !wp_verify_nonce($_POST['facebook_feed_exclude_nonce'], 'facebook_feed_exclude_metabox')) {
        return;
    }
    
    // Έλεγχος για autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Έλεγχος δικαιωμάτων
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Αποθήκευση της τιμής του checkbox
    $exclude = isset($_POST['exclude_from_facebook_feed']) ? 'yes' : 'no';
    update_post_meta($post_id, '_exclude_from_facebook_feed', $exclude);
}
add_action('save_post_product', 'save_facebook_feed_exclude_meta');

/**
 * Βοηθητική συνάρτηση για έλεγχο αν ένα προϊόν είναι εξαιρεμένο από το Facebook feed
 */
function is_product_excluded_from_facebook_feed($product_id) {
    $excluded = get_post_meta($product_id, '_exclude_from_facebook_feed', true);
    return ($excluded === 'yes');
}