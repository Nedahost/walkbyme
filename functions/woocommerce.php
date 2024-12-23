<?php 

// WooCommerce Support
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

add_action( 'after_setup_theme', 'walkbyme_woocommerce_support' );

// Modify gallery on mobile
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


// Remove sale flash
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
add_filter( 'woocommerce_sale_flash', '__return_null' );

// Remove product link wrapper
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );


add_action( 'woocommerce_after_shop_loop_item', 'remove_add_to_cart_buttons', 1 );
function remove_add_to_cart_buttons() {
    remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
}

// Change add to cart text
function woocommerce_custom_single_add_to_cart_text() {
    return __('Αγόρασε το', 'woocommerce');
}
add_filter('woocommerce_product_single_add_to_cart_text', 'woocommerce_custom_single_add_to_cart_text');
add_filter('woocommerce_product_add_to_cart_text', 'woocommerce_custom_single_add_to_cart_text');


// Customize variation dropdown
function customize_variation_dropdown($args)
{
    $args['class'] = 'custom-select';
    return $args;
}
add_filter('woocommerce_dropdown_variation_attribute_options_args', 'customize_variation_dropdown', 10, 1);

// Hide shipping when free shipping is available
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


// Custom pagination
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

// Remove result count
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );


// Product Search Functions
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