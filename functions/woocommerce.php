<?php 
/**
 * WooCommerce Theme Support & Configuration
 * Optimized for performance, security, and modern standards
 */

if ( ! class_exists( 'WooCommerce' ) ) {
    return;
}

// 1. Enhanced WooCommerce Support
function walkbyme_woocommerce_support() {
    add_theme_support( 'woocommerce', array(
        'thumbnail_image_width' => 300,
        'single_image_width'    => 600,
        'product_grid'          => array(
            'default_rows'    => 3,
            'min_rows'        => 2,
            'max_rows'        => 8,
            'default_columns' => 4,
            'min_columns'     => 2,
            'max_columns'     => 5,
        ),
    ) );
    
    // Gallery features
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'walkbyme_woocommerce_support' );

// 2. Mobile Gallery Modifications
function modify_woo_gallery_on_mobile() {
    if ( wp_is_mobile() ) {
        remove_theme_support( 'wc-product-gallery-zoom' ); // Zoom is annoying on touch
        
        // CSS fix for mobile gallery layout
        add_action( 'wp_footer', function() {
            if ( is_product() ) {
                echo '<style>@media (max-width: 480px) { .woocommerce-product-gallery__wrapper { display: flex; flex-direction: row; overflow-x: auto; scroll-snap-type: x mandatory; } .woocommerce-product-gallery__image { flex: 0 0 100%; scroll-snap-align: center; } }</style>';
            }
        });
    }
}
add_action( 'after_setup_theme', 'modify_woo_gallery_on_mobile', 11 );

/**
 * PRICING & DISCOUNT FUNCTIONS
 */

// Calculate discount percentage with caching
if (!function_exists('calculate_discount_percentage')) {
    function calculate_discount_percentage($regular_price, $sale_price) {
        if (empty($regular_price) || empty($sale_price) || $sale_price >= $regular_price) {
            return 0;
        }
        
        $cache_key = 'discount_' . md5($regular_price . '_' . $sale_price);
        $discount = wp_cache_get($cache_key, 'walkbyme_discounts');
        
        if (false === $discount) {
            $discount = round((($regular_price - $sale_price) / $regular_price) * 100);
            wp_cache_set($cache_key, $discount, 'walkbyme_discounts', 3600);
        }
        
        return $discount;
    }
}

// Custom sale badge
function custom_sale_badge() {
    global $product;
    
    if ( ! $product || ! $product->is_on_sale() ) return;
    
    $regular_price = $product->get_regular_price();
    $sale_price    = $product->get_sale_price();
    
    // Fallback for variable products
    if ($product->is_type('variable') && empty($regular_price)) {
         $percentage = 0; // Logic for variable products is complex, keeping simple for now
    } else {
        $percentage = calculate_discount_percentage($regular_price, $sale_price);
    }
    
    if ($percentage > 0) {
        echo '<span class="custom-sale-badge">-' . esc_html($percentage) . '%</span>';
    } else {
        echo '<span class="custom-sale-badge">' . esc_html__('Sale!', 'walkbyme') . '</span>';
    }
}

/**
 * WOOCOMMERCE UI CUSTOMIZATIONS
 */

// Clean up Loop Elements
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
add_filter( 'woocommerce_sale_flash', '__return_null' );

// Add custom badges
add_action( 'woocommerce_before_shop_loop_item_title', 'custom_sale_badge', 5 );
add_action( 'woocommerce_before_single_product_summary', 'custom_sale_badge', 5 );

// Remove default wrappers
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

// Remove Add to Cart buttons from loop
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

// Custom Add to Cart Text
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

// Variation Dropdown
function customize_variation_dropdown($args) {
    $args['class'] = 'custom-select';
    $args['show_option_none'] = __('Επιλέξτε μια επιλογή', 'walkbyme');
    return $args;
}
add_filter('woocommerce_dropdown_variation_attribute_options_args', 'customize_variation_dropdown', 10, 1);

/**
 * SHIPPING OPTIMIZATIONS
 */
function hide_shipping_when_free_is_available( $rates, $package ) {
    $new_rates = array();
    $has_free = false;
    $local_pickup = null;
    
    foreach ( $rates as $rate_id => $rate ) {
        if ( 'free_shipping' === $rate->method_id ) {
            $has_free = true;
            $new_rates[ $rate_id ] = $rate;
        }
        if ( 'local_pickup' === $rate->method_id ) {
            $local_pickup = $rate;
        }
    }
    
    if ( $has_free ) {
        if ($local_pickup) {
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
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );

function custom_woocommerce_pagination() {
    global $wp_query;
    
    if ( $wp_query->max_num_pages <= 1 ) return;
    
    $current = max( 1, get_query_var( 'paged' ) );
    
    $pages = paginate_links( array(
        'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
        'format'    => '?paged=%#%',
        'current'   => $current,
        'total'     => $wp_query->max_num_pages,
        'prev_text' => '&larr; ' . __('Προηγούμενη', 'walkbyme'),
        'next_text' => __('Επόμενη', 'walkbyme') . ' &rarr;',
        'type'      => 'array',
        'mid_size'  => 2,
        'end_size'  => 1,
    ) );
    
    if ( is_array( $pages ) ) {
        echo '<nav class="woocommerce-pagination"><ul class="page-numbers">';
        foreach ( $pages as $page ) {
            echo '<li>' . $page . '</li>';
        }
        echo '</ul></nav>';
    }
}
add_action( 'woocommerce_after_shop_loop', 'custom_woocommerce_pagination', 10 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );

/**
 * ENHANCED SEARCH (SECURE)
 */
function customize_product_search_form($form) {
    return '<form role="search" method="get" class="woocommerce-product-search" action="' . esc_url(home_url('/')) . '">
        <label class="screen-reader-text" for="wc-search-field">' . __('Αναζήτηση για:', 'walkbyme') . '</label>
        <input type="search" id="wc-search-field" name="s" value="' . get_search_query() . '" placeholder="' . esc_attr__('Αναζήτηση προϊόντων...', 'walkbyme') . '" autocomplete="off" />
        <input type="hidden" name="post_type" value="product" />
        <button type="submit" aria-label="' . esc_attr__('Αναζήτηση', 'walkbyme') . '"><i class="fas fa-search"></i></button>
    </form>';
}
add_filter('get_product_search_form', 'customize_product_search_form');

// Secure Search Filter using $wpdb->prepare
function extend_product_search( $search, $query ) {
    global $wpdb;

    if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
        return $search;
    }
    
    if ( ! isset( $_GET['post_type'] ) || $_GET['post_type'] !== 'product' ) {
        return $search;
    }

    $search_term = $query->get( 's' );
    if ( empty( $search_term ) ) return $search;

    $terms = array_filter( explode( ' ', $search_term ) );
    if ( empty( $terms ) ) return $search;

    // Build the query securely
    $search = '';
    foreach ( $terms as $term ) {
        $like = '%' . $wpdb->esc_like( $term ) . '%';
        
        $search .= $wpdb->prepare( " AND (
            ($wpdb->posts.post_title LIKE %s) OR 
            ($wpdb->posts.post_content LIKE %s) OR 
            ($wpdb->posts.ID IN (
                SELECT object_id FROM $wpdb->term_relationships 
                LEFT JOIN $wpdb->term_taxonomy ON $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id 
                LEFT JOIN $wpdb->terms ON $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id 
                WHERE $wpdb->terms.name LIKE %s
            ))
        )", $like, $like, $like );
    }

    return $search;
}
add_filter( 'posts_search', 'extend_product_search', 10, 2 );

/**
 * ENHANCED PRODUCT IMAGES
 */
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);

add_action('woocommerce_before_shop_loop_item_title', 'custom_woocommerce_template_loop_product_thumbnail', 11);
function custom_woocommerce_template_loop_product_thumbnail() {
    global $product;

    if (!$product) return;

    $main_image_id = $product->get_image_id();
    
    // Safety check if no image exists
    if (!$main_image_id) {
        echo '<a href="' . esc_url(get_permalink()) . '" class="product-image-link">';
        echo '<img src="' . esc_url(wc_placeholder_img_src()) . '" alt="Placeholder" class="main-image" />';
        echo '</a>';
        return;
    }

    $main_image_url = wp_get_attachment_image_url($main_image_id, 'woocommerce_thumbnail');
    
    // Check for custom secondary image
    $custom_image_id = get_post_meta($product->get_id(), '_custom_product_image_id', true);
    $custom_image_url = $custom_image_id ? wp_get_attachment_image_url($custom_image_id, 'woocommerce_thumbnail') : false;

    echo '<a href="' . esc_url(get_permalink($product->get_id())) . '" class="product-image-link">';
    
    if ($custom_image_url) {
        echo '<div class="product-image-wrapper">';
        echo '<img src="' . esc_url($main_image_url) . '" alt="' . esc_attr($product->get_name()) . '" class="main-image" loading="lazy" width="300" height="300" />';
        echo '<img src="' . esc_url($custom_image_url) . '" alt="' . esc_attr($product->get_name()) . '" class="hover-image" loading="lazy" width="300" height="300" />';
        echo '</div>';
    } else {
        echo '<img src="' . esc_url($main_image_url) . '" alt="' . esc_attr($product->get_name()) . '" class="main-image" loading="lazy" width="300" height="300" />';
    }
    
    echo '</a>';
}

// Function to display featured products on Home
function display_featured_products() {
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 12,
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_visibility',
                'field'    => 'name',
                'terms'    => 'featured',
                'operator' => 'IN',
            ),
        ),
    );

    $wc_query = new WP_Query( $args );

    if ( $wc_query->have_posts() ) :
        // Using output buffering to ensure HTML validity if echoed directly
        while ( $wc_query->have_posts() ) : $wc_query->the_post();
            ?>
            <div class="product-carousel-item">
                <figure>
                    <?php 
                    // Use the custom thumbnail function we defined above to keep consistency (hover effects etc)
                    custom_woocommerce_template_loop_product_thumbnail(); 
                    ?>
                </figure>
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                
                <?php 
                // Show price
                woocommerce_template_loop_price(); 
                ?>
            </div>
            <?php
        endwhile;
        wp_reset_postdata();
    endif;
}

/**
 * CACHE CLEARING
 */
function clear_walkbyme_product_cache($post_id) {
    if (get_post_type($post_id) === 'product') {
        wp_cache_delete('featured_products_12', 'walkbyme_products');
        // Clear specific cache keys if needed
    }
}
add_action('save_post_product', 'clear_walkbyme_product_cache');
add_action('woocommerce_product_set_visibility', 'clear_walkbyme_product_cache');