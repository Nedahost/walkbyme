<?php
/**
 * Custom XML Sitemaps
 * Generates virtual XML sitemaps dynamically with caching for performance.
 * * Access at: 
 * yoursite.gr/sitemap-products.xml
 * yoursite.gr/sitemap-articles.xml
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. REWRITE RULES
 * Register the custom URLs for the sitemaps.
 */
function walkbyme_sitemap_rewrites() {
    add_rewrite_rule('sitemap-products\.xml$', 'index.php?walkbyme_sitemap=products', 'top');
    add_rewrite_rule('sitemap-articles\.xml$', 'index.php?walkbyme_sitemap=articles', 'top');
}
add_action('init', 'walkbyme_sitemap_rewrites');

/**
 * 2. QUERY VARS
 * Allow WordPress to understand our custom query variable.
 */
function walkbyme_sitemap_query_vars($vars) {
    $vars[] = 'walkbyme_sitemap';
    return $vars;
}
add_filter('query_vars', 'walkbyme_sitemap_query_vars');

/**
 * 3. TEMPLATE REDIRECT
 * Intercept the request and generate the XML.
 */
function walkbyme_sitemap_render() {
    $type = get_query_var('walkbyme_sitemap');
    
    if ( empty($type) ) {
        return;
    }

    // Handle Products Sitemap
    if ( $type === 'products' ) {
        walkbyme_generate_xml('product', 'product_cat', 'daily', 'weekly');
        exit;
    }

    // Handle Articles Sitemap
    if ( $type === 'articles' ) {
        walkbyme_generate_xml('post', 'category', 'weekly', 'weekly');
        exit;
    }
}
add_action('template_redirect', 'walkbyme_sitemap_render');

/**
 * 4. XML GENERATOR FUNCTION
 * A unified function to generate XML for any post type with Caching.
 */
function walkbyme_generate_xml($post_type, $taxonomy, $post_freq, $tax_freq) {
    // Check for cached version
    $cache_key = 'walkbyme_sitemap_' . $post_type;
    $xml = get_transient($cache_key);

    // If cache exists, serve it and exit
    if ( $xml ) {
        header('Content-Type: application/xml; charset=utf-8');
        echo $xml;
        return;
    }

    // Start generating fresh XML
    ob_start();
    
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

    // A. Add Homepage (Only on articles/main sitemap)
    if ( $post_type === 'post' ) {
        echo "  <url>\n";
        echo "    <loc>" . esc_url(home_url('/')) . "</loc>\n";
        echo "    <changefreq>daily</changefreq>\n";
        echo "    <priority>1.0</priority>\n";
        echo "  </url>\n";
    }

    // B. Add Terms (Categories)
    $terms = get_terms(array(
        'taxonomy'   => $taxonomy,
        'hide_empty' => true,
    ));

    if ( ! is_wp_error($terms) && ! empty($terms) ) {
        foreach ($terms as $term) {
            echo "  <url>\n";
            echo "    <loc>" . esc_url(get_term_link($term)) . "</loc>\n";
            echo "    <changefreq>" . $tax_freq . "</changefreq>\n";
            echo "    <priority>0.8</priority>\n";
            echo "  </url>\n";
        }
    }

    // C. Add Posts/Products
    // Optimize Query for performance (no meta, only IDs and dates)
    $args = array(
        'post_type'      => $post_type,
        'posts_per_page' => 1000, // Limit to prevent memory crash (increase if needed)
        'post_status'    => 'publish',
        'no_found_rows'  => true, // Speed optimization
        'update_post_term_cache' => false, // Speed optimization
        'update_post_meta_cache' => false, // Speed optimization
        'fields'         => 'ids' // Only get IDs
    );

    $query = new WP_Query($args);

    if ( $query->have_posts() ) {
        foreach ( $query->posts as $post_id ) {
            $modified_date = get_the_modified_time('c', $post_id);
            $permalink = get_permalink($post_id);
            
            echo "  <url>\n";
            echo "    <loc>" . esc_url($permalink) . "</loc>\n";
            echo "    <lastmod>" . $modified_date . "</lastmod>\n";
            echo "    <changefreq>" . $post_freq . "</changefreq>\n";
            echo "    <priority>0.6</priority>\n";
            echo "  </url>\n";
        }
    }

    echo '</urlset>';

    $content = ob_get_clean();

    // Cache the result for 12 hours (43200 seconds)
    set_transient($cache_key, $content, 12 * HOUR_IN_SECONDS);

    // Output
    header('Content-Type: application/xml; charset=utf-8');
    echo $content;
}

/**
 * 5. CACHE CLEARING
 * Automatically clear cache when content changes.
 */
function walkbyme_clear_sitemap_cache($post_id) {
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    
    $post_type = get_post_type($post_id);
    
    if ( $post_type === 'product' ) {
        delete_transient('walkbyme_sitemap_product');
    } elseif ( $post_type === 'post' ) {
        delete_transient('walkbyme_sitemap_post');
    }
}
add_action('save_post', 'walkbyme_clear_sitemap_cache');
add_action('delete_post', 'walkbyme_clear_sitemap_cache');
add_action('publish_post', 'walkbyme_clear_sitemap_cache');

// Also clear on category changes
function walkbyme_clear_term_cache() {
    delete_transient('walkbyme_sitemap_product');
    delete_transient('walkbyme_sitemap_post');
}
add_action('create_product_cat', 'walkbyme_clear_term_cache');
add_action('edited_product_cat', 'walkbyme_clear_term_cache');
add_action('delete_product_cat', 'walkbyme_clear_term_cache');
add_action('create_category', 'walkbyme_clear_term_cache');
add_action('edited_category', 'walkbyme_clear_term_cache');