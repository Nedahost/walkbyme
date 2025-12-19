<?php 
/**
 * Theme Setup and Widget Registration
 */

function walkbyme_setup() {
    // 1. Title Tag Support (CRITICAL FOR SEO)
    // Allows WordPress to manage the document title dynamically.
    add_theme_support('title-tag');

    // 2. Custom Logo Support
    // Enables the standardized custom logo upload in Appearance > Customize
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // 3. Post Thumbnails Support
    add_theme_support('post-thumbnails');
    
    // 4. HTML5 Support
    // Ensures search forms, comments, etc., use valid HTML5 markup
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // 5. Menu Registration
    register_nav_menus(array(
        'primary'     => __('Primary Navigation', 'walkbyme'),
        'footermenu1' => __('Second Navigation', 'walkbyme'),
        'footermenu2' => __('Footer Navigation', 'walkbyme')
    )); 
    
    // 6. Custom Header Support (Legacy - prefer Custom Logo if possible, but kept as requested)
    $args = array(
        'width'         => 300,
        'height'        => 55,
        'default-image' => get_template_directory_uri() . '/assets/images/walkbyme_logo_site300pxls.svg',
        'uploads'       => true,
    );
    add_theme_support('custom-header', $args);
}
add_action('after_setup_theme', 'walkbyme_setup');


/**
 * Register Widget Areas (Sidebars)
 * Must be hooked to 'widgets_init', NOT 'after_setup_theme'
 */
function walkbyme_widgets_init() {
    
    // Footer Sidebars Loop
    // Note: We keep the names exactly as you had them to preserve widget assignments
    $footersidebars = array(
        'Footer first',
        'Footer second',
        'Footer third', // Corrected typo 'three' to 'third' to match footer.php checks
        'Footer fourth', // Corrected typo 'four' to 'fourth' to match footer.php checks
        'Footer five'
    );

    foreach ($footersidebars as $footersidebar) {
        // We create a slug for ID from the name (e.g., "footer-first")
        $id_slug = sanitize_title($footersidebar);
        
        register_sidebar(array(
            'name'          => $footersidebar,
            'id'            => $id_slug, // Explicit ID is better for performance
            'description'   => __('Widget area for the footer', 'walkbyme'),
            'before_widget' => '<div id="%1$s" class="module_cat %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widgettitle">',
            'after_title'   => '</h3>'
        ));
    }
    
    // Newsletter Sidebar
    register_sidebar(array(
        'name'          => 'newsletter',
        'id'            => 'newsletter',
        'description'   => 'Είναι η περιοχή που εμφανίζεται αριστερά στις κατηγορίες',
        'before_widget' => '<div id="%1$s" class="module_cat %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widgettitle">',
        'after_title'   => '</h3>'
    ));

    // Jewelry Sidebar
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
add_action('widgets_init', 'walkbyme_widgets_init');