<?php
/**
 * WalkByMe Slide Cart
 * Version 8.0: SIMPLE & STABLE - Ακριβώς όπως το Side Cart WooCommerce
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
            'checkout_button_text' => __('Checkout', 'walkbyme')
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
        
        // Footer
        ob_start();
        ?>
        <div class="walkbyme-footer">
            <div class="total"><strong><?php _e('Total:', 'walkbyme'); ?> <?php echo WC()->cart->get_cart_total(); ?></strong></div>
            <?php if (!WC()->cart->is_empty()) : ?>
                <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="btn checkout"><?php echo esc_html($this->settings['checkout_button_text']); ?></a>
            <?php endif; ?>
            <button class="btn-close"><?php _e('Continue Shopping', 'walkbyme'); ?></button>
        </div>
        <?php
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
        <a id="walkbyme-cart-trigger" class="cart-customlocation" href="#" title="<?php esc_attr_e('View cart', 'walkbyme'); ?>">
            <span class="cart-icon-wrapper">
                <i class="fas fa-shopping-bag" aria-hidden="true" style="font-size: 20px;"></i>
                <?php if ($cart_count > 0) : ?>
                    <span class="cart-dot"></span>
                <?php endif; ?>
            </span>
        </a>
        <?php
        $fragments['a.cart-customlocation'] = ob_get_clean();
        
        // ★ ΤΟ ΜΥΣΤΙΚΟ: Στέλνουμε flag να ανοίξει το cart
        $fragments['walkbyme_open_cart'] = true;
        
        return $fragments;
    }

    private function render_progress_bar() {
        if ($this->settings['show_progress_bar'] !== 'yes') return;
        $threshold = $this->get_free_shipping_threshold();
        if ($threshold <= 0) return;

        $total = WC()->cart->get_cart_contents_total(); 
        $progress = min(100, ($total / $threshold) * 100);
        $remaining = max(0, $threshold - $total);
        ?>
        <div class="walkbyme-progress">
            <?php if ($remaining > 0) : ?>
                <p><?php printf(__('Spend %s more for free shipping!', 'walkbyme'), wc_price($remaining)); ?></p>
            <?php else : ?>
                <p class="success">🎉 <?php _e('You qualify for free shipping!', 'walkbyme'); ?></p>
            <?php endif; ?>
            <div class="bar"><div class="fill" style="width:<?php echo esc_attr($progress); ?>%;background:<?php echo esc_attr($this->settings['progress_bar_color']); ?>"></div></div>
        </div>
        <?php
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
            } else {
                echo '<div class="empty-cart-msg"><p>' . __('Your cart is empty.', 'walkbyme') . '</p></div>';
            } ?>
        </div>
        <?php
    }

    public function output_cart_html() {
        if (is_admin() || is_checkout()) return;
        ?>
        <div id="walkbyme-overlay"></div>
        <div id="walkbyme-cart">
            <div class="walkbyme-header">
                <h3><?php _e('Shopping Cart', 'walkbyme'); ?> <span class="walkbyme-count count">(<?php echo WC()->cart->get_cart_contents_count(); ?>)</span></h3>
                <button class="close">&times;</button>
            </div>
            <?php $this->render_progress_bar(); ?>
            <?php $this->render_cart_items(); ?>
            <div class="walkbyme-footer">
                <div class="total"><strong><?php _e('Total:', 'walkbyme'); ?> <?php echo WC()->cart->get_cart_total(); ?></strong></div>
                <?php if (!WC()->cart->is_empty()) : ?>
                    <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="btn checkout"><?php echo esc_html($this->settings['checkout_button_text']); ?></a>
                <?php endif; ?>
                <button class="btn-close"><?php _e('Continue Shopping', 'walkbyme'); ?></button>
            </div>
        </div>

        <style>
            #walkbyme-overlay{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:99998;opacity:0;transition:opacity .3s}
            #walkbyme-overlay.open{display:block;opacity:1}
            #walkbyme-cart{position:fixed;top:0;right:0;width:600px;height:100%;background:#fff;z-index:99999;display:flex;flex-direction:column;transform:translateX(100%);transition:transform .3s ease;box-shadow:0 0 20px rgba(0,0,0,.1)}
            #walkbyme-cart.open{transform:translateX(0)!important}
            #walkbyme-cart.loading .walkbyme-items{opacity:.5;pointer-events:none}
            .walkbyme-header{padding:20px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center}
            .walkbyme-header h3{margin:0;font-size:18px}
            .walkbyme-header .close{background:0 0;border:none;font-size:24px;cursor:pointer}
            .walkbyme-items{flex:1;overflow-y:auto;padding:20px}
            .walkbyme-items .item{display:flex;gap:15px;margin-bottom:20px}
            .walkbyme-items .img{width:70px}
            .walkbyme-items .img img{width:100%;height:auto;border-radius:4px}
            .walkbyme-items .details{flex:1}
            .walkbyme-items .details h4{margin:0 0 5px;font-size:14px}
            .walkbyme-items .details h4 a{text-decoration:none;color:inherit}
            .walkbyme-items .qty{display:flex;border:1px solid #ddd;width:fit-content;border-radius:3px}
            .walkbyme-items .qty button{width:28px;height:28px;border:none;background:0 0;cursor:pointer;font-size:16px}
            .walkbyme-items .qty input{width:35px;border:none;text-align:center;padding:0}
            .walkbyme-items .controls{display:flex;flex-direction:column;align-items:center;gap:8px}
            .walkbyme-items .remove-item{border:none;background:0 0;color:#999;cursor:pointer;font-size:20px}
            .walkbyme-items .remove-item:hover{color:#c00}
            .walkbyme-footer{padding:20px;border-top:1px solid #eee}
            .walkbyme-footer .total{margin-bottom:15px;font-size:16px}
            .walkbyme-footer .btn{display:block;text-align:center;padding:12px;background:#000;color:#fff;text-decoration:none;border-radius:4px;font-weight:600;margin-bottom:10px}
            .walkbyme-footer .btn-close{width:100%;padding:10px;background:0 0;border:none;text-decoration:underline;cursor:pointer}
            .walkbyme-progress{padding:15px 20px;background:#f9f9f9;border-bottom:1px solid #eee}
            .walkbyme-progress p{margin:0 0 5px;font-size:13px;text-align:center}
            .walkbyme-progress .bar{height:6px;background:#e0e0e0;border-radius:3px;overflow:hidden}
            .walkbyme-progress .fill{height:100%;transition:width .3s}
            .empty-cart-msg{text-align:center;padding:40px 0;color:#777}
            @media(max-width:480px){#walkbyme-cart{width:100%}}
        </style>

        <script>
        jQuery(function($) {
            var $cart = $('#walkbyme-cart');
            var $overlay = $('#walkbyme-overlay');
            var isUpdating = false;

            function openCart() { 
                $cart.addClass('open'); 
                $overlay.addClass('open'); 
                $('body').addClass('walkbyme-no-scroll'); 
            }
            
            function closeCart() { 
                $cart.removeClass('open'); 
                $overlay.removeClass('open'); 
                $('body').removeClass('walkbyme-no-scroll'); 
            }

            // ★ Cart icon click
            $(document).on('click', '#walkbyme-cart-trigger, .cart-customlocation', function(e) { 
                e.preventDefault(); 
                openCart(); 
            });
            
            // ★ Close triggers
            $(document).on('click', '.walkbyme-header .close, .btn-close, #walkbyme-overlay', closeCart);
            
            // ★ ΤΟ ΚΛΕΙΔΙ: Ακούμε το WooCommerce event - ΛΕΙΤΟΥΡΓΕΙ ΠΑΝΤΟΥ
            $(document.body).on('added_to_cart', function(e, fragments) {
                // Update fragments αν υπάρχουν
                if (fragments) {
                    $.each(fragments, function(key, value) {
                        if (key !== 'walkbyme_open_cart') {
                            $(key).replaceWith(value);
                        }
                    });
                }
                openCart();
            });

            // ★ Για Single Product: Redirect μετά το submit με flag
            // Αυτό δουλεύει ΠΑΝΤΑ, ακόμα και χωρίς AJAX
            <?php if (is_product()) : ?>
            if (sessionStorage.getItem('walkbyme_open')) {
                sessionStorage.removeItem('walkbyme_open');
                openCart();
            }
            
            $('form.cart').on('submit', function() {
                sessionStorage.setItem('walkbyme_open', '1');
            });
            <?php endif; ?>

            // ★ Quantity buttons
            $(document).on('click', '.walkbyme-items .minus, .walkbyme-items .plus', function() {
                if (isUpdating) return;
                
                var $btn = $(this);
                var $item = $btn.closest('.item');
                var $input = $item.find('input');
                var key = $btn.data('key');
                var val = parseInt($input.val()) || 1;
                
                if ($btn.hasClass('plus')) val++;
                else if (val > 1) val--;
                
                $input.val(val);
                updateCart(key, val);
            });

            // ★ Remove button
            $(document).on('click', '.remove-item', function() {
                if (isUpdating) return;
                updateCart($(this).data('key'), 0);
            });

            function updateCart(key, qty) {
                isUpdating = true;
                $cart.addClass('loading');
                
                $.post(wc_add_to_cart_params.ajax_url, {
                    action: 'walkbyme_update_cart',
                    key: key,
                    qty: qty
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

// AJAX Handler
add_action('wp_ajax_walkbyme_update_cart', 'walkbyme_ajax_update_cart');
add_action('wp_ajax_nopriv_walkbyme_update_cart', 'walkbyme_ajax_update_cart');

function walkbyme_ajax_update_cart() {
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