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
// 1. Προσθήκη Admin Menu
add_action('admin_menu', 'promotional_banner_admin_menu');
function promotional_banner_admin_menu() {
    add_submenu_page(
        'woocommerce',
        'Promotional Banner',
        'Promotional Banner',
        'manage_options',
        'promotional-banner',
        'promotional_banner_admin_page'
    );
}

// 2. Εγγραφή Settings
add_action('admin_init', 'promotional_banner_settings_init');
function promotional_banner_settings_init() {
    register_setting('promotional_banner_settings', 'promotional_banner_options', 'promotional_banner_sanitize_options');
    
    add_settings_section(
        'promotional_banner_section',
        'Promotional Banner Settings',
        'promotional_banner_section_callback',
        'promotional_banner_settings'
    );
    
    // Enable Promotional Banner
    add_settings_field(
        'enable_promotional_banner',
        'Ενεργοποίηση Banner',
        'enable_promotional_banner_callback',
        'promotional_banner_settings',
        'promotional_banner_section'
    );
    
    // Upload Banner Image
    add_settings_field(
        'promotional_banner_image',
        'Εικόνα Banner',
        'promotional_banner_image_callback',
        'promotional_banner_settings',
        'promotional_banner_section'
    );
    
    // Banner URL
    add_settings_field(
        'promotional_banner_url',
        'URL Προορισμού',
        'promotional_banner_url_callback',
        'promotional_banner_settings',
        'promotional_banner_section'
    );
}

// 3. Sanitization function
function promotional_banner_sanitize_options($input) {
    $sanitized = array();
    
    $sanitized['enable_promotional_banner'] = isset($input['enable_promotional_banner']) ? 1 : 0;
    $sanitized['promotional_banner_image'] = isset($input['promotional_banner_image']) ? sanitize_url($input['promotional_banner_image']) : '';
    $sanitized['promotional_banner_url'] = isset($input['promotional_banner_url']) ? sanitize_url($input['promotional_banner_url']) : '';
    
    return $sanitized;
}

// 4. Callbacks για Settings Fields
function promotional_banner_section_callback() {
    echo '<p>Ρυθμίστε το promotional banner με image upload.</p>';
}

function enable_promotional_banner_callback() {
    $options = get_option('promotional_banner_options', array());
    $checked = isset($options['enable_promotional_banner']) ? intval($options['enable_promotional_banner']) : 0;
    ?>
    <label>
        <input type="checkbox" name="promotional_banner_options[enable_promotional_banner]" value="1" <?php checked(1, $checked); ?> />
        Ενεργοποίηση promotional banner
    </label>
    <?php
}

function promotional_banner_image_callback() {
    $options = get_option('promotional_banner_options', array());
    $image_url = isset($options['promotional_banner_image']) ? $options['promotional_banner_image'] : '';
    ?>
    <div class="promotional-banner-upload">
        <input type="url" name="promotional_banner_options[promotional_banner_image]" value="<?php echo esc_attr($image_url); ?>" 
               id="promotional_banner_image" class="large-text" placeholder="https://example.com/banner.jpg" />
        <button type="button" class="button" id="upload_banner_button">Επιλογή Εικόνας</button>
        
        <?php if ($image_url): ?>
            <div class="banner-preview" style="margin-top: 10px;">
                <p><strong>Προεπισκόπηση:</strong></p>
                <img src="<?php echo esc_url($image_url); ?>" style="max-width: 300px; height: auto; border: 1px solid #ddd;" />
            </div>
        <?php endif; ?>
    </div>
    <p class="description">Ανεβάστε την εικόνα του banner. Προτεινόμενες διαστάσεις: 400x300px.</p>
    
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
    $options = get_option('promotional_banner_options', array());
    $banner_url = isset($options['promotional_banner_url']) ? $options['promotional_banner_url'] : '';
    ?>
    <input type="url" name="promotional_banner_options[promotional_banner_url]" value="<?php echo esc_attr($banner_url); ?>" 
           class="large-text" placeholder="https://example.com/sale-page" />
    <p class="description">URL όπου θα μεταφέρεται ο χρήστης όταν κάνει κλικ στο banner.</p>
    <?php
}

// 5. Admin Page HTML
function promotional_banner_admin_page() {
    if (isset($_GET['settings-updated'])) {
        add_settings_error('promotional_banner_messages', 'promotional_banner_message', 'Οι ρυθμίσεις αποθηκεύτηκαν επιτυχώς!', 'updated');
    }
    
    settings_errors('promotional_banner_messages');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <form action="options.php" method="post">
            <?php
            settings_fields('promotional_banner_settings');
            do_settings_sections('promotional_banner_settings');
            submit_button('Αποθήκευση Ρυθμίσεων');
            ?>
        </form>
    </div>
    <?php
}

// 6. Enqueue media scripts
add_action('admin_enqueue_scripts', 'promotional_banner_admin_scripts');
function promotional_banner_admin_scripts($hook) {
    if ($hook !== 'woocommerce_page_promotional-banner') return;
    
    wp_enqueue_media();
}

// 7. Βοηθητικές συναρτήσεις
function is_promotional_banner_enabled() {
    $options = get_option('promotional_banner_options', array());
    return isset($options['enable_promotional_banner']) ? intval($options['enable_promotional_banner']) : 0;
}

function get_promotional_banner_image() {
    $options = get_option('promotional_banner_options', array());
    return isset($options['promotional_banner_image']) ? $options['promotional_banner_image'] : '';
}

function get_promotional_banner_url() {
    $options = get_option('promotional_banner_options', array());
    return isset($options['promotional_banner_url']) ? $options['promotional_banner_url'] : '';
}

// 8. Frontend - Εμφάνιση Banner
add_action('wp_footer', 'add_promotional_banner');
function add_promotional_banner() {
    if (!is_promotional_banner_enabled()) return;
    
    $banner_image = get_promotional_banner_image();
    $banner_url = get_promotional_banner_url();
    
    if (empty($banner_image)) return;
    
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
    }
    </style>
    
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
    
    <script>
    jQuery(document).ready(function($) {
        // Helper functions για cookies
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
        
        // Έλεγχος αν έχει δει το banner τις τελευταίες 3 μέρες
        var bannerSeen = getCookie('promotional_banner_seen');
        
        if (!bannerSeen) {
            // Εμφάνιση banner μετά από 2 δευτερόλεπτα
            setTimeout(function() {
                $('#promotional-banner').fadeIn(300);
            }, 2000);
        }
        
        // Κλείσιμο banner
        $(document).on('click', '.banner-close', function() {
            $('#promotional-banner').fadeOut(200);
            // Αποθήκευση cookie για 3 μέρες
            setCookie('promotional_banner_seen', '1', 3);
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
        
        // Κλείσιμο αυτόματα μετά από 30 δευτερόλεπτα
        setTimeout(function() {
            if ($('#promotional-banner').is(':visible')) {
                $('.banner-close').click();
            }
        }, 30000);
    });
    </script>
    <?php
}

