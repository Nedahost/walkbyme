<?php

require_once (get_template_directory() . '/inc/details-product.php');


// style load css

function walkbyme_load_css() {
    wp_register_style('mystyle', get_template_directory_uri() . '/assets/public/css/mystyle.css', array(), false, 'all');
    wp_enqueue_style('mystyle');
    wp_register_style( 'load-fa', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
    wp_enqueue_style('load-fa');
//    wp_register_style('responsive', get_template_directory_uri() . '/assets/public/css/responsive.css', array(), false, 'all');
//    wp_enqueue_style('responsive');
   // wp_register_style('fatNav', get_template_directory_uri() . '/assets/public/css/jquery.fatNav.css', array(), false, 'all');
    //wp_enqueue_style('fatNav');
    //wp_enqueue_style('responsive', get_template_directory_uri() . '/assets/public/css/responsive.css', '', '', 'all');
    //wp_enqueue_style('style' , get_stylesheet_uri(), '', '', 'all');    
}

add_action( 'wp_enqueue_scripts', 'walkbyme_load_css' );


//  Javascripts load js

function load_js(){
    
    wp_register_script('JQuery' , 'https://code.jquery.com/jquery-2.2.0.min.js', null, null, true);
    wp_enqueue_script('JQuery');
    wp_register_script('slick' , '//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js?ver=1.5', null, null, true);
    wp_enqueue_script('slick');
    wp_register_script('myjs' , get_template_directory_uri() . '/assets/js/myjs.js' , 'jquery', false, true);
    wp_enqueue_script('myjs');
    
}

add_action( 'wp_enqueue_scripts', 'load_js' );


// add a favicon to your site
function favicon() {
	echo '<link rel="Shortcut Icon" type="image/x-icon" href="'.get_bloginfo('stylesheet_directory').'/assets/images/favicon.jpg" />' . "\n";
}
add_action('wp_head', 'favicon');
add_action('admin_head', 'favicon');


add_action('after_setup_theme', 'walkbyme_setup');
function walkbyme_setup(){
    
    
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
    
    
    $footersidebar1 = array(
	'name'          => 'Footer first',
	'description'   => 'Είναι η περιοχή που εμφανίζεται αριστερά στις κατηγορίες',
	'before_widget' => '<div id="%1$s" class="module_cat %2$s">',
	'after_widget'  => '</div>',
	'before_title'  => '<h3 class="widgettitle">',
	'after_title'   => '</h3>' );


        register_sidebar($footersidebar1);
        
        $footersidebar2 = array(
	'name'          => 'Footer second',
	'description'   => 'Είναι η περιοχή που εμφανίζεται αριστερά στις κατηγορίες',
	'before_widget' => '<div id="%1$s" class="module_cat %2$s">',
	'after_widget'  => '</div>',
	'before_title'  => '<h3 class="widgettitle">',
	'after_title'   => '</h3>' );


        register_sidebar($footersidebar2);
        
        $footersidebar3 = array(
	'name'          => 'Footer three',
	'description'   => 'Είναι η περιοχή που εμφανίζεται αριστερά στις κατηγορίες',
	'before_widget' => '<div id="%1$s" class="module_cat %2$s">',
	'after_widget'  => '</div>',
	'before_title'  => '<h3 class="widgettitle">',
	'after_title'   => '</h3>' );


        register_sidebar($footersidebar3);
        
        $footersidebar4 = array(
	'name'          => 'Footer four',
	'description'   => 'Είναι η περιοχή που εμφανίζεται αριστερά στις κατηγορίες',
	'before_widget' => '<div id="%1$s" class="module_cat %2$s">',
	'after_widget'  => '</div>',
	'before_title'  => '<h3 class="widgettitle">',
	'after_title'   => '</h3>' );


        register_sidebar($footersidebar4);
        
        $footersidebar5 = array(
	'name'          => 'Footer five',
	'description'   => 'Είναι η περιοχή που εμφανίζεται αριστερά στις κατηγορίες',
	'before_widget' => '<div id="%1$s" class="module_cat %2$s">',
	'after_widget'  => '</div>',
	'before_title'  => '<h3 class="widgettitle">',
	'after_title'   => '</h3>' );


        register_sidebar($footersidebar5);
    
    
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
add_action( 'after_setup_theme', 'walkbyme_woocommerce_support' );



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
add_filter( 'wp_title', 'walkbyme_wp_title', 10, 2 );


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
        'prev_next' => TRUE,
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
                if ($current_page != 1 && $current_page == $i) {
                    echo "<li class='active'>$page</li>";
                } else {
                    echo "<li>$page</li>";
                }
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


function add_custom_category_field() {
    ?>
    <div class="form-field">
        <label for="show_on_homepage"><?php _e( 'Εμφάνιση στην αρχική σελίδα', 'walkbyme' ); ?></label>
        <select name="show_on_homepage" id="show_on_homepage">
            <option value="yes"><?php _e( 'Ναι', 'walkbyme' ); ?></option>
            <option value="no"><?php _e( 'Όχι', 'walkbyme' ); ?></option>
        </select>
    </div>


    <div class="form-field">
        <label for="category_priority">Προτεραιότητα Κατηγορίας:</label>
        <input type="number" name="category_priority" id="category_priority" class="regular-text">
        <p class="description">Εισάγετε έναν αριθμό για την προτεραιότητα της κατηγορίας (μεγαλύτερος αριθμός έχει υψηλότερη προτεραιότητα).</p>
    </div>

    <?php
}
add_action( 'product_cat_add_form_fields', 'add_custom_category_field', 10, 2 );

// Αποθηκεύστε την τιμή του προσαρμοσμένου πεδίου για κάθε κατηγορία
function save_custom_category_field( $term_id ) {
    if ( isset( $_POST['show_on_homepage'] ) ) {
        update_term_meta( $term_id, 'show_on_homepage', sanitize_text_field( $_POST['show_on_homepage'] ) );
    }

    if (isset($_POST['category_priority'])) {
        $category_priority = intval($_POST['category_priority']);
        update_term_meta($term_id, 'category_priority', $category_priority);
    }
}
add_action( 'edited_product_cat', 'save_custom_category_field', 10, 2 );
add_action( 'create_product_cat', 'save_custom_category_field', 10, 2 );


// Εμφανίστε το πεδίο στον πίνακα ελέγχου των κατηγοριών product_cat
function display_category_custom_fields( $term ) {
    $show_on_homepage = get_term_meta( $term->term_id, 'show_on_homepage', true );
    $category_priority = get_term_meta( $term->term_id, 'category_priority', true );
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="show_on_homepage"><?php _e( 'Εμφάνιση στην αρχική σελίδα', 'walkbyme' ); ?></label></th>
        <td>
            <select name="show_on_homepage" id="show_on_homepage">
                <option value="yes" <?php selected( $show_on_homepage, 'yes' ); ?>><?php _e( 'Ναι', 'walkbyme' ); ?></option>
                <option value="no" <?php selected( $show_on_homepage, 'no' ); ?>><?php _e( 'Όχι', 'walkbyme' ); ?></option>
            </select>
        </td>
    </tr>

    <tr class="form-field">
        <th scope="row" valign="top"><label for="category_priority"><?php _e( 'Προτεραιότητα Κατηγορίας', 'walkbyme' ); ?></label></th>
        <td>
            <input type="number" name="category_priority" id="category_priority" value="<?php echo esc_attr( $category_priority ); ?>" class="regular-text">
            <p class="description"><?php _e( 'Εισάγετε έναν αριθμό για την προτεραιότητα της κατηγορίας (μεγαλύτερος αριθμός έχει υψηλότερη προτεραιότητα).', 'walkbyme' ); ?></p>
        </td>
    </tr>

    <?php
}
add_action( 'product_cat_edit_form_fields', 'display_category_custom_fields', 10, 2 );





// Προσθέστε το πεδίο μεταφόρτωσης εικόνας στις κατηγορίες προϊόντων
function custom_category_image_field($term) {
    $category_image = get_term_meta($term->term_id, 'category_image', true);
    ?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="category_image">Εικόνα Κατηγορίας</label>
        </th>
        <td>
            <input type="text" name="category_image" id="category_image" class="meta-image" value="<?php echo esc_attr($category_image); ?>">
            <br>
            <button class="button image-upload">Επιλογή Εικόνας</button>
        </td>
    </tr>
    <script>
        jQuery(function ($) {
            // Κώδικας για το πεδίο μεταφόρτωσης εικόνας
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
        });
    </script>
    <?php
}
add_action('product_cat_edit_form_fields', 'custom_category_image_field', 10, 2);
add_action('product_cat_add_form_fields', 'custom_category_image_field', 10, 2);

// Αποθηκεύστε τη μεταδεδομένη εικόνας της κατηγορίας
function save_custom_category_image_field($term_id) {
    if (isset($_POST['category_image'])) {
        update_term_meta($term_id, 'category_image', sanitize_text_field($_POST['category_image']));
    }
}
add_action('edited_product_cat', 'save_custom_category_image_field', 10, 2);
add_action('create_product_cat', 'save_custom_category_image_field', 10, 2);




function custom_sitemap() {
    if (isset($_GET['custom-sitemap']) && $_GET['custom-sitemap'] === 'generate') {
        // Create a custom sitemap for WooCommerce products
        header('Content-Type: text/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

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
            echo '<lastmod>' . get_the_modified_date('c') . '</lastmod>'; // Include the last modification date if desired.
            echo '</url>';
        }

        wp_reset_postdata();

        echo '</urlset>';
        die();
    }
}

add_action('init', 'custom_sitemap');
