<?php

require_once (get_template_directory() . '/inc/details-product.php');
require_once (get_template_directory() . '/popup-builder/popup-builder.php');


// Load all required files
$required_files = [
    'functions/analytics.php',
    'functions/core.php',         // WordPress core customizations
    'functions/assets.php',       // Scripts and styles
    'functions/theme-setup.php',  // Theme setup
    'functions/woocommerce.php',  // WooCommerce functions
    'functions/seo.php',          // SEO functions
    'functions/sitemaps.php',     // Sitemap generation
    'functions/meta-boxes.php',   // Custom meta boxes
    'functions/custom-post-types.php', // Custom post types
    'functions/badges.php'
];

foreach ($required_files as $file) {
    if (file_exists(get_template_directory() . '/' . $file)) {
        require_once get_template_directory() . '/' . $file;
    }
}

add_action('after_setup_theme', function() {
    WC_Product_Badges_System::get_instance();
});





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







//popup cart


// ==============================================
// ADMIN PANEL ΓΙΑ POPUP SETTINGS
// ==============================================


// 1. Προσθήκη Admin Menu
add_action('admin_menu', 'popup_discount_admin_menu');
function popup_discount_admin_menu() {
    add_submenu_page(
        'woocommerce',
        'Popup Discounts',
        'Popup Discounts',
        'manage_options',
        'popup-discounts',
        'popup_discount_admin_page'
    );
}

// 2. Εγγραφή Settings
add_action('admin_init', 'popup_discount_settings_init');
function popup_discount_settings_init() {
    // Εγγραφή settings group με sanitization callback
    register_setting('popup_discount_settings', 'popup_discount_options', 'popup_discount_sanitize_options');
    
    // Βασικές Ρυθμίσεις Section
    add_settings_section(
        'popup_basic_settings',
        'Βασικές Ρυθμίσεις',
        'popup_basic_settings_callback',
        'popup_discount_settings'
    );
    
    // Enable/Disable Νέων Πελατών
    add_settings_field(
        'enable_new_customer_popup',
        'Popup Νέων Πελατών',
        'enable_new_customer_popup_callback',
        'popup_discount_settings',
        'popup_basic_settings'
    );
    
    // Enable/Disable Επιστρεφόντων Πελατών
    add_settings_field(
        'enable_returning_customer_popup',
        'Popup Επιστρεφόντων Πελατών',
        'enable_returning_customer_popup_callback',
        'popup_discount_settings',
        'popup_basic_settings'
    );
    
    // Επιλογή Σελίδας Εμφάνισης
    add_settings_field(
        'popup_display_pages',
        'Σελίδες Εμφάνισης',
        'popup_display_pages_callback',
        'popup_discount_settings',
        'popup_basic_settings'
    );
    
    // Ποσοστό Έκπτωσης Νέων Πελατών
    add_settings_field(
        'new_customer_discount',
        'Έκπτωση Νέων Πελατών (%)',
        'new_customer_discount_callback',
        'popup_discount_settings',
        'popup_basic_settings'
    );
    
    // Ποσοστό Έκπτωσης Επιστρεφόντων Πελατών
    add_settings_field(
        'returning_customer_discount',
        'Έκπτωση Επιστρεφόντων (%)',
        'returning_customer_discount_callback',
        'popup_discount_settings',
        'popup_basic_settings'
    );
    
    // Promotional Banner Section
    add_settings_section(
        'popup_promotional_settings',
        'Promotional Banner',
        'popup_promotional_settings_callback',
        'popup_discount_settings'
    );
    
    // Enable Promotional Banner
    add_settings_field(
        'enable_promotional_banner',
        'Ενεργοποίηση Banner',
        'enable_promotional_banner_callback',
        'popup_discount_settings',
        'popup_promotional_settings'
    );
    
    // Upload Banner Image
    add_settings_field(
        'promotional_banner_image',
        'Εικόνα Banner',
        'promotional_banner_image_callback',
        'popup_discount_settings',
        'popup_promotional_settings'
    );
    
    // Banner URL
    add_settings_field(
        'promotional_banner_url',
        'URL Προορισμού',
        'promotional_banner_url_callback',
        'popup_discount_settings',
        'popup_promotional_settings'
    );
    
    // Banner Display Pages
    add_settings_field(
        'promotional_banner_pages',
        'Σελίδες Εμφάνισης Banner',
        'promotional_banner_pages_callback',
        'popup_discount_settings',
        'popup_promotional_settings'
    );
    
    // Banner Frequency
    add_settings_field(
        'promotional_banner_frequency',
        'Συχνότητα Εμφάνισης',
        'promotional_banner_frequency_callback',
        'popup_discount_settings',
        'popup_promotional_settings'
    );
    
    // Banner Persistence
    add_settings_field(
        'promotional_banner_persistence',
        'Επίπεδο Επιμονής',
        'promotional_banner_persistence_callback',
        'popup_discount_settings',
        'popup_promotional_settings'
    );
}

// 3. Sanitization function για τις επιλογές
function popup_discount_sanitize_options($input) {
    $sanitized = array();
    
    // Checkboxes - αν δεν υπάρχουν στο input, σημαίνει ότι είναι unchecked
    $sanitized['enable_new_customer_popup'] = isset($input['enable_new_customer_popup']) ? 1 : 0;
    $sanitized['enable_returning_customer_popup'] = isset($input['enable_returning_customer_popup']) ? 1 : 0;
    
    // Array των σελίδων - αν δεν υπάρχει, άδειος πίνακας
    $sanitized['popup_display_pages'] = isset($input['popup_display_pages']) ? array_map('sanitize_text_field', $input['popup_display_pages']) : array();
    
    // Ποσοστά εκπτώσεων
    $sanitized['new_customer_discount'] = isset($input['new_customer_discount']) ? absint($input['new_customer_discount']) : 20;
    $sanitized['returning_customer_discount'] = isset($input['returning_customer_discount']) ? absint($input['returning_customer_discount']) : 10;
    
    // Validation για ποσοστά (1-100)
    if ($sanitized['new_customer_discount'] < 1 || $sanitized['new_customer_discount'] > 100) {
        $sanitized['new_customer_discount'] = 20;
    }
    if ($sanitized['returning_customer_discount'] < 1 || $sanitized['returning_customer_discount'] > 100) {
        $sanitized['returning_customer_discount'] = 10;
    }
    
    // Promotional Banner Settings
    $sanitized['enable_promotional_banner'] = isset($input['enable_promotional_banner']) ? 1 : 0;
    $sanitized['promotional_banner_image'] = isset($input['promotional_banner_image']) ? sanitize_url($input['promotional_banner_image']) : '';
    $sanitized['promotional_banner_url'] = isset($input['promotional_banner_url']) ? sanitize_url($input['promotional_banner_url']) : '';
    $sanitized['promotional_banner_pages'] = isset($input['promotional_banner_pages']) ? array_map('sanitize_text_field', $input['promotional_banner_pages']) : array();
    $sanitized['promotional_banner_frequency'] = isset($input['promotional_banner_frequency']) ? sanitize_text_field($input['promotional_banner_frequency']) : 'daily';
    $sanitized['promotional_banner_persistence'] = isset($input['promotional_banner_persistence']) ? sanitize_text_field($input['promotional_banner_persistence']) : 'balanced';
    
    return $sanitized;
}

// 4. Callbacks για Settings Fields
function popup_basic_settings_callback() {
    echo '<p>Ρυθμίστε τις βασικές παραμέτρους για τα popup εκπτώσεων.</p>';
}

function enable_new_customer_popup_callback() {
    $options = get_option('popup_discount_options', array());
    $checked = isset($options['enable_new_customer_popup']) ? intval($options['enable_new_customer_popup']) : 1;
    ?>
    <label>
        <input type="checkbox" name="popup_discount_options[enable_new_customer_popup]" value="1" <?php checked(1, $checked); ?> />
        Ενεργοποίηση popup για νέους πελάτες
    </label>
    <p class="description">Εμφάνιση popup με έκπτωση για χρήστες που δεν έχουν κάνει ποτέ παραγγελία.</p>
    <?php
}

function enable_returning_customer_popup_callback() {
    $options = get_option('popup_discount_options', array());
    $checked = isset($options['enable_returning_customer_popup']) ? intval($options['enable_returning_customer_popup']) : 1;
    ?>
    <label>
        <input type="checkbox" name="popup_discount_options[enable_returning_customer_popup]" value="1" <?php checked(1, $checked); ?> />
        Ενεργοποίηση popup για επιστρέφοντες πελάτες
    </label>
    <p class="description">Εμφάνιση popup με έκπτωση για πελάτες που έχουν κάνει παραγγελία στο παρελθόν.</p>
    <?php
}

function popup_display_pages_callback() {
    $options = get_option('popup_discount_options', array());
    $selected_pages = isset($options['popup_display_pages']) ? $options['popup_display_pages'] : array('cart');
    ?>
    <div style="margin-bottom: 10px;">
        <label>
            <input type="checkbox" name="popup_discount_options[popup_display_pages][]" value="cart" 
                   <?php echo in_array('cart', $selected_pages) ? 'checked' : ''; ?> />
            <strong>Σελίδα Καλαθιού (Cart)</strong>
        </label>
        <p class="description" style="margin-left: 20px; color: #666;">Εμφάνιση όταν ο χρήστης βρίσκεται στη σελίδα του καλαθιού</p>
    </div>
    
    <div style="margin-bottom: 10px;">
        <label>
            <input type="checkbox" name="popup_discount_options[popup_display_pages][]" value="checkout" 
                   <?php echo in_array('checkout', $selected_pages) ? 'checked' : ''; ?> />
            <strong>Σελίδα Checkout</strong>
        </label>
        <p class="description" style="margin-left: 20px; color: #666;">Εμφάνιση στη σελίδα ολοκλήρωσης παραγγελίας</p>
    </div>
    
    <div style="margin-bottom: 10px;">
        <label>
            <input type="checkbox" name="popup_discount_options[popup_display_pages][]" value="product" 
                   <?php echo in_array('product', $selected_pages) ? 'checked' : ''; ?> />
            <strong>Σελίδες Προϊόντων</strong>
        </label>
        <p class="description" style="margin-left: 20px; color: #666;">Εμφάνιση σε μεμονωμένες σελίδες προϊόντων</p>
    </div>
    
    <div style="margin-bottom: 10px;">
        <label>
            <input type="checkbox" name="popup_discount_options[popup_display_pages][]" value="shop" 
                   <?php echo in_array('shop', $selected_pages) ? 'checked' : ''; ?> />
            <strong>Σελίδα Shop</strong>
        </label>
        <p class="description" style="margin-left: 20px; color: #666;">Εμφάνιση στην κεντρική σελίδα του καταστήματος</p>
    </div>
    
    <div style="margin-bottom: 10px;">
        <label>
            <input type="checkbox" name="popup_display_options[popup_display_pages][]" value="category" 
                   <?php echo in_array('category', $selected_pages) ? 'checked' : ''; ?> />
            <strong>Σελίδες Κατηγοριών</strong>
        </label>
        <p class="description" style="margin-left: 20px; color: #666;">Εμφάνιση σε σελίδες κατηγοριών προϊόντων</p>
    </div>
    
    <p class="description"><strong>Προσοχή:</strong> Μπορείτε να επιλέξετε πολλαπλές σελίδες. Το popup θα εμφανίζεται σε όλες τις επιλεγμένες.</p>
    <?php
}

function new_customer_discount_callback() {
    $options = get_option('popup_discount_options', array());
    $discount = isset($options['new_customer_discount']) ? intval($options['new_customer_discount']) : 20;
    ?>
    <input type="number" name="popup_discount_options[new_customer_discount]" value="<?php echo esc_attr($discount); ?>" 
           min="1" max="100" step="1" class="regular-text" />
    <p class="description">Ποσοστό έκπτωσης για νέους πελάτες (1-100%). Προεπιλογή: 20%</p>
    <?php
}

function returning_customer_discount_callback() {
    $options = get_option('popup_discount_options', array());
    $discount = isset($options['returning_customer_discount']) ? intval($options['returning_customer_discount']) : 10;
    ?>
    <input type="number" name="popup_discount_options[returning_customer_discount]" value="<?php echo esc_attr($discount); ?>" 
           min="1" max="100" step="1" class="regular-text" />
    <p class="description">Ποσοστό έκπτωσης για επιστρέφοντες πελάτες (1-100%). Προεπιλογή: 10%</p>
    <?php
}

// Promotional Banner Callbacks
function popup_promotional_settings_callback() {
    echo '<p>Ρυθμίστε το promotional banner για ειδικές προσφορές και εκδηλώσεις.</p>';
    echo '<p><strong>Σημείωση:</strong> Όταν είναι ενεργό το promotional banner, τα αυτόματα popups απενεργοποιούνται.</p>';
}

function enable_promotional_banner_callback() {
    $options = get_option('popup_discount_options', array());
    $checked = isset($options['enable_promotional_banner']) ? intval($options['enable_promotional_banner']) : 0;
    ?>
    <label>
        <input type="checkbox" name="popup_discount_options[enable_promotional_banner]" value="1" <?php checked(1, $checked); ?> />
        Ενεργοποίηση promotional banner
    </label>
    <p class="description">Όταν είναι ενεργό, θα εμφανίζεται αντί των αυτόματων popup εκπτώσεων.</p>
    <?php
}

function promotional_banner_image_callback() {
    $options = get_option('popup_discount_options', array());
    $image_url = isset($options['promotional_banner_image']) ? $options['promotional_banner_image'] : '';
    ?>
    <div class="promotional-banner-upload">
        <input type="url" name="popup_discount_options[promotional_banner_image]" value="<?php echo esc_attr($image_url); ?>" 
               id="promotional_banner_image" class="large-text" placeholder="https://example.com/banner.jpg" />
        <button type="button" class="button" id="upload_banner_button">Επιλογή Εικόνας</button>
        
        <?php if ($image_url): ?>
            <div class="banner-preview" style="margin-top: 10px;">
                <p><strong>Προεπισκόπηση:</strong></p>
                <img src="<?php echo esc_url($image_url); ?>" style="max-width: 300px; height: auto; border: 1px solid #ddd;" />
            </div>
        <?php endif; ?>
    </div>
    <p class="description">Ανεβάστε την εικόνα του banner. Προτεινόμενες διαστάσεις: 400x300px ή παρόμοιες αναλογίες.</p>
    
    <script>
    jQuery(document).ready(function($) {
        $('#upload_banner_button').click(function(e) {
            e.preventDefault();
            
            var custom_uploader = wp.media({
                title: 'Επιλογή Banner Εικόνας',
                button: {
                    text: 'Χρήση αυτής της εικόνας'
                },
                multiple: false
            });
            
            custom_uploader.on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                $('#promotional_banner_image').val(attachment.url);
                
                // Ενημέρωση preview
                $('.banner-preview').remove();
                $('.promotional-banner-upload').append(
                    '<div class="banner-preview" style="margin-top: 10px;">' +
                    '<p><strong>Προεπισκόπηση:</strong></p>' +
                    '<img src="' + attachment.url + '" style="max-width: 300px; height: auto; border: 1px solid #ddd;" />' +
                    '</div>'
                );
            });
            
            custom_uploader.open();
        });
    });
    </script>
    <?php
}

function promotional_banner_url_callback() {
    $options = get_option('popup_discount_options', array());
    $banner_url = isset($options['promotional_banner_url']) ? $options['promotional_banner_url'] : '';
    ?>
    <input type="url" name="popup_discount_options[promotional_banner_url]" value="<?php echo esc_attr($banner_url); ?>" 
           class="large-text" placeholder="https://example.com/sale-page" />
    <p class="description">URL όπου θα μεταφέρεται ο χρήστης όταν κάνει κλικ στο banner. Αν μείνει κενό, το banner δεν θα είναι clickable.</p>
    <?php
}

function promotional_banner_pages_callback() {
    $options = get_option('popup_discount_options', array());
    $selected_pages = isset($options['promotional_banner_pages']) && is_array($options['promotional_banner_pages']) ? $options['promotional_banner_pages'] : array('sitewide');
    ?>
    <div style="margin-bottom: 15px;">
        <label>
            <input type="radio" name="popup_discount_options[promotional_banner_pages][]" value="sitewide" 
                   <?php echo in_array('sitewide', $selected_pages) ? 'checked' : ''; ?> />
            <strong>🌐 Όλες οι σελίδες (Sitewide)</strong>
        </label>
        <p class="description" style="margin-left: 20px; color: #666;">Εμφάνιση σε όλες τις σελίδες του website - Καλύτερο για μεγάλες προσφορές</p>
    </div>
    
    <div style="margin-bottom: 15px;">
        <label>
            <input type="radio" name="popup_discount_options[promotional_banner_pages][]" value="woocommerce" 
                   <?php echo in_array('woocommerce', $selected_pages) ? 'checked' : ''; ?> />
            <strong>🛒 Μόνο WooCommerce σελίδες</strong>
        </label>
        <p class="description" style="margin-left: 20px; color: #666;">Shop, προϊόντα, κατηγορίες, καλάθι, checkout - Εστιασμένο σε πωλήσεις</p>
    </div>
    
    <div style="margin-bottom: 15px;">
        <label>
            <input type="radio" name="popup_discount_options[promotional_banner_pages][]" value="custom" 
                   <?php echo in_array('custom', $selected_pages) || (!in_array('sitewide', $selected_pages) && !in_array('woocommerce', $selected_pages)) ? 'checked' : ''; ?> />
            <strong>⚙️ Προσαρμοσμένες σελίδες</strong>
        </label>
        <div id="custom-pages-options" style="margin-left: 20px; margin-top: 10px; <?php echo (!in_array('sitewide', $selected_pages) && !in_array('woocommerce', $selected_pages)) ? '' : 'display:none;'; ?>">
            <div style="margin-bottom: 8px;">
                <label>
                    <input type="checkbox" name="popup_discount_options[promotional_banner_custom_pages][]" value="cart" 
                           <?php echo in_array('cart', $selected_pages) ? 'checked' : ''; ?> />
                    Σελίδα Καλαθιού
                </label>
            </div>
            <div style="margin-bottom: 8px;">
                <label>
                    <input type="checkbox" name="popup_discount_options[promotional_banner_custom_pages][]" value="checkout" 
                           <?php echo in_array('checkout', $selected_pages) ? 'checked' : ''; ?> />
                    Σελίδα Checkout
                </label>
            </div>
            <div style="margin-bottom: 8px;">
                <label>
                    <input type="checkbox" name="popup_discount_options[promotional_banner_custom_pages][]" value="product" 
                           <?php echo in_array('product', $selected_pages) ? 'checked' : ''; ?> />
                    Σελίδες Προϊόντων
                </label>
            </div>
            <div style="margin-bottom: 8px;">
                <label>
                    <input type="checkbox" name="popup_discount_options[promotional_banner_custom_pages][]" value="shop" 
                           <?php echo in_array('shop', $selected_pages) ? 'checked' : ''; ?> />
                    Σελίδα Shop
                </label>
            </div>
            <div style="margin-bottom: 8px;">
                <label>
                    <input type="checkbox" name="popup_discount_options[promotional_banner_custom_pages][]" value="category" 
                           <?php echo in_array('category', $selected_pages) ? 'checked' : ''; ?> />
                    Σελίδες Κατηγοριών
                </label>
            </div>
            <div style="margin-bottom: 8px;">
                <label>
                    <input type="checkbox" name="popup_discount_options[promotional_banner_custom_pages][]" value="home" 
                           <?php echo in_array('home', $selected_pages) ? 'checked' : ''; ?> />
                    Αρχική Σελίδα
                </label>
            </div>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('input[name="popup_discount_options[promotional_banner_pages][]"]').change(function() {
            if ($(this).val() === 'custom') {
                $('#custom-pages-options').slideDown();
            } else {
                $('#custom-pages-options').slideUp();
            }
        });
    });
    </script>
    
    <p class="description"><strong>💡 Συμβουλή:</strong> Για εκπτώσεις/sales επιλέξτε "Όλες οι σελίδες" για μέγιστη εμβέλεια.</p>
    <?php
}

function promotional_banner_frequency_callback() {
    $options = get_option('popup_discount_options', array());
    $frequency = isset($options['promotional_banner_frequency']) ? $options['promotional_banner_frequency'] : 'daily';
    ?>
    <select name="popup_discount_options[promotional_banner_frequency]" class="regular-text">
        <option value="session" <?php selected($frequency, 'session'); ?>>🔄 Κάθε session (όταν κλείσει το browser)</option>
        <option value="daily" <?php selected($frequency, 'daily'); ?>>📅 Κάθε μέρα</option>
        <option value="every3days" <?php selected($frequency, 'every3days'); ?>>🗓️ Κάθε 3 μέρες</option>
        <option value="weekly" <?php selected($frequency, 'weekly'); ?>>📆 Κάθε εβδομάδα</option>
        <option value="once" <?php selected($frequency, 'once'); ?>>🔒 Μόνο μια φορά (never again)</option>
    </select>
    <p class="description">Πόσο συχνά θα εμφανίζεται το banner αφού ο χρήστης το κλείσει.</p>
    
    <div style="background: #f0f8ff; padding: 12px; border-left: 4px solid #3498db; margin-top: 10px;">
        <strong>📊 Προτάσεις ανά τύπο προσφοράς:</strong><br>
        • <strong>Black Friday/Μεγάλες εκπτώσεις:</strong> Κάθε session<br>
        • <strong>Νέα προϊόντα:</strong> Κάθε μέρα<br>
        • <strong>Newsletter signup:</strong> Κάθε εβδομάδα<br>
        • <strong>Ανακοινώσεις:</strong> Μόνο μια φορά
    </div>
    <?php
}

function promotional_banner_persistence_callback() {
    $options = get_option('popup_discount_options', array());
    $persistence = isset($options['promotional_banner_persistence']) ? $options['promotional_banner_persistence'] : 'balanced';
    ?>
    <select name="popup_discount_options[promotional_banner_persistence]" class="regular-text">
        <option value="gentle" <?php selected($persistence, 'gentle'); ?>>😌 Χαμηλή - Σεβασμός στον χρήστη</option>
        <option value="balanced" <?php selected($persistence, 'balanced'); ?>>🤝 Μεσαία - Ισορροπημένη προσέγγιση</option>
        <option value="aggressive" <?php selected($persistence, 'aggressive'); ?>>💪 Υψηλή - Μέγιστη εμβέλεια</option>
    </select>
    <p class="description">Πόσο "επιμονετικό" θα είναι το banner στην εμφάνιση.</p>
    
    <div style="background: #fff3cd; padding: 12px; border-left: 4px solid #ffc107; margin-top: 10px;">
        <strong>⚖️ Επεξήγηση επιπέδων:</strong><br>
        • <strong>Χαμηλή:</strong> Μια φορά ανά επίσκεψη, λιγότερες σελίδες<br>
        • <strong>Μεσαία:</strong> Κανονική συμπεριφορά, 1-2 φορές ανά session<br>
        • <strong>Υψηλή:</strong> Κάθε 3-4 σελίδες, περισσότερες ευκαιρίες
    </div>
    <?php
}

// 4. Admin Page HTML
function popup_discount_admin_page() {
    // Έλεγχος αν έγινε save
    if (isset($_GET['settings-updated'])) {
        add_settings_error('popup_discount_messages', 'popup_discount_message', 'Οι ρυθμίσεις αποθηκεύτηκαν επιτυχώς!', 'updated');
    }
    
    settings_errors('popup_discount_messages');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <div style="background: white; padding: 20px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px;">
            <h2 style="margin-top: 0;">📊 Γρήγορη Επισκόπηση</h2>
            <?php
            $options = get_option('popup_discount_options', array());
            $new_enabled = isset($options['enable_new_customer_popup']) ? intval($options['enable_new_customer_popup']) : 1;
            $returning_enabled = isset($options['enable_returning_customer_popup']) ? intval($options['enable_returning_customer_popup']) : 1;
            $promotional_enabled = isset($options['enable_promotional_banner']) ? intval($options['enable_promotional_banner']) : 0;
            $display_pages = isset($options['popup_display_pages']) && is_array($options['popup_display_pages']) ? $options['popup_display_pages'] : array('cart');
            $banner_pages = isset($options['promotional_banner_pages']) && is_array($options['promotional_banner_pages']) ? $options['promotional_banner_pages'] : array('cart');
            ?>
            <p><strong>Popup Νέων Πελατών:</strong> 
                <span style="color: <?php echo $new_enabled ? '#27ae60' : '#e74c3c'; ?>;">
                    <?php echo $new_enabled ? '✅ Ενεργό' : '❌ Ανενεργό'; ?>
                </span>
            </p>
            <p><strong>Popup Επιστρεφόντων:</strong> 
                <span style="color: <?php echo $returning_enabled ? '#27ae60' : '#e74c3c'; ?>;">
                    <?php echo $returning_enabled ? '✅ Ενεργό' : '❌ Ανενεργό'; ?>
                </span>
            </p>
            <p><strong>Promotional Banner:</strong> 
                <span style="color: <?php echo $promotional_enabled ? '#f39c12' : '#e74c3c'; ?>;">
                    <?php echo $promotional_enabled ? '🔥 Ενεργό' : '❌ Ανενεργό'; ?>
                </span>
            </p>
            <?php if ($promotional_enabled): ?>
                <p><strong>Banner εμφάνιση σε:</strong> <?php echo implode(', ', $banner_pages); ?></p>
                <p style="background: #fff3cd; padding: 10px; border-radius: 4px; border-left: 4px solid #ffc107;">
                    <strong>⚠️ Προσοχή:</strong> Το promotional banner έχει προτεραιότητα - τα αυτόματα popups δεν θα εμφανίζονται.
                </p>
            <?php else: ?>
                <p><strong>Popups εμφάνιση σε:</strong> <?php echo implode(', ', $display_pages); ?></p>
            <?php endif; ?>
        </div>

        <form action="options.php" method="post">
            <?php
            settings_fields('popup_discount_settings');
            do_settings_sections('popup_discount_settings');
            submit_button('Αποθήκευση Ρυθμίσεων');
            ?>
        </form>
        
        <div style="background: #f9f9f9; padding: 15px; border-left: 4px solid #3498db; margin-top: 30px;">
            <h3 style="margin-top: 0;">💡 Συμβουλές</h3>
            <ul>
                <li><strong>Καλάθι:</strong> Η καλύτερη σελίδα για popup - ο χρήστης έχει ήδη ενδιαφέρον για αγορά</li>
                <li><strong>Checkout:</strong> Τελευταία ευκαιρία πριν χάσετε τον πελάτη</li>
                <li><strong>Προϊόν:</strong> Καλό για impulse buying, αλλά μπορεί να είναι invasive</li>
                <li><strong>Ποσοστά:</strong> 15-25% για νέους, 10-15% για επιστρέφοντες είναι συνήθως αποτελεσματικά</li>
            </ul>
        </div>
    </div>
    
    <!-- WordPress Media Library Support -->
    <script>
    jQuery(document).ready(function($) {
        // Enqueue WordPress media scripts αν δεν είναι ήδη loaded
        if (typeof wp !== 'undefined' && wp.media) {
            // Already loaded
        } else {
            // Load media scripts
            wp.media = wp.media || {};
        }
    });
    </script>
    
    <style>
    .form-table th {
        width: 250px;
        font-weight: 600;
    }
    .form-table td input[type="number"] {
        width: 80px;
    }
    .description {
        font-style: italic;
        color: #666;
        margin-top: 5px;
    }
    .promotional-banner-upload {
        max-width: 600px;
    }
    .promotional-banner-upload input[type="url"] {
        width: 70%;
        margin-right: 10px;
    }
    .banner-preview img {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-radius: 4px;
    }
    </style>
    <?php
}

// Enqueue media scripts στο admin
add_action('admin_enqueue_scripts', 'popup_discount_admin_scripts');
function popup_discount_admin_scripts($hook) {
    if ($hook !== 'woocommerce_page_popup-discounts') return;
    
    wp_enqueue_media();
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_style('wp-color-picker');
}


// 5. Βοηθητική συνάρτηση για έλεγχο αν είναι ενεργοποιημένο το popup
function is_popup_enabled_for_page() {
    $options = get_option('popup_discount_options', array());
    $display_pages = isset($options['popup_display_pages']) ? $options['popup_display_pages'] : array('cart');
    
    if (is_cart() && in_array('cart', $display_pages)) return true;
    if (is_checkout() && in_array('checkout', $display_pages)) return true;
    if (is_product() && in_array('product', $display_pages)) return true;
    if (is_shop() && in_array('shop', $display_pages)) return true;
    if (is_product_category() && in_array('category', $display_pages)) return true;
    
    return false;
}

// 6. Βοηθητικές συναρτήσεις για τις ρυθμίσεις (ενημερωμένες)
function get_new_customer_discount_percentage() {
    $options = get_option('popup_discount_options', array());
    return isset($options['new_customer_discount']) ? intval($options['new_customer_discount']) : 20;
}

function get_returning_customer_discount_percentage() {
    $options = get_option('popup_discount_options', array());
    return isset($options['returning_customer_discount']) ? intval($options['returning_customer_discount']) : 10;
}

function is_new_customer_popup_enabled() {
    $options = get_option('popup_discount_options', array());
    return isset($options['enable_new_customer_popup']) ? intval($options['enable_new_customer_popup']) : 1;
}

function is_returning_customer_popup_enabled() {
    $options = get_option('popup_discount_options', array());
    return isset($options['enable_returning_customer_popup']) ? intval($options['enable_returning_customer_popup']) : 1;
}

// Βοηθητικές συναρτήσεις για promotional banner
function is_promotional_banner_enabled() {
    $options = get_option('popup_discount_options', array());
    return isset($options['enable_promotional_banner']) ? intval($options['enable_promotional_banner']) : 0;
}

function get_promotional_banner_image() {
    $options = get_option('popup_discount_options', array());
    return isset($options['promotional_banner_image']) ? $options['promotional_banner_image'] : '';
}

function get_promotional_banner_url() {
    $options = get_option('popup_discount_options', array());
    return isset($options['promotional_banner_url']) ? $options['promotional_banner_url'] : '';
}

function is_promotional_banner_enabled_for_page() {
    if (!is_promotional_banner_enabled()) return false;
    
    $options = get_option('popup_discount_options', array());
    $banner_pages = isset($options['promotional_banner_pages']) && is_array($options['promotional_banner_pages']) ? $options['promotional_banner_pages'] : array('sitewide');
    
    // Αν είναι sitewide, εμφάνιση παντού
    if (in_array('sitewide', $banner_pages)) {
        return true;
    }
    
    // Αν είναι μόνο WooCommerce σελίδες
    if (in_array('woocommerce', $banner_pages)) {
        if (is_woocommerce() || is_cart() || is_checkout() || is_account_page()) {
            return true;
        }
        return false;
    }
    
    // Custom σελίδες (παλιά λογική)
    if (is_cart() && in_array('cart', $banner_pages)) return true;
    if (is_checkout() && in_array('checkout', $banner_pages)) return true;
    if (is_product() && in_array('product', $banner_pages)) return true;
    if (is_shop() && in_array('shop', $banner_pages)) return true;
    if (is_product_category() && in_array('category', $banner_pages)) return true;
    if (is_home() && in_array('home', $banner_pages)) return true;
    
    return false;
}

function get_promotional_banner_frequency() {
    $options = get_option('popup_discount_options', array());
    return isset($options['promotional_banner_frequency']) ? $options['promotional_banner_frequency'] : 'daily';
}

function get_promotional_banner_persistence() {
    $options = get_option('popup_discount_options', array());
    return isset($options['promotional_banner_persistence']) ? $options['promotional_banner_persistence'] : 'balanced';
}


//front



// 1. Προσθήκη promotional banner στο footer
add_action('wp_footer', 'add_promotional_banner');
function add_promotional_banner() {
    // Έλεγχος αν είναι ενεργοποιημένο το promotional banner
    if (!is_promotional_banner_enabled_for_page()) return;
    
    // Έλεγχος αν πρέπει να εμφανιστεί βάσει frequency και persistence
    if (!should_show_promotional_banner()) return;
    
    // Παίρνουμε τις ρυθμίσεις
    $banner_image = get_promotional_banner_image();
    $banner_url = get_promotional_banner_url();
    $persistence = get_promotional_banner_persistence();
    
    // Αν δεν υπάρχει εικόνα, δεν εμφανίζουμε banner
    if (empty($banner_image)) return;
    
    ?>
    <div id="promotional-banner" style="display:none;">
        <div class="banner-overlay">
            <div class="banner-content">
                <span class="banner-close">&times;</span>
                <?php if (!empty($banner_url)): ?>
                    <a href="<?php echo esc_url($banner_url); ?>" class="banner-link" target="_blank">
                        <img src="<?php echo esc_url($banner_image); ?>" alt="Promotional Banner" class="banner-image" />
                    </a>
                <?php else: ?>
                    <img src="<?php echo esc_url($banner_image); ?>" alt="Promotional Banner" class="banner-image" />
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

// 2. CSS και JavaScript για το promotional banner
add_action('wp_footer', 'add_promotional_banner_styles_scripts');
function add_promotional_banner_styles_scripts() {
    if (!is_promotional_banner_enabled_for_page()) return;
    ?>
    <style>
    #promotional-banner {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 999999;
    }
    
    .banner-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        box-sizing: border-box;
    }
    
    .banner-content {
        position: relative;
        max-width: 90%;
        max-height: 90%;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        animation: bannerSlideIn 0.4s ease-out;
    }
    
    @keyframes bannerSlideIn {
        from { 
            transform: scale(0.8) translateY(-20px); 
            opacity: 0; 
        }
        to { 
            transform: scale(1) translateY(0); 
            opacity: 1; 
        }
    }
    
    .banner-close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 28px;
        font-weight: bold;
        color: #666;
        cursor: pointer;
        z-index: 10;
        background: rgba(255,255,255,0.9);
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .banner-close:hover {
        background: rgba(231, 76, 60, 0.9);
        color: white;
        transform: scale(1.1);
    }
    
    .banner-image {
        display: block;
        max-width: 100%;
        height: auto;
        border-radius: 10px;
    }
    
    .banner-link {
        display: block;
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    
    .banner-link:hover {
        transform: scale(1.02);
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .banner-content {
            max-width: 95%;
            max-height: 80%;
            margin: 10px;
        }
        
        .banner-close {
            top: 5px;
            right: 5px;
            font-size: 24px;
            width: 30px;
            height: 30px;
        }
        
        .banner-image {
            border-radius: 8px;
        }
    }
    
    @media (max-width: 480px) {
        .banner-overlay {
            padding: 10px;
        }
        
        .banner-content {
            max-width: 100%;
            max-height: 85%;
        }
    }
    
    /* Accessibility */
    .banner-overlay:focus {
        outline: 3px solid #007cba;
        outline-offset: 2px;
    }
    
    .banner-close:focus {
        outline: 2px solid #007cba;
        outline-offset: 2px;
    }
    </style>

    <script>
    jQuery(document).ready(function($) {
        // Helper function για cookies
        function setCookie(name, value, days) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + value + expires + "; path=/";
        }
        
        function getCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for(var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }
        
        // Παίρνουμε τις ρυθμίσεις από PHP
        var frequency = '<?php echo get_promotional_banner_frequency(); ?>';
        var persistence = '<?php echo get_promotional_banner_persistence(); ?>';
        
        // Υπολογισμός delay βάσει persistence
        var showDelay = 1000; // Default 1 second
        if (persistence === 'gentle') {
            showDelay = 3000; // 3 seconds
        } else if (persistence === 'aggressive') {
            showDelay = 500; // 0.5 seconds
        }
        
        // Εμφάνιση banner
        setTimeout(function() {
            $('#promotional-banner').fadeIn(300);
            $('.banner-overlay').focus();
            
            // Αύξηση counter για persistence tracking
            var viewCount = parseInt(getCookie('banner_view_count') || '0') + 1;
            setCookie('banner_view_count', viewCount, 1);
            
        }, showDelay);
        
        // Κλείσιμο banner
        $(document).on('click', '.banner-close', function() {
            $('#promotional-banner').fadeOut(200);
            
            // Υπολογισμός cookie expiration βάσει frequency
            var cookieDays = 1; // Default
            switch(frequency) {
                case 'session':
                    // Session cookie - εξαφανίζεται όταν κλείσει browser
                    document.cookie = "promotional_banner_closed=1; path=/";
                    break;
                case 'daily':
                    cookieDays = 1;
                    break;
                case 'every3days':
                    cookieDays = 3;
                    break;
                case 'weekly':
                    cookieDays = 7;
                    break;
                case 'once':
                    cookieDays = 365; // 1 year = never again
                    break;
            }
            
            if (frequency !== 'session') {
                setCookie('promotional_banner_closed', '1', cookieDays);
            }
            
            // Reset view count
            setCookie('banner_view_count', '0', 1);
        });
        
        // Κλείσιμο με ESC key
        $(document).on('keydown', function(e) {
            if (e.keyCode === 27 && $('#promotional-banner').is(':visible')) {
                $('.banner-close').click();
            }
        });
        
        // Κλείσιμο με κλικ στο background
        $(document).on('click', '.banner-overlay', function(e) {
            if (e.target === this) {
                $('.banner-close').click();
            }
        });
        
        // Παρακολούθηση κλικ στο banner
        $(document).on('click', '.banner-link', function() {
            console.log('Promotional banner clicked!');
            
            // Μαρκάρισμα ότι έγινε κλικ
            setCookie('banner_clicked', '1', 30);
        });
        
        // Auto-close logic βάσει persistence
        var autoCloseTime = 0;
        if (persistence === 'gentle') {
            autoCloseTime = 20000; // 20 seconds
        } else if (persistence === 'balanced') {
            autoCloseTime = 30000; // 30 seconds  
        } else if (persistence === 'aggressive') {
            autoCloseTime = 45000; // 45 seconds
        }
        
        if (autoCloseTime > 0) {
            setTimeout(function() {
                if ($('#promotional-banner').is(':visible')) {
                    $('.banner-close').click();
                }
            }, autoCloseTime);
        }
    });
    </script>
    <?php
}

// 3. Βοηθητική συνάρτηση που ελέγχει αν πρέπει να εμφανιστεί το banner
function should_show_promotional_banner() {
    $frequency = get_promotional_banner_frequency();
    $persistence = get_promotional_banner_persistence();
    
    // Έλεγχος βάσει frequency
    if (isset($_COOKIE['promotional_banner_closed'])) {
        return false; // Ήδη κλεισμένο βάσει frequency
    }
    
    // Έλεγχος βάσει persistence level
    $view_count = isset($_COOKIE['banner_view_count']) ? intval($_COOKIE['banner_view_count']) : 0;
    
    switch ($persistence) {
        case 'gentle':
            // Μια φορά ανά session, max 2 φορές την ημέρα
            if ($view_count >= 2) return false;
            break;
            
        case 'balanced':
            // Κανονική συμπεριφορά, max 4 φορές την ημέρα
            if ($view_count >= 4) return false;
            break;
            
        case 'aggressive':
            // Πιο επιθετική εμφάνιση, max 8 φορές την ημέρα
            if ($view_count >= 8) return false;
            break;
    }
    
    // Έλεγχος αν έχει κάνει κλικ (μειωμένη επανεμφάνιση)
    if (isset($_COOKIE['banner_clicked'])) {
        // Αν έχει κάνει κλικ, λιγότερες εμφανίσεις
        switch ($persistence) {
            case 'gentle':
                if ($view_count >= 1) return false;
                break;
            case 'balanced':
                if ($view_count >= 2) return false;
                break;
            case 'aggressive':
                if ($view_count >= 3) return false;
                break;
        }
    }
    
    return true;
}

// 4. Ορισμός flag όταν προστίθεται προϊόν στο καλάθι (ενημερωμένο με promotional priority)
add_action('woocommerce_add_to_cart', 'set_popup_flag_with_promotional_priority');
function set_popup_flag_with_promotional_priority() {
    // Αν είναι ενεργό promotional banner, δεν χρειάζεται flag για αυτόματα popups
    if (is_promotional_banner_enabled()) return;
    
    // Μόνο αν είναι ενεργοποιημένο κάποιο αυτόματο popup
    if (is_new_customer_popup_enabled() || is_returning_customer_popup_enabled()) {
        WC()->session->set('show_discount_popup', true);
    }
}

// 5. AJAX handler για καθαρισμό promotional banner cookie (για testing)
add_action('wp_ajax_reset_promotional_banner', 'handle_reset_promotional_banner');
add_action('wp_ajax_nopriv_reset_promotional_banner', 'handle_reset_promotional_banner');

function handle_reset_promotional_banner() {
    if (!wp_verify_nonce($_POST['nonce'], 'reset_banner_nonce')) {
        wp_send_json_error('Μη έγκυρο αίτημα');
        return;
    }
    
    // Καθαρισμός όλων των promotional banner cookies
    setcookie('promotional_banner_closed', '', time() - 3600, '/');
    setcookie('banner_view_count', '', time() - 3600, '/');
    setcookie('banner_clicked', '', time() - 3600, '/');
    
    wp_send_json_success('Banner cookies cleared');
}

// 6. Ορισμός cookie όταν ολοκληρώνεται παραγγελία (για promotional tracking)
add_action('woocommerce_thankyou', 'set_promotional_purchase_cookie');
function set_promotional_purchase_cookie($order_id) {
    // Μαρκάρισμα ότι έγινε παραγγελία (για analytics)
    setcookie('promotional_converted', '1', time() + (30 * 24 * 60 * 60), '/'); // 30 μέρες
    
    // Καθαρισμός promotional banner cookies μετά από conversion
    setcookie('promotional_banner_closed', '', time() - 3600, '/');
    setcookie('banner_view_count', '', time() - 3600, '/');
}
