<?php

require_once (get_template_directory() . '/inc/details-product.php');

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

// ΔΙΟΡΘΩΜΕΝΟΣ ΠΛΗΡΗΣ ΚΩΔΙΚΑΣ POPUP - ΜΟΝΟ ΕΚΠΤΩΣΕΙΣ

// 1. Βοηθητική συνάρτηση για έλεγχο αν ο χρήστης έχει αγοράσει στο παρελθόν
function user_has_purchased() {
    // Για logged in χρήστες
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $orders = wc_get_orders(array(
            'customer_id' => $user_id,
            'status' => array('completed', 'processing'),
            'limit' => 1
        ));
        return !empty($orders);
    }
    
    // Εναλλακτικά, έλεγχος με cookie αν έχει ολοκληρώσει παραγγελία
    return isset($_COOKIE['has_purchased']);
}

// 2. Δημιουργία popup στη σελίδα cart για ΝΕΟΥΣ ΠΕΛΑΤΕΣ
add_action('wp_footer', 'add_cart_discount_popup');
function add_cart_discount_popup() {
    // Μόνο στη σελίδα cart
    if (!is_cart()) return;
    
    // Έλεγχος αν έχει ήδη εμφανιστεί το popup
    if (isset($_COOKIE['discount_popup_shown'])) return;
    
    // Έλεγχος αν έχει ήδη πάρει την προσφορά (ΠΟΤΕ ξανά)
    if (isset($_COOKIE['discount_taken_forever'])) return;
    
    // Έλεγχος αν είναι η δεύτερη φορά που λήγει (ΠΟΤΕ ξανά)
    if (isset($_COOKIE['discount_expired_twice'])) return;
    
    // Έλεγχος αν έχει πει "όχι" δύο φορές (ΠΟΤΕ ξανά)
    if (isset($_COOKIE['discount_declined_twice'])) return;
    
    // Έλεγχος αν υπάρχουν προϊόντα στο καλάθι
    if (WC()->cart->is_empty()) return;
    
    // Αν ο χρήστης έχει ήδη παραγγείλει, δεν εμφανίζουμε popup
    if (user_has_purchased()) return;
    
    ?>
    <div id="discount-popup" style="display:none;">
        <div class="popup-overlay">
            <div class="popup-content">
                <span class="popup-close">&times;</span>
                <h3>🎉 Καλωσόρισες – 20% σήμερα!</h3>
                <div class="offer-details">
                    <div class="main-offer">
                        <span class="discount-badge">Ο κωδικός WELCOME20 μπήκε στο καλάθι σου</span>
                    </div>
                    <div class="bonus-offer">
                        <p>✅ Δωρεάν αποστολή πανελλαδικά</p>
                        <p>✅ Άμεση παράδοση</p>
                        <p>✅ 100% ασφαλείς πληρωμές</p>
                    </div>
                    <div class="urgency-timer">
                        <p><strong>⏰ Ισχύει για τα επόμενα:</strong></p>
                        <div id="countdown-timer">
                            <span id="minutes">15</span>:<span id="seconds">00</span>
                        </div>
                    </div>
                </div>
                <button id="apply-discount-btn" class="btn-primary">Θέλω το -20%</button>
                <button id="close-popup-btn" class="btn-secondary">Όχι, δεν θέλω έκπτωση 😞</button>
            </div>
        </div>
    </div>
    <?php
}

// 3. Popup για ΕΠΙΣΤΡΕΦΟΝΤΕΣ ΠΕΛΑΤΕΣ
add_action('wp_footer', 'add_returning_customer_popup');
function add_returning_customer_popup() {
    if (!is_cart() || !user_has_purchased()) return;
    if (isset($_COOKIE['returning_popup_shown'])) return;
    if (WC()->cart->is_empty()) return;
    
    ?>
    <div id="returning-customer-popup" style="display:none;">
        <div class="popup-overlay">
            <div class="popup-content">
                <span class="popup-close">&times;</span>
                <h3>🙏 Καλώς ήρθες πίσω!</h3>
                <p><strong>Ειδική προσφορά</strong> για πιστούς πελάτες:</p>
                <div class="returning-offer">
                    <p>✅ <strong>10% έκπτωση</strong> στην παραγγελία σου</p>
                    <p>✅ Δωρεάν μεταφορικά (πάντα δωρεάν!)</p>
                    <p>✅ Προτεραιότητα στην εξυπηρέτηση</p>
                </div>
                <button id="apply-returning-discount-btn" class="btn-primary">Εφαρμογή Έκπτωσης</button>
                <button class="popup-close btn-secondary">Όχι, ευχαριστώ</button>
            </div>
        </div>
    </div>
    <?php
}

// 4. CSS και JavaScript για τα popups
add_action('wp_footer', 'add_popup_scripts_and_styles');
function add_popup_scripts_and_styles() {
    if (!is_cart()) return;
    ?>
    <style>
    .popup-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.7);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .popup-content {
        background: white;
        padding: 30px;
        border-radius: 10px;
        text-align: center;
        max-width: 400px;
        position: relative;
        animation: slideIn 0.3s ease;
    }
    @keyframes slideIn {
        from { transform: scale(0.7); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .popup-close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 24px;
        cursor: pointer;
        color: #999;
    }
    .btn-primary, .btn-secondary {
        padding: 12px 24px;
        margin: 10px 5px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }
    .btn-primary {
        background: #e74c3c;
        color: white;
    }
    .btn-secondary {
        background: #95a5a6;
        color: white;
    }
    .offer-details {
        text-align: left;
        margin: 20px 0;
    }
    .main-offer {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        margin-bottom: 15px;
    }
    .discount-badge {
        font-size: 16px;
        font-weight: bold;
        display: block;
    }
    .urgency-timer {
        background: #fff3cd;
        border: 2px solid #ffc107;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        margin-top: 15px;
    }
    .urgency-timer p {
        margin: 0 0 10px 0;
        color: #856404;
        font-weight: bold;
    }
    #countdown-timer {
        font-size: 24px;
        font-weight: bold;
        color: #dc3545;
        font-family: 'Courier New', monospace;
    }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    .bonus-offer {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #27ae60;
    }
    .bonus-offer p {
        margin: 5px 0;
        font-size: 14px;
    }
    .returning-offer {
        background: #f0f8ff;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #3498db;
    }
    .returning-offer p {
        margin: 8px 0;
        font-size: 14px;
    }
    .btn-primary:hover {
        background: #c0392b;
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
    .btn-secondary:hover {
        background: #7f8c8d;
        transition: all 0.3s ease;
    }
    @media (max-width: 768px) {
        .popup-content {
            margin: 20px;
            padding: 20px;
            max-width: calc(100% - 40px);
        }
    }
    </style>

    <script>
    jQuery(document).ready(function($) {
        // Helper function για ανάγνωση cookies
        function getCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for(var i=0;i < ca.length;i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
            }
            return null;
        }

        // Έλεγχος αν πρέπει να εμφανιστεί popup (από session)
        <?php if (WC()->session && WC()->session->get('show_discount_popup')): ?>
            setTimeout(function() {
                // Εμφάνιση του κατάλληλου popup
                if ($('#discount-popup').length) {
                    $('#discount-popup').fadeIn();
                    startCountdown();
                } else if ($('#returning-customer-popup').length) {
                    $('#returning-customer-popup').fadeIn();
                }
            }, 1000);
            
            // Καθαρισμός flag από session
            <?php WC()->session->set('show_discount_popup', false); ?>
        <?php endif; ?>
        
        // Countdown timer function
        function startCountdown() {
            var timeLeft = 15 * 60; // 15 λεπτά σε δευτερόλεπτα
            
            var countdownInterval = setInterval(function() {
                var minutes = Math.floor(timeLeft / 60);
                var seconds = timeLeft % 60;
                
                // Προσθήκη leading zero
                minutes = minutes < 10 ? '0' + minutes : minutes;
                seconds = seconds < 10 ? '0' + seconds : seconds;
                
                $('#minutes').text(minutes);
                $('#seconds').text(seconds);
                
                timeLeft--;
                
                // Όταν τελειώσει ο χρόνος
                if (timeLeft < 0) {
                    clearInterval(countdownInterval);
                    
                    // Αφαίρεση του κουπονιού αν υπάρχει στο καλάθι
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        data: {
                            action: 'expire_discount_coupon',
                            nonce: '<?php echo wp_create_nonce("expire_coupon_nonce"); ?>'
                        },
                        success: function(response) {
                            $('#discount-popup').fadeOut();
                            
                            // Έληξε για ΠΡΩΤΗ φορά - δεύτερη ευκαιρία σε 24 ώρες
                            if (!getCookie('discount_expired_once')) {
                                document.cookie = "discount_popup_shown=1; path=/; max-age=86400"; // 24 ώρες
                                document.cookie = "discount_expired_once=1; path=/; max-age=31536000"; // Μαρκάρισμα ότι έληξε μια φορά
                                alert('⏰ Ο χρόνος για την έκπτωση έληξε! Το κουπόνι αφαιρέθηκε από το καλάθι σου. Θα έχεις μια ακόμα ευκαιρία αύριο!');
                            } else {
                                // Έληξε για ΔΕΥΤΕΡΗ φορά - ΠΟΤΕ ξανά
                                document.cookie = "discount_expired_twice=1; path=/; max-age=31536000"; // 1 χρόνος
                                alert('⏰ Ο χρόνος για την έκπτωση έληξε οριστικά! Το κουπόνι αφαιρέθηκε από το καλάθι σου.');
                            }
                            
                            if (response.success && response.data.removed) {
                                location.reload();
                            }
                        }
                    });
                }
                
                // Χρωματισμός για urgency (τελευταία 2 λεπτά)
                if (timeLeft <= 120) {
                    $('#countdown-timer').css('color', '#dc3545');
                    $('.urgency-timer').css('animation', 'pulse 1s infinite');
                }
            }, 1000);
        }

        // Κλείσιμο popup νέων πελατών (ΔΕΝ ΘΕΛΕΙ ΕΚΠΤΩΣΗ)
        $(document).on('click', '#discount-popup .popup-close, #close-popup-btn', function() {
            $('#discount-popup').fadeOut();
            
            // Έλεγχος αν είναι πρώτη φορά που λέει "όχι"
            if (!getCookie('discount_declined_once')) {
                // ΠΡΩΤΗ φορά "όχι" - δεύτερη ευκαιρία σε 5 μέρες
                document.cookie = "discount_popup_shown=1; path=/; max-age=432000"; // 5 μέρες (5 * 24 * 60 * 60)
                document.cookie = "discount_declined_once=1; path=/; max-age=31536000"; // Μαρκάρισμα ότι είπε όχι μια φορά
            } else {
                // ΔΕΥΤΕΡΗ φορά "όχι" - ΠΟΤΕ ξανά
                document.cookie = "discount_declined_twice=1; path=/; max-age=31536000"; // 1 χρόνος
            }
        });

        // Κλείσιμο popup επιστρεφόντων πελατών
        $(document).on('click', '#returning-customer-popup .popup-close', function() {
            $('#returning-customer-popup').fadeOut();
            document.cookie = "returning_popup_shown=1; path=/; max-age=2592000"; // 30 μέρες
        });

        // Εφαρμογή έκπτωσης νέων πελατών
        $(document).on('click', '#apply-discount-btn', function() {
            $(this).text('Εφαρμόζεται...');
            
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'apply_auto_discount',
                    nonce: '<?php echo wp_create_nonce("auto_discount_nonce"); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        $('#discount-popup').fadeOut();
                        // ΠΗΡΕ ΤΗΝ ΕΚΠΤΩΣΗ - ΠΟΤΕ ξανά popup
                        document.cookie = "discount_taken_forever=1; path=/; max-age=31536000"; // 1 χρόνος
                        location.reload();
                    } else {
                        alert('Σφάλμα: ' + (response.data || 'Άγνωστο σφάλμα'));
                        $('#apply-discount-btn').text('Θέλω το -20%');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Σφάλμα σύνδεσης: ' + error);
                    $('#apply-discount-btn').text('Θέλω το -20%');
                }
            });
        });

        // Εφαρμογή έκπτωσης επιστρεφόντων πελατών
        $(document).on('click', '#apply-returning-discount-btn', function() {
            $(this).text('Εφαρμόζεται...');
            
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'apply_returning_discount',
                    nonce: '<?php echo wp_create_nonce("returning_discount_nonce"); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        $('#returning-customer-popup').fadeOut();
                        document.cookie = "returning_popup_shown=1; path=/; max-age=2592000";
                        location.reload();
                    } else {
                        alert('Σφάλμα: ' + (response.data || 'Άγνωστο σφάλμα'));
                        $('#apply-returning-discount-btn').text('Εφαρμογή Έκπτωσης');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Σφάλμα σύνδεσης: ' + error);
                    $('#apply-returning-discount-btn').text('Εφαρμογή Έκπτωσης');
                }
            });
        });
    });
    </script>
    <?php
}

// 5. AJAX handler για εφαρμογή έκπτωσης νέων πελατών
add_action('wp_ajax_apply_auto_discount', 'handle_auto_discount');
add_action('wp_ajax_nopriv_apply_auto_discount', 'handle_auto_discount');

function handle_auto_discount() {
    if (!wp_verify_nonce($_POST['nonce'], 'auto_discount_nonce')) {
        wp_send_json_error('Μη έγκυρο αίτημα');
        return;
    }

    if (!WC()->cart || WC()->cart->is_empty()) {
        wp_send_json_error('Το καλάθι είναι άδειο');
        return;
    }

    $coupon_code = 'AUTO20_' . time() . '_' . wp_rand(100, 999);
    
    try {
        $coupon = new WC_Coupon();
        $coupon->set_code($coupon_code);
        $coupon->set_discount_type('percent');
        $coupon->set_amount(20);
        $coupon->set_individual_use(true);
        $coupon->set_usage_limit(1);
        $coupon->set_usage_limit_per_user(1);
        $coupon->set_date_expires(time() + (15 * 60)); // Λήγει σε 15 λεπτά
        $coupon->set_description('Αυτόματη έκπτωση 20% - Λήγει σε 15 λεπτά');
        
        $coupon_id = $coupon->save();
        
        if ($coupon_id) {
            $result = WC()->cart->apply_coupon($coupon_code);
            
            if ($result) {
                WC()->session->set('auto_discount_applied', $coupon_code);
                
                wp_send_json_success(array(
                    'message' => 'Η έκπτωση εφαρμόστηκε επιτυχώς!',
                    'coupon_code' => $coupon_code
                ));
            } else {
                wp_send_json_error('Αποτυχία εφαρμογής κουπονιού στο καλάθι');
            }
        } else {
            wp_send_json_error('Αποτυχία δημιουργίας κουπονιού');
        }
    } catch (Exception $e) {
        wp_send_json_error('Σφάλμα: ' . $e->getMessage());
    }
}

// 6. AJAX handler για επιστρέφοντες πελάτες
add_action('wp_ajax_apply_returning_discount', 'handle_returning_discount');
add_action('wp_ajax_nopriv_apply_returning_discount', 'handle_returning_discount');

function handle_returning_discount() {
    if (!wp_verify_nonce($_POST['nonce'], 'returning_discount_nonce')) {
        wp_send_json_error('Μη έγκυρο αίτημα');
        return;
    }

    if (!WC()->cart || WC()->cart->is_empty()) {
        wp_send_json_error('Το καλάθι είναι άδειο');
        return;
    }

    $coupon_code = 'RETURN10_' . time() . '_' . wp_rand(100, 999);
    
    try {
        $coupon = new WC_Coupon();
        $coupon->set_code($coupon_code);
        $coupon->set_discount_type('percent');
        $coupon->set_amount(10);
        $coupon->set_individual_use(true);
        $coupon->set_usage_limit(1);
        $coupon->set_usage_limit_per_user(1);
        $coupon->set_date_expires(time() + (48 * 60 * 60)); // 48 ώρες
        $coupon->set_description('Έκπτωση 10% για επιστρέφοντες πελάτες');
        
        $coupon_id = $coupon->save();
        
        if ($coupon_id) {
            $result = WC()->cart->apply_coupon($coupon_code);
            
            if ($result) {
                WC()->session->set('returning_discount_applied', $coupon_code);
                
                wp_send_json_success(array(
                    'message' => 'Η έκπτωση 10% εφαρμόστηκε!',
                    'coupon_code' => $coupon_code
                ));
            } else {
                wp_send_json_error('Αποτυχία εφαρμογής κουπονιού στο καλάθι');
            }
        } else {
            wp_send_json_error('Αποτυχία δημιουργίας κουπονιού');
        }
    } catch (Exception $e) {
        wp_send_json_error('Σφάλμα: ' . $e->getMessage());
    }
}

// 7. AJAX handler για λήξη κουπονιού
add_action('wp_ajax_expire_discount_coupon', 'handle_expire_discount_coupon');
add_action('wp_ajax_nopriv_expire_discount_coupon', 'handle_expire_discount_coupon');

function handle_expire_discount_coupon() {
    if (!wp_verify_nonce($_POST['nonce'], 'expire_coupon_nonce')) {
        wp_send_json_error('Μη έγκυρο αίτημα');
        return;
    }

    $removed = false;
    
    if (WC()->cart && !WC()->cart->is_empty()) {
        $applied_coupons = WC()->cart->get_applied_coupons();
        
        foreach ($applied_coupons as $coupon_code) {
            if (strpos($coupon_code, 'AUTO20_') === 0) {
                WC()->cart->remove_coupon($coupon_code);
                $removed = true;
            }
        }
        
        if ($removed) {
            WC()->cart->calculate_totals();
        }
    }
    
    wp_send_json_success(array(
        'message' => $removed ? 'Το κουπόνι αφαιρέθηκε' : 'Δεν βρέθηκε κουπόνι',
        'removed' => $removed
    ));
}

// 8. Ορισμός flag όταν προστίθεται προϊόν στο καλάθι
add_action('woocommerce_add_to_cart', 'set_popup_flag');
function set_popup_flag() {
    WC()->session->set('show_discount_popup', true);
}

// 9. Ορισμός cookie όταν ολοκληρώνεται παραγγελία
add_action('woocommerce_thankyou', 'set_purchase_cookie');
function set_purchase_cookie($order_id) {
    setcookie('has_purchased', '1', time() + (365 * 24 * 60 * 60), '/');
}

// 10. Τροποποίηση εμφάνισης κουπονιού στο cart/checkout
add_filter('woocommerce_cart_totals_coupon_label', 'custom_coupon_label', 10, 2);
function custom_coupon_label($label, $coupon) {
    if (strpos($coupon->get_code(), 'AUTO20_') === 0) {
        return 'WELCOME20 - Έκπτωση 20%';
    } elseif (strpos($coupon->get_code(), 'RETURN10_') === 0) {
        return 'Έκπτωση 10% (Πιστός πελάτης)';
    }
    return $label;
}

// 11. Τροποποίηση της εμφάνισης στον πίνακα κουπονιών
add_filter('woocommerce_cart_totals_coupon_html', 'custom_coupon_html', 10, 3);
function custom_coupon_html($coupon_html, $coupon, $discount_amount_html) {
    $code = $coupon->get_code();
    
    if (strpos($code, 'AUTO20_') === 0) {
        $custom_html = 'WELCOME20 - Έκπτωση 20% <span style="color: #27ae60; font-weight: bold;">' . $discount_amount_html . '</span>';
        $custom_html .= ' <a href="' . esc_url(add_query_arg('remove_coupon', urlencode($code), wc_get_cart_url())) . '" class="woocommerce-remove-coupon" data-coupon="' . esc_attr($code) . '">Αφαίρεση</a>';
        return $custom_html;
    } elseif (strpos($code, 'RETURN10_') === 0) {
        $custom_html = 'Έκπτωση 10% <span style="color: #3498db; font-weight: bold;">' . $discount_amount_html . '</span>';
        $custom_html .= ' <a href="' . esc_url(add_query_arg('remove_coupon', urlencode($code), wc_get_cart_url())) . '" class="woocommerce-remove-coupon" data-coupon="' . esc_attr($code) . '">Αφαίρεση</a>';
        return $custom_html;
    }
    
    return $coupon_html;
}

// 12. Καθαρισμός ληγμένων κουπονιών
add_action('wp_scheduled_delete', 'cleanup_expired_auto_coupons');
function cleanup_expired_auto_coupons() {
    global $wpdb;
    
    $expired_coupons = $wpdb->get_results("
        SELECT ID FROM {$wpdb->posts} 
        WHERE post_type = 'shop_coupon' 
        AND (post_title LIKE 'AUTO20_%' OR post_title LIKE 'RETURN10_%')
        AND post_date < DATE_SUB(NOW(), INTERVAL 7 DAY)
    ");
    
    foreach ($expired_coupons as $coupon) {
        wp_delete_post($coupon->ID, true);
    }
}
?>