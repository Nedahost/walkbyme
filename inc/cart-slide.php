<?php
/**
 * WalkByMe Slide Cart
 * Version 8.1: Διορθωμένη έκδοση
 * - Ενιαίο render_footer() method
 * - Nonce security στο AJAX
 * - Σωστός υπολογισμός τιμών με ΦΠΑ
 * - Πλήρως ελληνικά κείμενα
 */

if (!defined('ABSPATH')) {
    exit;
}

class WalkByMe_Slide_Cart {
    
    private $settings;
    
    public function __construct() {
        $this->settings = array(
            'cart_position'      => 'right',
            'cart_width'         => '400',
            'show_progress_bar'  => 'yes',
            'progress_bar_color' => '#28a745',
            'checkout_button_text' => 'Ολοκλήρωση Αγοράς',
        );
        
        $this->init();
    }
    
    private function init() {
        if (!class_exists('WooCommerce')) return;
        
        add_action('wp_footer', array($this, 'output_cart_html'), 999);
        add_filter('woocommerce_add_to_cart_fragments', array($this, 'update_cart_fragments'));
    }
    
    public function update_cart_fragments($fragments) {
        // Cart items
        ob_start();
        $this->render_cart_items();
        $fragments['div.walkbyme-items'] = ob_get_clean();
        
        // Footer — χρησιμοποιούμε την κοινή μέθοδο
        ob_start();
        $this->render_footer();
        $fragments['div.walkbyme-footer'] = ob_get_clean();
        
        // Progress bar
        ob_start();
        $this->render_progress_bar();
        $fragments['div.walkbyme-progress'] = ob_get_clean();

        // Count
        $fragments['span.walkbyme-count'] = '<span class="walkbyme-count count">(' . WC()->cart->get_cart_contents_count() . ')</span>';

        // Header icon
        ob_start();
        $cart_count = WC()->cart->get_cart_contents_count();
        ?>
        <a id="walkbyme-cart-trigger" class="cart-customlocation" href="#" title="<?php esc_attr_e('Προβολή καλαθιού', 'walkbyme'); ?>">
            <span class="cart-icon-wrapper">
                <i class="fas fa-shopping-bag" aria-hidden="true" style="font-size: 20px;"></i>
                <?php if ($cart_count > 0) : ?>
                    <span class="cart-dot"></span>
                <?php endif; ?>
            </span>
        </a>
        <?php
        $fragments['a.cart-customlocation'] = ob_get_clean();
        
        // Flag για να ανοίξει το cart
        $fragments['walkbyme_open_cart'] = true;
        
        return $fragments;
    }

    /**
     * Κοινή μέθοδος για το footer — χρησιμοποιείται παντού
     */
    private function render_footer() {
        ?>
        <div class="walkbyme-footer">
            <?php if (!WC()->cart->is_empty()) : ?>
                <div class="total">
                    <strong><?php _e('Σύνολο:', 'walkbyme'); ?> <?php echo WC()->cart->get_cart_total(); ?></strong>
                </div>
                <div class="walkbyme-footer-note">
                    <?php _e('Περιλαμβάνεται φόρος. Τα έξοδα αποστολής και οι εκπτώσεις υπολογίζονται κατά την ολοκλήρωση της αγοράς.', 'walkbyme'); ?>
                </div>
                <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="btn checkout">
                    <?php echo esc_html($this->settings['checkout_button_text']); ?>
                </a>
                <?php if (wc_coupons_enabled()) : ?>
                    <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="walkbyme-view-cart">
                        <?php _e('Προβολή Καλαθιού', 'walkbyme'); ?>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php
    }

    private function render_progress_bar() {
        if ($this->settings['show_progress_bar'] !== 'yes') return;
        $threshold = $this->get_free_shipping_threshold();
        if ($threshold <= 0) return;
         if (!WC()->cart->is_empty()) : 
        // Χρησιμοποιούμε get_cart_subtotal για σωστή τιμή με ΦΠΑ
        $total = WC()->cart->get_displayed_subtotal();
        $progress = min(100, ($total / $threshold) * 100);
        $remaining = max(0, $threshold - $total);
        ?>
        <div class="walkbyme-progress">
            <?php if ($remaining > 0) : ?>
                <p><?php printf(__('Είστε %s μακριά από Δωρεάν Αποστολή', 'walkbyme'), wc_price($remaining)); ?></p>
            <?php else : ?>
                <p class="success"><?php _e('Συγχαρητήρια! Δικαιούστε Δωρεάν Μεταφορικά!', 'walkbyme'); ?></p>
            <?php endif; ?>
            <div class="bar">
                <div class="fill" style="width:<?php echo esc_attr($progress); ?>%;background:<?php echo esc_attr($this->settings['progress_bar_color']); ?>"></div>
            </div>
        </div>
        <?php
        endif;
    }

    private function render_cart_items() {
        $cart = WC()->cart;
        ?>
        <div class="walkbyme-items">
            <?php if (!$cart->is_empty()) {
                foreach ($cart->get_cart() as $key => $item) {
                    $product = $item['data'];
                    if (!$product) continue;
                    ?>
                    <div class="item" data-key="<?php echo esc_attr($key); ?>">
                        <div class="img"><?php echo $product->get_image('thumbnail'); ?></div>
                        <div class="details">
                            <h4><a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($product->get_name()); ?></a></h4>
                            <div class="price"><?php echo WC()->cart->get_product_subtotal($product, $item['quantity']); ?></div>
                            <?php echo wc_get_formatted_cart_item_data($item); ?>
                        </div>
                        <div class="controls">
                            <div class="qty">
                                <button type="button" class="minus" data-key="<?php echo esc_attr($key); ?>">−</button>
                                <input type="number" value="<?php echo esc_attr($item['quantity']); ?>" data-key="<?php echo esc_attr($key); ?>" readonly>
                                <button type="button" class="plus" data-key="<?php echo esc_attr($key); ?>">+</button>
                            </div>
                            <button type="button" class="remove-item" data-key="<?php echo esc_attr($key); ?>">&times;</button>
                        </div>
                    </div>
                    <?php
                }
            } else { ?>
                <div class="empty-cart">
                    <h3 class="empty-cart__title"><?php _e('ΤΟ ΚΑΛΑΘΙ ΣΑΣ ΕΙΝΑΙ ΑΔΕΙΟ', 'walkbyme'); ?></h3>
                    <p class="empty-cart__text"><?php _e('Εξερευνήστε και προσθέστε τα αγαπημένα σας προϊόντα στο καλάθι.', 'walkbyme'); ?></p>
                    
                    <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="empty-cart__btn">
                        <?php _e('Συνέχεια αγορών', 'walkbyme'); ?>
                    </a>
                    
                    <?php if (!is_user_logged_in()) : ?>
                        <p class="empty-cart__login">
                            <?php _e('Μην χάσετε το καλάθι σας.', 'walkbyme'); ?>
                            <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"><?php _e('Σύνδεση', 'walkbyme'); ?></a>.
                        </p>
                    <?php endif; ?>
                    
                    <?php
                    $categories = get_terms(array(
                        'taxonomy'   => 'product_cat',
                        'hide_empty' => true,
                        'number'     => 4,
                        'parent'     => 0,
                    ));
                    
                    if (!empty($categories) && !is_wp_error($categories)) : ?>
                        <div class="empty-cart__categories">
                            <h4><?php _e('Δημοφιλείς Κατηγορίες', 'walkbyme'); ?></h4>
                            <ul>
                                <?php foreach ($categories as $cat) : 
                                    $thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
                                    $image = wp_get_attachment_url($thumbnail_id);
                                ?>
                                    <li>
                                        <a href="<?php echo esc_url(get_term_link($cat)); ?>">
                                            <?php if ($image) : ?>
                                                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($cat->name); ?>">
                                            <?php endif; ?>
                                            <span><?php echo esc_html($cat->name); ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            <?php } ?>
        </div>
        <?php
    }

    public function output_cart_html() {
        if (is_admin() || is_checkout() || is_cart()) return;
        
        // Δημιουργία nonce για ασφαλή AJAX κλήσεις
        $nonce = wp_create_nonce('walkbyme-nonce');
        ?>
        <div id="walkbyme-overlay"></div>
        <div id="walkbyme-cart">
            <div class="walkbyme-header">
                <h3>
                    <?php _e('Καλάθι Αγορών', 'walkbyme'); ?>
                    <span class="walkbyme-count count">(<?php echo WC()->cart->get_cart_contents_count(); ?>)</span>
                </h3>
                <button class="close">&times;</button>
            </div>
            <?php $this->render_progress_bar(); ?>
            <?php $this->render_cart_items(); ?>
            <?php $this->render_footer(); ?>
        </div>

        <style>
            #walkbyme-overlay{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:99998;opacity:0;transition:opacity .3s}
            #walkbyme-overlay.open{display:block;opacity:1}
            #walkbyme-cart{position:fixed;top:0;right:0;width:400px;height:100%;background:#fff;z-index:99999;display:flex;flex-direction:column;transform:translateX(100%);transition:transform .3s ease;box-shadow:0 0 20px rgba(0,0,0,.1)}
            #walkbyme-cart.open{transform:translateX(0)!important}
            #walkbyme-cart.loading .walkbyme-items{opacity:.5;pointer-events:none}
            .walkbyme-header{padding:20px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center}
            .walkbyme-header h3{margin:0;font-size:18px}
            .walkbyme-header .close{background:0 0;border:none;font-size:24px;cursor:pointer}
            .walkbyme-items{flex:1;overflow-y:auto;padding:20px}
            .walkbyme-items .item{display:flex;gap:15px;margin-bottom:20px;border-bottom: 1px solid #eee;}
            .walkbyme-items .item:last-child{border-bottom: 0;}
            .walkbyme-items .img{width:70px}
            .walkbyme-items .img img{width:100%;height:auto;border-radius:4px}
            .walkbyme-items .details{flex:1}
            .walkbyme-items .details h4{margin:0 0 5px;font-size:14px}
            .walkbyme-items .details h4 a{text-decoration:none;color:inherit}
            .walkbyme-items .qty{display:flex;align-items:center;gap:4px;width:fit-content}
            .walkbyme-items .qty button{width:22px;height:22px;border:none;background:0 0;cursor:pointer;font-size:18px;color:#333;line-height:1;padding:0;display:flex;align-items:center;justify-content:center;transition:color .2s}
            .walkbyme-items .qty button:hover{color:#000}
            .walkbyme-items .qty input{width:24px;border:none;text-align:center;padding:0;font-size:13px;font-weight:600;color:#333;background:0 0;-moz-appearance:textfield}
            .walkbyme-items .qty input::-webkit-outer-spin-button,
            .walkbyme-items .qty input::-webkit-inner-spin-button{-webkit-appearance:none;margin:0}
            .walkbyme-items .controls{display:flex;flex-direction:column;align-items:center;gap:8px}
            .walkbyme-items .remove-item{border:none;background:0 0;color:#999;cursor:pointer;font-size:20px}
            .walkbyme-items .remove-item:hover{color:#c00}
            .walkbyme-footer{padding:20px;border-top:1px solid #eee}
            .walkbyme-footer .total{margin-bottom:10px;font-size:16px}
            .walkbyme-footer .walkbyme-footer-note{font-size:12px;color:#777;margin-bottom:15px}
            .walkbyme-footer .btn{display:block;text-align:center;padding:12px;background:#000;color:#fff;text-decoration:none;border-radius:4px;font-weight:600;margin-bottom:10px}
            .walkbyme-progress{padding:15px 20px;background:#f9f9f9;border-bottom:1px solid #eee}
            .walkbyme-progress p{margin:0 0 5px;font-size:13px;text-align:center}
            .walkbyme-progress .bar{height:6px;background:#e0e0e0;border-radius:3px;overflow:hidden}
            .walkbyme-progress .fill{height:100%;transition:width .3s}
            @media(max-width:480px){#walkbyme-cart{width:100%}}

            /* Empty Cart */
            .empty-cart{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:60px 20px;height:100%}
            .empty-cart__title{font-size:18px;font-weight:600;margin:0 0 10px;color:#333}
            .empty-cart__text{font-size:14px;color:#777;margin:0 0 30px}
            .empty-cart__btn{display:inline-block;padding:14px 40px;background:#010101;color:#fff;text-decoration:none;font-size:14px;font-weight:600;border-radius:4px;transition:background .3s ease}
            .empty-cart__btn:hover{background:#333;color:#fff}
            .empty-cart__login{font-size:13px;color:#777;margin-top:25px}
            .empty-cart__login a{color:#333;text-decoration:underline}
            .empty-cart__categories{margin-top:50px;width:100%;border-top:1px solid #eee;padding-top:30px}
            .empty-cart__categories h4{font-size:14px;font-weight:600;margin-bottom:20px;color:#333}
            .empty-cart__categories ul{display:grid;grid-template-columns:repeat(2,1fr);gap:20px;list-style:none;margin:0;padding:0}
            .empty-cart__categories li a{display:flex;flex-direction:column;align-items:center;text-decoration:none;color:#333;transition:color .3s}
            .empty-cart__categories li a:hover{color:#9a715b}
            .empty-cart__categories img{width:70px;height:70px;object-fit:cover;border-radius:50%;margin-bottom:10px;border:1px solid #eee}
            .empty-cart__categories span{font-size:13px}
        </style>

        <script>
        jQuery(function($) {
            var $cart  = $('#walkbyme-cart');
            var $overlay = $('#walkbyme-overlay');
            var isUpdating = false;

            function openCart() {
                $cart.addClass('open');
                $overlay.addClass('open');
                $('body').addClass('walkbyme-no-scroll');
                // Αποτρέπουμε το swipe-back gesture στα κινητά
                document.addEventListener('touchstart', preventSwipeBack, { passive: false });
            }
            
            function closeCart() {
                $cart.removeClass('open');
                $overlay.removeClass('open');
                $('body').removeClass('walkbyme-no-scroll');
                document.removeEventListener('touchstart', preventSwipeBack);
            }

            // Μπλοκάρει το swipe από την άκρη της οθόνης (iOS/Android back gesture)
            function preventSwipeBack(e) {
                var touch = e.touches[0];
                // Αν το touch ξεκινά από τα 20px της αριστερής άκρης → μπλοκάρουμε
                if (touch.clientX < 20) {
                    e.preventDefault();
                }
            }

            // Cart icon click
            $(document).on('click', '#walkbyme-cart-trigger, .cart-customlocation', function(e) {
                e.preventDefault();
                openCart();
            });
            
            // Close triggers
            $(document).on('click', '.walkbyme-header .close, #walkbyme-overlay', closeCart);
            
            // WooCommerce AJAX event — ανοίγει το cart μετά από προσθήκη προϊόντος
            $(document.body).on('added_to_cart', function(e, fragments) {
                if (fragments) {
                    $.each(fragments, function(key, value) {
                        if (key !== 'walkbyme_open_cart') {
                            $(key).replaceWith(value);
                        }
                    });
                }
                openCart();
            });

            // Single Product: Άνοιγμα cart μετά από redirect
            <?php if (is_product()) : ?>
            if (sessionStorage.getItem('walkbyme_open')) {
                sessionStorage.removeItem('walkbyme_open');
                openCart();
            }
            $('form.cart').on('submit', function() {
                sessionStorage.setItem('walkbyme_open', '1');
            });
            <?php endif; ?>

            // Quantity buttons
            $(document).on('click', '.walkbyme-items .minus, .walkbyme-items .plus', function() {
                if (isUpdating) return;
                var $btn  = $(this);
                var $item = $btn.closest('.item');
                var $input = $item.find('input');
                var key   = $btn.data('key');
                var val   = parseInt($input.val()) || 1;
                
                if ($btn.hasClass('plus')) val++;
                else if (val > 1) val--;
                
                $input.val(val);
                updateCart(key, val);
            });

            // Remove button
            $(document).on('click', '.remove-item', function() {
                if (isUpdating) return;
                updateCart($(this).data('key'), 0);
            });

            function updateCart(key, qty) {
                isUpdating = true;
                $cart.addClass('loading');
                
                $.post(wc_add_to_cart_params.ajax_url, {
                    action: 'walkbyme_update_cart',
                    nonce:  '<?php echo esc_js($nonce); ?>',
                    key:    key,
                    qty:    qty
                }, function(res) {
                    if (res.fragments) {
                        $.each(res.fragments, function(sel, html) {
                            $(sel).replaceWith(html);
                        });
                    }
                    $cart.removeClass('loading');
                    isUpdating = false;
                }).fail(function() {
                    location.reload();
                });
            }
        });
        </script>
        <?php
    }
    
    private function get_free_shipping_threshold() {
        if (!class_exists('WC_Shipping_Zones')) return 0;
        $threshold = 0;
        foreach (WC_Shipping_Zones::get_zones() as $zone) {
            $z = new WC_Shipping_Zone($zone['id']);
            foreach ($z->get_shipping_methods() as $m) {
                if ($m->id === 'free_shipping' && $m->enabled === 'yes') {
                    $min = floatval($m->get_option('min_amount'));
                    if ($min > 0) $threshold = max($threshold, $min);
                }
            }
        }
        return $threshold;
    }
}

// AJAX Handler — με nonce verification
add_action('wp_ajax_walkbyme_update_cart', 'walkbyme_ajax_update_cart');
add_action('wp_ajax_nopriv_walkbyme_update_cart', 'walkbyme_ajax_update_cart');

function walkbyme_ajax_update_cart() {
    // Έλεγχος nonce για ασφάλεια
    if (!check_ajax_referer('walkbyme-nonce', 'nonce', false)) {
        wp_send_json_error('Μη έγκυρο αίτημα.');
        wp_die();
    }

    $key = isset($_POST['key']) ? sanitize_text_field($_POST['key']) : '';
    $qty = isset($_POST['qty']) ? intval($_POST['qty']) : 0;
    
    if ($key) {
        if ($qty > 0) {
            WC()->cart->set_quantity($key, $qty);
        } else {
            WC()->cart->remove_cart_item($key);
        }
        WC()->cart->calculate_totals();
    }
    
    WC_AJAX::get_refreshed_fragments();
    wp_die();
}

new WalkByMe_Slide_Cart();