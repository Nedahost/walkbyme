<?php

require_once (get_template_directory() . '/inc/details-product.php');

// style load css

function walkbyme_load_css() {
    wp_enqueue_style('mystyle', get_template_directory_uri() . '/assets/public/css/mystyle.css');
    wp_enqueue_style('load-fa', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
}

add_action( 'wp_enqueue_scripts', 'walkbyme_load_css' );


//  Javascripts load js

function load_js(){
    wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.6.0.min.js', array(), null, true);
    wp_enqueue_script('slick', '//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js?ver=1.5', array(), null, true);
    wp_enqueue_script('myjs', get_template_directory_uri() . '/assets/js/myjs.js', array('jquery'), false, true);

    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
}

add_action( 'wp_enqueue_scripts', 'load_js' );

function remove_wp_emoji_styles() {
    wp_style_add_data( 'wp-emoji-styles', 'html5', 'text/css' );
    wp_dequeue_style( 'wp-emoji-styles' );
    wp_dequeue_style( 'wp-emoji-styles-inline' );
}
add_action( 'wp_print_styles', 'remove_wp_emoji_styles' );


// add a favicon to your site
function favicon() {
	echo '<link rel="Shortcut Icon" type="image/x-icon" href="'.get_bloginfo('stylesheet_directory').'/assets/images/favicon.jpg" />' . "\n";
}
add_action('wp_head', 'favicon');
add_action('admin_head', 'favicon');





// Προσθέστε αυτές τις σταθερές στην αρχή του αρχείου σας ή στο αρχείο ρυθμίσεων σας
define('FB_ACCESS_TOKEN', 'EAAVjM1dKZAp0BOxbdGQoRPhHVDH6nnHJjP2l2yFcnjxV3mskuVqJ4wNT2fqcNrj8AUsRqpyFNCpDOodBpb9huN3bELLspPTXiAgXdXQbQU1pzmV4icdZAwqBZAKVQe1opr8NjgcdvPX9sS0dC9lbMoPtYeSAMd6QoiZCkqRxcuwzYMcvTsZBpUQ7P5yujapCf1wZDZD');
define('FB_PIXEL_ID', '891327725921929');

// Add Facebook Pixel base code
function add_facebook_pixel_code() {
    ?>
    <!-- Meta Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window,document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '<?php echo FB_PIXEL_ID; ?>');
    <?php if (!is_cart()): ?>
    fbq('track', 'PageView');
    <?php endif; ?>
    </script>
    <noscript>
    <img height="1" width="1" style="display:none"
         src="https://www.facebook.com/tr?id=<?php echo FB_PIXEL_ID; ?>&ev=PageView&noscript=1"/>
    </noscript>
    <!-- End Meta Pixel Code -->
    <?php
}
add_action('wp_head', 'add_facebook_pixel_code');

function facebook_pixel_events() {
    $current_user = wp_get_current_user();
    ?>
    <script>
    // Ορίζουμε τη συνάρτηση sendServerEvent στο global scope
    function sendServerEvent(eventName, eventData, eventId) {
        // Αποστολή του event στο server για το Conversion API
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'facebook_capi_event',
                event_name: eventName,
                event_data: JSON.stringify(eventData),
                event_id: eventId
            },
            success: function(response) {
                console.log('Server event sent:', response);
            },
            error: function(xhr, status, error) {
                console.error('Error sending server event:', error);
            }
        });
    }

    function getParameters() {
        return {
            fbc: getCookie('_fbc') || null,
            fbp: getCookie('_fbp') || null,
            external_id: getExternalId()
        };
    }

    function getCookie(name) {
        var value = "; " + document.cookie;
        var parts = value.split("; " + name + "=");
        if (parts.length == 2) return parts.pop().split(";").shift();
    }

    function getExternalId() {
        var externalId = getCookie('external_id');
        if (!externalId) {
            externalId = generateEventId();
            document.cookie = "external_id=" + externalId + "; path=/; max-age=31536000"; // 1 year
        }
        return externalId;
    }

    function generateEventId() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        var parameters = getParameters();

        <?php if (is_product_category()): 
        $category = get_queried_object();
        ?>
        var viewCategoryEventId = generateEventId();
        fbq('track', 'ViewCategory', Object.assign({
            content_name: '<?php echo esc_js($category->name); ?>',
            content_category: '<?php echo esc_js($category->slug); ?>',
            content_ids: ['<?php echo esc_js($category->term_id); ?>'],
            content_type: 'product_category'
        }, parameters), {eventID: viewCategoryEventId});
        sendServerEvent('ViewCategory', Object.assign({
            content_name: '<?php echo esc_js($category->name); ?>',
            content_category: '<?php echo esc_js($category->slug); ?>',
            content_ids: ['<?php echo esc_js($category->term_id); ?>'],
            content_type: 'product_category'
        }, parameters), viewCategoryEventId);
        <?php endif; ?>

        <?php if (is_product()): 
        global $product;
        $category_names = wp_get_post_terms($product->get_id(), 'product_cat', array('fields' => 'names'));
        $category_names = !empty($category_names) ? $category_names : ['Uncategorized'];
        ?>
        var viewContentEventId = generateEventId();
        fbq('track', 'ViewContent', Object.assign({
            content_ids: ['<?php echo esc_js($product->get_id()); ?>'],
            content_type: 'product',
            content_name: '<?php echo esc_js($product->get_name()); ?>',
            content_category: '<?php echo esc_js(implode(", ", $category_names)); ?>',
            value: <?php echo esc_js($product->get_price()); ?>,
            currency: '<?php echo esc_js(get_woocommerce_currency()); ?>',
            availability: '<?php echo esc_js($product->get_stock_status()); ?>',
            sku: '<?php echo esc_js($product->get_sku()); ?>',
            em: '<?php echo esc_js($current_user->user_email); ?>',
            fn: '<?php echo esc_js($current_user->user_firstname); ?>',
            ln: '<?php echo esc_js($current_user->user_lastname); ?>'
        }, parameters), {eventID: viewContentEventId});
        sendServerEvent('ViewContent', Object.assign({
            content_ids: ['<?php echo esc_js($product->get_id()); ?>'],
            content_type: 'product',
            content_name: '<?php echo esc_js($product->get_name()); ?>',
            content_category: '<?php echo esc_js(implode(", ", $category_names)); ?>',
            value: <?php echo esc_js($product->get_price()); ?>,
            currency: '<?php echo esc_js(get_woocommerce_currency()); ?>',
            availability: '<?php echo esc_js($product->get_stock_status()); ?>',
            sku: '<?php echo esc_js($product->get_sku()); ?>',
            em: '<?php echo esc_js($current_user->user_email); ?>',
            fn: '<?php echo esc_js($current_user->user_firstname); ?>',
            ln: '<?php echo esc_js($current_user->user_lastname); ?>'
        }, parameters), viewContentEventId);
        <?php endif; ?>

        <?php if (is_cart()): 
        $cart_contents = WC()->cart->get_cart_contents();
        $content_ids = $content_names = array();
        foreach ($cart_contents as $cart_item) {
            $product = $cart_item['data'];
            $content_ids[] = $product->get_id();
            $content_names[] = $product->get_name();
        }
        $cart_total = WC()->cart->get_cart_contents_total();
        ?>
        var viewCartEventId = generateEventId();
        fbq('track', 'ViewCart', Object.assign({
            content_ids: <?php echo json_encode(array_map('esc_js', $content_ids)); ?>,
            content_names: <?php echo json_encode(array_map('esc_js', $content_names)); ?>,
            content_type: 'product',
            value: <?php echo esc_js($cart_total); ?>,
            currency: '<?php echo esc_js(get_woocommerce_currency()); ?>',
            em: '<?php echo esc_js($current_user->user_email); ?>',
            fn: '<?php echo esc_js($current_user->user_firstname); ?>',
            ln: '<?php echo esc_js($current_user->user_lastname); ?>'
        }, parameters), {eventID: viewCartEventId});
        sendServerEvent('ViewCart', Object.assign({
            content_ids: <?php echo json_encode(array_map('esc_js', $content_ids)); ?>,
            content_names: <?php echo json_encode(array_map('esc_js', $content_names)); ?>,
            content_type: 'product',
            value: <?php echo esc_js($cart_total); ?>,
            currency: '<?php echo esc_js(get_woocommerce_currency()); ?>',
            em: '<?php echo esc_js($current_user->user_email); ?>',
            fn: '<?php echo esc_js($current_user->user_firstname); ?>',
            ln: '<?php echo esc_js($current_user->user_lastname); ?>'
        }, parameters), viewCartEventId);

        jQuery('button[name="update_cart"]').on('click', function() {
            var cart_contents = <?php echo json_encode(WC()->cart->get_cart()); ?>;
            var content_ids = [];
            var content_names = [];
            var content_categories = [];
            var value = 0;

            jQuery.each(cart_contents, function(key, item) {
                if (item && item.product_id && item.data) {
                    var product_id = item.product_id;
                    var product = item.data;
                    content_ids.push(product_id);
                    content_names.push(product.name || '');
                    var categories = product.category || ['Uncategorized'];
                    content_categories = content_categories.concat(categories);
                    value += parseFloat(item.line_total || 0);
                }
            });

            if (content_ids.length > 0) {
                var addToCartEventId = generateEventId();
                fbq('track', 'AddToCart', Object.assign({
                    content_ids: content_ids,
                    content_name: content_names.join(', '),
                    content_category: content_categories.join(', '),
                    content_type: 'product',
                    value: value,
                    currency: '<?php echo esc_js(get_woocommerce_currency()); ?>'
                }, parameters), {eventID: addToCartEventId});

                sendServerEvent('AddToCart', Object.assign({
                    content_ids: content_ids,
                    content_name: content_names.join(', '),
                    content_category: content_categories.join(', '),
                    content_type: 'product',
                    value: value,
                    currency: '<?php echo esc_js(get_woocommerce_currency()); ?>'
                }, parameters), addToCartEventId);
            }
        });
        <?php endif; ?>

        <?php if (is_checkout() && !is_order_received_page()): 
        $cart_contents = WC()->cart->get_cart_contents();
        $content_ids = $content_names = $content_categories = array();
        $value = 0;
        foreach ($cart_contents as $cart_item) {
            $product = $cart_item['data'];
            $product_id = $product->get_id();
            $content_ids[] = $product_id;
            $content_names[] = $product->get_name();
            $categories = get_the_terms($product_id, 'product_cat');
            $content_categories[] = $categories ? implode(', ', wp_list_pluck($categories, 'name')) : 'Uncategorized';
            $value += $cart_item['line_total'];
        }
        $customer = WC()->customer;
        ?>
        var initiateCheckoutEventId = generateEventId();
        fbq('track', 'InitiateCheckout', Object.assign({
            content_ids: <?php echo json_encode(array_map('esc_js', $content_ids)); ?>,
            content_names: <?php echo json_encode(array_map('esc_js', $content_names)); ?>,
            content_categories: <?php echo json_encode(array_map('esc_js', $content_categories)); ?>,
            content_type: 'product',
            value: <?php echo esc_js($value); ?>,
            currency: '<?php echo esc_js(get_woocommerce_currency()); ?>',
            em: '<?php echo esc_js($customer->get_billing_email()); ?>',
            ph: '<?php echo esc_js($customer->get_billing_phone()); ?>',
            fn: '<?php echo esc_js($customer->get_billing_first_name()); ?>',
            ln: '<?php echo esc_js($customer->get_billing_last_name()); ?>',
            ct: '<?php echo esc_js($customer->get_billing_city()); ?>',
            st: '<?php echo esc_js($customer->get_billing_state()); ?>',
            zp: '<?php echo esc_js($customer->get_billing_postcode()); ?>',
            country: '<?php echo esc_js($customer->get_billing_country()); ?>'
        }, parameters), {eventID: initiateCheckoutEventId});
        sendServerEvent('InitiateCheckout', Object.assign({
            content_ids: <?php echo json_encode(array_map('esc_js', $content_ids)); ?>,
            content_names: <?php echo json_encode(array_map('esc_js', $content_names)); ?>,
            content_categories: <?php echo json_encode(array_map('esc_js', $content_categories)); ?>,
            content_type: 'product',
            value: <?php echo esc_js($value); ?>,
            currency: '<?php echo esc_js(get_woocommerce_currency()); ?>',
            em: '<?php echo esc_js($customer->get_billing_email()); ?>',
            ph: '<?php echo esc_js($customer->get_billing_phone()); ?>',
            fn: '<?php echo esc_js($customer->get_billing_first_name()); ?>',
            ln: '<?php echo esc_js($customer->get_billing_last_name()); ?>',
            ct: '<?php echo esc_js($customer->get_billing_city()); ?>',
            st: '<?php echo esc_js($customer->get_billing_state()); ?>',
            zp: '<?php echo esc_js($customer->get_billing_postcode()); ?>',
            country: '<?php echo esc_js($customer->get_billing_country()); ?>'
        }, parameters), initiateCheckoutEventId);
        <?php endif; ?>

        <?php if (is_search()): ?>
        var searchEventId = generateEventId();
        fbq('track', 'Search', Object.assign({
            search_string: '<?php echo esc_js(get_search_query()); ?>'
        }, parameters), {eventID: searchEventId});
        sendServerEvent('Search', Object.assign({
            search_string: '<?php echo esc_js(get_search_query()); ?>'
        }, parameters), searchEventId);
        <?php endif; ?>
    });

    jQuery(document).ready(function($) {
        // Add to Cart Event
        $('body').on('added_to_cart', function(event, fragments, cart_hash, button) {
            var product_id = button.data('product_id');
            var product_name = button.data('product_name');
            var product_category = button.data('product_category');
            var price = button.data('price');
            var current_user = <?php echo json_encode([
                'email' => $current_user->user_email,
                'firstname' => $current_user->user_firstname,
                'lastname' => $current_user->user_lastname
            ]); ?>;
            
            // Αποφυγή διπλής καταγραφής
            if (!window.addToCartTracked) {
                var addToCartEventId = generateEventId();
                fbq('track', 'AddToCart', Object.assign({
                    content_ids: [product_id],
                    content_type: 'product',
                    content_name: product_name,
                    content_category: product_category,
                    value: price,
                    currency: '<?php echo esc_js(get_woocommerce_currency()); ?>',
                    em: current_user.email,
                    fn: current_user.firstname,
                    ln: current_user.lastname
                }, getParameters()), {eventID: addToCartEventId});
                sendServerEvent('AddToCart', Object.assign({
                    content_ids: [product_id],
                    content_type: 'product',
                    content_name: product_name,
                    content_category: product_category,
                    value: price,
                    currency: '<?php echo esc_js(get_woocommerce_currency()); ?>',
                    em: current_user.email,
                    fn: current_user.firstname,
                    ln: current_user.lastname
                }, getParameters()), addToCartEventId);
                window.addToCartTracked = true;
                setTimeout(function() { window.addToCartTracked = false; }, 1000);
            }
        });

        // Remove from Cart Event
        $(document.body).on('removed_from_cart', function(event, fragments, cart_hash, button) {
            var product_id = button.data('product_id');
            var product_name = button.data('product_name');
            var product_category = button.data('product_category');
            var price = button.data('price');
            
            // Αποφυγή διπλής καταγραφής
            if (!window.removeFromCartTracked) {
                var removeFromCartEventId = generateEventId();
                fbq('trackCustom', 'RemoveFromCart', Object.assign({
                    content_ids: [product_id],
                    content_type: 'product',
                    content_name: product_name,
                    content_category: product_category,
                    value: price,
                    currency: '<?php echo esc_js(get_woocommerce_currency()); ?>'
                }, getParameters()), {eventID: removeFromCartEventId});
                sendServerEvent('RemoveFromCart', Object.assign({
                    content_ids: [product_id],
                    content_type: 'product',
                    content_name: product_name,
                    content_category: product_category,
                    value: price,
                    currency: '<?php echo esc_js(get_woocommerce_currency()); ?>'
                }, getParameters()), removeFromCartEventId);
                window.removeFromCartTracked = true;
                setTimeout(function() { window.removeFromCartTracked = false; }, 1000);
            }
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'facebook_pixel_events');

function facebook_pixel_purchase($order_id) {
    $order = wc_get_order($order_id);
    if (!$order) return;
    $items = $order->get_items();
    $content_ids = $content_names = $content_categories = array();
    $total_quantity = 0;
    foreach ($items as $item) {
        $product = $item->get_product();
        if (!$product) continue;
        $product_id = $product->get_id();
        $content_ids[] = $product_id;
        $content_names[] = $product->get_name();
        $categories = get_the_terms($product_id, 'product_cat');
        $content_categories[] = $categories ? wp_list_pluck($categories, 'name')[0] : 'Uncategorized';
        $total_quantity += $item->get_quantity();
    }
    $customer_email = $order->get_billing_email();
    $customer_phone = $order->get_billing_phone();
    $customer_first_name = $order->get_billing_first_name();
    $customer_last_name = $order->get_billing_last_name();
    $customer_city = $order->get_billing_city();
    $customer_state = $order->get_billing_state();
    $customer_postcode = $order->get_billing_postcode();
    $customer_country = $order->get_billing_country();
    ?>
    <script>
    var purchaseEventId = generateEventId();
    fbq('track', 'Purchase', Object.assign({
        content_ids: <?php echo json_encode(array_map('esc_js', $content_ids)); ?>,
        content_names: <?php echo json_encode(array_map('esc_js', $content_names)); ?>,
        content_categories: <?php echo json_encode(array_map('esc_js', $content_categories)); ?>,
        content_type: 'product',
        value: <?php echo esc_js($order->get_total()); ?>,
        currency: '<?php echo esc_js(get_woocommerce_currency()); ?>',
        num_items: <?php echo esc_js($total_quantity); ?>,
        em: '<?php echo esc_js($customer_email); ?>',
        ph: '<?php echo esc_js($customer_phone); ?>',
        fn: '<?php echo esc_js($customer_first_name); ?>',
        ln: '<?php echo esc_js($customer_last_name); ?>',
        ct: '<?php echo esc_js($customer_city); ?>',
        st: '<?php echo esc_js($customer_state); ?>',
        zp: '<?php echo esc_js($customer_postcode); ?>',
        country: '<?php echo esc_js($customer_country); ?>'
    }, getParameters()), {eventID: purchaseEventId});

    sendServerEvent('Purchase', Object.assign({
        content_ids: <?php echo json_encode(array_map('esc_js', $content_ids)); ?>,
        content_names: <?php echo json_encode(array_map('esc_js', $content_names)); ?>,
        content_categories: <?php echo json_encode(array_map('esc_js', $content_categories)); ?>,
        content_type: 'product',
        value: <?php echo esc_js($order->get_total()); ?>,
        currency: '<?php echo esc_js(get_woocommerce_currency()); ?>',
        num_items: <?php echo esc_js($total_quantity); ?>,
        em: '<?php echo esc_js($customer_email); ?>',
        ph: '<?php echo esc_js($customer_phone); ?>',
        fn: '<?php echo esc_js($customer_first_name); ?>',
        ln: '<?php echo esc_js($customer_last_name); ?>',
        ct: '<?php echo esc_js($customer_city); ?>',
        st: '<?php echo esc_js($customer_state); ?>',
        zp: '<?php echo esc_js($customer_postcode); ?>',
        country: '<?php echo esc_js($customer_country); ?>'
    }, getParameters()), purchaseEventId);
    </script>
    <?php
}
add_action('woocommerce_thankyou', 'facebook_pixel_purchase');

function handle_facebook_capi_event() {
    if (!isset($_POST['event_name']) || !isset($_POST['event_data']) || !isset($_POST['event_id'])) {
        wp_send_json_error('Invalid event data');
        return;
    }

    $event_name = sanitize_text_field($_POST['event_name']);
    $event_data = json_decode(stripslashes($_POST['event_data']), true);
    $event_id = sanitize_text_field($_POST['event_id']);

    if (!$event_data) {
        wp_send_json_error('Invalid event data format');
        return;
    }

    $url = 'https://graph.facebook.com/v13.0/' . FB_PIXEL_ID . '/events';
    $data = array(
        'data' => array(
            array(
                'event_name' => $event_name,
                'event_time' => time(),
                'event_id' => $event_id,
                'user_data' => $event_data,
                'custom_data' => $event_data,
                'action_source' => 'website'
            )
        ),
        'access_token' => FB_ACCESS_TOKEN
    );

    $args = array(
        'body'        => json_encode($data),
        'headers'     => array('Content-Type' => 'application/json'),
        'timeout'     => 60,
        'redirection' => 5,
        'blocking'    => true,
        'httpversion' => '1.0',
        'sslverify'   => false,
    );

    $response = wp_remote_post($url, $args);

    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        wp_send_json_error("Something went wrong: $error_message");
    } else {
        wp_send_json_success('Event sent successfully');
    }
}
add_action('wp_ajax_facebook_capi_event', 'handle_facebook_capi_event');
add_action('wp_ajax_nopriv_facebook_capi_event', 'handle_facebook_capi_event');


remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
add_filter( 'woocommerce_sale_flash', '__return_null' );

remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );



function custom_add_canonical_tag() {
    if(is_home()){
        $canonical_url = home_url();
        echo '<link rel="canonical" href="' . esc_url($canonical_url) . '" />' . "\n";
    }
    if (is_category() || is_tax('product_cat')) {
        // Αν είστε σε σελίδα κατηγορίας προϊόντος
        $category = get_queried_object();
        $canonical_url = get_term_link($category);
        echo '<link rel="canonical" href="' . esc_url($canonical_url) . '" />' . "\n";
    } 
    
    elseif (is_singular('product') || isset($_GET['attribute_pa_size'])) {
        global $post;
        $canonical_url = get_permalink($post->ID);

    }
    elseif (is_page()) {
        $page_id = get_queried_object_id();
        $canonical_url = get_permalink($page_id);
    }

    
}

// Προσθήκη του κώδικα στο <head>
add_action('wp_head', 'custom_add_canonical_tag', 5);


remove_filter('wp_robots', 'wp_robots_max_image_preview_large');

function custom_meta_robots() {
    echo '<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />' . "\n";
}
add_action('wp_head', 'custom_meta_robots' , 1);



add_action('after_setup_theme', 'walkbyme_setup');

function walkbyme_setup(){
    // Post Thumbnails Support
    add_theme_support('post-thumbnails');
    
    // Menu 
    register_nav_menus(array(
        'primary' => __('Primary Navigation', 'walkbyme'),
        'footermenu1' => __('Second Navigation', 'walkbyme'),
        'footermenu2' => __('Footer Navigation', 'walkbyme')
    )); 
    
    // Default image and If want change theme logo 
    $args = array(
	'width'         => 300,
	'height'        => 55,
	'default-image' => get_template_directory_uri() . '/assets/images/walkbyme_logo_site300pxls.svg',
	'uploads'       => true,
    );
    add_theme_support( 'custom-header', $args );
    
    
    // Footer Sidebars
    $footersidebars = array(
        'Footer first',
        'Footer second',
        'Footer three',
        'Footer four',
        'Footer five'
    );

    foreach ($footersidebars as $index => $footersidebar) {
        $args = array(
            'name' => $footersidebar,
            'description' => 'This is the area displayed on the left in categories',
            'before_widget' => '<div id="%1$s" class="module_cat %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="widgettitle">',
            'after_title' => '</h3>'
        );

        register_sidebar($args);
    }
    
    
    $newsletter = array(
	'name'          => 'newsletter',
	'description'   => 'Είναι η περιοχή που εμφανίζεται αριστερά στις κατηγορίες',
	'before_widget' => '<div id="%1$s" class="module_cat %2$s">',
	'after_widget'  => '</div>',
	'before_title'  => '<h3 class="widgettitle">',
	'after_title'   => '</h3>' );


        register_sidebar($newsletter);
}



function jewelry_theme_widgets_init() {
    register_sidebar( array(
        'name'          => 'Jewelry Sidebar',
        'id'            => 'sidebar-jewelry',
        'description'   => 'Add widgets here to appear in your jewelry sidebar.',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'jewelry_theme_widgets_init' );


add_action( 'after_setup_theme', 'walkbyme_woocommerce_support' );


function walkbyme_woocommerce_support() {
	add_theme_support( 'woocommerce', array(
		//'thumbnail_image_width' => 150,
		'single_image_width' => 600,
		//'gallery_thumbnail_image_width' => 600,
        'product_grid'          => array(
            'default_rows'    => 3,
            'min_rows'        => 2,
            'max_rows'        => 8,
            'default_columns' => 4,
            'min_columns'     => 2,
            'max_columns'     => 5,
        ),
	) );
    add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-slider' );
}


function modify_woo_gallery_on_mobile() {
    if ( wp_is_mobile() ) {
        remove_theme_support( 'wc-product-gallery-zoom' );
        //remove_theme_support( 'wc-product-gallery-lightbox' );
    }
}
add_action( 'after_setup_theme', 'modify_woo_gallery_on_mobile', 11 );

// Συνάρτηση για τον υπολογισμό του ποσοστού έκπτωσης
if (!function_exists('calculate_discount_percentage')) {
    function calculate_discount_percentage($regular_price, $sale_price) {
        if (!empty($regular_price) && !empty($sale_price) && $sale_price < $regular_price) {
            $discount_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
            return $discount_percentage;
        }
        return 0;
    }
}




add_filter( 'wp_title', 'walkbyme_wp_title', 10, 2 );

function walkbyme_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'walkbyme' ), max( $paged, $page ) );

	return $title;
}


//Αναζήτηση με τίτλο , περιγραφή , ετικέτες
function customize_product_search_form($form) {
    $form = '<form role="search" method="get" id="searchform" action="' . esc_url(home_url('/')) . '" class="woocommerce-product-search" aria-label="' . esc_attr__('Product Search', 'woocommerce') . '">
        <label class="screen-reader-text" for="s">' . __('Search for:', 'woocommerce') . '</label>
        <input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="' . __('Search products', 'woocommerce') . '" aria-label="' . esc_attr__('Search products', 'woocommerce') . '" autocomplete="off" />
        <input type="submit" id="searchsubmit" value="'. esc_attr__('Search', 'woocommerce') .'" />
        <input type="hidden" name="post_type" value="product" />
    </form>';
    return $form;
}
add_filter('get_product_search_form', 'customize_product_search_form');


function extend_product_search($search, $query) {
    global $wpdb;

    if (!is_admin() && $query->is_main_query() && $query->is_search() && isset($_GET['post_type']) && $_GET['post_type'] === 'product') {
        $search_terms = explode(' ', $query->get('s'));
        $search_terms = array_map('esc_sql', $search_terms);
        $search_terms = array_map('trim', $search_terms);

        $tag_search = "SELECT {$wpdb->term_relationships}.object_id 
                       FROM {$wpdb->term_relationships}
                       INNER JOIN {$wpdb->term_taxonomy} ON {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id
                       INNER JOIN {$wpdb->terms} ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id
                       WHERE {$wpdb->term_taxonomy}.taxonomy = 'product_tag' 
                       AND ({$wpdb->terms}.name LIKE '%" . implode("%' OR {$wpdb->terms}.name LIKE '%", $search_terms) . "%')";

        $search = " AND ({$wpdb->posts}.ID IN ({$tag_search}) OR {$wpdb->posts}.post_title LIKE '%" . implode("%' OR {$wpdb->posts}.post_title LIKE '%", $search_terms) . "%' OR {$wpdb->posts}.post_content LIKE '%" . implode("%' OR {$wpdb->posts}.post_content LIKE '%", $search_terms) . "%')";
    }

    return $search;
}
add_filter('posts_search', 'extend_product_search', 10, 2);
 






add_action( 'woocommerce_after_shop_loop_item', 'remove_add_to_cart_buttons', 1 );
function remove_add_to_cart_buttons() {
    remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
}


remove_action( 'wp_head', 'feed_links_extra', 3 ); // Display the links to the extra feeds such as category feeds
remove_action( 'wp_head', 'feed_links', 2 ); // Display the links to the general feeds: Post and Comment Feed
remove_action( 'wp_head', 'rsd_link' ); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action( 'wp_head', 'wlwmanifest_link' ); // Display the link to the Windows Live Writer manifest file.
remove_action( 'wp_head', 'index_rel_link' ); // index link
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); // prev link
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 ); // start link
remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 ); // Display relational links for the posts adjacent to the current post.
remove_action( 'wp_head', 'wp_generator' ); // Display the XHTML generator that is generated on the wp_head hook, WP version


// To change add to cart text on single product page
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woocommerce_custom_single_add_to_cart_text' ); 
function woocommerce_custom_single_add_to_cart_text() {
    return __( 'Αγόρασε το', 'woocommerce' ); 
}

// To change add to cart text on product archives(Collection) page
add_filter( 'woocommerce_product_add_to_cart_text', 'woocommerce_custom_product_add_to_cart_text' );  
function woocommerce_custom_product_add_to_cart_text() {
    return __( 'Αγόρασε το', 'woocommerce' );
}

add_filter('woocommerce_dropdown_variation_attribute_options_args', 'customize_variation_dropdown', 10, 1);
function customize_variation_dropdown($args)
{
    $args['class'] = 'custom-select';
    return $args;
}



/**
 * Hide shipping rates when free shipping is available, but keep "Local pickup" 
 * Updated to support WooCommerce 2.6 Shipping Zones
 */

function hide_shipping_when_free_is_available( $rates, $package ) {
	$new_rates = array();
	foreach ( $rates as $rate_id => $rate ) {
		// Only modify rates if free_shipping is present.
		if ( 'free_shipping' === $rate->method_id ) {
			$new_rates[ $rate_id ] = $rate;
			break;
		}
	}

	if ( ! empty( $new_rates ) ) {
		//Save local pickup if it's present.
		foreach ( $rates as $rate_id => $rate ) {
			if ('local_pickup' === $rate->method_id ) {
				$new_rates[ $rate_id ] = $rate;
				break;
			}
		}
		return $new_rates;
	}

	return $rates;
}

add_filter( 'woocommerce_package_rates', 'hide_shipping_when_free_is_available', 10, 2 );



remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );

function custom_pagination() {
    global $wp_query;
    $big = 999999999;
    $pages = paginate_links(array(
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '?page=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $wp_query->max_num_pages,
        'prev_next' => false,
        'type' => 'array',
        'prev_text' => '&larr;',
        'next_text' => '&rarr;',
    ));
    
    if (is_array($pages)) {
        $current_page = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
        echo '<nav class="outerpagination"><ul class="pagination">';
        
        foreach ($pages as $i => $page) {
            if ($current_page == 1 && $i == 0) {
                echo "<li class='active'>$page</li>";
            } else {
                echo "<li>$page</li>";
            }
        }
        
        echo '</ul></nav>';
    }
}


remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );


add_filter( 'woocommerce_enqueue_styles', '__return_false' );





// Συνάρτηση για τη δημιουργία και απόκτηση των προϊόντων στην αρχική σελίδα
function get_featured_products() {
    $meta_query  = WC()->query->get_meta_query();
    $tax_query   = WC()->query->get_tax_query();
    $tax_query[] = array(
        'taxonomy' => 'product_visibility',
        'field'    => 'name',
        'terms'    => 'featured',
        'operator' => 'IN',
    );

    $args = array(
        'post_type'           => 'product',
        'post_status'         => 'publish',
        'posts_per_page'      => 12,
        'meta_query'          => $meta_query,
        'tax_query'           => $tax_query,
    );

    return new WP_Query( $args );
}

// Συνάρτηση για την εμφάνιση των προϊόντων στην αρχική σελίδα
function display_featured_products() {
    $wc_query = get_featured_products();

    if ( $wc_query->have_posts() ) :
        while ( $wc_query->have_posts() ) : $wc_query->the_post();
            echo '<div>';
            echo '<figure>';
            echo '<a href="' . get_permalink() . '">';
            $attr = array();
            $thumb = get_the_post_thumbnail( $loop->ID, $attr );
            echo $thumb;
            echo '</a>';
            echo '</figure>';
            echo '<h3><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
            echo '</div>';
        endwhile;
        wp_reset_postdata();
    endif;
}


// Προσαρμοσμένη συνάρτηση για προσθήκη πεδίων κατηγορίας
function add_custom_category_field($term) {
    $show_on_homepage = get_term_meta($term->term_id, 'show_on_homepage', true);
    $category_priority = get_term_meta($term->term_id, 'category_priority', true);
    $category_image = get_term_meta($term->term_id, 'category_image', true);
    ?>

    <script>
        jQuery(function ($) {
            $(document).on('click', '.image-upload', function (e) {
                e.preventDefault();
                var button = $(this),
                    custom_uploader = wp.media({
                        title: 'Επιλογή Εικόνας',
                        library: {
                            type: 'image'
                        },
                        button: {
                            text: 'Επιλογή Εικόνας'
                        },
                        multiple: false
                    }).on('select', function () {
                        var attachment = custom_uploader.state().get('selection').first().toJSON();
                        $('#category_image').val(attachment.url);
                    }).open();
            });

            // Μετακίνηση πεδίων προς το τέλος της φόρμας
            $('#show_on_homepage').closest('.form-field').insertAfter('form#edit-tag-form .form-field.term-description:last');
            $('#category_priority').closest('.form-field').insertAfter('form#edit-tag-form .form-field.term-description:last');
            $('#category_image').closest('.form-field').insertAfter('form#edit-tag-form .form-field.term-description:last');
        });
    </script>

    <div class="form-field">
        <label for="show_on_homepage"><?php _e('Εμφάνιση στην αρχική σελίδα', 'walkbyme'); ?></label>
        <select name="show_on_homepage" id="show_on_homepage">
            <option value="yes" <?php selected($show_on_homepage, 'yes'); ?>><?php _e('Ναι', 'walkbyme'); ?></option>
            <option value="no" <?php selected($show_on_homepage, 'no'); ?>><?php _e('Όχι', 'walkbyme'); ?></option>
        </select>
    </div>

    <div class="form-field">
        <label for="category_priority">Προτεραιότητα Κατηγορίας:</label>
        <input type="number" name="category_priority" id="category_priority" value="<?php echo esc_attr($category_priority); ?>" class="regular-text">
        <p class="description">Εισάγετε έναν αριθμό για την προτεραιότητα της κατηγορίας (μεγαλύτερος αριθμός έχει υψηλότερη προτεραιότητα).</p>
    </div>

    <div class="form-field">
        <label for="category_image">Εικόνα Κατηγορίας</label>
        <input type="text" name="category_image" id="category_image" class="meta-image" value="<?php echo esc_attr($category_image); ?>">
        <br>
        <button class="button image-upload">Επιλογή Εικόνας</button>
    </div>
    <?php
}
add_action('product_cat_edit_form_fields', 'add_custom_category_field', 999, 1);
add_action('product_cat_add_form_fields', 'add_custom_category_field', 999, 1);

// Προσαρμοσμένη συνάρτηση για αποθήκευση προσαρμοσμένων πεδίων κατηγορίας
function save_custom_category_field($term_id) {
    if (isset($_POST['show_on_homepage'])) {
        update_term_meta($term_id, 'show_on_homepage', sanitize_text_field($_POST['show_on_homepage']));
    }

    if (isset($_POST['category_priority'])) {
        update_term_meta($term_id, 'category_priority', intval($_POST['category_priority']));
    }

    if (isset($_POST['category_image'])) {
        update_term_meta($term_id, 'category_image', sanitize_text_field($_POST['category_image']));
    }
}
add_action('edited_product_cat', 'save_custom_category_field', 20, 1);
add_action('create_product_cat', 'save_custom_category_field', 20, 1);



// function update_custom_sitemap($post_id, $post, $update) {
//     // Ελέγχουμε αν το αποθηκευμένο έγγραφο είναι ένα προϊόν του WooCommerce
//     if (get_post_type($post_id) === 'product') {
//         // Καλούμε τη συνάρτηση custom_sitemap για να ενημερώσουμε τον χάρτη ιστοσελίδας
//         custom_sitemap();
//     }
// }

// add_action('save_post', 'update_custom_sitemap', 10, 3);


function custom_sitemap() {
    if (isset($_GET['custom-sitemap']) && $_GET['custom-sitemap'] === 'generate') {
        // Create a custom sitemap for WooCommerce products
        header('Content-Type: text/xml; charset=utf-8');
        
        // Set the file path for the XML file
        $file_path = ABSPATH . 'custom-sitemap.xml';

        // Open the file for writing
        $file = fopen($file_path, 'w');

        // Check if the file was opened successfully
        if ($file !== false) {
            // Start buffering the output
            ob_start();

            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
            xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

            echo '<url>';
            echo '<loc>https://www.walkbyme.gr/</loc>';
            echo '<changefreq>daily</changefreq>';
            echo '<priority>1.0</priority>';
            echo '</url>';

            // Ανάκτηση όλων των κατηγοριών
            $categories = get_categories(array(
                'taxonomy' => 'product_cat',
                'hide_empty' => false,
            ));

            // Προσθήκη κατηγοριών στον χάρτη ιστοσελίδας
            foreach ($categories as $category) {
                $category_url = get_term_link($category);
                echo '<url>';
                echo '<loc>' . esc_url($category_url) . '</loc>';
                echo '<changefreq>weekly</changefreq>';
                echo '<priority>0.80</priority>'; // Προσαρμόστε την προτεραιότητα (αν χρειάζεται)
                echo '</url>';
            }

            $args = array(
                'post_type' => 'product',
                'posts_per_page' => -1,
            );

            $products = new WP_Query($args);
            while ($products->have_posts()) {
                $products->the_post();
                $product_url = get_permalink();
                echo '<url>';
                echo '<loc>' . esc_url($product_url) . '</loc>';
                echo '<lastmod>' . gmdate('c', strtotime(get_the_modified_date('Y-m-d H:i:s'))) . '</lastmod>'; // Include the last modification date if desired.
                echo '<priority>0.80</priority>';
                echo '</url>';
            }

            wp_reset_postdata();

            echo '</urlset>';

            // End buffering and write the contents to the file
            $xml_content = ob_get_clean();
            fwrite($file, $xml_content);

            // Ανανέωση των δικαιωμάτων του αρχείου
            chmod($file_path, 0644);

            // Close the file
            fclose($file);

            // Output the XML to the browser
            echo $xml_content;

            // Terminate script execution
            die();
        }
    }
}

add_action('init', 'custom_sitemap');

add_action('save_post', 'update_custom_sitemap');

function update_custom_sitemap($post_id) {
    // Ελέγχουμε αν το αποθηκευμένο έγγραφο είναι ένα προϊόν του WooCommerce
    if (get_post_type($post_id) === 'product') {
        // Καλούμε τη συνάρτηση custom_sitemap για να ενημερώσουμε τον χάρτη ιστοσελίδας
        custom_sitemap();
    }
}



function custom_sitemap_articles() {
    $sitemap_action = isset($_GET['custom-sitemap-articles']) ? sanitize_text_field($_GET['custom-sitemap-articles']) : '';
    if ($sitemap_action === 'generate') {
        // Αύξηση του ορίου μνήμης
        ini_set('memory_limit', '256M');
        
        header('Content-Type: text/xml; charset=utf-8');
       
        $file_path = ABSPATH . 'custom-sitemap-articles.xml';
        
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once (ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }
        
        if ($wp_filesystem->put_contents($file_path, '', FS_CHMOD_FILE)) {
            ob_start();
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
            
            $categories = get_categories(array(
                'taxonomy' => 'category',
                'hide_empty' => false,
            ));
            
            foreach ($categories as $category) {
                $category_url = get_term_link($category);
                if (!is_wp_error($category_url)) {
                    echo '<url>';
                    echo '<loc>' . esc_url(trim($category_url)) . '</loc>';
                    echo '<changefreq>weekly</changefreq>';
                    echo '<priority>0.80</priority>';
                    echo '</url>';
                } else {
                    custom_error_log('Σφάλμα στην κατηγορία: ' . $category->name . ' - ' . $category_url->get_error_message());
                }
            }
            
            $args = array(
                'post_type' => 'post',
                'posts_per_page' => -1,
                'post_status' => 'publish',
            );
            $articles = new WP_Query($args);
            
            $article_count = 0;
            if ($articles->have_posts()) {
                while ($articles->have_posts()) {
                    $articles->the_post();
                    $article_url = get_permalink();
                    echo '<url>';
                    echo '<loc>' . esc_url(trim($article_url)) . '</loc>';
                    echo '<lastmod>' . get_the_modified_time('c') . '</lastmod>';
                    echo '<priority>0.80</priority>';
                    echo '</url>';
                    $article_count++;
                }
                wp_reset_postdata();
            }
            
            echo '</urlset>';
            
            $xml_content = ob_get_clean();
            $wp_filesystem->put_contents($file_path, $xml_content, FS_CHMOD_FILE);
            
            custom_error_log('Sitemap δημιουργήθηκε επιτυχώς. Συμπεριλήφθηκαν ' . $article_count . ' άρθρα.');
            
            echo $xml_content;
            die();
        } else {
            custom_error_log('Αποτυχία ανοίγματος του αρχείου sitemap για εγγραφή.');
        }
    }
}

add_action('init', 'custom_sitemap_articles');

function update_custom_sitemap_articles($post_id) {
    $post_type = get_post_type($post_id);
    if ($post_type === 'post' && get_post_status($post_id) === 'publish') {
        custom_sitemap_articles();
    }
}

add_action('save_post', 'update_custom_sitemap_articles');
add_action('edit_category', 'update_custom_sitemap_articles');
add_action('delete_category', 'update_custom_sitemap_articles');

function custom_error_log($message) {
    if (WP_DEBUG_LOG) {
        error_log($message);
    }
}






// Προσθέστε προσαρμοσμένα πεδία στην απαρίθμηση "product_cat"
function custom_taxonomy_fields() {
    ?>
    <div class="form-field">
        <label for="custom_field_one">Προσαρμοσμένο Πεδίο 1</label>
        <input type="text" name="custom_field_one" id="custom_field_one">
    </div>
    <div class="form-field">
        <label for="custom_field_two">Προσαρμοσμένο Πεδίο 2</label>
        <input type="text" name="custom_field_two" id="custom_field_two">
    </div>
    <?php
}
add_action('product_cat_add_form_fields', 'custom_taxonomy_fields', 10, 2);

// Αποθηκεύστε τιμές προσαρμοσμένων πεδίων για την απαρίθμηση "product_cat"
function save_taxonomy_custom_fields($term_id) {
    if (isset($_POST['custom_field_one'])) {
        $custom_field_one = sanitize_text_field($_POST['custom_field_one']);
        add_term_meta($term_id, 'custom_field_one', $custom_field_one, true);
    }
    if (isset($_POST['custom_field_two'])) {
        $custom_field_two = sanitize_text_field($_POST['custom_field_two']);
        add_term_meta($term_id, 'custom_field_two', $custom_field_two, true);
    }
}
add_action('edited_product_cat', 'save_taxonomy_custom_fields', 10, 2);
add_action('create_product_cat', 'save_taxonomy_custom_fields', 10, 2);






// Πεδία για κατηγορίες άρθρων
add_action('edit_category_form_fields', 'category_custom_meta_fields');
add_action('edited_category', 'save_category_custom_meta');

function category_custom_meta_fields($term) {
    $meta_title = get_term_meta($term->term_id, '_category_meta_title', true);
    $meta_description = get_term_meta($term->term_id, '_category_meta_description', true);
    ?>
    <tr class="form-field">
        <th scope="row"><label for="meta_title">Custom Meta Title</label></th>
        <td><input type="text" name="meta_title" id="meta_title" value="<?php echo esc_attr($meta_title); ?>" /></td>
    </tr>
    <tr class="form-field">
        <th scope="row"><label for="meta_description">Custom Meta Description</label></th>
        <td><textarea name="meta_description" id="meta_description"><?php echo esc_textarea($meta_description); ?></textarea></td>
    </tr>
    <?php
}

function save_category_custom_meta($term_id) {
    $meta_title = isset($_POST['meta_title']) ? sanitize_text_field($_POST['meta_title']) : '';
    $meta_description = isset($_POST['meta_description']) ? sanitize_textarea_field($_POST['meta_description']) : '';

    update_term_meta($term_id, '_category_meta_title', $meta_title);
    update_term_meta($term_id, '_category_meta_description', $meta_description);
}


function woocommerce_fbq_purchase_event($order_id) {
  // Get the order data
  $order = wc_get_order($order_id);

  // Set the event data
  $eventData = [
    'value' => $order->total,
    'currency' => $order->currency,
  ];

  // Add the product data
  $products = [];

  foreach ($order->get_items() as $item_id => $item) {
    $product = $item->get_product();

    $products[] = [
      'product_id' => $product->get_id(),
      'quantity' => $item->get_quantity(),
    ];
  }

  $eventData['content_ids'] = wp_list_pluck($products, 'product_id');
  $eventData['contents'] = $products;

  // Track the event
  fbq('track', 'Purchase', $eventData);
}



function create_slider_post_type() {
    register_post_type('gallery',
        array(
            'labels' => array(
                'name' => __('Slider'),
                'singular_name' => __('Slider')
            ),
            'public' => false,
            'has_archive' => false,
            'supports' => array('title','thumbnail', 'editor'),
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'menu_position' => null,
            'menu_icon' => 'dashicons-format-gallery'
        )
    );
}
add_action('init', 'create_slider_post_type');

function add_slider_meta_boxes() {
    add_meta_box(
        'slider_button_meta_box', // ID του meta box
        'Slider Details', // Τίτλος του meta box
        'render_slider_button_meta_box', // Συνάρτηση για το rendering του περιεχομένου
        'gallery', // Custom post type
        'normal', // Context
        'default' // Priority
    );
}
add_action('add_meta_boxes', 'add_slider_meta_boxes');

function render_slider_button_meta_box($post) {
    // Απόκτηση των τρεχουσών τιμών για τα πεδία
    $button_text = get_post_meta($post->ID, '_slider_button_text', true);
    $button_url = get_post_meta($post->ID, '_slider_button_url', true);
    $slider_order = get_post_meta($post->ID, '_slider_order', true);

    // Απόδοση των πεδίων στο meta box
    ?>
    <label for="slider_button_text">Button Text:</label>
    <input type="text" name="slider_button_text" id="slider_button_text" value="<?php echo esc_attr($button_text); ?>" style="width: 100%;" />

    <label for="slider_button_url" style="margin-top: 10px; display: block;">Button URL:</label>
    <input type="url" name="slider_button_url" id="slider_button_url" value="<?php echo esc_attr($button_url); ?>" style="width: 100%;" />

    <label for="slider_order" style="margin-top: 10px; display: block;">Slider Order:</label>
    <input type="number" name="slider_order" id="slider_order" value="<?php echo esc_attr($slider_order); ?>" style="width: 100%;" />
    <?php
    // Προσθέτουμε nonce για ασφαλή αποθήκευση
    wp_nonce_field(basename(__FILE__), 'slider_button_nonce');
}

function save_slider_meta_boxes($post_id) {
    // Έλεγχος για nonce και autosave
    if (!isset($_POST['slider_button_nonce']) || !wp_verify_nonce($_POST['slider_button_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // Αποθήκευση των τιμών για τα πεδία
    if (isset($_POST['slider_button_text'])) {
        update_post_meta($post_id, '_slider_button_text', sanitize_text_field($_POST['slider_button_text']));
    }

    if (isset($_POST['slider_button_url'])) {
        update_post_meta($post_id, '_slider_button_url', esc_url($_POST['slider_button_url']));
    }

    if (isset($_POST['slider_order'])) {
        update_post_meta($post_id, '_slider_order', intval($_POST['slider_order']));
    }
}
add_action('save_post', 'save_slider_meta_boxes');



// Προσθήκη του metabox για την επιλογή δεύτερης εικόνας προϊόντος
add_action('add_meta_boxes', 'add_custom_image_metabox');
function add_custom_image_metabox() {
    add_meta_box(
        'custom_product_image_metabox',
        __('Custom Product Image', 'woocommerce'),
        'custom_product_image_metabox_callback',
        'product',
        'side'
    );
}

// Callback function για την εμφάνιση του metabox
function custom_product_image_metabox_callback($post) {
    wp_nonce_field('save_custom_product_image', 'custom_product_image_nonce');
    $custom_image_id = get_post_meta($post->ID, '_custom_product_image_id', true);
    $custom_image_src = wp_get_attachment_image_src($custom_image_id, 'thumbnail');
    ?>
    <div>
        <img id="custom_product_image_preview" src="<?php echo esc_url($custom_image_src ? $custom_image_src[0] : ''); ?>" style="max-width:100%; height:auto;" />
        <input type="hidden" id="custom_product_image_id" name="custom_product_image_id" value="<?php echo esc_attr($custom_image_id); ?>" />
        <button type="button" class="button" id="custom_product_image_upload"><?php _e('Select Image', 'woocommerce'); ?></button>
        <button type="button" class="button" id="custom_product_image_remove"><?php _e('Remove Image', 'woocommerce'); ?></button>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var frame;
            $('#custom_product_image_upload').on('click', function(event) {
                event.preventDefault();
                if (frame) {
                    frame.open();
                    return;
                }
                frame = wp.media({
                    title: '<?php _e('Select or Upload Custom Product Image', 'woocommerce'); ?>',
                    button: {
                        text: '<?php _e('Use this image', 'woocommerce'); ?>',
                    },
                    multiple: false
                });
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#custom_product_image_id').val(attachment.id);
                    $('#custom_product_image_preview').attr('src', attachment.url);
                });
                frame.open();
            });
            $('#custom_product_image_remove').on('click', function(event) {
                event.preventDefault();
                $('#custom_product_image_id').val('');
                $('#custom_product_image_preview').attr('src', '');
            });
        });
    </script>
    <?php
}

// Αποθήκευση της επιλεγμένης εικόνας
add_action('save_post', 'save_custom_product_image');
function save_custom_product_image($post_id) {
    if (!isset($_POST['custom_product_image_nonce']) || !wp_verify_nonce($_POST['custom_product_image_nonce'], 'save_custom_product_image')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['custom_product_image_id'])) {
        update_post_meta($post_id, '_custom_product_image_id', absint($_POST['custom_product_image_id']));
    } else {
        delete_post_meta($post_id, '_custom_product_image_id');
    }
}


// Αφαίρεση της προεπιλεγμένης εικόνας προϊόντος
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);

// Προσθήκη δεύτερης εικόνας στο loop προϊόντων
add_action('woocommerce_before_shop_loop_item_title', 'custom_woocommerce_template_loop_product_thumbnail', 11);
function custom_woocommerce_template_loop_product_thumbnail() {
    global $product;

    // Λήψη της κύριας εικόνας
    $main_image_id = $product->get_image_id();
    $main_image_url = wp_get_attachment_image_url($main_image_id, 'woocommerce_thumbnail');

    // Λήψη της δεύτερης εικόνας
    $custom_image_id = get_post_meta($product->get_id(), '_custom_product_image_id', true);
    $custom_image_url = wp_get_attachment_image_url($custom_image_id, 'woocommerce_thumbnail');

    if ($custom_image_url) {
        echo '<a href="' . get_permalink($product->get_id()) . '" class="product-image-link">';
        echo '<img src="' . esc_url($main_image_url) . '" alt="' . esc_attr($product->get_name()) . '" class="main-image" />';
        echo '<img src="' . esc_url($custom_image_url) . '" alt="' . esc_attr($product->get_name()) . '" class="hover-image" />';
        echo '</a>';
    } else {
        echo '<a href="' . get_permalink($product->get_id()) . '" class="product-image-link">';
        echo '<img src="' . esc_url($main_image_url) . '" alt="' . esc_attr($product->get_name()) . '" class="main-image" />';
        echo '</a>';
    }
}