<?php
/**
 * WooCommerce Product Badges System
 * 
 * Professional badge management system for WooCommerce products
 * 
 * @package WooCommerce_Product_Badges
 * @version 1.0.0
 * @author Your Name
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class WC_Product_Badges_System {
    
    /**
     * Single instance of the class
     */
    private static $instance = null;
    
    /**
     * Get single instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init();
    }
    
    /**
     * Initialize the badges system
     */
    private function init() {
        // Core hooks
        add_action('init', array($this, 'create_badge_post_type'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_badge_data'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        
        // Admin customizations
        add_filter('manage_product_badge_posts_columns', array($this, 'badge_columns'));
        add_action('manage_product_badge_posts_custom_column', array($this, 'badge_column_content'), 10, 2);
    }
    
    /**
     * Create the Product Badge post type
     */
    public function create_badge_post_type() {
        $labels = array(
            'name' => __('Product Badges', 'wc-badges'),
            'singular_name' => __('Product Badge', 'wc-badges'),
            'add_new' => __('Add New Badge', 'wc-badges'),
            'add_new_item' => __('Add New Product Badge', 'wc-badges'),
            'edit_item' => __('Edit Badge', 'wc-badges'),
            'new_item' => __('New Badge', 'wc-badges'),
            'view_item' => __('View Badge', 'wc-badges'),
            'search_items' => __('Search Badges', 'wc-badges'),
            'not_found' => __('No badges found', 'wc-badges'),
            'not_found_in_trash' => __('No badges found in trash', 'wc-badges'),
            'menu_name' => __('Badges', 'wc-badges')
        );
        
        $args = array(
            'labels' => $labels,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'edit.php?post_type=product',
            'capability_type' => 'post',
            'hierarchical' => false,
            'supports' => array('title'),
            'menu_icon' => 'dashicons-awards',
            'show_in_nav_menus' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'menu_position' => null
        );
        
        register_post_type('product_badge', $args);
    }
    
    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        // Badge configuration
        add_meta_box(
            'badge_config',
            __('Badge Configuration', 'wc-badges'),
            array($this, 'badge_config_metabox'),
            'product_badge',
            'normal',
            'high'
        );
        
        // Badge preview
        add_meta_box(
            'badge_preview',
            __('Preview', 'wc-badges'),
            array($this, 'badge_preview_metabox'),
            'product_badge',
            'side',
            'default'
        );
        
        // Product badge selection
        add_meta_box(
            'product_badges',
            __('Product Badges', 'wc-badges'),
            array($this, 'product_badges_metabox'),
            'product',
            'side',
            'default'
        );
    }
    
    /**
     * Badge configuration metabox
     */
    public function badge_config_metabox($post) {
        wp_nonce_field('save_badge_config', 'badge_config_nonce');
        $config = $this->get_badge_config($post->ID);
        ?>
        <div class="badge-config-wrapper">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="badge_text"><?php _e('Badge Text', 'wc-badges'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="badge_text" 
                                   name="badge_text" 
                                   value="<?php echo esc_attr($config['text']); ?>" 
                                   class="regular-text" 
                                   placeholder="<?php _e('e.g., New, Handmade, Sale', 'wc-badges'); ?>" 
                                   required />
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Colors', 'wc-badges'); ?></th>
                        <td>
                            <p>
                                <label for="badge_bg_color"><?php _e('Background:', 'wc-badges'); ?></label>
                                <input type="color" id="badge_bg_color" name="badge_bg_color" value="<?php echo esc_attr($config['bg_color']); ?>" />
                            </p>
                            <p>
                                <label for="badge_text_color"><?php _e('Text:', 'wc-badges'); ?></label>
                                <input type="color" id="badge_text_color" name="badge_text_color" value="<?php echo esc_attr($config['text_color']); ?>" />
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="badge_type"><?php _e('Badge Type', 'wc-badges'); ?></label>
                        </th>
                        <td>
                            <select id="badge_type" name="badge_type" onchange="toggleConditions(this.value)">
                                <option value="manual" <?php selected($config['type'], 'manual'); ?>>
                                    <?php _e('Manual Selection', 'wc-badges'); ?>
                                </option>
                                <option value="automatic" <?php selected($config['type'], 'automatic'); ?>>
                                    <?php _e('Automatic (Condition Based)', 'wc-badges'); ?>
                                </option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr id="condition_row" style="<?php echo $config['type'] === 'manual' ? 'display:none;' : ''; ?>">
                        <th scope="row">
                            <label for="badge_condition"><?php _e('Condition', 'wc-badges'); ?></label>
                        </th>
                        <td>
                            <select id="badge_condition" name="badge_condition">
                                <option value=""><?php _e('Select condition', 'wc-badges'); ?></option>
                                <option value="new_product" <?php selected($config['condition'], 'new_product'); ?>><?php _e('New Product (30 days)', 'wc-badges'); ?></option>
                                <option value="on_sale" <?php selected($config['condition'], 'on_sale'); ?>><?php _e('On Sale', 'wc-badges'); ?></option>
                                <option value="out_of_stock" <?php selected($config['condition'], 'out_of_stock'); ?>><?php _e('Out of Stock', 'wc-badges'); ?></option>
                                <option value="low_stock" <?php selected($config['condition'], 'low_stock'); ?>><?php _e('Low Stock', 'wc-badges'); ?></option>
                                <option value="featured" <?php selected($config['condition'], 'featured'); ?>><?php _e('Featured', 'wc-badges'); ?></option>
                                <option value="high_rated" <?php selected($config['condition'], 'high_rated'); ?>><?php _e('High Rated', 'wc-badges'); ?></option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="badge_position"><?php _e('Position', 'wc-badges'); ?></label>
                        </th>
                        <td>
                            <select id="badge_position" name="badge_position">
                                <option value="top-left" <?php selected($config['position'], 'top-left'); ?>><?php _e('Top Left', 'wc-badges'); ?></option>
                                <option value="top-right" <?php selected($config['position'], 'top-right'); ?>><?php _e('Top Right', 'wc-badges'); ?></option>
                                <option value="bottom-left" <?php selected($config['position'], 'bottom-left'); ?>><?php _e('Bottom Left', 'wc-badges'); ?></option>
                                <option value="bottom-right" <?php selected($config['position'], 'bottom-right'); ?>><?php _e('Bottom Right', 'wc-badges'); ?></option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="badge_priority"><?php _e('Priority', 'wc-badges'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="badge_priority" name="badge_priority" value="<?php echo esc_attr($config['priority']); ?>" min="1" max="10" />
                            <p class="description"><?php _e('1 = highest priority', 'wc-badges'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="badge_status"><?php _e('Status', 'wc-badges'); ?></label>
                        </th>
                        <td>
                            <select id="badge_status" name="badge_status">
                                <option value="active" <?php selected($config['status'], 'active'); ?>><?php _e('Active', 'wc-badges'); ?></option>
                                <option value="inactive" <?php selected($config['status'], 'inactive'); ?>><?php _e('Inactive', 'wc-badges'); ?></option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <script>
        function toggleConditions(type) {
            document.getElementById('condition_row').style.display = type === 'automatic' ? 'table-row' : 'none';
        }
        </script>
        <?php
    }
    
    /**
     * Badge preview metabox
     */
    public function badge_preview_metabox($post) {
        $config = $this->get_badge_config($post->ID);
        ?>
        <div id="badge-preview-container" style="text-align: center; padding: 20px;">
            <div id="badge-preview" 
                 style="display: inline-block; 
                        padding: 6px 12px; 
                        border-radius: 4px; 
                        font-size: 12px; 
                        font-weight: bold; 
                        background-color: <?php echo esc_attr($config['bg_color']); ?>; 
                        color: <?php echo esc_attr($config['text_color']); ?>;">
                <?php echo esc_html($config['text'] ?: __('Badge Preview', 'wc-badges')); ?>
            </div>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const preview = document.getElementById('badge-preview');
            const textField = document.getElementById('badge_text');
            const bgField = document.getElementById('badge_bg_color');
            const textColorField = document.getElementById('badge_text_color');
            
            function updatePreview() {
                if (preview) {
                    preview.textContent = textField.value || '<?php _e('Badge Preview', 'wc-badges'); ?>';
                    preview.style.backgroundColor = bgField.value;
                    preview.style.color = textColorField.value;
                }
            }
            
            [textField, bgField, textColorField].forEach(field => {
                if (field) field.addEventListener('input', updatePreview);
                if (field) field.addEventListener('change', updatePreview);
            });
        });
        </script>
        <?php
    }
    
    /**
     * Product badges metabox
     */
    public function product_badges_metabox($post) {
        wp_nonce_field('save_product_badges', 'product_badges_nonce');
        
        $manual_badges = $this->get_manual_badges();
        $selected_badges = get_post_meta($post->ID, '_selected_badges', true) ?: array();
        
        if (!empty($manual_badges)) {
            echo '<div class="badges-selection">';
            foreach ($manual_badges as $badge) {
                $config = $this->get_badge_config($badge->ID);
                $checked = in_array($badge->ID, $selected_badges) ? 'checked' : '';
                
                printf(
                    '<label style="display: block; margin-bottom: 8px;">
                        <input type="checkbox" name="selected_badges[]" value="%d" %s />
                        <span style="display: inline-block; margin-left: 5px; padding: 2px 6px; font-size: 11px; border-radius: 2px; background-color: %s; color: %s;">%s</span>
                    </label>',
                    $badge->ID,
                    $checked,
                    esc_attr($config['bg_color']),
                    esc_attr($config['text_color']),
                    esc_html($config['text'])
                );
            }
            echo '</div>';
        } else {
            echo '<p>' . __('No manual badges available.', 'wc-badges') . '</p>';
            echo '<a href="' . admin_url('post-new.php?post_type=product_badge') . '" class="button">' . __('Create Badge', 'wc-badges') . '</a>';
        }
        
        // Show auto badges info
        $auto_badges = $this->get_product_auto_badges($post->ID);
        if (!empty($auto_badges)) {
            echo '<hr><p><strong>' . __('Auto Badges:', 'wc-badges') . '</strong></p>';
            foreach ($auto_badges as $badge) {
                printf(
                    '<span style="display: inline-block; margin: 2px; padding: 2px 6px; font-size: 11px; border-radius: 2px; background-color: %s; color: %s;">%s</span>',
                    esc_attr($badge['bg_color']),
                    esc_attr($badge['text_color']),
                    esc_html($badge['text'])
                );
            }
        }
    }
    
    /**
     * Save badge data
     */
    public function save_badge_data($post_id) {
        // Save badge config
        if (isset($_POST['badge_config_nonce']) && wp_verify_nonce($_POST['badge_config_nonce'], 'save_badge_config')) {
            if (get_post_type($post_id) === 'product_badge') {
                $this->save_badge_config($post_id);
            }
        }
        
        // Save product badges
        if (isset($_POST['product_badges_nonce']) && wp_verify_nonce($_POST['product_badges_nonce'], 'save_product_badges')) {
            if (get_post_type($post_id) === 'product') {
                $this->save_product_badges($post_id);
            }
        }
    }
    
    /**
     * Save badge configuration
     */
    private function save_badge_config($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;
        
        $fields = array(
            'badge_text' => 'text',
            'badge_bg_color' => 'text',
            'badge_text_color' => 'text',
            'badge_type' => 'text',
            'badge_condition' => 'text',
            'badge_position' => 'text',
            'badge_priority' => 'int',
            'badge_status' => 'text'
        );
        
        foreach ($fields as $field => $type) {
            if (isset($_POST[$field])) {
                $value = $type === 'int' ? intval($_POST[$field]) : sanitize_text_field($_POST[$field]);
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
    }
    
    /**
     * Save product badges
     */
    private function save_product_badges($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;
        
        $selected = isset($_POST['selected_badges']) ? array_map('intval', $_POST['selected_badges']) : array();
        update_post_meta($post_id, '_selected_badges', $selected);
    }
    
    /**
     * Get badge configuration with defaults
     */
    private function get_badge_config($badge_id) {
        return array(
            'text' => get_post_meta($badge_id, '_badge_text', true),
            'bg_color' => get_post_meta($badge_id, '_badge_bg_color', true) ?: '#e74c3c',
            'text_color' => get_post_meta($badge_id, '_badge_text_color', true) ?: '#ffffff',
            'type' => get_post_meta($badge_id, '_badge_type', true) ?: 'manual',
            'condition' => get_post_meta($badge_id, '_badge_condition', true),
            'position' => get_post_meta($badge_id, '_badge_position', true) ?: 'top-right',
            'priority' => get_post_meta($badge_id, '_badge_priority', true) ?: 1,
            'status' => get_post_meta($badge_id, '_badge_status', true) ?: 'active'
        );
    }
    
    /**
     * Get manual badges
     */
    private function get_manual_badges() {
        return get_posts(array(
            'post_type' => 'product_badge',
            'post_status' => 'publish',
            'numberposts' => -1,
            'meta_query' => array(
                array('key' => '_badge_type', 'value' => 'manual'),
                array('key' => '_badge_status', 'value' => 'active')
            ),
            'orderby' => 'meta_value_num',
            'meta_key' => '_badge_priority'
        ));
    }
    
    /**
     * Get automatic badges for product
     */
    private function get_product_auto_badges($product_id) {
        $product = wc_get_product($product_id);
        if (!$product) return array();
        
        $auto_badges = get_posts(array(
            'post_type' => 'product_badge',
            'post_status' => 'publish',
            'numberposts' => -1,
            'meta_query' => array(
                array('key' => '_badge_type', 'value' => 'automatic'),
                array('key' => '_badge_status', 'value' => 'active')
            )
        ));
        
        $badges = array();
        foreach ($auto_badges as $badge) {
            $config = $this->get_badge_config($badge->ID);
            if ($this->check_condition($product, $config['condition'])) {
                $badges[] = array(
                    'text' => $config['text'],
                    'bg_color' => $config['bg_color'],
                    'text_color' => $config['text_color'],
                    'position' => $config['position'],
                    'priority' => $config['priority']
                );
            }
        }
        
        return $badges;
    }
    
    /**
     * Check automatic condition
     */
    private function check_condition($product, $condition) {
        switch ($condition) {
            case 'new_product':
                $date = get_the_date('Y-m-d', $product->get_id());
                return (time() - strtotime($date)) < (30 * 24 * 60 * 60);
            case 'on_sale':
                return $product->is_on_sale();
            case 'out_of_stock':
                return !$product->is_in_stock();
            case 'low_stock':
                $stock = $product->get_stock_quantity();
                return $stock && $stock <= 5 && $stock > 0;
            case 'featured':
                return $product->is_featured();
            case 'high_rated':
                return $product->get_average_rating() > 4;
            default:
                return false;
        }
    }
    
    /**
     * Get product badges for display
     */
    public function get_product_badges($product_id) {
        $badges = array();
        
        // Manual badges
        $selected = get_post_meta($product_id, '_selected_badges', true) ?: array();
        foreach ($selected as $badge_id) {
            $config = $this->get_badge_config($badge_id);
            if ($config['status'] === 'active') {
                $badges[] = $config;
            }
        }
        
        // Auto badges
        $auto_badges = $this->get_product_auto_badges($product_id);
        $badges = array_merge($badges, $auto_badges);
        
        // Sort by priority
        usort($badges, function($a, $b) {
            return intval($a['priority']) - intval($b['priority']);
        });
        
        return $badges;
    }
    
    /**
     * Admin columns
     */
    public function badge_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = $columns['title'];
        $new_columns['badge_preview'] = __('Preview', 'wc-badges');
        $new_columns['badge_type'] = __('Type', 'wc-badges');
        $new_columns['badge_status'] = __('Status', 'wc-badges');
        $new_columns['date'] = $columns['date'];
        return $new_columns;
    }
    
    /**
     * Admin column content
     */
    public function badge_column_content($column, $post_id) {
        $config = $this->get_badge_config($post_id);
        
        switch ($column) {
            case 'badge_preview':
                printf(
                    '<span style="padding: 4px 8px; border-radius: 3px; font-size: 11px; background-color: %s; color: %s;">%s</span>',
                    esc_attr($config['bg_color']),
                    esc_attr($config['text_color']),
                    esc_html($config['text'])
                );
                break;
            case 'badge_type':
                echo ucfirst($config['type']);
                break;
            case 'badge_status':
                $status = $config['status'];
                $color = $status === 'active' ? 'green' : 'red';
                printf('<span style="color: %s;">%s</span>', $color, ucfirst($status));
                break;
        }
    }
    
    /**
     * Admin scripts
     */
    public function admin_scripts($hook) {
        global $post_type;
        if (($hook === 'post.php' || $hook === 'post-new.php') && 
            ($post_type === 'product_badge' || $post_type === 'product')) {
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_style('wp-color-picker');
        }
    }
}

// Helper function to get badges (for templates)
function get_wc_product_badges($product_id) {
    return WC_Product_Badges_System::get_instance()->get_product_badges($product_id);
}