<?php 
/**
 * Custom Meta Boxes & Term Fields
 * Handles custom fields for Products, Posts, and Categories.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. ADMIN SCRIPTS
 * Load Media Uploader logic properly via WordPress hooks.
 */
function walkbyme_admin_scripts($hook) {
    // Load only on Post Edit screens or Category Edit screens
    $screen = get_current_screen();
    
    if ( ! $screen ) return;

    // Check if we are on product, post, or category editing screens
    if ( $screen->base === 'post' || $screen->base === 'term' || $screen->base === 'edit-tags' ) {
        
        wp_enqueue_media(); // Load WP Media Uploader

        // Custom JS for Image Uploading
        $custom_js = "
        jQuery(document).ready(function($) {
            // Generic Media Uploader Logic
            function initMediaUploader(buttonClass, inputId, previewId) {
                var frame;
                $(document).on('click', buttonClass, function(e) {
                    e.preventDefault();
                    
                    if (frame) {
                        frame.open();
                        return;
                    }
                    
                    frame = wp.media({
                        title: '" . esc_js(__('Select Image', 'walkbyme')) . "',
                        button: { text: '" . esc_js(__('Use this image', 'walkbyme')) . "' },
                        multiple: false
                    });
                    
                    frame.on('select', function() {
                        var attachment = frame.state().get('selection').first().toJSON();
                        $(inputId).val(attachment.id || attachment.url); // Handle ID for products, URL for cats
                        
                        if(previewId) {
                           $(previewId).attr('src', attachment.url).show();
                        }
                    });
                    
                    frame.open();
                });
            }

            // Product Custom Image
            initMediaUploader('#custom_product_image_upload', '#custom_product_image_id', '#custom_product_image_preview');
            
            // Remove Product Image
            $('#custom_product_image_remove').on('click', function(e) {
                e.preventDefault();
                $('#custom_product_image_id').val('');
                $('#custom_product_image_preview').attr('src', '').hide();
            });

            // Category Image (Fix for dynamic button)
            var catFrame;
            $(document).on('click', '.image-upload', function (e) {
                e.preventDefault();
                if (catFrame) { catFrame.open(); return; }
                
                catFrame = wp.media({
                    title: '" . esc_js(__('Επιλογή Εικόνας', 'walkbyme')) . "',
                    library: { type: 'image' },
                    button: { text: '" . esc_js(__('Επιλογή Εικόνας', 'walkbyme')) . "' },
                    multiple: false
                });
                
                catFrame.on('select', function () {
                    var attachment = catFrame.state().get('selection').first().toJSON();
                    $('#category_image').val(attachment.url);
                });
                
                catFrame.open();
            });
        });
        ";
        
        wp_add_inline_script('common', $custom_js);
    }
}
add_action('admin_enqueue_scripts', 'walkbyme_admin_scripts');


/**
 * 2. PRODUCT EXTRA IMAGE META BOX
 */
function add_custom_image_metabox() {
    add_meta_box(
        'custom_product_image_metabox',
        __('Custom Product Image (Hover)', 'walkbyme'),
        'render_custom_product_image_metabox',
        'product',
        'side',
        'low'
    );
}
add_action('add_meta_boxes', 'add_custom_image_metabox');

function render_custom_product_image_metabox($post) {
    wp_nonce_field('save_custom_product_image', 'custom_product_image_nonce');
    
    $custom_image_id = get_post_meta($post->ID, '_custom_product_image_id', true);
    $image_src = '';
    
    if ($custom_image_id) {
        $image_attributes = wp_get_attachment_image_src($custom_image_id, 'thumbnail');
        if ($image_attributes) {
            $image_src = $image_attributes[0];
        }
    }
    ?>
    <div class="image-preview-wrapper" style="margin-bottom: 10px;">
        <img id="custom_product_image_preview" src="<?php echo esc_url($image_src); ?>" style="max-width:100%; height:auto; display: <?php echo $image_src ? 'block' : 'none'; ?>;" />
    </div>
    
    <input type="hidden" id="custom_product_image_id" name="custom_product_image_id" value="<?php echo esc_attr($custom_image_id); ?>" />
    
    <p>
        <button type="button" class="button" id="custom_product_image_upload"><?php _e('Select Image', 'walkbyme'); ?></button>
        <button type="button" class="button" id="custom_product_image_remove" style="color: #a00;"><?php _e('Remove', 'walkbyme'); ?></button>
    </p>
    <p class="description"><?php _e('This image appears when hovering over the product.', 'walkbyme'); ?></p>
    <?php
}

function save_custom_product_image($post_id) {
    if (!isset($_POST['custom_product_image_nonce']) || !wp_verify_nonce($_POST['custom_product_image_nonce'], 'save_custom_product_image')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (!empty($_POST['custom_product_image_id'])) {
        update_post_meta($post_id, '_custom_product_image_id', absint($_POST['custom_product_image_id']));
    } else {
        delete_post_meta($post_id, '_custom_product_image_id');
    }
}
add_action('save_post_product', 'save_custom_product_image');


/**
 * 3. CUSTOM CTA META BOX (POSTS)
 */
function add_custom_cta_meta_box() {
    add_meta_box(
        'custom_cta_meta_box',
        __('Προτροπή Δράσης (CTA)', 'walkbyme'),
        'render_custom_cta_meta_box',
        'post',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_custom_cta_meta_box');

function render_custom_cta_meta_box($post) {
    wp_nonce_field('custom_cta_action', 'custom_cta_nonce');
    $value = get_post_meta($post->ID, '_custom_cta', true);
    
    wp_editor($value, 'custom_cta', array(
        'textarea_name' => 'custom_cta',
        'media_buttons' => true,
        'textarea_rows' => 5,
        'teeny'         => true // Simplified toolbar
    ));
}

function save_custom_cta_meta_box($post_id) {
    if (!isset($_POST['custom_cta_nonce']) || !wp_verify_nonce($_POST['custom_cta_nonce'], 'custom_cta_action')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['custom_cta'])) {
        update_post_meta($post_id, '_custom_cta', wp_kses_post($_POST['custom_cta']));
    }
}
add_action('save_post_post', 'save_custom_cta_meta_box');


/**
 * 4. CATEGORY CUSTOM FIELDS (Split Add/Edit for Bug Fix)
 */

// A. EDIT FORM (When editing an existing category)
function edit_product_cat_custom_fields($term) {
    $show_on_homepage = get_term_meta($term->term_id, 'show_on_homepage', true);
    $category_priority = get_term_meta($term->term_id, 'category_priority', true);
    $category_image = get_term_meta($term->term_id, 'category_image', true);
    ?>
    <tr class="form-field">
        <th scope="row"><label for="show_on_homepage"><?php _e('Εμφάνιση στην αρχική', 'walkbyme'); ?></label></th>
        <td>
            <select name="show_on_homepage" id="show_on_homepage">
                <option value="no" <?php selected($show_on_homepage, 'no'); ?>><?php _e('Όχι', 'walkbyme'); ?></option>
                <option value="yes" <?php selected($show_on_homepage, 'yes'); ?>><?php _e('Ναι', 'walkbyme'); ?></option>
            </select>
        </td>
    </tr>

    <tr class="form-field">
        <th scope="row"><label for="category_priority"><?php _e('Προτεραιότητα', 'walkbyme'); ?></label></th>
        <td>
            <input type="number" name="category_priority" id="category_priority" value="<?php echo esc_attr($category_priority); ?>">
            <p class="description"><?php _e('Μικρότερος αριθμός = Εμφανίζεται πρώτο (π.χ. 1, 2, 3)', 'walkbyme'); ?></p>
        </td>
    </tr>

    <tr class="form-field">
        <th scope="row"><label for="category_image"><?php _e('Εικόνα Κατηγορίας', 'walkbyme'); ?></label></th>
        <td>
            <input type="text" name="category_image" id="category_image" class="regular-text" value="<?php echo esc_url($category_image); ?>">
            <button class="button image-upload"><?php _e('Επιλογή Εικόνας', 'walkbyme'); ?></button>
        </td>
    </tr>
    <?php
}
add_action('product_cat_edit_form_fields', 'edit_product_cat_custom_fields');

// B. ADD FORM (When adding a new category)
function add_product_cat_custom_fields($taxonomy) {
    ?>
    <div class="form-field">
        <label for="show_on_homepage"><?php _e('Εμφάνιση στην αρχική', 'walkbyme'); ?></label>
        <select name="show_on_homepage" id="show_on_homepage">
            <option value="no"><?php _e('Όχι', 'walkbyme'); ?></option>
            <option value="yes"><?php _e('Ναι', 'walkbyme'); ?></option>
        </select>
    </div>

    <div class="form-field">
        <label for="category_priority"><?php _e('Προτεραιότητα', 'walkbyme'); ?></label>
        <input type="number" name="category_priority" id="category_priority" value="0">
    </div>

    <div class="form-field">
        <label for="category_image"><?php _e('Εικόνα Κατηγορίας URL', 'walkbyme'); ?></label>
        <input type="text" name="category_image" id="category_image">
        <button class="button image-upload" style="margin-top:5px;"><?php _e('Επιλογή Εικόνας', 'walkbyme'); ?></button>
    </div>
    
    <div class="form-field">
        <label for="custom_field_one"><?php _e('Προσαρμοσμένο Πεδίο 1', 'walkbyme'); ?></label>
        <input type="text" name="custom_field_one" id="custom_field_one">
    </div>
    <div class="form-field">
        <label for="custom_field_two"><?php _e('Προσαρμοσμένο Πεδίο 2', 'walkbyme'); ?></label>
        <input type="text" name="custom_field_two" id="custom_field_two">
    </div>
    <?php
}
add_action('product_cat_add_form_fields', 'add_product_cat_custom_fields');


// C. SAVE CATEGORY FIELDS
function save_product_cat_custom_fields($term_id) {
    if (isset($_POST['show_on_homepage'])) {
        update_term_meta($term_id, 'show_on_homepage', sanitize_text_field($_POST['show_on_homepage']));
    }

    if (isset($_POST['category_priority'])) {
        update_term_meta($term_id, 'category_priority', intval($_POST['category_priority']));
    }

    if (isset($_POST['category_image'])) {
        update_term_meta($term_id, 'category_image', esc_url_raw($_POST['category_image']));
    }
    
    // Extra fields
    if (isset($_POST['custom_field_one'])) {
        update_term_meta($term_id, 'custom_field_one', sanitize_text_field($_POST['custom_field_one']));
    }
    if (isset($_POST['custom_field_two'])) {
        update_term_meta($term_id, 'custom_field_two', sanitize_text_field($_POST['custom_field_two']));
    }
}
add_action('edited_product_cat', 'save_product_cat_custom_fields');
add_action('create_product_cat', 'save_product_cat_custom_fields');