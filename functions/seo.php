<?php 
/**
 * Custom SEO Functions
 * Handles Canonicals, Meta Tags, and Robots.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. CANONICAL TAGS OPTIMIZATION
 * Removes default WP canonicals to prevent duplicates and uses our custom logic.
 */
remove_action( 'wp_head', 'rel_canonical' );

function walkbyme_custom_canonical() {
    $canonical_url = '';

    // A. Homepage
    if ( is_home() || is_front_page() ) {
        $canonical_url = home_url('/');
    }
    // B. Categories & Taxonomies (Works for Blog & WooCommerce)
    elseif ( is_category() || is_tag() || is_tax() ) {
        $term = get_queried_object();
        $canonical_url = get_term_link( $term );
        
        // Handle Pagination
        if ( get_query_var('paged') > 1 ) {
            $canonical_url = trailingslashit($canonical_url) . 'page/' . get_query_var('paged') . '/';
        }
    } 
    // C. Single Product or Post
    elseif ( is_singular() ) {
        global $post;
        $canonical_url = get_permalink( $post->ID );
    }
    // D. Static Pages
    elseif ( is_page() ) {
        $canonical_url = get_permalink( get_queried_object_id() );
    }
    // E. Search Results
    elseif ( is_search() ) {
        $canonical_url = get_search_link();
    }

    // Filter to allow other functions to modify it (e.g. for specific attributes)
    $canonical_url = apply_filters( 'walkbyme_canonical_url', $canonical_url );

    if ( $canonical_url ) {
        echo '<link rel="canonical" href="' . esc_url( $canonical_url ) . '" />' . "\n";
    }
}
add_action( 'wp_head', 'walkbyme_custom_canonical', 5 );


/**
 * 2. META ROBOTS (The Modern Way)
 * Uses the WP 5.7+ filter instead of hardcoded strings.
 */
function walkbyme_robots_filter( $robots ) {
    $robots['max-image-preview'] = 'large';
    $robots['max-snippet'] = '-1';
    $robots['max-video-preview'] = '-1';
    $robots['index'] = true;
    $robots['follow'] = true;

    return $robots;
}
add_filter( 'wp_robots', 'walkbyme_robots_filter' );


/**
 * 3. CUSTOM META TITLE & DESCRIPTION OUTPUT
 * Injects the saved custom meta data into the head.
 */

// A. Inject Custom Title
function walkbyme_custom_document_title( $title ) {
    if ( is_category() || is_tax('product_cat') ) {
        $term = get_queried_object();
        $custom_title = get_term_meta( $term->term_id, '_category_meta_title', true );
        
        if ( ! empty( $custom_title ) ) {
            $title['title'] = esc_html( $custom_title );
        }
    }
    return $title;
}
add_filter( 'document_title_parts', 'walkbyme_custom_document_title' );

// B. Inject Custom Description
function walkbyme_custom_meta_description() {
    $description = '';

    // Check if we are on a category or product category page
    if ( is_category() || is_tax('product_cat') ) {
        $term = get_queried_object();
        $custom_desc = get_term_meta( $term->term_id, '_category_meta_description', true );
        
        if ( ! empty( $custom_desc ) ) {
            $description = $custom_desc;
        } else {
            // Fallback to term description
            $description = term_description( $term->term_id );
        }
    }
    // Check if we are on a single post/product
    elseif ( is_singular() ) {
        global $post;
        // You could add a custom field for posts here too, using excerpt as fallback
        $description = has_excerpt() ? get_the_excerpt() : wp_trim_words( $post->post_content, 25 );
    }

    if ( ! empty( $description ) ) {
        // Strip tags and shortcodes
        $description = wp_strip_all_tags( strip_shortcodes( $description ) );
        echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
    }
}
add_action( 'wp_head', 'walkbyme_custom_meta_description', 1 );


/**
 * 4. OPEN GRAPH META TAGS (Basic)
 * Essential for Facebook/WhatsApp sharing.
 */
function walkbyme_open_graph_tags() {
    if ( is_singular() ) {
        global $post;
        echo '<meta property="og:title" content="' . esc_attr( get_the_title() ) . '" />' . "\n";
        echo '<meta property="og:type" content="article" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url( get_permalink() ) . '" />' . "\n";
        
        if ( has_post_thumbnail( $post->ID ) ) {
            $img_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' );
            echo '<meta property="og:image" content="' . esc_url( $img_src[0] ) . '" />' . "\n";
        }
    }
}
add_action( 'wp_head', 'walkbyme_open_graph_tags', 10 );


/**
 * 5. BACKEND META FIELDS (Categories & Product Categories)
 */

function walkbyme_taxonomy_seo_fields( $term ) {
    // Check if we are in add or edit mode
    $term_id = isset($term->term_id) ? $term->term_id : 0;
    
    $meta_title = $term_id ? get_term_meta( $term_id, '_category_meta_title', true ) : '';
    $meta_description = $term_id ? get_term_meta( $term_id, '_category_meta_description', true ) : '';
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="meta_title"><?php _e('SEO Meta Title', 'walkbyme'); ?></label></th>
        <td>
            <input type="text" name="meta_title" id="meta_title" value="<?php echo esc_attr( $meta_title ); ?>" class="regular-text" />
            <p class="description"><?php _e('Custom title for search engines. Leave empty to use default.', 'walkbyme'); ?></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="meta_description"><?php _e('SEO Meta Description', 'walkbyme'); ?></label></th>
        <td>
            <textarea name="meta_description" id="meta_description" rows="3" class="large-text"><?php echo esc_textarea( $meta_description ); ?></textarea>
            <p class="description"><?php _e('Recommended length: 150-160 characters.', 'walkbyme'); ?></p>
        </td>
    </tr>
    <?php
}

// Hook into BOTH standard categories and WooCommerce product categories
add_action( 'category_edit_form_fields', 'walkbyme_taxonomy_seo_fields' );
add_action( 'product_cat_edit_form_fields', 'walkbyme_taxonomy_seo_fields' );
add_action( 'category_add_form_fields', 'walkbyme_taxonomy_seo_fields' );
add_action( 'product_cat_add_form_fields', 'walkbyme_taxonomy_seo_fields' );


function walkbyme_save_taxonomy_seo( $term_id ) {
    if ( isset( $_POST['meta_title'] ) ) {
        update_term_meta( $term_id, '_category_meta_title', sanitize_text_field( $_POST['meta_title'] ) );
    }
    if ( isset( $_POST['meta_description'] ) ) {
        update_term_meta( $term_id, '_category_meta_description', sanitize_textarea_field( $_POST['meta_description'] ) );
    }
}
add_action( 'edited_category', 'walkbyme_save_taxonomy_seo' );
add_action( 'create_category', 'walkbyme_save_taxonomy_seo' );
add_action( 'edited_product_cat', 'walkbyme_save_taxonomy_seo' );
add_action( 'create_product_cat', 'walkbyme_save_taxonomy_seo' );