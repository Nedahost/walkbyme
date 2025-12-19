<?php
/**
 * Custom Post Types Registration & Meta Boxes
 */

// 1. Slider Post Type Registration
function create_slider_post_type() {
    $labels = array(
        'name'                  => _x('Slider', 'Post Type General Name', 'walkbyme'),
        'singular_name'         => _x('Slider Item', 'Post Type Singular Name', 'walkbyme'),
        'menu_name'             => __('Slider', 'walkbyme'),
        'name_admin_bar'        => __('Slider', 'walkbyme'),
        'archives'              => __('Item Archives', 'walkbyme'),
        'attributes'            => __('Item Attributes', 'walkbyme'),
        'parent_item_colon'     => __('Parent Item:', 'walkbyme'),
        'all_items'             => __('All Slides', 'walkbyme'),
        'add_new_item'          => __('Add New Slide', 'walkbyme'),
        'add_new'               => __('Add New', 'walkbyme'),
        'new_item'              => __('New Slide', 'walkbyme'),
        'edit_item'             => __('Edit Slide', 'walkbyme'),
        'update_item'           => __('Update Slide', 'walkbyme'),
        'view_item'             => __('View Slide', 'walkbyme'),
        'view_items'            => __('View Slides', 'walkbyme'),
        'search_items'          => __('Search Slide', 'walkbyme'),
        'not_found'             => __('Not found', 'walkbyme'),
        'not_found_in_trash'    => __('Not found in Trash', 'walkbyme'),
        'featured_image'        => __('Slide Image', 'walkbyme'),
        'set_featured_image'    => __('Set slide image', 'walkbyme'),
        'remove_featured_image' => __('Remove slide image', 'walkbyme'),
        'use_featured_image'    => __('Use as slide image', 'walkbyme'),
    );

    $args = array(
        'label'                 => __('Slider', 'walkbyme'),
        'description'           => __('Homepage Main Slider', 'walkbyme'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail'), // Editor is kept for Caption content
        'taxonomies'            => array(),
        'hierarchical'          => false,
        'public'                => false, // It's not a public page, it's a module
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 20,
        'menu_icon'             => 'dashicons-images-alt2',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'capability_type'       => 'post',
    );
    
    // Note: We keep the post type key 'gallery' to preserve existing data
    register_post_type('gallery', $args);
}
add_action('init', 'create_slider_post_type');

// 2. Slider Meta Boxes
function add_slider_meta_boxes() {
    add_meta_box(
        'slider_details_meta_box',
        __('Slider Options', 'walkbyme'),
        'render_slider_meta_box',
        'gallery',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_slider_meta_boxes');

function render_slider_meta_box($post) {
    // Secure retrieval
    $button_text  = get_post_meta($post->ID, '_slider_button_text', true);
    $button_url   = get_post_meta($post->ID, '_slider_button_url', true);
    $slider_order = get_post_meta($post->ID, '_slider_order', true);

    // Security Nonce
    wp_nonce_field('save_slider_details', 'slider_nonce');
    ?>
    <div class="walkbyme_meta_box_wrapper" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <p style="grid-column: 1 / -1; margin-bottom: 0;">
            <label for="slider_button_text"><strong><?php esc_html_e('Button Text', 'walkbyme'); ?></strong></label><br>
            <input type="text" name="slider_button_text" id="slider_button_text" value="<?php echo esc_attr($button_text); ?>" class="widefat" />
        </p>

        <p style="grid-column: 1 / -1; margin-bottom: 0;">
            <label for="slider_button_url"><strong><?php esc_html_e('Button URL', 'walkbyme'); ?></strong></label><br>
            <input type="url" name="slider_button_url" id="slider_button_url" value="<?php echo esc_attr($button_url); ?>" class="widefat" placeholder="https://..." />
        </p>

        <p style="margin-bottom: 0;">
            <label for="slider_order"><strong><?php esc_html_e('Order (Priority)', 'walkbyme'); ?></strong></label><br>
            <input type="number" name="slider_order" id="slider_order" value="<?php echo esc_attr($slider_order); ?>" class="widefat" style="max-width: 100px;" />
            <br><small><?php esc_html_e('Lower numbers appear first (e.g. 1, 2, 3)', 'walkbyme'); ?></small>
        </p>
    </div>
    <?php
}

// 3. Save Meta Data
function save_slider_meta_boxes($post_id) {
    // 1. Check Nonce
    if (!isset($_POST['slider_nonce']) || !wp_verify_nonce($_POST['slider_nonce'], 'save_slider_details')) {
        return;
    }

    // 2. Check Autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // 3. Check Permissions (Security Fix)
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save Button Text
    if (isset($_POST['slider_button_text'])) {
        update_post_meta($post_id, '_slider_button_text', sanitize_text_field($_POST['slider_button_text']));
    }

    // Save Button URL (Use esc_url_raw for database)
    if (isset($_POST['slider_button_url'])) {
        update_post_meta($post_id, '_slider_button_url', esc_url_raw($_POST['slider_button_url']));
    }

    // Save Order (Sanitize as integer)
    if (isset($_POST['slider_order'])) {
        update_post_meta($post_id, '_slider_order', intval($_POST['slider_order']));
    }
}
add_action('save_post_gallery', 'save_slider_meta_boxes');

// 4. IMPROVEMENT: Show Columns in Admin List
// This helps you see the image and the order without opening the post
add_filter('manage_gallery_posts_columns', 'walkbyme_gallery_columns');
function walkbyme_gallery_columns($columns) {
    $new_columns = array(
        'cb' => $columns['cb'],
        'featured_image' => __('Image', 'walkbyme'),
        'title' => $columns['title'],
        'slider_order' => __('Order', 'walkbyme'),
        'date' => $columns['date']
    );
    return $new_columns;
}

add_action('manage_gallery_posts_custom_column', 'walkbyme_gallery_custom_column', 10, 2);
function walkbyme_gallery_custom_column($column, $post_id) {
    switch ($column) {
        case 'featured_image':
            if (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, array(80, 80));
            } else {
                echo '<span style="color:#ccc;">' . __('No Image', 'walkbyme') . '</span>';
            }
            break;

        case 'slider_order':
            $order = get_post_meta($post_id, '_slider_order', true);
            echo $order ? esc_html($order) : '0';
            break;
    }
}

// Make the Order column sortable
add_filter('manage_edit-gallery_sortable_columns', 'walkbyme_gallery_sortable_columns');
function walkbyme_gallery_sortable_columns($columns) {
    $columns['slider_order'] = 'slider_order';
    return $columns;
}

add_action('pre_get_posts', 'walkbyme_gallery_orderby');
function walkbyme_gallery_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->get('post_type') === 'gallery' && $query->get('orderby') === 'slider_order') {
        $query->set('meta_key', '_slider_order');
        $query->set('orderby', 'meta_value_num');
    }
}