<?php 

/**
 * WooCommerce Theme Support & Configuration
 * Optimized for performance and modern standards
 */

// Enhanced WooCommerce Support
function walkbyme_woocommerce_support() {
    add_theme_support( 'woocommerce', array(
        'single_image_width'    => 600,
        'thumbnail_image_width' => 300,
        'gallery_thumbnail_image_width' => 150,
        'product_grid' => array(
            'default_rows'    => 3,
            'min_rows'        => 2,
            'max_rows'        => 8,
            'default_columns' => 4,
            'min_columns'     => 2,
            'max_columns'     => 5,
        ),
    ) );
    
    // Gallery features
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-slider' );
    
    // Modern WooCommerce features
    add_theme_support( 'wc-product-gallery-slider' );
    add_theme_support( 'woocommerce', array(
        'gallery_thumbnail_image_width' => 150,
    ) );
}
add_action( 'after_setup_theme', 'walkbyme_woocommerce_support' );

// Enhanced mobile gallery modifications
function modify_woo_gallery_on_mobile() {
    if ( wp_is_mobile() ) {
        remove_theme_support( 'wc-product-gallery-zoom' );
        // Keep lightbox for mobile UX
        // Disable slider on very small screens
        add_action( 'wp_footer', function() {
            if ( wp_is_mobile() ) {
                echo '<style>@media (max-width: 480px) { .woocommerce-product-gallery__wrapper { flex-direction: column; } }</style>';
            }
        });
    }
}
add_action( 'after_setup_theme', 'modify_woo_gallery_on_mobile', 11 );

/**
 * PRICING & DISCOUNT FUNCTIONS
 */

// Enhanced discount percentage calculation with caching
if (!function_exists('calculate_discount_percentage')) {
    function calculate_discount_percentage($regular_price, $sale_price) {
        if (empty($regular_price) || empty($sale_price) || $sale_price >= $regular_price) {
            return 0;
        }
        
        // Use wp_cache for performance
        $cache_key = 'discount_' . md5($regular_price . $sale_price);
        $discount = wp_cache_get($cache_key, 'walkbyme_discounts');
        
        if (false === $discount) {
            $discount = round((($regular_price - $sale_price) / $regular_price) * 100);
            wp_cache_set($cache_key, $discount, 'walkbyme_discounts', 3600); // Cache for 1 hour
        }
        
        return $discount;
    }
}

// Enhanced sale badge with custom styling
function custom_sale_badge() {
    global $product;
    
    if (!$product->is_on_sale()) return;
    
    $regular_price = $product->get_regular_price();
    $sale_price = $product->get_sale_price();
    $discount = calculate_discount_percentage($regular_price, $sale_price);
    
    if ($discount > 0) {
        echo '<span class="custom-sale-badge">-' . $discount . '%</span>';
    }
}

/**
 * WOOCOMMERCE UI CUSTOMIZATIONS
 */

// Remove default sale flash and add custom one
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
add_filter( 'woocommerce_sale_flash', '__return_null' );

// Add custom sale badge
add_action( 'woocommerce_before_shop_loop_item_title', 'custom_sale_badge', 5 );
add_action( 'woocommerce_before_single_product_summary', 'custom_sale_badge', 5 );

// Remove product link wrapper for custom control
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

// Remove add to cart buttons from loop
add_action( 'woocommerce_after_shop_loop_item', 'remove_add_to_cart_buttons', 1 );
function remove_add_to_cart_buttons() {
    remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
}

// Enhanced add to cart text with product type detection
function woocommerce_custom_add_to_cart_text($text, $product = null) {
    if (!$product) {
        global $product;
    }
    
    if (!$product) return $text;
    
    switch ($product->get_type()) {
        case 'variable':
            return __('Επιλογή', 'walkbyme');
        case 'grouped':
            return __('Προβολή', 'walkbyme');
        case 'external':
            return __('Αγόρασε τώρα', 'walkbyme');
        default:
            return __('Αγόρασε το', 'walkbyme');
    }
}
add_filter('woocommerce_product_single_add_to_cart_text', 'woocommerce_custom_add_to_cart_text', 10, 2);
add_filter('woocommerce_product_add_to_cart_text', 'woocommerce_custom_add_to_cart_text', 10, 2);

// Enhanced variation dropdown with accessibility
function customize_variation_dropdown($args) {
    $args['class'] = 'custom-select';
    $args['show_option_none'] = __('Επιλέξτε μια επιλογή', 'walkbyme');
    return $args;
}
add_filter('woocommerce_dropdown_variation_attribute_options_args', 'customize_variation_dropdown', 10, 1);

/**
 * SHIPPING OPTIMIZATIONS
 */

// Enhanced shipping logic with local pickup priority
function hide_shipping_when_free_is_available( $rates, $package ) {
    $new_rates = array();
    $has_free_shipping = false;
    $local_pickup = null;
    
    // Check for free shipping and local pickup
    foreach ( $rates as $rate_id => $rate ) {
        if ( 'free_shipping' === $rate->method_id ) {
            $new_rates[ $rate_id ] = $rate;
            $has_free_shipping = true;
        } elseif ( 'local_pickup' === $rate->method_id ) {
            $local_pickup = $rate;
        }
    }
    
    // If free shipping exists, only show free shipping and local pickup
    if ( $has_free_shipping ) {
        if ( $local_pickup ) {
            $new_rates[ $local_pickup->id ] = $local_pickup;
        }
        return $new_rates;
    }
    
    return $rates;
}
add_filter( 'woocommerce_package_rates', 'hide_shipping_when_free_is_available', 10, 2 );

/**
 * PAGINATION & NAVIGATION
 */

// Enhanced custom pagination
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );

function custom_woocommerce_pagination() {
    global $wp_query;
    
    if ($wp_query->max_num_pages <= 1) return;
    
    $current_page = max(1, get_query_var('paged'));
    $big = 999999999;
    
    $pages = paginate_links(array(
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '?paged=%#%',
        'current' => $current_page,
        'total' => $wp_query->max_num_pages,
        'prev_text' => '&larr; ' . __('Προηγούμενη', 'walkbyme'),
        'next_text' => __('Επόμενη', 'walkbyme') . ' &rarr;',
        'type' => 'array',
        'mid_size' => 2,
        'end_size' => 1,
    ));
    
    if (is_array($pages)) {
        echo '<nav class="woocommerce-pagination">';
        echo '<ul class="page-numbers">';
        
        foreach ($pages as $page) {
            echo '<li>' . $page . '</li>';
        }
        
        echo '</ul></nav>';
    }
}
add_action( 'woocommerce_after_shop_loop', 'custom_woocommerce_pagination', 10 );

// Remove result count for cleaner UI
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );

/**
 * ENHANCED SEARCH FUNCTIONALITY
 */

// Improved product search form
function customize_product_search_form($form) {
    $form = '<form role="search" method="get" class="woocommerce-product-search" action="' . esc_url(home_url('/')) . '">
        <label class="screen-reader-text" for="wc-search-field">' . __('Αναζήτηση για:', 'walkbyme') . '</label>
        <input type="search" 
               id="wc-search-field" 
               name="s" 
               value="' . get_search_query() . '" 
               placeholder="' . esc_attr__('Αναζήτηση προϊόντων...', 'walkbyme') . '" 
               autocomplete="off" />
        <input type="submit" value="'. esc_attr__('Αναζήτηση', 'walkbyme') .'" />
        <input type="hidden" name="post_type" value="product" />
    </form>';
    return $form;
}
add_filter('get_product_search_form', 'customize_product_search_form');

// Enhanced search with better performance and security
function extend_product_search($search, $query) {
    global $wpdb;

    if (is_admin() || !$query->is_main_query() || !$query->is_search()) {
        return $search;
    }
    
    if (!isset($_GET['post_type']) || $_GET['post_type'] !== 'product') {
        return $search;
    }

    $search_term = $query->get('s');
    if (empty($search_term)) {
        return $search;
    }

    // Sanitize and prepare search terms
    $search_terms = array_filter(array_map('trim', explode(' ', $search_term)));
    $search_terms = array_slice($search_terms, 0, 5); // Limit to 5 terms for performance
    
    if (empty($search_terms)) {
        return $search;
    }

    $escaped_terms = array_map('esc_sql', $search_terms);
    
    // Build search conditions
    $title_conditions = array();
    $content_conditions = array();
    $tag_conditions = array();
    
    foreach ($escaped_terms as $term) {
        $title_conditions[] = "{$wpdb->posts}.post_title LIKE '%{$term}%'";
        $content_conditions[] = "{$wpdb->posts}.post_content LIKE '%{$term}%'";
        $tag_conditions[] = "{$wpdb->terms}.name LIKE '%{$term}%'";
    }
    
    $tag_search = "SELECT DISTINCT {$wpdb->term_relationships}.object_id 
                   FROM {$wpdb->term_relationships}
                   INNER JOIN {$wpdb->term_taxonomy} ON {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id
                   INNER JOIN {$wpdb->terms} ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id
                   WHERE {$wpdb->term_taxonomy}.taxonomy IN ('product_tag', 'product_cat')
                   AND (" . implode(' OR ', $tag_conditions) . ")";

    $search = " AND (
        (" . implode(' OR ', $title_conditions) . ") OR
        (" . implode(' OR ', $content_conditions) . ") OR
        {$wpdb->posts}.ID IN ({$tag_search})
    )";

    return $search;
}
add_filter('posts_search', 'extend_product_search', 10, 2);

/**
 * ENHANCED PRODUCT IMAGES
 */

// Remove default product thumbnail
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);

// Enhanced product thumbnail with better performance
add_action('woocommerce_before_shop_loop_item_title', 'custom_woocommerce_template_loop_product_thumbnail', 11);
function custom_woocommerce_template_loop_product_thumbnail() {
    global $product;

    if (!$product) return;

    // Λήψη της κύριας εικόνας
    $main_image_id = $product->get_image_id();
    $main_image_url = wp_get_attachment_image_url($main_image_id, 'woocommerce_thumbnail');

    // Λήψη της δεύτερης εικόνας
    $custom_image_id = get_post_meta($product->get_id(), '_custom_product_image_id', true);
    $custom_image_url = wp_get_attachment_image_url($custom_image_id, 'woocommerce_thumbnail');

    echo '<a href="' . get_permalink($product->get_id()) . '" class="product-image-link">';
    
    if ($custom_image_url) {
        echo '<div class="product-image-wrapper">';
        echo '<img src="' . esc_url($main_image_url) . '" alt="' . esc_attr($product->get_name()) . '" class="main-image" loading="lazy" />';
        echo '<img src="' . esc_url($custom_image_url) . '" alt="' . esc_attr($product->get_name()) . '" class="hover-image" loading="lazy" />';
        echo '</div>';
    } else {
        echo '<img src="' . esc_url($main_image_url) . '" alt="' . esc_attr($product->get_name()) . '" class="main-image" loading="lazy" />';
    }
    
    echo '</a>';
}

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
            $attr = array('loading' => 'lazy');
            $thumb = get_the_post_thumbnail( get_the_ID(), 'woocommerce_thumbnail', $attr );
            echo $thumb;
            echo '</a>';
            echo '</figure>';
            echo '<h3><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
            echo '</div>';
        endwhile;
        wp_reset_postdata();
    endif;
}

/**
 * PERFORMANCE OPTIMIZATIONS
 */

// Clear cache when products are updated
function clear_walkbyme_product_cache($post_id) {
    if (get_post_type($post_id) === 'product') {
        wp_cache_delete('featured_products_12', 'walkbyme_products');
        wp_cache_flush_group('walkbyme_discounts');
    }
}
add_action('save_post_product', 'clear_walkbyme_product_cache');
add_action('woocommerce_product_set_visibility', 'clear_walkbyme_product_cache');