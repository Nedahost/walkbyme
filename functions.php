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
    wp_enqueue_script('JQuery', 'https://code.jquery.com/jquery-2.2.0.min.js', array(), null, true);
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

add_action( 'after_setup_theme', 'walkbyme_woocommerce_support' );

function walkbyme_woocommerce_support() {
	add_theme_support( 'woocommerce', array(
		//'thumbnail_image_width' => 150,
		'single_image_width' => 600,
		'gallery_thumbnail_image_width' => 600,
        'product_grid'          => array(
            'default_rows'    => 3,
            'min_rows'        => 2,
            'max_rows'        => 8,
            'default_columns' => 4,
            'min_columns'     => 2,
            'max_columns'     => 5,
        ),
	) );
	//add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	//add_theme_support( 'wc-product-gallery-slider' );
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

// add_action( 'woocommerce_archive_description', 'woocommerce_category_image', 2 );
// function woocommerce_category_image() {
//     if ( is_product_category() ){
// 	    global $wp_query;
// 	    $cat = $wp_query->get_queried_object();
// 	    $thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
// 	    $image = wp_get_attachment_url( $thumbnail_id );
// 	    if ( $image ) {
// 		    echo '<img src="' . $image . '" alt="' . $cat->name . '" />';
// 		}
// 	}
// }



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


function calculate_dynamic_discount_percentage($regular_price, $sale_price)
{
    if ($regular_price > 0 && $sale_price > 0) {
        return round((($regular_price - $sale_price) / $regular_price) * 100, 2);
    } else {
        return 0;
    }
}

function display_dynamic_discount_percentage($product)
{
    if ('simple' == $product->product_type) {
        $regular_price = $product->get_regular_price();
        $sales_price = $product->get_sale_price();

        $dynamic_discount_percentage = calculate_dynamic_discount_percentage($regular_price, $sales_price);

        // Ελέγξτε εάν το ποσοστό είναι διάφορο του μηδενός πριν το εμφανίσετε
        if ($dynamic_discount_percentage != 0 && $dynamic_discount_percentage != '') {
            echo 'Ποσοστό Έκπτωσης: - ' . $dynamic_discount_percentage . '%';
        }
    } elseif ('variable' == $product->product_type) {
        $variations = $product->get_available_variations();
        $variation = reset($variations);
        $variation_id = $variation['variation_id'];
        $variable_product = new WC_Product_Variation($variation_id);
        $regular_price = $variable_product->get_regular_price();
        $sales_price = $variable_product->get_sale_price();

        $dynamic_discount_percentage = calculate_dynamic_discount_percentage($regular_price, $sales_price);

        // Ελέγξτε εάν το ποσοστό είναι διάφορο του μηδενός πριν το εμφανίσετε
        if ($dynamic_discount_percentage != 0 && $dynamic_discount_percentage != '') {
            echo 'Ποσοστό Έκπτωσης: - ' . $dynamic_discount_percentage . '%';
        }
    }
}


add_filter('woocommerce_product_is_on_sale', '__return_false');



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

// Πεδία για κατηγορίες προϊόντων WooCommerce
add_action('product_cat_add_form_fields', 'product_category_custom_meta_fields');
add_action('product_cat_edit_form_fields', 'product_category_custom_meta_fields');
add_action('edited_term', 'save_product_category_custom_meta');
add_action('create_term', 'save_product_category_custom_meta');

function product_category_custom_meta_fields($term) {
    $meta_title = get_term_meta($term->term_id, '_product_category_meta_title', true);
    $meta_description = get_term_meta($term->term_id, '_product_category_meta_description', true);
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

function save_product_category_custom_meta($term_id) {
    $meta_title = isset($_POST['meta_title']) ? sanitize_text_field($_POST['meta_title']) : '';
    $meta_description = isset($_POST['meta_description']) ? sanitize_textarea_field($_POST['meta_description']) : '';

    update_term_meta($term_id, '_product_category_meta_title', $meta_title);
    update_term_meta($term_id, '_product_category_meta_description', $meta_description);
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
