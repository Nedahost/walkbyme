<?php
// Slider Post Type
function create_slider_post_type() {
    register_post_type('gallery',
        array(
            'labels' => array(
                'name' => __('Slider'),
                'singular_name' => __('Slider')
            ),
            'public' => false,
            'has_archive' => false,
            'supports' => array('title','thumbnail', 'editor'),
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'menu_position' => null,
            'menu_icon' => 'dashicons-format-gallery'
        )
    );
}
add_action('init', 'create_slider_post_type');

// Slider Meta Boxes
function add_slider_meta_boxes() {
    add_meta_box(
        'slider_button_meta_box', // ID του meta box
        'Slider Details', // Τίτλος του meta box
        'render_slider_button_meta_box', // Συνάρτηση για το rendering του περιεχομένου
        'gallery', // Custom post type
        'normal', // Context
        'default' // Priority
    );
}
add_action('add_meta_boxes', 'add_slider_meta_boxes');

function render_slider_button_meta_box($post) {
    $button_text = get_post_meta($post->ID, '_slider_button_text', true);
    $button_url = get_post_meta($post->ID, '_slider_button_url', true);
    $slider_order = get_post_meta($post->ID, '_slider_order', true);

    ?>
    <label for="slider_button_text">Button Text:</label>
    <input type="text" name="slider_button_text" id="slider_button_text" value="<?php echo esc_attr($button_text); ?>" style="width: 100%;" />

    <label for="slider_button_url" style="margin-top: 10px; display: block;">Button URL:</label>
    <input type="url" name="slider_button_url" id="slider_button_url" value="<?php echo esc_attr($button_url); ?>" style="width: 100%;" />

    <label for="slider_order" style="margin-top: 10px; display: block;">Slider Order:</label>
    <input type="number" name="slider_order" id="slider_order" value="<?php echo esc_attr($slider_order); ?>" style="width: 100%;" />
    <?php
    wp_nonce_field(basename(__FILE__), 'slider_button_nonce');
}


function save_slider_meta_boxes($post_id) {
    if (!isset($_POST['slider_button_nonce']) || !wp_verify_nonce($_POST['slider_button_nonce'], basename(__FILE__))) {
        return $post_id;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    
    $fields = array('slider_button_text', 'slider_button_url', 'slider_order');
    
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $value = sanitize_text_field($_POST[$field]);
            update_post_meta($post_id, '_' . $field, $value);
        }
    }
}
add_action('save_post', 'save_slider_meta_boxes');