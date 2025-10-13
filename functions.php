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










// Εμφάνιση των image points στο frontend
function display_product_image_points() {
    global $product;
    
    if (!$product) return;
    
    $image_points = get_post_meta($product->get_id(), 'image_points', true);
    
    if (empty($image_points)) return;
    
    ?>
    <style>
    .woocommerce-product-gallery__wrapper,
    .woocommerce-product-gallery__image {
        position: relative !important;
    }
    .frontend-image-point {
        position: absolute;
        width: 14px;
        height: 14px;
        background-color: red;
        border-radius: 50%;
        transform: translate(-50%, -50%);
        cursor: pointer;
        z-index: 9999 !important;
        pointer-events: auto;
        box-shadow: 0 0 0 3px white, 0 0 10px rgba(0,0,0,0.3);
    }
    #frontend-image-points {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 9998 !important;
    }
    .point-tooltip {
        position: absolute;
        background-color: rgba(0, 0, 0, 0.9);
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 14px;
        white-space: nowrap;
        pointer-events: none;
        z-index: 10000;
        opacity: 0;
        bottom: calc(100% + 10px);
        left: 50%;
        transform: translateX(-50%);
    }
    .point-tooltip:after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 6px solid transparent;
        border-top-color: rgba(0, 0, 0, 0.9);
    }
    .frontend-image-point:hover .point-tooltip {
        opacity: 1;
    }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Περίμενε για το WooCommerce gallery
        setTimeout(function() {
            addImagePoints();
        }, 1000);
        
        function addImagePoints() {
            // Δοκίμασε διάφορους selectors για να βρεις την κύρια εικόνα
            var productImage = document.querySelector('.woocommerce-product-gallery__image img') ||
                              document.querySelector('.product-image img') ||
                              document.querySelector('.woocommerce-product-gallery img');
            
            if (!productImage) {
                console.log('Image not found, trying again...');
                setTimeout(addImagePoints, 500);
                return;
            }
            
            var imagePoints = <?php echo json_encode($image_points); ?>;
            
            // Βρες το σωστό container - το πρώτο div που περιέχει την εικόνα
            var imageContainer = productImage.closest('.woocommerce-product-gallery__image') ||
                                productImage.closest('.product-image') ||
                                productImage.parentElement;
            
            if (!imageContainer) {
                console.log('Container not found');
                return;
            }
            
            // Βεβαιώσου ότι το container είναι relative
            imageContainer.style.position = 'relative';
            
            // Αφαίρεσε παλιά points αν υπάρχουν
            var oldPoints = imageContainer.querySelector('#frontend-image-points');
            if (oldPoints) {
                oldPoints.remove();
            }
            
            // Δημιούργησε container για τα points που να ταιριάζει ακριβώς με την εικόνα
            var pointsContainer = document.createElement('div');
            pointsContainer.id = 'frontend-image-points';
            
            // Κάνε το container να έχει το ίδιο μέγεθος με την εικόνα
            pointsContainer.style.width = productImage.offsetWidth + 'px';
            pointsContainer.style.height = productImage.offsetHeight + 'px';
            
            imageContainer.appendChild(pointsContainer);
            
            console.log('Image dimensions:', productImage.offsetWidth, 'x', productImage.offsetHeight);
            console.log('Container:', imageContainer);
            console.log('Image points:', imagePoints);
            
            // Πρόσθεσε κάθε σημείο
            imagePoints.forEach(function(point, index) {
                var pointEl = document.createElement('div');
                pointEl.className = 'frontend-image-point';
                pointEl.style.left = point.x + '%';
                pointEl.style.top = point.y + '%';
                
                // Απενεργοποίηση zoom όταν περνάς από πάνω από το point
                pointEl.addEventListener('mouseenter', function(e) {
                    e.stopPropagation();
                    // Απενεργοποίηση zoom
                    var img = productImage;
                    if (img && img.classList) {
                        img.style.pointerEvents = 'none';
                    }
                });
                
                pointEl.addEventListener('mouseleave', function(e) {
                    // Επανενεργοποίηση zoom
                    var img = productImage;
                    if (img && img.classList) {
                        setTimeout(function() {
                            img.style.pointerEvents = '';
                        }, 100);
                    }
                });
                
                // Αποτροπή zoom click
                pointEl.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                });
                
                // Custom Tooltip με το κείμενο
                if (point.text && point.text.trim() !== '') {
                    var tooltip = document.createElement('div');
                    tooltip.className = 'point-tooltip';
                    tooltip.textContent = point.text;
                    pointEl.appendChild(tooltip);
                }
                
                console.log('Point ' + (index + 1) + ' at:', point.x.toFixed(2) + '%, ' + point.y.toFixed(2) + '%', '- text:', point.text);
                
                pointsContainer.appendChild(pointEl);
            });
        }
        
        // Ξανά-τοποθέτησε τα points αν αλλάξει το μέγεθος
        var resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                addImagePoints();
            }, 250);
        });
        
        // Παρακολούθησε αν αλλάξει η εικόνα (για galleries με πολλές εικόνες)
        var observer = new MutationObserver(function() {
            setTimeout(addImagePoints, 300);
        });
        
        var gallery = document.querySelector('.woocommerce-product-gallery');
        if (gallery) {
            observer.observe(gallery, { 
                childList: true, 
                subtree: true,
                attributes: true,
                attributeFilter: ['class']
            });
        }
    });
    </script>
    <?php
}
add_action('woocommerce_before_single_product', 'display_product_image_points');






//image point 
function add_custom_image_point_meta_box() {
    add_meta_box(
        'custom_image_point_meta_box',
        __('Image Points', 'woocommerce'),
        'display_custom_image_point_meta_box',
        'product',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_custom_image_point_meta_box');

function display_custom_image_point_meta_box($post) {
    $image_points = get_post_meta($post->ID, 'image_points', true);
    $image_src = has_post_thumbnail($post->ID) ? wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full') : '';

    // Print Product Image
    if ($image_src) {
        echo '<div id="product-image-container" style="position: relative; width: 800px; height: 800px; overflow: hidden;">'; 
        echo '<img id="product-image" src="' . esc_url($image_src[0]) . '" alt="' . esc_attr(get_the_title($post->ID)) . '" style="width: 100%; height: auto;" />';
        
        // Print Image Points
        echo '<div id="product-image-points">';
        if (!empty($image_points)) {
            foreach ($image_points as $index => $point) {
                echo '<div class="product-image-point" data-index="' . $index . '" data-text="' . esc_attr($point['text']) . '" style="position: absolute; left: ' . $point['x'] . '%; top: ' . $point['y'] . '%;"></div>';
            }
        }
        echo '</div></div>';
    }
    
    // Print Form Fields
    echo '<p><label for="image_point_x">' . __('Image Point X Coordinate (%):', 'woocommerce') . '</label>';
    echo '<input type="text" id="image_point_x" name="image_point_x" value="" /></p>';
    echo '<p><label for="image_point_y">' . __('Image Point Y Coordinate (%):', 'woocommerce') . '</label>';
    echo '<input type="text" id="image_point_y" name="image_point_y" value="" /></p>';
    echo '<p><label for="image_point_text">' . __('Additional Text:', 'woocommerce') . '</label>  <br />';
    echo '<textarea id="image_point_text" name="image_point_text"></textarea></p>';

    echo '<input type="hidden" id="points_data" name="points_data" value="" />';
    echo '<input type="hidden" id="editing_index" name="editing_index" value="" />';
    echo '<button type="button" id="add-point-btn">' . __('Add Point', 'woocommerce') . '</button>';
    echo '<button type="button" id="update-point-btn" style="display:none;">' . __('Update Point', 'woocommerce') . '</button>';
    echo '<button type="button" id="cancel-edit-btn" style="display:none;">' . __('Cancel', 'woocommerce') . '</button>';

    // Print Coordinates List
    echo '<p><strong>' . __('Coordinates of Selected Points:', 'woocommerce') . '</strong></p>';
    echo '<ul id="points-list" style="list-style: none; padding: 0;">';
    if (!empty($image_points)) {
        foreach ($image_points as $index => $point) {
            echo '<li data-index="' . $index . '" style="margin-bottom: 10px; padding: 10px; background: #f5f5f5; border-radius: 4px;">';
            echo '<span>X: ' . number_format($point['x'], 2) . '%, Y: ' . number_format($point['y'], 2) . '%, text: ' . esc_html($point['text']) . '</span> ';
            echo '<button type="button" class="edit-point-list-btn" data-index="' . $index . '" style="margin-left: 10px;">Edit</button> ';
            echo '<button type="button" class="delete-point-list-btn" data-index="' . $index . '" style="margin-left: 5px;">Delete</button>';
            echo '</li>';
        }
    }
    echo '</ul>';

    // JavaScript
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var imagePointXInput = document.getElementById('image_point_x');
        var imagePointYInput = document.getElementById('image_point_y');
        var imagePointTextInput = document.getElementById('image_point_text');
        var pointsDataInput = document.getElementById('points_data');
        var editingIndexInput = document.getElementById('editing_index');
        var addPointBtn = document.getElementById('add-point-btn');
        var updatePointBtn = document.getElementById('update-point-btn');
        var cancelEditBtn = document.getElementById('cancel-edit-btn');
        var productImage = document.getElementById('product-image');
        var productImagePoints = document.getElementById('product-image-points');
        var pointsList = document.getElementById('points-list');
        
        var allPoints = [];
        var isDragging = false;
        var currentDragPoint = null;
        var isEditMode = false;

        // Φόρτωση υπαρχόντων σημείων
        document.querySelectorAll('.product-image-point').forEach(function(point) {
            var index = parseInt(point.getAttribute('data-index'));
            var x = parseFloat(point.style.left);
            var y = parseFloat(point.style.top);
            var text = point.getAttribute('data-text');
            allPoints[index] = { x: x, y: y, text: text };
        });

        // Drag & Drop functionality
        function makeDraggable(point) {
            point.addEventListener('mousedown', function(e) {
                isDragging = true;
                currentDragPoint = point;
                point.style.cursor = 'grabbing';
                e.preventDefault();
                e.stopPropagation();
            });
        }

        document.addEventListener('mousemove', function(e) {
            if (isDragging && currentDragPoint) {
                var container = productImage.getBoundingClientRect();
                var x = e.clientX - container.left;
                var y = e.clientY - container.top;
                
                var imageWidth = productImage.offsetWidth;
                var imageHeight = productImage.offsetHeight;
                
                var xPercent = (x / imageWidth) * 100;
                var yPercent = (y / imageHeight) * 100;
                
                // Περιορισμός εντός ορίων
                xPercent = Math.max(0, Math.min(100, xPercent));
                yPercent = Math.max(0, Math.min(100, yPercent));
                
                currentDragPoint.style.left = xPercent + '%';
                currentDragPoint.style.top = yPercent + '%';
                
                // Ενημέρωση στον πίνακα
                var index = parseInt(currentDragPoint.getAttribute('data-index'));
                if (allPoints[index]) {
                    allPoints[index].x = xPercent;
                    allPoints[index].y = yPercent;
                }
            }
        });

        document.addEventListener('mouseup', function() {
            if (isDragging && currentDragPoint) {
                currentDragPoint.style.cursor = 'grab';
                isDragging = false;
                currentDragPoint = null;
                updatePointsList();
            }
        });

        // Κάνε όλα τα υπάρχοντα σημεία draggable
        document.querySelectorAll('.product-image-point').forEach(makeDraggable);

        // Add Click Event to Product Image - μόνο αν δεν κάνουμε drag
        productImage.addEventListener('click', function(event) {
            if (isEditMode || isDragging) return;
            
            var imageWidth = this.offsetWidth;
            var imageHeight = this.offsetHeight;
            
            var xPercent = (event.offsetX / imageWidth) * 100;
            var yPercent = (event.offsetY / imageHeight) * 100;
            
            // Δημιουργία νέου σημείου
            var newIndex = allPoints.length;
            var point = document.createElement('div');
            point.className = 'product-image-point';
            point.setAttribute('data-index', newIndex);
            point.setAttribute('data-text', '');
            point.style.left = xPercent + '%';
            point.style.top = yPercent + '%';
            point.style.cursor = 'grab';
            
            var pointNumber = document.createElement('span');
            pointNumber.className = 'point-number';
            pointNumber.textContent = (newIndex + 1) + '.';
            point.appendChild(pointNumber);
            
            productImagePoints.appendChild(point);
            makeDraggable(point);
            
            allPoints.push({ x: xPercent, y: yPercent, text: '' });
            
            imagePointXInput.value = xPercent.toFixed(2);
            imagePointYInput.value = yPercent.toFixed(2);
            
            updatePointsList();
        });

        // Add Point Button
        addPointBtn.addEventListener('click', function() {
            var xValue = parseFloat(imagePointXInput.value);
            var yValue = parseFloat(imagePointYInput.value);
            var textValue = imagePointTextInput.value;
            
            if (!isNaN(xValue) && !isNaN(yValue)) {
                var newIndex = allPoints.length;
                var point = document.createElement('div');
                point.className = 'product-image-point';
                point.setAttribute('data-index', newIndex);
                point.setAttribute('data-text', textValue);
                point.style.left = xValue + '%';
                point.style.top = yValue + '%';
                point.style.cursor = 'grab';
                
                var pointNumber = document.createElement('span');
                pointNumber.className = 'point-number';
                pointNumber.textContent = (newIndex + 1) + '.';
                point.appendChild(pointNumber);
                
                productImagePoints.appendChild(point);
                makeDraggable(point);
                
                allPoints.push({ x: xValue, y: yValue, text: textValue });
                
                imagePointXInput.value = '';
                imagePointYInput.value = '';
                imagePointTextInput.value = '';
                
                updatePointsList();
            }
        });

        // Edit Point από τη λίστα
        pointsList.addEventListener('click', function(event) {
            if (event.target && event.target.classList.contains('edit-point-list-btn')) {
                var index = parseInt(event.target.getAttribute('data-index'));
                var pointData = allPoints[index];
                
                if (pointData) {
                    imagePointXInput.value = pointData.x.toFixed(2);
                    imagePointYInput.value = pointData.y.toFixed(2);
                    imagePointTextInput.value = pointData.text;
                    editingIndexInput.value = index;
                    
                    isEditMode = true;
                    addPointBtn.style.display = 'none';
                    updatePointBtn.style.display = 'inline-block';
                    cancelEditBtn.style.display = 'inline-block';
                    
                    // Highlight το σημείο
                    var point = document.querySelector('.product-image-point[data-index="' + index + '"]');
                    if (point) {
                        point.style.backgroundColor = 'blue';
                    }
                }
            }
            
            // Delete Point από τη λίστα
            if (event.target && event.target.classList.contains('delete-point-list-btn')) {
                var index = parseInt(event.target.getAttribute('data-index'));
                
                var point = document.querySelector('.product-image-point[data-index="' + index + '"]');
                if (point) {
                    point.remove();
                }
                
                delete allPoints[index];
                updatePointsList();
            }
        });

        // Update Point Button
        updatePointBtn.addEventListener('click', function() {
            var index = parseInt(editingIndexInput.value);
            var xValue = parseFloat(imagePointXInput.value);
            var yValue = parseFloat(imagePointYInput.value);
            var textValue = imagePointTextInput.value;
            
            if (!isNaN(index) && !isNaN(xValue) && !isNaN(yValue)) {
                allPoints[index] = { x: xValue, y: yValue, text: textValue };
                
                var point = document.querySelector('.product-image-point[data-index="' + index + '"]');
                if (point) {
                    point.style.left = xValue + '%';
                    point.style.top = yValue + '%';
                    point.setAttribute('data-text', textValue);
                    point.style.backgroundColor = '';
                }
                
                cancelEdit();
                updatePointsList();
            }
        });

        // Cancel Edit Button
        cancelEditBtn.addEventListener('click', function() {
            cancelEdit();
        });

        function cancelEdit() {
            imagePointXInput.value = '';
            imagePointYInput.value = '';
            imagePointTextInput.value = '';
            editingIndexInput.value = '';
            
            isEditMode = false;
            addPointBtn.style.display = 'inline-block';
            updatePointBtn.style.display = 'none';
            cancelEditBtn.style.display = 'none';
            
            // Αφαίρεση highlight από όλα τα σημεία
            document.querySelectorAll('.product-image-point').forEach(function(p) {
                p.style.backgroundColor = '';
            });
        }

        function updatePointsList() {
            pointsList.innerHTML = '';
            allPoints.forEach(function(point, index) {
                if (point) {
                    var li = document.createElement('li');
                    li.setAttribute('data-index', index);
                    li.style.marginBottom = '10px';
                    li.style.padding = '10px';
                    li.style.background = '#f5f5f5';
                    li.style.borderRadius = '4px';
                    
                    var span = document.createElement('span');
                    span.textContent = 'X: ' + point.x.toFixed(2) + '%, Y: ' + point.y.toFixed(2) + '%, text: ' + point.text;
                    
                    var editBtn = document.createElement('button');
                    editBtn.type = 'button';
                    editBtn.className = 'edit-point-list-btn';
                    editBtn.setAttribute('data-index', index);
                    editBtn.textContent = 'Edit';
                    editBtn.style.marginLeft = '10px';
                    
                    var deleteBtn = document.createElement('button');
                    deleteBtn.type = 'button';
                    deleteBtn.className = 'delete-point-list-btn';
                    deleteBtn.setAttribute('data-index', index);
                    deleteBtn.textContent = 'Delete';
                    deleteBtn.style.marginLeft = '5px';
                    
                    li.appendChild(span);
                    li.appendChild(editBtn);
                    li.appendChild(deleteBtn);
                    pointsList.appendChild(li);
                }
            });
        }

        // Πριν το submit, αποθήκευσε όλα τα σημεία
        var form = document.querySelector('#post');
        if (form) {
            form.addEventListener('submit', function(e) {
                var validPoints = allPoints.filter(function(p) { return p !== undefined; });
                pointsDataInput.value = JSON.stringify(validPoints);
            });
        }
    });
    </script>
    <?php
}

function save_custom_image_point_meta_box($post_id) {
    if (isset($_POST['points_data']) && !empty($_POST['points_data'])) {
        $points_data = json_decode(stripslashes($_POST['points_data']), true);
        
        if (is_array($points_data)) {
            $image_points = [];
            foreach ($points_data as $point) {
                $image_points[] = [
                    'x' => floatval($point['x']),
                    'y' => floatval($point['y']),
                    'text' => sanitize_text_field($point['text']),
                ];
            }
            update_post_meta($post_id, 'image_points', $image_points);
        }
    }
}
add_action('save_post_product', 'save_custom_image_point_meta_box');
?>