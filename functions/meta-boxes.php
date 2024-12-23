<?php 

// Product Image Meta Box 
function add_custom_image_metabox() {
    add_meta_box(
        'custom_product_image_metabox',
        __('Custom Product Image', 'woocommerce'),
        'custom_product_image_metabox_callback',
        'product',
        'side'
    );
}
add_action('add_meta_boxes', 'add_custom_image_metabox');

function custom_product_image_metabox_callback($post) {
    wp_nonce_field('save_custom_product_image', 'custom_product_image_nonce');
    $custom_image_id = get_post_meta($post->ID, '_custom_product_image_id', true);
    $custom_image_src = wp_get_attachment_image_src($custom_image_id, 'thumbnail');
    ?>
    <div>
        <img id="custom_product_image_preview" src="<?php echo esc_url($custom_image_src ? $custom_image_src[0] : ''); ?>" style="max-width:100%; height:auto;" />
        <input type="hidden" id="custom_product_image_id" name="custom_product_image_id" value="<?php echo esc_attr($custom_image_id); ?>" />
        <button type="button" class="button" id="custom_product_image_upload"><?php _e('Select Image', 'woocommerce'); ?></button>
        <button type="button" class="button" id="custom_product_image_remove"><?php _e('Remove Image', 'woocommerce'); ?></button>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var frame;
            $('#custom_product_image_upload').on('click', function(event) {
                event.preventDefault();
                if (frame) {
                    frame.open();
                    return;
                }
                frame = wp.media({
                    title: '<?php _e('Select or Upload Custom Product Image', 'woocommerce'); ?>',
                    button: {
                        text: '<?php _e('Use this image', 'woocommerce'); ?>',
                    },
                    multiple: false
                });
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#custom_product_image_id').val(attachment.id);
                    $('#custom_product_image_preview').attr('src', attachment.url);
                });
                frame.open();
            });
            $('#custom_product_image_remove').on('click', function(event) {
                event.preventDefault();
                $('#custom_product_image_id').val('');
                $('#custom_product_image_preview').attr('src', '');
            });
        });
    </script>
    <?php
}


function save_custom_product_image($post_id) {
    if (!isset($_POST['custom_product_image_nonce']) || !wp_verify_nonce($_POST['custom_product_image_nonce'], 'save_custom_product_image')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['custom_product_image_id'])) {
        update_post_meta($post_id, '_custom_product_image_id', absint($_POST['custom_product_image_id']));
    } else {
        delete_post_meta($post_id, '_custom_product_image_id');
    }
}
add_action('save_post', 'save_custom_product_image');

// Custom CTA Meta Box For Posts
function add_custom_meta_box() {
    add_meta_box(
        'custom_cta_meta_box',
        'Προτροπή Δράσης',
        'custom_cta_meta_box_callback',
        'post'
    );
}
add_action('add_meta_boxes', 'add_custom_meta_box');

function custom_cta_meta_box_callback($post) {
    wp_nonce_field('custom_cta_meta_box', 'custom_cta_meta_box_nonce');
    $value = get_post_meta($post->ID, '_custom_cta', true);
    wp_editor($value, 'custom_cta_editor', array(
        'textarea_name' => 'custom_cta',
        'media_buttons' => true,
        'tinymce'       => true,
        'quicktags'     => true,
    ));
}

function save_custom_cta_meta_box_data($post_id) {
    if (!isset($_POST['custom_cta_meta_box_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['custom_cta_meta_box_nonce'], 'custom_cta_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['custom_cta'])) {
        update_post_meta($post_id, '_custom_cta', wp_kses_post($_POST['custom_cta']));
    }
}
add_action('save_post', 'save_custom_cta_meta_box_data');



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
