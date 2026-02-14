<?php
/**
 * Product Tabs - Accordion για WooCommerce προϊόντα
 */

if (!defined('ABSPATH')) {
    exit;
}

// ==================================================
// ADMIN: Menu
// ==================================================
function product_tabs_admin_menu() {
    add_menu_page(
        'Product Tabs',
        'Product Tabs',
        'manage_options',
        'product-tabs',
        'product_tabs_admin_page',
        'dashicons-list-view',
        30
    );
}
add_action('admin_menu', 'product_tabs_admin_menu');

// ==================================================
// ADMIN: Σελίδα ρυθμίσεων με wp_editor
// ==================================================
function product_tabs_admin_page() {
    // Αποθήκευση
    if (isset($_POST['submit_tabs']) && check_admin_referer('product_tabs_nonce', 'product_tabs_nonce_field')) {
        $tabs_items = array();
        
        if (isset($_POST['tab_title'])) {
            foreach ($_POST['tab_title'] as $index => $title) {
                $content = isset($_POST['tab_content_' . $index]) ? $_POST['tab_content_' . $index] : '';
                $categories = isset($_POST['tab_categories'][$index]) ? $_POST['tab_categories'][$index] : array();
                
                if (!empty($title) && !empty($content)) {
                    $tabs_items[] = array(
                        'title'      => sanitize_text_field($title),
                        'content'    => wp_kses_post($content),
                        'categories' => array_map('sanitize_text_field', $categories),
                    );
                }
            }
        }
        
        // Έλεγχος για νέο tab
        $new_index = count($tabs_items);
        $new_title = isset($_POST['tab_title_new']) ? $_POST['tab_title_new'] : '';
        $new_content = isset($_POST['tab_content_new']) ? $_POST['tab_content_new'] : '';
        $new_categories = isset($_POST['tab_categories_new']) ? $_POST['tab_categories_new'] : array();
        
        if (!empty($new_title) && !empty($new_content)) {
            $tabs_items[] = array(
                'title'      => sanitize_text_field($new_title),
                'content'    => wp_kses_post($new_content),
                'categories' => array_map('sanitize_text_field', $new_categories),
            );
        }
        
        update_option('product_tabs_data', $tabs_items);
        echo '<div class="notice notice-success"><p>Τα tabs αποθηκεύτηκαν!</p></div>';
        
        // Reload για να εμφανιστούν τα νέα
        $tabs_items = get_option('product_tabs_data', array());
    } else {
        $tabs_items = get_option('product_tabs_data', array());
    }
    
    $categories = get_terms(array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
    ));
    ?>
    
    <div class="wrap">
        <h1>Product Tabs</h1>
        
        <form method="post">
            <?php wp_nonce_field('product_tabs_nonce', 'product_tabs_nonce_field'); ?>
            
            <table class="widefat striped" style="margin-top: 20px;">
                <thead>
                    <tr>
                        <th style="width: 20%;">Τίτλος</th>
                        <th style="width: 50%;">Περιεχόμενο</th>
                        <th style="width: 25%;">Κατηγορίες</th>
                        <th style="width: 5%;">Διαγραφή</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tabs_items)) : ?>
                        <?php foreach ($tabs_items as $index => $item) : ?>
                            <tr>
                                <td>
                                    <input type="text" 
                                           name="tab_title[<?php echo $index; ?>]" 
                                           value="<?php echo esc_attr($item['title']); ?>" 
                                           class="widefat">
                                </td>
                                <td>
                                    <?php
                                    wp_editor($item['content'], 'tab_content_' . $index, array(
                                        'textarea_name' => 'tab_content_' . $index,
                                        'textarea_rows' => 5,
                                        'media_buttons' => false,
                                        'teeny'         => true,
                                        'tinymce'       => true,
                                        'quicktags'     => true,
                                    ));
                                    ?>
                                </td>
                                <td>
                                    <div style="max-height: 150px; overflow-y: auto;">
                                        <?php foreach ($categories as $cat) : ?>
                                            <label style="display: block; margin-bottom: 5px;">
                                                <input type="checkbox" 
                                                       name="tab_categories[<?php echo $index; ?>][]" 
                                                       value="<?php echo esc_attr($cat->slug); ?>"
                                                       <?php checked(in_array($cat->slug, (array)$item['categories'])); ?>>
                                                <?php echo esc_html($cat->name); ?>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <label>
                                        <input type="checkbox" name="delete_tab[<?php echo $index; ?>]" value="1">
                                        🗑️
                                    </label>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <!-- Νέο Tab -->
            <h2 style="margin-top: 30px;">Προσθήκη νέου Tab</h2>
            <table class="widefat" style="background: #f9f9f9;">
                <tr>
                    <td style="width: 20%;">
                        <input type="text" 
                               name="tab_title_new" 
                               class="widefat" 
                               placeholder="Τίτλος...">
                    </td>
                    <td style="width: 50%;">
                        <?php
                        wp_editor('', 'tab_content_new', array(
                            'textarea_name' => 'tab_content_new',
                            'textarea_rows' => 5,
                            'media_buttons' => false,
                            'teeny'         => true,
                            'tinymce'       => true,
                            'quicktags'     => true,
                        ));
                        ?>
                    </td>
                    <td style="width: 25%;">
                        <div style="max-height: 150px; overflow-y: auto;">
                            <?php foreach ($categories as $cat) : ?>
                                <label style="display: block; margin-bottom: 5px;">
                                    <input type="checkbox" 
                                           name="tab_categories_new[]" 
                                           value="<?php echo esc_attr($cat->slug); ?>">
                                    <?php echo esc_html($cat->name); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </td>
                    <td style="width: 5%;"></td>
                </tr>
            </table>
            
            <p style="margin-top: 20px;">
                <input type="submit" name="submit_tabs" value="Αποθήκευση" class="button button-primary button-large">
            </p>
        </form>
    </div>
    <?php
}