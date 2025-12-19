<?php
/**
 * Core WordPress Customizations
 * Cleanup, security, and basic functionality.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * CLEANUP HEAD
 * Removes unnecessary links that clutter the code and reveal info.
 */
function walkbyme_cleanup_head() {
    // Remove detailed feed links (keep basic if needed, otherwise remove all)
    remove_action( 'wp_head', 'feed_links_extra', 3 ); 
    remove_action( 'wp_head', 'feed_links', 2 );
    
    // Remove RSD link (used by external editors like Flickr)
    remove_action( 'wp_head', 'rsd_link' ); 
    
    // Remove Windows Live Writer link (obsolete)
    remove_action( 'wp_head', 'wlwmanifest_link' ); 
    
    // Remove WP Version generator (Security risk)
    remove_action( 'wp_head', 'wp_generator' ); 
    
    // Remove Shortlink
    remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
    
    // Remove API links (if not using Headless WP)
    // remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
    // remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
}
add_action( 'after_setup_theme', 'walkbyme_cleanup_head' );

/**
 * DISABLE EMOJIS
 * Improves load time as most e-commerce sites don't need WP Emojis.
 */
function walkbyme_disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    
    // Remove DNS prefetch for emojis
    add_filter( 'emoji_svg_url', '__return_false' );
}
add_action( 'init', 'walkbyme_disable_emojis' );

/**
 * SECURITY: HIDE WP VERSION
 * Removes version parameter from scripts and styles
 */
function walkbyme_remove_wp_version_strings( $src ) {
    global $wp_version;
    parse_str( parse_url($src, PHP_URL_QUERY), $query );
    if ( !empty( $query['ver'] ) && $query['ver'] === $wp_version ) {
        $src = remove_query_arg( 'ver', $src );
    }
    return $src;
}
add_filter( 'script_loader_src', 'walkbyme_remove_wp_version_strings' );
add_filter( 'style_loader_src', 'walkbyme_remove_wp_version_strings' );


/**
 * FAVICON HANDLING
 * Uses WordPress native "Site Icon" first, falls back to legacy file.
 */
function walkbyme_favicon() {
    // First check if user set a Site Icon via Appearance > Customize
    if ( has_site_icon() ) {
        return;
    }
    
    // Fallback to the file in assets
    $favicon_url = get_template_directory_uri() . '/assets/images/favicon.jpg';
    if (file_exists(get_template_directory() . '/assets/images/favicon.jpg')) {
        echo '<link rel="shortcut icon" type="image/x-icon" href="' . esc_url($favicon_url) . '" />' . "\n";
    }
}
add_action('wp_head', 'walkbyme_favicon');
add_action('admin_head', 'walkbyme_favicon');

/**
 * SVG SUPPORT (Optional but Recommended)
 * Since you use SVGs in your header/logo, this allows uploading them safely.
 */
function walkbyme_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'walkbyme_mime_types');