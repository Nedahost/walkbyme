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
        'primary'     => __('Primary Navigation', 'walkbyme')
    )); 
    

}
add_action('after_setup_theme', 'walkbyme_setup');


/**
 * Register Widget Areas (Sidebars)
 * Must be hooked to 'widgets_init', NOT 'after_setup_theme'
 */
function walkbyme_widgets_init() {
    
    $footer_columns = 4; 
    
    for ($i = 1; $i <= $footer_columns; $i++) {
        register_sidebar(array(
            'name'          => sprintf(__('Footer %s', 'walkbyme'), $i), // Footer 1, Footer 2...
            'id'            => 'footer-' . $i, // ID: footer-1
            'description'   => sprintf(__('Widget area for footer column %s', 'walkbyme'), $i),
            'before_widget' => '<div id="%1$s" class="module_cat %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widgettitle">',
            'after_title'   => '</h3>'
        ));
    }
    

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