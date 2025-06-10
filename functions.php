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




// Δημιουργία κουπονιού για δωρεάν αντικαταβολή για συγκεκριμένο πελάτη
function create_free_cod_coupon_for_customer($customer_email, $expiry_days = 7) {
    // Δημιουργία μοναδικού κωδικού κουπονιού
    $coupon_code = 'FREE-COD-' . wp_generate_password(8, false);
    
    // Δημιουργία νέου κουπονιού
    $coupon = array(
        'post_title' => $coupon_code,
        'post_content' => '',
        'post_status' => 'publish',
        'post_author' => 1,
        'post_type' => 'shop_coupon'
    );
    
    // Εισαγωγή του κουπονιού στη βάση
    $coupon_id = wp_insert_post($coupon);
    
    // Ορισμός των ιδιοτήτων του κουπονιού - Το κάνουμε fixed_cart με 0 ποσό
    // γιατί θα χειριστούμε την αντικαταβολή ξεχωριστά
    update_post_meta($coupon_id, 'discount_type', 'fixed_cart');
    update_post_meta($coupon_id, 'coupon_amount', '0'); // Μηδενικό ποσό, δεν θέλουμε να κάνει έκπτωση
    update_post_meta($coupon_id, 'individual_use', 'no');
    update_post_meta($coupon_id, 'usage_limit', '1');
    update_post_meta($coupon_id, 'expiry_date', date('Y-m-d', strtotime("+{$expiry_days} days")));
    update_post_meta($coupon_id, 'apply_before_tax', 'yes');
    update_post_meta($coupon_id, 'customer_email', array($customer_email));
    
    // Προσθέτουμε ένα ειδικό πεδίο που θα χρησιμοποιήσουμε για να αναγνωρίσουμε
    // ότι είναι κουπόνι δωρεάν αντικαταβολής
    update_post_meta($coupon_id, 'free_cod_coupon', 'yes');
    
    return $coupon_code;
}

// Αποστολή email με το κουπόνι για δωρεάν αντικαταβολή
function send_free_cod_email($customer_email, $coupon_code, $gender = '', $customer_name = '') {
    // Ανάκτηση της τιμής της αντικαταβολής από τις ρυθμίσεις
    $cod_fee = get_option('cash_on_delivery_fee', 2.5);
    
    // Προσαρμογή προσφώνησης ανάλογα με το φύλο και το όνομα
    $greeting = 'Αγαπητέ/ή πελάτη';
    if (!empty($customer_name)) {
        if ($gender === 'male') {
            $greeting = 'Αγαπητέ ' . $customer_name;
        } elseif ($gender === 'female') {
            $greeting = 'Αγαπητή ' . $customer_name;
        } else {
            $greeting = 'Αγαπητέ/ή ' . $customer_name;
        }
    } else {
        if ($gender === 'male') {
            $greeting = 'Αγαπητέ πελάτη';
        } elseif ($gender === 'female') {
            $greeting = 'Αγαπητή πελάτισσα';
        }
    }
    
    $subject = 'Ειδική προσφορά για εσάς: Δωρεάν αντικαταβολή!';
    
    // Δημιουργία του πλαισίου κουπονιού με CSS
    $coupon_style = '
    <div style="border: 2px dashed #ddd; padding: 15px; margin: 15px 0; text-align: center; background-color: #f9f9f9;">
        <span style="font-size: 20px; font-weight: bold; color: #0066cc;">' . $coupon_code . '</span>
    </div>';
    
    $message = '
    <html>
    <body style="font-family: Arial, sans-serif; line-height: 1.6;">
        <p>' . $greeting . ',</p>
        
        <p>Παρατηρήσαμε ότι έχετε προϊόντα στο καλάθι σας. Θα θέλαμε να σας προσφέρουμε <strong>ΔΩΡΕΑΝ ΑΝΤΙΚΑΤΑΒΟΛΗ</strong> ειδικά για εσάς!</p>

        <p><strong>Για να επωφεληθείτε από αυτήν την προσφορά:</strong></p>
        <ol>
            <li>Επιστρέψτε στο ηλεκτρονικό μας κατάστημα</li>
            <li>Χρησιμοποιήστε τον παρακάτω κωδικό κουπονιού:</li>
        </ol>
        
        ' . $coupon_style . '
        
        <ol start="3">
            <li>Επιλέξτε ως τρόπο πληρωμής την "<strong>Αντικαταβολή</strong>"</li>
        </ol>

        <p>Το κόστος της αντικαταβολής (' . $cod_fee . '€) θα αφαιρεθεί αυτόματα από τη συνολική τιμή της παραγγελίας σας!</p>

        <p>Η προσφορά ισχύει για 7 ημέρες. Μην χάσετε αυτήν την ευκαιρία!</p>

        <p>Με εκτίμηση,<br>
        Η ομάδα του ' . get_bloginfo('name') . '</p>
    </body>
    </html>';
    
    // Ρύθμιση επικεφαλίδων για HTML email
    $headers = array('Content-Type: text/html; charset=UTF-8');
    
    // Αποστολή του email
    wp_mail($customer_email, $subject, $message, $headers);
}

// Λειτουργία για χειροκίνητη αποστολή email σε συγκεκριμένο πελάτη
function send_free_cod_to_specific_customer($customer_email, $gender = '', $customer_name = '') {
    // Δημιουργία κουπονιού
    $coupon_code = create_free_cod_coupon_for_customer($customer_email);
    
    // Αποστολή email
    if ($coupon_code) {
        send_free_cod_email($customer_email, $coupon_code, $gender, $customer_name);
        return true;
    }
    
    return false;
}

// Έλεγχος αν ένα κουπόνι είναι κουπόνι δωρεάν αντικαταβολής
function is_free_cod_coupon($coupon_code) {
    $coupon = new WC_Coupon($coupon_code);
    return (get_post_meta($coupon->get_id(), 'free_cod_coupon', true) === 'yes');
}

// Έλεγχος αν το καλάθι έχει κουπόνι δωρεάν αντικαταβολής
function cart_has_free_cod_coupon() {
    if (!isset(WC()->cart) || WC()->cart->is_empty()) {
        return false;
    }
    
    $applied_coupons = WC()->cart->get_applied_coupons();
    
    if (empty($applied_coupons)) {
        return false;
    }
    
    foreach ($applied_coupons as $coupon_code) {
        if (is_free_cod_coupon($coupon_code)) {
            return true;
        }
    }
    
    return false;
}

// Τροποποίηση της συνάρτησης add_cash_on_delivery_fee για να υποστηρίζει δωρεάν αντικαταβολή
function override_add_cash_on_delivery_fee() {
    if (!is_checkout()) {
        return;
    }

    // Ανάκτηση του ποσού αντικαταβολής
    $cash_on_delivery_fee = get_option('cash_on_delivery_fee', 2.5);

    // Έλεγχος αν το καλάθι έχει προϊόντα με ενεργοποιημένη αντικαταβολή
    if (cart_has_cash_on_delivery() && WC()->session->get('chosen_payment_method') === 'cod') {
        // Έλεγχος αν υπάρχει κουπόνι δωρεάν αντικαταβολής
        if (cart_has_free_cod_coupon()) {
            // Προσθήκη μηδενικού τέλους αντικαταβολής με ετικέτα "Δωρεάν"
            WC()->cart->add_fee(__('Αντικαταβολή (Δωρεάν)', 'your_plugin_textdomain'), 0);
        } else {
            // Προσθήκη κανονικού τέλους αντικαταβολής
            WC()->cart->add_fee(__('Αντικαταβολή', 'your_plugin_textdomain'), $cash_on_delivery_fee);
        }
    }
}

// Αφαίρεση του αρχικού hook για το τέλος αντικαταβολής
remove_action('woocommerce_cart_calculate_fees', 'add_cash_on_delivery_fee');

// Προσθήκη του τροποποιημένου hook
add_action('woocommerce_cart_calculate_fees', 'override_add_cash_on_delivery_fee');

// Τροποποίηση του τίτλου της μεθόδου πληρωμής αντικαταβολής
add_filter('woocommerce_gateway_title', 'modify_cod_gateway_title', 10, 2);
function modify_cod_gateway_title($title, $payment_id) {
    if ($payment_id === 'cod' && cart_has_free_cod_coupon()) {
        $cod_fee = get_option('cash_on_delivery_fee', 2.5);
        return 'Αντικαταβολή (Δωρεάν - Εξοικονομείτε ' . $cod_fee . '€)';
    }
    
    return $title;
}

// Προσθήκη ειδικών μηνυμάτων στο καλάθι όταν είναι ενεργό το κουπόνι δωρεάν αντικαταβολής
add_action('woocommerce_before_cart_totals', 'add_free_cod_message_in_cart');
function add_free_cod_message_in_cart() {
    if (cart_has_free_cod_coupon()) {
        $cod_fee = get_option('cash_on_delivery_fee', 2.5);
        echo '<div class="woocommerce-info">Έχετε εφαρμόσει το κουπόνι δωρεάν αντικαταβολής. Επιλέξτε αντικαταβολή ως τρόπο πληρωμής για να μην χρεωθείτε το τέλος των ' . $cod_fee . '€.</div>';
    }
}

// Προσθήκη ειδικού μηνύματος στη σελίδα checkout
add_action('woocommerce_before_checkout_form', 'add_free_cod_message_in_checkout');
function add_free_cod_message_in_checkout() {
    if (cart_has_free_cod_coupon()) {
        $cod_fee = get_option('cash_on_delivery_fee', 2.5);
        echo '<div class="woocommerce-info">Έχετε εφαρμόσει το κουπόνι δωρεάν αντικαταβολής. Επιλέξτε αντικαταβολή ως τρόπο πληρωμής για να μην χρεωθείτε το τέλος των ' . $cod_fee . '€.</div>';
    }
}

// ΝΕΟΣ ΚΩΔΙΚΑΣ: Παρακολούθηση αλλαγών στη μέθοδο πληρωμής
add_action('woocommerce_checkout_update_order_review', 'check_payment_method_for_cod_coupon');
function check_payment_method_for_cod_coupon($post_data) {
    parse_str($post_data, $output);
    
    // Έλεγχος αν το καλάθι έχει κουπόνι δωρεάν αντικαταβολής
    if (cart_has_free_cod_coupon()) {
        // Έλεγχος αν η επιλεγμένη μέθοδος πληρωμής ΔΕΝ είναι η αντικαταβολή
        if (isset($output['payment_method']) && $output['payment_method'] !== 'cod') {
            // Αποθήκευση του κουπονιού για να μπορούμε να το επαναφέρουμε αργότερα
            $free_cod_coupon = '';
            foreach (WC()->cart->get_applied_coupons() as $coupon_code) {
                if (is_free_cod_coupon($coupon_code)) {
                    $free_cod_coupon = $coupon_code;
                    break;
                }
            }
            
            // Αποθήκευση του κουπονιού στη συνεδρία για πιθανή επαναφορά
            if (!empty($free_cod_coupon)) {
                WC()->session->set('saved_free_cod_coupon', $free_cod_coupon);
                // Αφαίρεση του κουπονιού όταν η μέθοδος πληρωμής δεν είναι αντικαταβολή
                WC()->cart->remove_coupon($free_cod_coupon);
                
                // Προσθήκη μηνύματος
                wc_add_notice('Το κουπόνι δωρεάν αντικαταβολής αφαιρέθηκε επειδή επιλέξατε διαφορετικό τρόπο πληρωμής.', 'notice');
            }
        } 
    } else if (isset($output['payment_method']) && $output['payment_method'] === 'cod') {
        // Έλεγχος αν υπάρχει αποθηκευμένο κουπόνι στη συνεδρία και αν η μέθοδος πληρωμής είναι αντικαταβολή
        $saved_coupon = WC()->session->get('saved_free_cod_coupon');
        if (!empty($saved_coupon)) {
            // Επαναφορά του κουπονιού
            WC()->cart->apply_coupon($saved_coupon);
            WC()->session->set('saved_free_cod_coupon', '');
            
            // Προσθήκη μηνύματος
            wc_add_notice('Το κουπόνι δωρεάν αντικαταβολής εφαρμόστηκε ξανά.', 'success');
        }
    }
}

// ΝΕΟΣ ΚΩΔΙΚΑΣ: Έλεγχος εφαρμογής κουπονιού σε σχέση με τη μέθοδο πληρωμής
add_filter('woocommerce_coupon_is_valid', 'validate_free_cod_coupon', 10, 3);
function validate_free_cod_coupon($is_valid, $coupon, $discount) {
    // Έλεγχος αν είναι κουπόνι δωρεάν αντικαταβολής
    if (get_post_meta($coupon->get_id(), 'free_cod_coupon', true) === 'yes') {
        // Έλεγχος αν η επιλεγμένη μέθοδος πληρωμής είναι αντικαταβολή
        $chosen_payment_method = WC()->session->get('chosen_payment_method');
        
        if ($chosen_payment_method !== 'cod') {
            // Προσθήκη μηνύματος σφάλματος
            if (is_checkout()) {
                wc_add_notice('Το κουπόνι δωρεάν αντικαταβολής μπορεί να χρησιμοποιηθεί μόνο με τη μέθοδο πληρωμής "Αντικαταβολή".', 'error');
            }
            return false;
        }
    }
    
    return $is_valid;
}

// ΝΕΟΣ ΚΩΔΙΚΑΣ: Ενημέρωση του καλαθιού όταν αλλάζει η μέθοδος πληρωμής
add_action('woocommerce_review_order_before_payment', 'add_payment_method_script');
function add_payment_method_script() {
    if (cart_has_free_cod_coupon() || WC()->session->get('saved_free_cod_coupon')) {
        ?>
        <script type="text/javascript">
            jQuery(function($) {
                $('form.checkout').on('change', 'input[name="payment_method"]', function() {
                    // Ενημέρωση του καλαθιού όταν αλλάζει η μέθοδος πληρωμής
                    $('body').trigger('update_checkout');
                });
            });
        </script>
        <?php
    }
}

// Προσθήκη σελίδας διαχειριστή για αποστολή προσαρμοσμένων emails
function custom_admin_menu() {
    add_menu_page(
        'Αποστολή Προσφοράς',
        'Προσφορά Αντικαταβολής',
        'manage_options',
        'custom-offer-sender',
        'custom_offer_sender_page',
        'dashicons-email',
        99
    );
}
add_action('admin_menu', 'custom_admin_menu');

// Περιεχόμενο της σελίδας διαχειριστή
function custom_offer_sender_page() {
    ?>
    <div class="wrap">
        <h1>Αποστολή Προσφοράς Δωρεάν Αντικαταβολής</h1>
        
        <?php
        // Έλεγχος αν υποβλήθηκε η φόρμα
        if (isset($_POST['submit_offer'])) {
            $customer_email = sanitize_email($_POST['customer_email']);
            $gender = isset($_POST['customer_gender']) ? sanitize_text_field($_POST['customer_gender']) : '';
            $customer_name = isset($_POST['customer_name']) ? sanitize_text_field($_POST['customer_name']) : '';
            
            if (!empty($customer_email) && is_email($customer_email)) {
                $result = send_free_cod_to_specific_customer($customer_email, $gender, $customer_name);
                
                if ($result) {
                    echo '<div class="notice notice-success"><p>Το email με το κουπόνι δωρεάν αντικαταβολής εστάλη επιτυχώς στο ' . $customer_email . '!</p></div>';
                } else {
                    echo '<div class="notice notice-error"><p>Υπήρξε πρόβλημα κατά την αποστολή του email.</p></div>';
                }
            } else {
                echo '<div class="notice notice-error"><p>Παρακαλώ εισάγετε ένα έγκυρο email.</p></div>';
            }
        }
        ?>
        
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="customer_email">Email Πελάτη</label></th>
                    <td>
                        <input type="email" name="customer_email" id="customer_email" class="regular-text" required>
                        <p class="description">Εισάγετε το email του πελάτη στον οποίο θέλετε να στείλετε την προσφορά δωρεάν αντικαταβολής.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="customer_name">Όνομα Πελάτη</label></th>
                    <td>
                        <input type="text" name="customer_name" id="customer_name" class="regular-text">
                        <p class="description">Εισάγετε το όνομα του πελάτη για προσωποποιημένη προσφώνηση (π.χ. "Αγαπητέ Γιώργο").</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="customer_gender">Φύλο Πελάτη</label></th>
                    <td>
                        <select name="customer_gender" id="customer_gender" class="regular-text">
                            <option value="">-- Επιλέξτε φύλο --</option>
                            <option value="male">Άνδρας</option>
                            <option value="female">Γυναίκα</option>
                        </select>
                        <p class="description">Επιλέξτε το φύλο του πελάτη για σωστή προσφώνηση στο email.</p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button('Αποστολή Προσφοράς', 'primary', 'submit_offer'); ?>
        </form>
        
        <div class="card" style="max-width: 600px; margin-top: 20px;">
            <h2>Προεπισκόπηση Email</h2>
            <div style="border: 1px solid #ddd; padding: 15px; background-color: #fff;">
                <p><strong>Παράδειγμα προσφώνησης:</strong></p>
                <ul>
                    <li>Με όνομα: "Αγαπητέ Γιώργο," ή "Αγαπητή Μαρία,"</li>
                    <li>Χωρίς όνομα: "Αγαπητέ πελάτη," ή "Αγαπητή πελάτισσα,"</li>
                </ul>
                <p><strong>Παράδειγμα μορφοποίησης κουπονιού:</strong></p>
                <div style="border: 2px dashed #ddd; padding: 15px; margin: 15px 0; text-align: center; background-color: #f9f9f9;">
                    <span style="font-size: 20px; font-weight: bold; color: #0066cc;">FREE-COD-XXXXXXXX</span>
                </div>
                <p>Το κουπόνι θα εμφανίζεται με αυτή τη μορφή στο email που θα σταλεί στον πελάτη.</p>
            </div>
        </div>
    </div>
    <?php
}




