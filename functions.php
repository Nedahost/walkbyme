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
    // wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.6.2.js', array(), null, true);
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


function customize_product_search_form($form) {
    $form = '<form role="search" method="get" id="searchform" action="' . home_url('/') . '" class="woocommerce-product-search">
        <label class="screen-reader-text" for="s">' . __('Search for:', 'woocommerce') . '</label>
        <input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="' . __('Search products', 'woocommerce') . '" />
        <input type="submit" id="searchsubmit" value="'. esc_attr__('Search', 'woocommerce') .'" />
        <input type="hidden" name="post_type" value="product" />
    </form>';

    return $form;
}
add_filter('get_product_search_form', 'customize_product_search_form');










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




//XML FEED for Article and Categories

function custom_sitemap_articles() {
    if (isset($_GET['custom-sitemap']) && $_GET['custom-sitemap'] === 'generate-articles') {
        // Create a custom sitemap for WordPress articles and categories
        header('Content-Type: text/xml; charset=utf-8');
        
        // Set the file path for the XML file
        $file_path = ABSPATH . '/custom-sitemap-articles.xml';

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

            // Retrieve all categories
            $categories = get_categories(array(
                'taxonomy' => 'category',
                'hide_empty' => false,
            ));

            // Add categories to the sitemap
            foreach ($categories as $category) {
                $category_url = get_term_link($category);
                echo '<url>';
                echo '<loc>' . esc_url($category_url) . '</loc>';
                echo '<changefreq>weekly</changefreq>';
                echo '<priority>0.80</priority>'; // Adjust priority if needed
                echo '</url>';
            }

            // Retrieve all articles
            $args = array(
                'post_type' => 'post',
                'posts_per_page' => -1,
            );

            $articles = new WP_Query($args);
            while ($articles->have_posts()) {
                $articles->the_post();
                $article_url = get_permalink();
                echo '<url>';
                echo '<loc>' . esc_url($article_url) . '</loc>';
                echo '<lastmod>' . gmdate('c', strtotime(get_the_modified_date('Y-m-d H:i:s'))) . '</lastmod>'; // Include the last modification date if desired.
                echo '<priority>0.80</priority>';
                echo '</url>';
            }

            wp_reset_postdata();

            echo '</urlset>';

            // End buffering and write the contents to the file
            $xml_content = ob_get_clean();
            fwrite($file, $xml_content);

            // Set file permissions
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

add_action('init', 'custom_sitemap_articles');

add_action('save_post', 'update_custom_sitemap_articles');

function update_custom_sitemap_articles($post_id) {
    $post_type = get_post_type($post_id);
    if (in_array($post_type, array('post', 'category'))) {
        custom_sitemap_articles();
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


add_action('wp_footer', 'add_product_json_ld');
function add_product_json_ld() {
    if (is_product()) {
        global $product;

        $name_product= $product->get_name();
        $description_product = $product->get_description(); 
        $url_image = wp_get_attachment_image_src($product->get_image_id())[0];
        $price =  $product->get_price();
        $sku_product = $product->get_sku();
        $availability=  $product->is_in_stock() ? 'http://schema.org/InStock' : 'http://schema.org/OutOfStock';
        $size_product = $product->get_attribute('size');
        $url_products = get_permalink($product->get_id());
        $valid_until = date('c', strtotime('+1 week')); 
        $average_rating = $product->get_average_rating();
        $merchant_return_policy_url = 'https://www.walkbyme.gr/%cf%8c%cf%81%ce%bf%ce%b9-%cf%87%cf%81%ce%ae%cf%83%ce%b7%cf%82/';
        $return_policy_category = "https://www.walkbyme.gr/%cf%8c%cf%81%ce%bf%ce%b9-%cf%87%cf%81%ce%ae%cf%83%ce%b7%cf%82/";

        
     
        // Προεπιλεγμένη βαθμολογία στο 0, αν δεν υπάρχουν κριτικές
        $default_rating = 0;

        // Προεπιλεγμένος αριθμός κριτικών στο 0, αν δεν υπάρχουν κριτικές
        $default_review_count = 0;

        // Προεπιλεγμένο όνομα συγγραφέα κριτικής
        $default_author_name = "Walk by me";

        // Προεπιλεγμένο αντικείμενο "review" (αν είναι προαιρετικό)
        $reviews = [];

        if ($product->get_review_count() > 0) {
            $reviews = array_fill(0, $product->get_review_count(), [
                "@type" => "Review",
                "reviewRating" => [
                    "@type" => "Rating",
                    "ratingValue" => $average_rating,
                ],
                "author" => [
                    "@type" => "Person",
                    "name" => $default_author_name,
                ],
            ]);
        }


        $shipping_details = [
            "@type" => "OfferShippingDetails",
            "shippingRate" => [
                "@type" => "MonetaryAmount",
                "currency" => "EUR",
                "value" => "3.00", // Κόστος μεταφορικών
            ],
            "deliveryTime" => [
                "@type" => "ShippingDeliveryTime",
                "handlingTime"=> [
                    "@type" => "QuantitativeValue",
                    "minValue"=> 0,
                    "maxValue"=> 1,
                    "unitCode"=> "DAY"
                ],
                  "transitTime"=> [
                    "@type"=> "QuantitativeValue",
                    "minValue"=> 1,
                    "maxValue"=> 5,
                    "unitCode"=> "DAY"
                  ],      
            ],
            "shippingDestination" => [
                "@type" => "DefinedRegion",
                "addressCountry" => "GR",
                // Εάν χρειαστεί, προσθέστε περισσότερες πληροφορίες για τον προορισμό σας
            ],
            "transitTimeLabel" => "Standard Shipping",
        ];

        $product_data = [
            '@context' => 'http://schema.org',
            '@type' => 'Product',
            'name' => $name_product,
            'description' => $description_product,
            'image' => $url_image ? [
                '@type' => 'ImageObject',
                'url' => $url_image
            ] : null,
            'sku' => $sku_product,
            'size' => $size_product ,
            "offers" => [
                "@type" => "Offer",
                "url" => $url_products,
                "priceCurrency" => "EUR",
                "price" => $price, 
                "availability" => $availability,
                "seller" => [
                    "@type" => "Organization",
                    "name" => "Executive Objects",
                ],
                "priceValidUntil" => $valid_until,
                "eligibleRegion"  => "GR",
                "hasMerchantReturnPolicy" => [
                    "@type" => "MerchantReturnPolicy",
                    "applicableCountry"=> "GR",
                    "returnPolicyCategory" => "https://schema.org/MerchantReturnFiniteReturnWindow",
                    "merchantReturnDays"=> 14,
                    "returnMethod"=> "https://schema.org/ReturnByMail",
                    "returnFees"=> "https://schema.org/FreeReturn",
                    "url" => $merchant_return_policy_url,
                    "description" => "Το προϊόν πρέπει να βρίσκεται σε άριστη κατάσταση, να μην έχει χρησιμοποιηθεί, και η συσκευασία του να είναι άθικτη (κλειστή). Τα έξοδα επιστροφής θα βαρύνουν τον πελάτη."
                ], 
                "shippingDetails" => $shipping_details,
            ],

            "aggregateRating" => ($product->get_review_count() > 0 && $average_rating > 0) ? [
                "@type" => "AggregateRating",
                "ratingValue" => $average_rating,
                "reviewCount" => $product->get_review_count()
            ] : null,            
            "review" => $reviews,
        ];

        echo '<script type="application/ld+json">' . json_encode($product_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
    }
}

add_action('wp_head', 'add_homepage_schema');
function add_homepage_schema() {
    if (is_home()) {

        $name = get_bloginfo('name');
        $home_description = 'Το πάθος μας για χειροποίητα κοσμήματα από ασήμι 925 και χρυσό! Η κατασκευή χειροποίητων ασημένιων και χρυσών κοσμημάτων είναι το πάθος μας.';
        $url_home =  get_home_url();


        $homepage_data = [
            '@context' => 'http://schema.org',
            '@type' => 'WebPage',
            'name' => $name,
            'description' => $home_description,
            'url' => $url_home,
        ];

        echo '<script type="application/ld+json">' . json_encode($homepage_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
    }
}



add_action('wp_head', 'add_organization_schema_markup');

function add_organization_schema_markup() {
    $name = get_bloginfo('name');
    $url_home =  get_home_url();
    $logo_page = 'https://www.walkbyme.gr/wp-content/uploads/2024/01/logo_walkbyme.png';

    

    $organization_data = array(
        '@context' => 'http://schema.org',
        '@type' => 'Organization',
        'name' => $name,
        'url' => $url_home,
        'logo' => array(
            '@type' => 'ImageObject',
            'inLanguage' => 'el',
            'url' => 'https://www.walkbyme.gr/wp-content/uploads/2024/01/walkbyme_schema_logo.jpg',
            'contentUrl' => 'https://www.walkbyme.gr/wp-content/uploads/2024/01/walkbyme_schema_logo.jpg',
            'width' => 300,
            'height' => 50,
            'caption' => 'Walk by me'
        ),
        'image' => array(
            '@type' => 'ImageObject',
            'inLanguage' => 'el',
            'url' => 'https://www.walkbyme.gr/wp-content/uploads/2024/01/walkbyme_schema_logo.jpg',
            'contentUrl' => 'https://www.walkbyme.gr/wp-content/uploads/2024/01/walkbyme_schema_logo.jpg',
            'width' => 300,
            'height' => 50,
            'caption' => 'Walk by me'
        ),
        'address' => array(
            '@type' => 'PostalAddress',
            'streetAddress' => 'Ελασσώνος 16',
            'addressLocality' => 'Περιστέρι',
            'addressRegion' => 'Περιστέρι',
            'postalCode' => '121 37',
            'addressCountry' => 'Ελλάδα', 
        ),
        'telephone' => '+30-697-5686473',
        'email' => 'info@walkbyme.gr'
    );

    echo '<script type="application/ld+json">' . json_encode($organization_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
}



function add_breadcrumbs_schema_markup() {
    $breadcrumbs_data = array(
        '@context' => 'http://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => array(),
    );

    // Add Home breadcrumb
    $breadcrumbs_data['itemListElement'][] = array(
        '@type' => 'ListItem',
        'position' => 1,
        'item' => array(
            '@id' => get_home_url(),
            'name' => 'Home',
        ),
    );

    $product_cat = null; // Προσθέστε αυτή τη γραμμή εδώ

    // Check if it's a product category page
    if (is_product_category() ) {
        $product_cat = get_queried_object();
        $ancestors = get_ancestors($product_cat->term_id, 'product_cat');
        $ancestors = array_reverse($ancestors);

        // Add category breadcrumbs
        foreach ($ancestors as $ancestor_id) {
            $ancestor = get_term($ancestor_id, 'product_cat');
            $link = get_term_link($ancestor);

            $breadcrumbs_data['itemListElement'][] = array(
                '@type' => 'ListItem',
                'position' => count($breadcrumbs_data['itemListElement']) + 1,
                'item' => array(
                    '@id' => $link,
                    'name' => $ancestor->name,
                ),
            );
        }
    }
    
     // Add current category breadcrumb
     if ($product_cat ) { // Ελέγξτε αν υπάρχει προϊόν κατηγορίας πριν προσθέσετε το breadcrumb
        $permlink = get_term_link($product_cat);
        $title = $product_cat->name;
        $breadcrumbs_data['itemListElement'][] = array(
            '@type' => 'ListItem',
            'position' => count($breadcrumbs_data['itemListElement']) + 1,
            'item' => array(
                '@id' => $permlink,
                'name' => $title,
            ),
        );
    }

    if(is_product()){

    global $product;

    // Get product categories
    $product_id = method_exists($product, 'get_id') ? $product->get_id() : $product->id;
    $product_cats = get_the_terms($product_id, 'product_cat');

    // Add product category breadcrumbs
    if ($product_cats && !is_wp_error($product_cats)) {
        foreach ($product_cats as $count => $cat) {
            $breadcrumbs_data['itemListElement'][] = array(
                '@type' => 'ListItem',
                'position' => count($breadcrumbs_data['itemListElement']) + 1,
                'name' => $cat->name,
                'item' => get_term_link($cat),
            );
        }
    }

    // Add current page breadcrumb
    $permlink = get_permalink();
    $title = get_the_title();
    $breadcrumbs_data['itemListElement'][] = array(
        '@type' => 'ListItem',
        'position' => count($breadcrumbs_data['itemListElement']) + 1,
        'item' => array(
            '@id' => $permlink,
            'name' => $title,
        ),
    );

}




    echo '<script type="application/ld+json">' . json_encode($breadcrumbs_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
}

add_action('wp_head', 'add_breadcrumbs_schema_markup');






// Προσθήκη Ρυθμίσεων στο WooCommerce
add_filter('woocommerce_get_sections_products', 'add_custom_filter_settings_section');

function add_custom_filter_settings_section($sections) {
    $sections['custom_filters'] = __('Custom Filters', 'woocommerce');
    return $sections;
}

add_filter('woocommerce_get_settings_products', 'add_custom_filter_settings', 10, 2);

function add_custom_filter_settings($settings, $current_section) {
    if ($current_section == 'custom_filters') {
        $attribute_taxonomies = wc_get_attribute_taxonomies();
        $options = array();
        
        foreach ($attribute_taxonomies as $tax) {
            $taxonomy = wc_attribute_taxonomy_name($tax->attribute_name);
            $options[$taxonomy] = $tax->attribute_label;
        }
        
        $settings_custom_filters = array();
        $settings_custom_filters[] = array('title' => __('Custom Filters', 'woocommerce'), 'type' => 'title', 'desc' => '', 'id' => 'custom_filters_options');
        
        $settings_custom_filters[] = array(
            'title'    => __('Select Filters', 'woocommerce'),
            'desc'     => __('Select which filters to display on the shop page.', 'woocommerce'),
            'id'       => 'custom_filters_attributes',
            'default'  => '',
            'type'     => 'multiselect',
            'class'    => 'wc-enhanced-select',
            'options'  => $options,
            'autoload' => false,
        );
        
        $settings_custom_filters[] = array('type' => 'sectionend', 'id' => 'custom_filters_options');
        
        return $settings_custom_filters;
    } else {
        return $settings;
    }
} 

// Προσαρμογή της Λειτουργίας Φίλτρων
add_action('woocommerce_before_shop_loop', 'custom_product_filters', 20);

function custom_product_filters() {
    if (!is_shop() && !is_product_taxonomy()) {
        return;
    }

    $attribute_taxonomies = wc_get_attribute_taxonomies();
    $current_category = is_shop() ? '' : get_queried_object()->slug;
    $enabled_filters = get_option('custom_filters_attributes', array());

    if (!empty($attribute_taxonomies)) {
        $category_attributes = array();

        foreach ($attribute_taxonomies as $tax) {
            $taxonomy = wc_attribute_taxonomy_name($tax->attribute_name);
            if (!in_array($taxonomy, $enabled_filters)) {
                continue;
            }
            $terms = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => true));

            if (!empty($terms)) {
                $related_terms = array();

                $selected_filters = array();
                foreach ($attribute_taxonomies as $selected_tax) {
                    $selected_taxonomy = wc_attribute_taxonomy_name($selected_tax->attribute_name);
                    if (isset($_GET[$selected_taxonomy])) {
                        $selected_filters[$selected_taxonomy] = sanitize_text_field($_GET[$selected_taxonomy]);
                    }
                }

                foreach ($terms as $term) {
                    $query_args = array(
                        'tax_query' => array(
                            array(
                                'taxonomy' => $taxonomy,
                                'field' => 'slug',
                                'terms' => sanitize_text_field($term->slug),
                            ),
                        ),
                        'limit' => 1,
                    );

                    if (!empty($current_category)) {
                        $query_args['tax_query'][] = array(
                            'taxonomy' => 'product_cat',
                            'field' => 'slug',
                            'terms' => sanitize_text_field($current_category),
                        );
                    }

                    foreach ($selected_filters as $selected_taxonomy => $selected_term) {
                        if ($selected_taxonomy !== $taxonomy) {
                            $query_args['tax_query'][] = array(
                                'taxonomy' => $selected_taxonomy,
                                'field' => 'slug',
                                'terms' => sanitize_text_field($selected_term),
                            );
                        }
                    }

                    // Cache query results to improve performance
                    $cache_key = 'filter_products_' . md5(json_encode($query_args));
                    $products = wp_cache_get($cache_key, 'woocommerce');

                    if ($products === false) {
                        $products = wc_get_products($query_args);
                        wp_cache_set($cache_key, $products, 'woocommerce', 3600); // Cache for 1 hour
                    }

                    if (!empty($products)) {
                        $related_terms[] = $term;
                    }
                }

                if (!empty($related_terms)) {
                    $category_attributes[$taxonomy] = array(
                        'label' => $tax->attribute_label,
                        'terms' => $related_terms,
                    );
                }
            }
        }

        if (!empty($category_attributes)) {
            echo '<div class="product-filters">';
            echo '<span>Φίλτρα:</span>';
        
            foreach ($category_attributes as $taxonomy => $data) {
                $selected_term = isset($_GET[$taxonomy]) ? sanitize_text_field($_GET[$taxonomy]) : '';
        
                echo '<div class="filter-group">';
                echo '<select name="' . esc_attr($taxonomy) . '-filter" class="filter-select" data-taxonomy="' . esc_attr($taxonomy) . '">';
                echo '<option value="">' . esc_html($data['label']) . '</option>';
        
                foreach ($data['terms'] as $term) {
                    $filter_url = add_query_arg($taxonomy, $term->slug, get_pagenum_link());
                    $selected = $selected_term === $term->slug ? 'selected' : '';
                    echo '<option value="' . esc_attr($term->slug) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
                }
        
                echo '</select>';
        
                if ($selected_term) {
                    echo '<a class="clear-filter" href="#" data-taxonomy="' . esc_attr($taxonomy) . '"></a>';
                }
        
                echo '</div>';
            }
        
            echo '</div>';
            ?>
            <style>
            .product-filters {
                margin-bottom: 20px;
                display: flex;
                flex-wrap: wrap;
                align-items: center;
            }
            .product-filters span {
                margin-right: 10px;
                font-weight: bold;
            }
            .filter-group {
                display: flex;
                align-items: center;
                margin-right: 20px;
                margin-bottom: 10px;
                position: relative;
            }
            .filter-group select {
                padding: 8px 30px 8px 10px;
                border: 1px solid #ddd;
                border-radius: 4px;
                background: #fff;
                font-size: 14px;
                appearance: none;
                -webkit-appearance: none;
                -moz-appearance: none;
                outline: none;
                cursor: pointer;
                box-shadow: none;
                width: auto;
                min-width: 150px;
            }
            .filter-group select:focus {
                border-color: #999;
            }
            .filter-group::after {
                content: "";
                width: 12px;
                height: 12px;
                background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%23333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>') no-repeat center;
                position:absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                pointer-events: none;
                transition: all 0.3s ease;
            }
            .filter-group.selected::after {
                display: none;
            }
            .filter-group .clear-filter {
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                width: 12px;
                height: 12px;
                background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%23333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>') no-repeat center;
                cursor: pointer;
                transition: all 0.3s ease;
                display: none;
            }
            .filter-group.selected .clear-filter {
                display: block;
            }
        
            /* Ενημερωμένα στυλ για κινητά */
            @media (max-width: 767px) {
                .product-filters {
                    flex-direction: column;
                    align-items: stretch;
                }
                .product-filters span {
                    margin-bottom: 10px;
                }
                .filter-group {
                    margin-right: 0;
                    margin-bottom: 10px;
                    position: relative;
                    width: 100%;
                }
                .filter-group select {
                    width: 100%;
                }
                .filter-group .clear-filter {
                    right: 20px;
                }
            }
            </style>
            <script>
            jQuery(function($) {
                // Toggle selected class on filter change
                $('.filter-select').on('change', function() {
                    if ($(this).val() !== '') {
                        $(this).parent('.filter-group').addClass('selected');
                    } else {
                        $(this).parent('.filter-group').removeClass('selected');
                    }
                    updateFilters();
                });
        
                // Clear individual filter
                $('.clear-filter').on('click', function(e) {
                    e.preventDefault();
                    var taxonomy = $(this).data('taxonomy');
                    $('.filter-select[data-taxonomy="' + taxonomy + '"]').val('').trigger('change');
                    updateFilters();
                });
        
                // Initialize selected class on page load
                $('.filter-select').each(function() {
                    if ($(this).val() !== '') {
                        $(this).parent('.filter-group').addClass('selected');
                    }
                });
        
                // Update filters
                function updateFilters() {
                    var filters = {};
                    $('.filter-select').each(function() {
                        var taxonomy = $(this).data('taxonomy');
                        var term = $(this).val();
                        if (term !== '') {
                            filters[taxonomy] = term;
                        }
                    });
                    var url = new URL(window.location.href);
                    url.search = new URLSearchParams(filters).toString();
                    window.location.href = url.href;
                }
        
                // Collapse filters for mobile
                function setupCollapsibleFilters() {
                    if ($(window).width() < 768) {
                        if (!$('.filter-toggle').length) {
                            $('<button class="filter-toggle">Φίλτρα</button>').insertBefore('.product-filters');
                            $('.product-filters').hide();
                        }
                    } else {
                        $('.filter-toggle').remove();
                        $('.product-filters').show();
                    }
                }
        
                $(window).on('resize', setupCollapsibleFilters);
                setupCollapsibleFilters();
        
                $(document).on('click', '.filter-toggle', function() {
                    $('.product-filters').slideToggle();
                });
            });
            </script>
            <?php
        }
    }
}

add_filter('woocommerce_product_query', 'filter_products_by_attributes');

function filter_products_by_attributes($query) {
    if (!is_admin() && $query->is_main_query() && (is_shop() || is_product_taxonomy())) {
        $attribute_taxonomies = wc_get_attribute_taxonomies();
        $tax_query = $query->get('tax_query') ? $query->get('tax_query') : array();

        foreach ($attribute_taxonomies as $tax) {
            $taxonomy = wc_attribute_taxonomy_name($tax->attribute_name);
            if (isset($_GET[$taxonomy])) {
                $tax_query[] = array(
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => sanitize_text_field($_GET[$taxonomy])
                );
            }
        }

        if (!empty($tax_query)) {
            $query->set('tax_query', $tax_query);
        }
    }
}
