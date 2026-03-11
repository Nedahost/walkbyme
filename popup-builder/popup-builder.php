<?php
/**
 * Simple Welcome Popup
 * Email capture + automatic WooCommerce coupon
 */

if (!defined('ABSPATH')) exit;

// =========================================================
// ADMIN MENU
// =========================================================

add_action('admin_menu', function() {
    add_submenu_page('woocommerce', 'Welcome Popup', 'Welcome Popup', 'manage_options', 'welcome-popup', 'apb_admin_page');
});

function apb_admin_page() {
    if (isset($_POST['apb_save']) && check_admin_referer('apb_save_nonce')) {
        update_option('apb_enabled',         isset($_POST['apb_enabled']) ? 1 : 0);
        update_option('apb_headline',        sanitize_text_field($_POST['apb_headline']));
        update_option('apb_subheadline',     sanitize_textarea_field($_POST['apb_subheadline']));
        update_option('apb_button_text',     sanitize_text_field($_POST['apb_button_text']));
        update_option('apb_coupon_amount',   floatval($_POST['apb_coupon_amount']));
        update_option('apb_coupon_type',     sanitize_text_field($_POST['apb_coupon_type']));
        update_option('apb_coupon_prefix',   strtoupper(sanitize_text_field($_POST['apb_coupon_prefix'])));
        update_option('apb_delay',           intval($_POST['apb_delay']));
        update_option('apb_cookie_days',     intval($_POST['apb_cookie_days']));
        update_option('apb_accent_color',    sanitize_hex_color($_POST['apb_accent_color']));
        update_option('apb_bg_color',        sanitize_hex_color($_POST['apb_bg_color']));
        update_option('apb_image_url',       sanitize_url($_POST['apb_image_url']));
        echo '<div class="notice notice-success"><p>Αποθηκεύτηκε!</p></div>';
    }

    $o = array(
        'enabled'       => get_option('apb_enabled', 0),
        'headline'      => get_option('apb_headline',    'Αποκτήστε -10€ στην πρώτη σας παραγγελία!'),
        'subheadline'   => get_option('apb_subheadline', 'Εγγραφείτε και λάβετε αμέσως τον κωδικό έκπτωσής σας.'),
        'button_text'   => get_option('apb_button_text', 'Θέλω την έκπτωσή μου!'),
        'coupon_amount' => get_option('apb_coupon_amount', 10),
        'coupon_type'   => get_option('apb_coupon_type', 'fixed_cart'),
        'coupon_prefix' => get_option('apb_coupon_prefix', 'WELCOME'),
        'delay'         => get_option('apb_delay', 3),
        'cookie_days'   => get_option('apb_cookie_days', 3),
        'accent_color'  => get_option('apb_accent_color', '#000000'),
        'bg_color'      => get_option('apb_bg_color', '#ffffff'),
        'image_url'     => get_option('apb_image_url', ''),
    );
    ?>
    <div class="wrap" style="max-width:680px;">
        <h1>Welcome Popup</h1>

        <form method="post">
            <?php wp_nonce_field('apb_save_nonce'); ?>

            <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:20px;margin-bottom:16px;">
                <label style="display:flex;align-items:center;gap:10px;font-size:15px;font-weight:600;cursor:pointer;">
                    <input type="checkbox" name="apb_enabled" value="1" <?php checked($o['enabled'], 1); ?> style="width:18px;height:18px;">
                    Popup Ενεργό
                </label>
            </div>

            <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:20px;margin-bottom:16px;">
                <h3 style="margin-top:0;">Κείμενα</h3>
                <table class="form-table" style="margin:0;">
                    <tr>
                        <th style="width:150px;">Τίτλος</th>
                        <td><input type="text" name="apb_headline" value="<?php echo esc_attr($o['headline']); ?>" class="large-text"></td>
                    </tr>
                    <tr>
                        <th>Υπότιτλος</th>
                        <td><textarea name="apb_subheadline" class="large-text" rows="2"><?php echo esc_textarea($o['subheadline']); ?></textarea></td>
                    </tr>
                    <tr>
                        <th>Κείμενο Button</th>
                        <td><input type="text" name="apb_button_text" value="<?php echo esc_attr($o['button_text']); ?>" class="regular-text"></td>
                    </tr>
                </table>
            </div>

            <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:20px;margin-bottom:16px;">
                <h3 style="margin-top:0;">Εμφάνιση</h3>
                <table class="form-table" style="margin:0;">
                    <tr>
                        <th style="width:150px;">Εικόνα (προαιρετικά)</th>
                        <td>
                            <input type="url" name="apb_image_url" id="apb_image_url" value="<?php echo esc_attr($o['image_url']); ?>" class="large-text" placeholder="https://...">
                            <button type="button" class="button" style="margin-top:6px;" onclick="apbSelectImage()">Επιλογή από Media</button>
                            <?php if ($o['image_url']): ?>
                                <div style="margin-top:8px;"><img src="<?php echo esc_url($o['image_url']); ?>" style="max-width:200px;max-height:120px;object-fit:cover;border-radius:4px;border:1px solid #ddd;"></div>
                            <?php endif; ?>
                            <p class="description">Αν υπάρχει εικόνα → split layout. Αν όχι → centered layout.</p>
                        </td>
                    </tr>
                    <tr>
                        <th>Χρώμα φόντου</th>
                        <td>
                            <input type="color" name="apb_bg_color" value="<?php echo esc_attr($o['bg_color']); ?>">
                            <span style="color:#666;font-size:13px;margin-left:8px;">Φόντο του popup</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Χρώμα button</th>
                        <td>
                            <input type="color" name="apb_accent_color" value="<?php echo esc_attr($o['accent_color']); ?>">
                            <span style="color:#666;font-size:13px;margin-left:8px;">Button & accent χρώμα</span>
                        </td>
                    </tr>
                </table>
            </div>

            <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:20px;margin-bottom:16px;">
                <h3 style="margin-top:0;">Κουπόνι</h3>
                <table class="form-table" style="margin:0;">
                    <tr>
                        <th style="width:150px;">Τύπος έκπτωσης</th>
                        <td>
                            <select name="apb_coupon_type" style="width:200px;">
                                <option value="fixed_cart" <?php selected($o['coupon_type'], 'fixed_cart'); ?>>€ Σταθερό ποσό</option>
                                <option value="percent"    <?php selected($o['coupon_type'], 'percent'); ?>>% Ποσοστό</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Αξία έκπτωσης</th>
                        <td>
                            <input type="number" name="apb_coupon_amount" value="<?php echo esc_attr($o['coupon_amount']); ?>" min="1" style="width:80px;">
                            <span style="color:#666;font-size:13px;margin-left:4px;"><?php echo $o['coupon_type'] === 'percent' ? '%' : '€'; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Πρόθεμα κωδικού</th>
                        <td>
                            <input type="text" name="apb_coupon_prefix" value="<?php echo esc_attr($o['coupon_prefix']); ?>" style="width:120px;">
                            <span style="color:#666;font-size:13px;"> π.χ. <?php echo esc_html($o['coupon_prefix']); ?>AB12CD</span>
                        </td>
                    </tr>
                </table>
            </div>

            <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:20px;margin-bottom:20px;">
                <h3 style="margin-top:0;">Εμφάνιση</h3>
                <table class="form-table" style="margin:0;">
                    <tr>
                        <th style="width:150px;">Καθυστέρηση</th>
                        <td><input type="number" name="apb_delay" value="<?php echo esc_attr($o['delay']); ?>" min="1" max="60" style="width:70px;"> δευτερόλεπτα</td>
                    </tr>
                    <tr>
                        <th>Να μη ξαναφανεί</th>
                        <td><input type="number" name="apb_cookie_days" value="<?php echo esc_attr($o['cookie_days']); ?>" min="1" style="width:70px;"> μέρες</td>
                    </tr>
                </table>
            </div>

            <input type="submit" name="apb_save" class="button button-primary button-large" value="Αποθήκευση">
        </form>

        <script>
        function apbSelectImage() {
            if (typeof wp === 'undefined' || !wp.media) return;
            var uploader = wp.media({ title: 'Επιλογή Εικόνας', button: { text: 'Χρήση εικόνας' }, multiple: false });
            uploader.on('select', function() {
                var att = uploader.state().get('selection').first().toJSON();
                document.getElementById('apb_image_url').value = att.url;
            });
            uploader.open();
        }
        </script>

        <?php apb_render_leads(); ?>
    </div>
    <?php
}

function apb_render_leads() {
    global $wpdb;
    $table = $wpdb->prefix . 'apb_leads';
    $leads = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC LIMIT 100");
    $total = $wpdb->get_var("SELECT COUNT(*) FROM $table");

    // Στατιστικά χρήσης
    $used_count = 0;
    foreach ($leads as $l) {
        if (!empty($l->coupon_code)) {
            $coupon = $wpdb->get_row($wpdb->prepare(
                "SELECT p.ID, pm.meta_value as usage_count
                FROM {$wpdb->posts} p
                LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'usage_count'
                WHERE p.post_title = %s AND p.post_type = 'shop_coupon'",
                $l->coupon_code
            ));
            if ($coupon && intval($coupon->usage_count) > 0) $used_count++;
        }
    }
    $not_used = intval($total) - $used_count;
    ?>
    <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:20px;margin-top:24px;">
        <h3 style="margin-top:0;">Εγγραφές (<?php echo intval($total); ?>)</h3>

        <!-- Στατιστικά -->
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px;">
            <div style="background:#f8f8f8;border-radius:6px;padding:14px;text-align:center;">
                <div style="font-size:24px;font-weight:700;color:#333;"><?php echo intval($total); ?></div>
                <div style="font-size:12px;color:#888;margin-top:4px;">Συνολικές εγγραφές</div>
            </div>
            <div style="background:#f0fff4;border:1px solid #c6f6d5;border-radius:6px;padding:14px;text-align:center;">
                <div style="font-size:24px;font-weight:700;color:#276749;"><?php echo $used_count; ?></div>
                <div style="font-size:12px;color:#888;margin-top:4px;">Χρησιμοποίησαν κουπόνι ✅</div>
            </div>
            <div style="background:#fff5f5;border:1px solid #fed7d7;border-radius:6px;padding:14px;text-align:center;">
                <div style="font-size:24px;font-weight:700;color:#c53030;"><?php echo $not_used; ?></div>
                <div style="font-size:12px;color:#888;margin-top:4px;">Δεν αγόρασαν ⭕</div>
            </div>
        </div>

        <?php if (empty($leads)): ?>
            <p style="color:#888;">Δεν υπάρχουν εγγραφές ακόμα.</p>
        <?php else: ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th width="150">Κουπόνι</th>
                        <th width="110">Κατάσταση</th>
                        <th width="140">Ημερομηνία</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($leads as $l):
                    // Έλεγχος αν χρησιμοποιήθηκε το κουπόνι
                    $used = false;
                    if (!empty($l->coupon_code)) {
                        $usage = $wpdb->get_var($wpdb->prepare(
                            "SELECT pm.meta_value
                            FROM {$wpdb->posts} p
                            LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'usage_count'
                            WHERE p.post_title = %s AND p.post_type = 'shop_coupon'",
                            $l->coupon_code
                        ));
                        $used = intval($usage) > 0;
                    }
                ?>
                    <tr>
                        <td><?php echo esc_html($l->email); ?></td>
                        <td><code><?php echo esc_html($l->coupon_code); ?></code></td>
                        <td>
                            <?php if ($used): ?>
                                <span style="color:#276749;font-weight:600;">✅ Αγόρασε</span>
                            <?php else: ?>
                                <span style="color:#c53030;">⭕ Δεν αγόρασε</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($l->created_at)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
}

// =========================================================
// DATABASE
// =========================================================

add_action('init', function() {
    global $wpdb;
    $table = $wpdb->prefix . 'apb_leads';
    if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta("CREATE TABLE $table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            email varchar(255) NOT NULL,
            coupon_code varchar(100) DEFAULT '',
            ip_address varchar(45) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY email (email)
        ) " . $wpdb->get_charset_collate() . ";");
    }
});

// =========================================================
// AJAX
// =========================================================

add_action('wp_ajax_apb_subscribe',        'apb_handle_subscribe');
add_action('wp_ajax_nopriv_apb_subscribe', 'apb_handle_subscribe');

function apb_handle_subscribe() {
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'apb_nonce')) {
        wp_send_json_error(array('message' => 'Σφάλμα ασφαλείας.'));
    }

    $email = sanitize_email($_POST['email'] ?? '');
    if (!is_email($email)) {
        wp_send_json_error(array('message' => 'Παρακαλώ εισάγετε έγκυρο email.'));
    }
    if (empty($_POST['gdpr'])) {
        wp_send_json_error(array('message' => 'Παρακαλώ αποδεχτείτε την πολιτική απορρήτου.'));
    }

    global $wpdb;
    $table = $wpdb->prefix . 'apb_leads';

    if ($wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE email = %s", $email))) {
        wp_send_json_error(array('message' => 'Αυτό το email έχει ήδη εγγραφεί!'));
    }

    // ── IP RATE LIMITING ──
    // Παίρνουμε το πραγματικό IP (λαμβάνουμε υπόψη proxies/CDN)
    $ip = '';
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP']; // Cloudflare
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    }
    $ip = sanitize_text_field(trim($ip));

    // Max 2 εγγραφές ανά IP τις τελευταίες 24 ώρες
    $ip_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE ip_address = %s AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)",
        $ip
    ));
    if ($ip_count >= 2) {
        wp_send_json_error(array('message' => 'Έχετε φτάσει το όριο εγγραφών. Δοκιμάστε αύριο.'));
    }

    $prefix       = strtoupper(get_option('apb_coupon_prefix', 'WELCOME'));
    $code         = $prefix . strtoupper(wp_generate_password(6, false));
    $amount       = floatval(get_option('apb_coupon_amount', 10));
    $coupon_type  = get_option('apb_coupon_type', 'fixed_cart'); // fixed_cart ή percent

    $coupon_id = wp_insert_post(array(
        'post_title'  => $code,
        'post_type'   => 'shop_coupon',
        'post_status' => 'publish',
    ));

    if ($coupon_id && !is_wp_error($coupon_id)) {
        update_post_meta($coupon_id, 'discount_type',        $coupon_type);
        update_post_meta($coupon_id, 'coupon_amount',        $amount);
        update_post_meta($coupon_id, 'usage_limit',          1);
        update_post_meta($coupon_id, 'usage_limit_per_user', 1);
        update_post_meta($coupon_id, 'individual_use',       'yes');
        update_post_meta($coupon_id, 'customer_email',       array($email));
        update_post_meta($coupon_id, 'date_expires',         strtotime('+7 days'));
        update_post_meta($coupon_id, 'is_for_new_customers',   'yes');
    }

    $wpdb->insert($table, array(
        'email'      => $email,
        'coupon_code'=> $code,
        'ip_address' => $ip,
    ));

    // ── MAILER HOOK ──
    // Εδώ "κρεμάς" όποιον mailer θέλεις χωρίς να αγγίξεις τον υπόλοιπο κώδικα.
    // Παράδειγμα Klaviyo: add_action('apb_new_subscriber', 'my_klaviyo_sync', 10, 2);
    // Παράδειγμα ActiveCampaign: add_action('apb_new_subscriber', 'my_ac_sync', 10, 2);
    do_action('apb_new_subscriber', $email, $code);

    wp_send_json_success(array('coupon_code' => $code));
}

// =========================================================
// FRONTEND
// =========================================================

add_action('wp_footer', function() {
    if (is_admin()) return;
    if (!get_option('apb_enabled', 0)) return;

    $headline    = esc_html(get_option('apb_headline',    'Αποκτήστε -10€ στην πρώτη σας παραγγελία!'));
    $subheadline = esc_html(get_option('apb_subheadline', 'Εγγραφείτε και λάβετε αμέσως τον κωδικό έκπτωσής σας.'));
    $button_text = esc_html(get_option('apb_button_text', 'Θέλω την έκπτωσή μου!'));
    $accent      = esc_attr(get_option('apb_accent_color', '#000000'));
    $bg_color    = esc_attr(get_option('apb_bg_color', '#ffffff'));
    $image_url   = esc_url(get_option('apb_image_url', ''));
    $has_image   = !empty($image_url);
    $delay       = intval(get_option('apb_delay', 3)) * 1000;
    $cookie_days = intval(get_option('apb_cookie_days', 3));
    $nonce       = wp_create_nonce('apb_nonce');
    $ajax_url    = esc_url(admin_url('admin-ajax.php'));
    $privacy_url = esc_url(get_privacy_policy_url());
    ?>
    <style>
    #apb-overlay{display:none;position:fixed;inset:0;z-index:999999;background:rgba(0,0,0,0.6);}
    /* Box — με εικόνα: split max-width 800px / χωρίς: centered max-width 440px */
    #apb-box{
        position:absolute;top:50%;left:50%;
        transform:translate(-50%,-50%);
        background:<?php echo $bg_color; ?>;
        width:92%;
        max-width:<?php echo $has_image ? '800px' : '440px'; ?>;
        border-radius:12px;
        box-shadow:0 20px 60px rgba(0,0,0,0.25);
        box-sizing:border-box;
        overflow:hidden;
        display:flex;
        max-height:90vh;
    }
    /* Αριστερά: εικόνα */
    #apb-image-col{
        flex:0 0 45%;
        min-height:400px;
        background:url('<?php echo esc_js($image_url); ?>') center center/cover no-repeat;
    }
    /* Δεξιά: form */
    #apb-form-col{
        flex:1;
        padding:40px 36px 32px;
        display:flex;
        flex-direction:column;
        justify-content:center;
        overflow-y:auto;
    }
    #apb-close{position:absolute;top:12px;right:14px;font-size:24px;background:none;border:none;cursor:pointer;color:#aaa;line-height:1;padding:4px;z-index:10;}
    #apb-close:hover{color:#333;}
    #apb-headline{font-size:22px;font-weight:700;margin:0 0 8px;line-height:1.3;color:#111;}
    #apb-sub{font-size:14px;color:#666;margin:0 0 20px;line-height:1.6;}
    .apb-input{width:100%;padding:12px 14px;border:1.5px solid #ddd;border-radius:6px;font-size:14px;margin-bottom:10px;box-sizing:border-box;font-family:inherit;background:#fff;}
    .apb-input:focus{outline:none;border-color:<?php echo $accent; ?>;}
    #apb-gdpr-wrap{display:flex;gap:10px;align-items:flex-start;font-size:12px;color:#888;line-height:1.5;margin-bottom:14px;cursor:pointer;}
    #apb-gdpr-wrap input{margin-top:2px;flex-shrink:0;}
    #apb-btn{width:100%;padding:13px;background:<?php echo $accent; ?>;color:#fff;border:none;border-radius:6px;font-size:15px;font-weight:600;cursor:pointer;font-family:inherit;letter-spacing:0.03em;}
    #apb-btn:hover{opacity:0.88;}
    #apb-btn:disabled{opacity:0.6;cursor:not-allowed;}
    #apb-error{display:none;color:#c00;font-size:13px;margin-bottom:8px;}
    #apb-success{display:none;text-align:center;}
    #apb-code{font-size:22px;font-weight:700;letter-spacing:3px;padding:14px;background:#f5f5f5;border:2px dashed <?php echo $accent; ?>;border-radius:6px;margin:12px 0 6px;cursor:pointer;}
    @media(max-width:600px){
        #apb-image-col{display:none;}
        #apb-form-col{padding:32px 22px 24px;}
        #apb-headline{font-size:19px;}
    }
    </style>

    <div id="apb-overlay">
        <div id="apb-box">
            <button id="apb-close">&times;</button>

            <?php if ($has_image): ?>
                <div id="apb-image-col" role="img" aria-label="Popup image"></div>
            <?php endif; ?>

            <div id="apb-form-col">
                <div id="apb-form-wrap">
                    <h2 id="apb-headline"><?php echo $headline; ?></h2>
                    <p id="apb-sub"><?php echo $subheadline; ?></p>
                    <div id="apb-error"></div>
                    <input type="email" id="apb-email" class="apb-input" placeholder="Το email σας *">
                    <label id="apb-gdpr-wrap">
                        <input type="checkbox" id="apb-gdpr">
                        <span>Συμφωνώ με την <a href="<?php echo $privacy_url; ?>" target="_blank" style="color:inherit;">Πολιτική Απορρήτου</a>.</span>
                    </label>
                    <button id="apb-btn"><?php echo $button_text; ?></button>
                </div>

                <div id="apb-success">
                    <div style="font-size:44px;margin-bottom:10px;">🎉</div>
                    <p style="font-size:16px;font-weight:700;margin:0 0 6px;">Ο κωδικός σας είναι:</p>
                    <div id="apb-code" onclick="apbCopy(this)"></div>
                    <p style="font-size:11px;color:#aaa;margin:0 0 16px;">Κάντε κλικ για αντιγραφή</p>
                    <button onclick="apbClose()" style="background:<?php echo $accent; ?>;color:#fff;border:none;padding:10px 24px;border-radius:6px;cursor:pointer;font-size:14px;">Ξεκινήστε τις αγορές →</button>
                </div>
            </div><!-- end apb-form-col -->
        </div><!-- end apb-box -->
    </div><!-- end apb-overlay -->

    <script>
    (function(){
        var COOKIE='apb_seen', DELAY=<?php echo $delay; ?>, DAYS=<?php echo $cookie_days; ?>;
        var BTN_TEXT='<?php echo esc_js($button_text); ?>';

        function getCookie(n){var m=document.cookie.match(new RegExp('(^| )'+n+'=([^;]+)'));return m?m[2]:null;}
        function setCookie(n,v,d){var e=new Date();e.setTime(e.getTime()+d*86400000);document.cookie=n+'='+v+';expires='+e.toUTCString()+';path=/';}

        window.apbClose=function(){document.getElementById('apb-overlay').style.display='none';setCookie(COOKIE,'1',DAYS);};
        window.apbCopy=function(el){var t=el.textContent.trim();if(navigator.clipboard){navigator.clipboard.writeText(t).then(function(){el.textContent='Αντιγράφηκε!';setTimeout(function(){el.textContent=t;},2000);});}};

        document.getElementById('apb-overlay').addEventListener('click',function(e){if(e.target===this)apbClose();});
        document.getElementById('apb-close').addEventListener('click',apbClose);
        document.addEventListener('keydown',function(e){if(e.key==='Escape')apbClose();});

        document.getElementById('apb-btn').addEventListener('click',function(){
            var email=document.getElementById('apb-email').value.trim();
            var gdpr=document.getElementById('apb-gdpr').checked;
            var btn=this, err=document.getElementById('apb-error');
            err.style.display='none';
            if(!email||!/\S+@\S+\.\S+/.test(email)){err.textContent='Παρακαλώ εισάγετε έγκυρο email.';err.style.display='block';return;}
            if(!gdpr){err.textContent='Παρακαλώ αποδεχτείτε την πολιτική απορρήτου.';err.style.display='block';return;}
            btn.disabled=true; btn.textContent='...';
            var xhr=new XMLHttpRequest();
            xhr.open('POST','<?php echo $ajax_url; ?>');
            xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
            xhr.onload=function(){
                try{var r=JSON.parse(xhr.responseText);}catch(e){err.textContent='Σφάλμα.';err.style.display='block';btn.disabled=false;btn.textContent=BTN_TEXT;return;}
                if(r.success){
                    document.getElementById('apb-form-wrap').style.display='none';
                    document.getElementById('apb-code').textContent=r.data.coupon_code;
                    document.getElementById('apb-success').style.display='block';
                    setCookie(COOKIE,'1',DAYS);
                }else{
                    err.textContent=r.data.message;err.style.display='block';
                    btn.disabled=false;btn.textContent=BTN_TEXT;
                }
            };
            xhr.onerror=function(){err.textContent='Σφάλμα σύνδεσης.';err.style.display='block';btn.disabled=false;btn.textContent=BTN_TEXT;};
            xhr.send('action=apb_subscribe&nonce=<?php echo esc_js($nonce); ?>&email='+encodeURIComponent(email)+'&gdpr=1');
        });

        if(!getCookie(COOKIE)){setTimeout(function(){document.getElementById('apb-overlay').style.display='block';},DELAY);}
    })();
    </script>
    <?php
}, 20);