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






// Ορισμός σταθερώνω
define('FB_PIXEL_ID', '891327725921929');

// Προσθήκη βασικού κώδικα Facebook Pixel
function add_facebook_pixel_base() {
    ?>
    <!-- Facebook Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '<?php echo FB_PIXEL_ID; ?>');
    fbq('track', 'PageView');
    </script>
    <noscript>
    <img height="1" width="1" style="display:none" 
         src="https://www.facebook.com/tr?id=<?php echo FB_PIXEL_ID; ?>&ev=PageView&noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->
    <?php
}
add_action('wp_head', 'add_facebook_pixel_base');

// Καταγραφή events
function facebook_pixel_events() {
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Μεταβλητή για να αποφύγουμε διπλές καταγραφές
        var addToCartProcessing = false;

        // Συνάρτηση για την καταγραφή του AddToCart event
        function trackAddToCart(product_id, variation_id, quantity) {
            if (addToCartProcessing) return;
            addToCartProcessing = true;

            // Ανάκτηση πληροφοριών προϊόντος μέσω AJAX
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'get_product_info_for_pixel',
                    product_id: product_id,
                    variation_id: variation_id
                },
                success: function(response) {
                    if (response.success) {
                        var productInfo = response.data;
                        fbq('track', 'AddToCart', {
                            content_ids: [productInfo.id],
                            content_type: 'product',
                            value: productInfo.price * quantity,
                            currency: '<?php echo get_woocommerce_currency(); ?>',
                            contents: [{
                                id: productInfo.id,
                                quantity: quantity
                            }]
                        });
                    }
                },
                complete: function() {
                    addToCartProcessing = false;
                }
            });
        }

        // AddToCart event - AJAX
        $(document.body).on('added_to_cart', function(event, fragments, cart_hash, $button) {
            var product_id = $button.data('product_id');
            var variation_id = $button.data('variation_id') || product_id;
            var quantity = $button.data('quantity') || 1;
            trackAddToCart(product_id, variation_id, quantity);
        });

        // AddToCart event - Button click (για περιπτώσεις που το AJAX αποτύχει)
        $('.single_add_to_cart_button').on('click', function(e) {
            var $form = $(this).closest('form.cart');
            var product_id = $form.find('input[name=product_id]').val() || $(this).val();
            var variation_id = $form.find('input[name=variation_id]').val() || product_id;
            var quantity = $form.find('input[name=quantity]').val() || 1;
            trackAddToCart(product_id, variation_id, quantity);
        });

        // ViewContent event για σελίδες προϊόντων
        <?php if (is_product()) : 
            global $product;
           
            $category_names = wp_get_post_terms($product->get_id(), 'product_cat', array('fields' => 'names'));
            $category = !empty($category_names) ? implode(', ', $category_names) : 'Uncategorized';
           
            $product_type = $product->get_type();
           
            $content_ids = array($product->get_id());
            $contents = array();
            $regular_price = $sale_price = 0;
            $sku = $product->get_sku();
            
            if ($product_type === 'variable') {
                $variations = $product->get_available_variations();
                $variation_prices = $product->get_variation_prices();
                
                foreach ($variations as $variation) {
                    $variation_id = $variation['variation_id'];
                    $content_ids[] = $variation_id;
                    $contents[] = array(
                        'id' => $variation_id,
                        'quantity' => 1
                    );
                }
                
                $regular_price = max($variation_prices['regular_price']);
                $sale_price = min($variation_prices['sale_price']);
            } else {
                $regular_price = $product->get_regular_price();
                $sale_price = $product->get_sale_price();
                $contents[] = array(
                    'id' => $product->get_id(),
                    'quantity' => 1
                );
            }

            if (empty($sale_price)) {
                $sale_price = $regular_price;
            }

            $stock_status = $product->get_stock_status();
            $stock_quantity = $product->get_stock_quantity();
        ?>
        fbq('track', 'ViewContent', {
            content_name: '<?php echo esc_js($product->get_name()); ?>',
            content_ids: <?php echo json_encode(array_unique($content_ids)); ?>,
            content_type: 'product',
            value: <?php echo esc_js($sale_price); ?>,
            currency: '<?php echo esc_js(get_woocommerce_currency()); ?>',
            content_category: '<?php echo esc_js($category); ?>',
            contents: <?php echo json_encode($contents); ?>,
            availability: '<?php echo esc_js($stock_status); ?>',
            <?php if (!is_null($stock_quantity)) : ?>
            inventory: <?php echo esc_js($stock_quantity); ?>,
            <?php endif; ?>
            item_group_id: <?php echo $product_type === 'variable' ? esc_js($product->get_id()) : 'null'; ?>,
            sku: '<?php echo esc_js($sku); ?>',
            regular_price: <?php echo esc_js($regular_price); ?>,
            sale_price: <?php echo esc_js($sale_price); ?>
        });
        <?php endif; ?>

        // ViewCart event
        <?php if (is_cart()) : 
            $cart = WC()->cart;
            $cart_total = $cart->get_cart_contents_total();
            $currency = get_woocommerce_currency();
            
            $content_ids = [];
            $contents = [];
            $num_items = 0;
            
            foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
                $product = $cart_item['data'];
                $product_id = $product->get_id();
                $quantity = $cart_item['quantity'];
                
                $content_ids[] = $product_id;
                $contents[] = [
                    'id' => $product_id,
                    'quantity' => $quantity,
                ];
                $num_items += $quantity;
            }
        ?>
        fbq('trackCustom', 'ViewCart', {
            content_ids: <?php echo json_encode($content_ids); ?>,
            content_type: 'product',
            contents: <?php echo json_encode($contents); ?>,
            value: <?php echo $cart_total; ?>,
            currency: '<?php echo $currency; ?>',
            num_items: <?php echo $num_items; ?>
        });
        <?php endif; ?>

        // InitiateCheckout event
        <?php if (is_checkout() && !is_order_received_page()) : 
            $cart = WC()->cart;
            $cart_total = $cart->get_cart_contents_total();
            $currency = get_woocommerce_currency();
            
            $content_ids = [];
            $contents = [];
            $num_items = 0;
            
            foreach ($cart->get_cart() as $cart_item) {
                $product = $cart_item['data'];
                $product_id = $product->get_id();
                $quantity = $cart_item['quantity'];
                
                $content_ids[] = $product_id;
                $contents[] = [
                    'id' => $product_id,
                    'quantity' => $quantity,
                ];
                $num_items += $quantity;
            }
            
            $customer = WC()->customer;
        ?>
        fbq('track', 'InitiateCheckout', {
            content_ids: <?php echo json_encode($content_ids); ?>,
            content_type: 'product',
            contents: <?php echo json_encode($contents); ?>,
            num_items: <?php echo $num_items; ?>,
            value: <?php echo $cart_total; ?>,
            currency: '<?php echo $currency; ?>',
            user_data: {
                em: '<?php echo esc_js($customer->get_billing_email()); ?>',
                ph: '<?php echo esc_js($customer->get_billing_phone()); ?>',
                fn: '<?php echo esc_js($customer->get_billing_first_name()); ?>',
                ln: '<?php echo esc_js($customer->get_billing_last_name()); ?>',
                ct: '<?php echo esc_js($customer->get_billing_city()); ?>',
                st: '<?php echo esc_js($customer->get_billing_state()); ?>',
                zp: '<?php echo esc_js($customer->get_billing_postcode()); ?>',
                country: '<?php echo esc_js($customer->get_billing_country()); ?>'
            }
        });
        <?php endif; ?>
    });
    </script>
    <?php
}
add_action('wp_footer', 'facebook_pixel_events');



// Purchase event

function facebook_pixel_purchase($order_id) {
    $order = wc_get_order($order_id);
    if (!$order) return;

    $value = floatval($order->get_total());
    $currency = $order->get_currency();
   
    $content_ids = [];
    $contents = [];
    $num_items = 0;
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        $product_id = $product->get_id();
        $quantity = $item->get_quantity();
        $content_ids[] = (string)$product_id;
        $contents[] = [
            'id' => (string)$product_id,
            'quantity' => intval($quantity),
            'item_price' => floatval($product->get_price())
        ];
        $num_items += $quantity;
    }

    // Πληροφορίες πελάτη
    $user_data = [
        'em' => hash('sha256', strtolower($order->get_billing_email())),
        'ph' => hash('sha256', preg_replace('/[^0-9]/', '', $order->get_billing_phone())),
        'fn' => hash('sha256', strtolower($order->get_billing_first_name())),
        'ln' => hash('sha256', strtolower($order->get_billing_last_name())),
        'ct' => hash('sha256', strtolower($order->get_billing_city())),
        'st' => hash('sha256', strtolower($order->get_billing_state())),
        'zp' => hash('sha256', $order->get_billing_postcode()),
        'country' => hash('sha256', strtolower($order->get_billing_country()))
    ];

    // Αφαίρεση κενών τιμών
    $user_data = array_filter($user_data);

    $event_id = wp_generate_uuid4();

    ?>
    <script>
    console.log('Attempting to track Purchase event');
    fbq('track', 'Purchase', {
        content_ids: <?php echo json_encode($content_ids); ?>,
        content_type: <?php echo count($content_ids) > 1 ? "'product_group'" : "'product'"; ?>,
        contents: <?php echo json_encode($contents); ?>,
        value: <?php echo json_encode($value); ?>,
        currency: '<?php echo esc_js($currency); ?>',
        num_items: <?php echo intval($num_items); ?>,
        order_id: '<?php echo esc_js($order->get_order_number()); ?>'
    }, {
        eventID: '<?php echo $event_id; ?>',
        user_data: <?php echo json_encode($user_data); ?>
    });
    console.log('Purchase event tracked');
    </script>
    <?php
}
add_action('woocommerce_thankyou', 'facebook_pixel_purchase', 10, 1);
add_action('woocommerce_payment_complete', 'facebook_pixel_purchase', 10, 1);


// AJAX handler για λήψη πληροφοριών προϊόντος
function get_product_info_for_pixel() {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $variation_id = isset($_POST['variation_id']) ? intval($_POST['variation_id']) : 0;
    
    if ($product_id) {
        $product = wc_get_product($variation_id ? $variation_id : $product_id);
        if ($product) {
            wp_send_json_success(array(
                'id' => $product->get_id(),
                'name' => $product->get_name(),
                'price' => $product->get_price(),
                'sku' => $product->get_sku()
            ));
        }
    }
    wp_send_json_error();
}
add_action('wp_ajax_get_product_info_for_pixel', 'get_product_info_for_pixel');
add_action('wp_ajax_nopriv_get_product_info_for_pixel', 'get_product_info_for_pixel');




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




function custom_product_sitemap() {
    if (isset($_GET['custom-sitemap']) && $_GET['custom-sitemap'] === 'generate') {
        // Αύξηση ορίου μνήμης και χρόνου εκτέλεσης
        ini_set('memory_limit', '256M');
        set_time_limit(300); // 5 λεπτά

        header('Content-Type: application/xml; charset=utf-8');

        $file_path = ABSPATH . 'custom-sitemap.xml';

        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once (ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }

        ob_start();
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
              xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
              http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

        // Προσθήκη αρχικής σελίδας
        echo '  <url>' . "\n";
        echo '    <loc>' . esc_url(home_url('/')) . '</loc>' . "\n";
        echo '    <changefreq>daily</changefreq>' . "\n";
        echo '    <priority>1.0</priority>' . "\n";
        echo '  </url>' . "\n";

        // Προσθήκη κατηγοριών προϊόντων
        $categories = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
        ));

        foreach ($categories as $category) {
            $category_url = get_term_link($category);
            if (!is_wp_error($category_url)) {
                echo '  <url>' . "\n";
                echo '    <loc>' . esc_url($category_url) . '</loc>' . "\n";
                echo '    <changefreq>weekly</changefreq>' . "\n";
                echo '    <priority>0.8</priority>' . "\n";
                echo '  </url>' . "\n";
            }
        }

        // Προσθήκη προϊόντων
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );
        $products = new WP_Query($args);

        if ($products->have_posts()) {
            while ($products->have_posts()) {
                $products->the_post();
                $product_url = get_permalink();
                $modified_time = get_the_modified_time('c');
                echo '  <url>' . "\n";
                echo '    <loc>' . esc_url($product_url) . '</loc>' . "\n";
                echo '    <lastmod>' . $modified_time . '</lastmod>' . "\n";
                echo '    <changefreq>weekly</changefreq>' . "\n";
                echo '    <priority>0.6</priority>' . "\n";
                echo '  </url>' . "\n";
            }
            wp_reset_postdata();
        }

        echo '</urlset>';

        $xml_content = ob_get_clean();
        
        if ($wp_filesystem->put_contents($file_path, $xml_content, FS_CHMOD_FILE)) {
            echo $xml_content;
        } else {
            wp_die('Αποτυχία εγγραφής του sitemap στο αρχείο.');
        }
        
        die();
    }
}
add_action('init', 'custom_product_sitemap');

function schedule_product_sitemap_update($post_id) {
    if (get_post_type($post_id) === 'product' && get_post_status($post_id) === 'publish') {
        wp_schedule_single_event(time() + 300, 'update_product_sitemap_event');
    }
}
add_action('save_post', 'schedule_product_sitemap_update');
add_action('edited_product_cat', 'schedule_product_sitemap_update');
add_action('delete_product_cat', 'schedule_product_sitemap_update');

function do_update_product_sitemap() {
    $sitemap_url = add_query_arg('custom-sitemap', 'generate', home_url());
    wp_remote_get($sitemap_url);
}
add_action('update_product_sitemap_event', 'do_update_product_sitemap');

function add_update_product_sitemap_button() {
    add_submenu_page(
        'edit.php?post_type=product',
        'Ενημέρωση Sitemap Προϊόντων',
        'Ενημέρωση Sitemap Προϊόντων',
        'manage_options',
        'update-product-sitemap',
        'update_product_sitemap_page'
    );
}
add_action('admin_menu', 'add_update_product_sitemap_button');

function update_product_sitemap_page() {
    echo '<div class="wrap">';
    echo '<h1>Ενημέρωση XML Sitemap Προϊόντων</h1>';
    echo '<p>Πατήστε το κουμπί για να ενημερώσετε χειροκίνητα το XML sitemap των προϊόντων.</p>';
    echo '<a href="' . esc_url(add_query_arg('custom-sitemap', 'generate', home_url())) . '" class="button button-primary">Ενημέρωση Sitemap Προϊόντων</a>';
    echo '</div>';
}



function custom_sitemap_articles() {
    $sitemap_action = isset($_GET['custom-sitemap-articles']) ? sanitize_text_field($_GET['custom-sitemap-articles']) : '';
    if ($sitemap_action === 'generate') {
        // Αύξηση του ορίου μνήμης και χρόνου εκτέλεσης
        ini_set('memory_limit', '256M');
        set_time_limit(300); // 5 λεπτά

        header('Content-Type: application/xml; charset=utf-8');

        $file_path = ABSPATH . 'custom-sitemap-articles.xml';

        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once (ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }

        ob_start();
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
              xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
              http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

        // Προσθήκη κατηγοριών
        $categories = get_categories(array('taxonomy' => 'category', 'hide_empty' => false));
        foreach ($categories as $category) {
            $category_url = get_term_link($category);
            if (!is_wp_error($category_url)) {
                echo '  <url>' . "\n";
                echo '    <loc>' . esc_url(trim($category_url)) . '</loc>' . "\n";
                echo '    <changefreq>weekly</changefreq>' . "\n";
                echo '    <priority>0.8</priority>' . "\n";
                echo '  </url>' . "\n";
            }
        }

        // Προσθήκη άρθρων
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );
        $articles = new WP_Query($args);

        if ($articles->have_posts()) {
            while ($articles->have_posts()) {
                $articles->the_post();
                $post_modified = get_the_modified_time('c');
                echo '  <url>' . "\n";
                echo '    <loc>' . esc_url(get_permalink()) . '</loc>' . "\n";
                echo '    <lastmod>' . $post_modified . '</lastmod>' . "\n";
                echo '    <changefreq>monthly</changefreq>' . "\n";
                echo '    <priority>0.6</priority>' . "\n";
                echo '  </url>' . "\n";
            }
            wp_reset_postdata();
        }

        echo '</urlset>';

        $xml_content = ob_get_clean();
        
        if ($wp_filesystem->put_contents($file_path, $xml_content, FS_CHMOD_FILE)) {
            echo $xml_content;
        } else {
            wp_die('Αποτυχία εγγραφής του sitemap στο αρχείο.');
        }
        
        die();
    }
}
add_action('init', 'custom_sitemap_articles');

function update_custom_sitemap_articles($post_id) {
    // Ελέγχουμε αν πρόκειται για αυτόματη αποθήκευση
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    // Ελέγχουμε τον τύπο του post και την κατάστασή του
    $post_type = get_post_type($post_id);
    if ($post_type === 'post' && get_post_status($post_id) === 'publish') {
        // Προγραμματίζουμε την ενημέρωση του sitemap
        wp_schedule_single_event(time() + 300, 'update_sitemap_event');
    }
}
add_action('save_post', 'update_custom_sitemap_articles');
add_action('edit_category', 'update_custom_sitemap_articles');
add_action('delete_category', 'update_custom_sitemap_articles');

// Ορίζουμε τη λειτουργία που θα εκτελεστεί όταν ενεργοποιηθεί το event
function do_update_sitemap() {
    custom_sitemap_articles();
}
add_action('update_sitemap_event', 'do_update_sitemap');

// Προσθέτουμε ένα κουμπί στο admin menu για χειροκίνητη ενημέρωση
function add_update_sitemap_button() {
    add_management_page('Ενημέρωση Sitemap', 'Ενημέρωση Sitemap', 'manage_options', 'update-sitemap', 'update_sitemap_page');
}
add_action('admin_menu', 'add_update_sitemap_button');

function update_sitemap_page() {
    echo '<div class="wrap">';
    echo '<h1>Ενημέρωση XML Sitemap</h1>';
    echo '<p>Πατήστε το κουμπί για να ενημερώσετε χειροκίνητα το XML sitemap.</p>';
    echo '<a href="' . esc_url(add_query_arg('custom-sitemap-articles', 'generate', home_url())) . '" class="button button-primary">Ενημέρωση Sitemap</a>';
    echo '</div>';
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







function add_custom_meta_box() {
    add_meta_box(
        'custom_cta_meta_box',
        'Προτροπή Δράσης',
        'custom_cta_meta_box_callback',
        'post'
    );
}
add_action('add_meta_boxes', 'add_custom_meta_box');

function custom_cta_meta_box_callback($post) {
    wp_nonce_field('custom_cta_meta_box', 'custom_cta_meta_box_nonce');
    $value = get_post_meta($post->ID, '_custom_cta', true);
    wp_editor($value, 'custom_cta_editor', array(
        'textarea_name' => 'custom_cta',
        'media_buttons' => true,
        'tinymce'       => true,
        'quicktags'     => true,
    ));
}

function save_custom_cta_meta_box_data($post_id) {
    if (!isset($_POST['custom_cta_meta_box_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['custom_cta_meta_box_nonce'], 'custom_cta_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['custom_cta'])) {
        update_post_meta($post_id, '_custom_cta', wp_kses_post($_POST['custom_cta']));
    }
}
add_action('save_post', 'save_custom_cta_meta_box_data');



