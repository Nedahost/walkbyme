<?php
/**
 * WooCommerce Product Badges System
 * Professional badge management system for WooCommerce products
 */

if (!defined('ABSPATH')) {
    exit;
}

class WC_Product_Badges_System {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        $this->init();
    }
    
    private function init() {
        // 1. Admin Hooks
        add_action('init', array($this, 'create_badge_post_type'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_badge_data'));
        
        // Admin Columns
        add_filter('manage_product_badge_posts_columns', array($this, 'badge_columns'));
        add_action('manage_product_badge_posts_custom_column', array($this, 'badge_column_content'), 10, 2);
        
        // Clear Cache when badges change
        add_action('save_post_product_badge', array($this, 'clear_badge_cache'));
        
        // 2. Frontend Hooks (Display Badges automatically)
        // Show on Loop (Category pages)
        add_action('woocommerce_before_shop_loop_item_title', array($this, 'render_badges_on_loop'), 10);
        // Show on Single Product Image
        add_action('woocommerce_product_thumbnails', array($this, 'render_badges_on_single'), 5);
    }

    /**
     * Cache Clearing
     */
    public function clear_badge_cache() {
        delete_transient('walkbyme_auto_badges_list');
        delete_transient('walkbyme_manual_badges_list');
    }
    
    /**
     * Create Post Type
     */
    public function create_badge_post_type() {
        register_post_type('product_badge', array(
            'labels' => array(
                'name' => __('Product Badges', 'walkbyme'),
                'singular_name' => __('Product Badge', 'walkbyme'),
                'add_new' => __('Add New Badge', 'walkbyme'),
                'menu_name' => __('Badges', 'walkbyme')
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'edit.php?post_type=product',
            'capability_type' => 'post',
            'supports' => array('title'),
            'menu_icon' => 'dashicons-awards',
            'exclude_from_search' => true
        ));
    }
    
    /**
     * Meta Boxes
     */
    public function add_meta_boxes() {
        add_meta_box('badge_config', __('Badge Configuration', 'walkbyme'), array($this, 'badge_config_metabox'), 'product_badge', 'normal', 'high');
        add_meta_box('badge_preview', __('Preview', 'walkbyme'), array($this, 'badge_preview_metabox'), 'product_badge', 'side', 'default');
        add_meta_box('product_badges', __('Product Badges', 'walkbyme'), array($this, 'product_badges_metabox'), 'product', 'side', 'default');
    }
    
    /**
     * Config Metabox HTML
     */
    public function badge_config_metabox($post) {
        wp_nonce_field('save_badge_config', 'badge_config_nonce');
        $config = $this->get_badge_config($post->ID);
        ?>
        <div class="badge-config-wrapper">
            <style>.form-table th { width: 150px; }</style>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><label for="badge_text"><?php _e('Badge Text', 'walkbyme'); ?></label></th>
                        <td><input type="text" id="badge_text" name="badge_text" value="<?php echo esc_attr($config['text']); ?>" class="regular-text" required /></td>
                    </tr>
                    <tr>
                        <th><?php _e('Colors', 'walkbyme'); ?></th>
                        <td>
                            <div style="display:flex; gap:20px;">
                                <div><label><?php _e('Background:', 'walkbyme'); ?></label><br><input type="color" id="badge_bg_color" name="badge_bg_color" value="<?php echo esc_attr($config['bg_color']); ?>" /></div>
                                <div><label><?php _e('Text:', 'walkbyme'); ?></label><br><input type="color" id="badge_text_color" name="badge_text_color" value="<?php echo esc_attr($config['text_color']); ?>" /></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="badge_type"><?php _e('Badge Type', 'walkbyme'); ?></label></th>
                        <td>
                            <select id="badge_type" name="badge_type" onchange="toggleConditions(this.value)">
                                <option value="manual" <?php selected($config['type'], 'manual'); ?>><?php _e('Manual Selection', 'walkbyme'); ?></option>
                                <option value="automatic" <?php selected($config['type'], 'automatic'); ?>><?php _e('Automatic (Condition Based)', 'walkbyme'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr id="condition_row" style="<?php echo $config['type'] === 'manual' ? 'display:none;' : ''; ?>">
                        <th><label for="badge_condition"><?php _e('Condition', 'walkbyme'); ?></label></th>
                        <td>
                            <select id="badge_condition" name="badge_condition">
                                <option value=""><?php _e('Select condition', 'walkbyme'); ?></option>
                                <option value="new_product" <?php selected($config['condition'], 'new_product'); ?>><?php _e('New Product (30 days)', 'walkbyme'); ?></option>
                                <option value="on_sale" <?php selected($config['condition'], 'on_sale'); ?>><?php _e('On Sale', 'walkbyme'); ?></option>
                                <option value="out_of_stock" <?php selected($config['condition'], 'out_of_stock'); ?>><?php _e('Out of Stock', 'walkbyme'); ?></option>
                                <option value="low_stock" <?php selected($config['condition'], 'low_stock'); ?>><?php _e('Low Stock (< 5)', 'walkbyme'); ?></option>
                                <option value="featured" <?php selected($config['condition'], 'featured'); ?>><?php _e('Featured', 'walkbyme'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="badge_position"><?php _e('Position', 'walkbyme'); ?></label></th>
                        <td>
                            <select id="badge_position" name="badge_position">
                                <option value="top-left" <?php selected($config['position'], 'top-left'); ?>><?php _e('Top Left', 'walkbyme'); ?></option>
                                <option value="top-right" <?php selected($config['position'], 'top-right'); ?>><?php _e('Top Right', 'walkbyme'); ?></option>
                                <option value="bottom-left" <?php selected($config['position'], 'bottom-left'); ?>><?php _e('Bottom Left', 'walkbyme'); ?></option>
                                <option value="bottom-right" <?php selected($config['position'], 'bottom-right'); ?>><?php _e('Bottom Right', 'walkbyme'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="badge_priority"><?php _e('Priority', 'walkbyme'); ?></label></th>
                        <td><input type="number" id="badge_priority" name="badge_priority" value="<?php echo esc_attr($config['priority']); ?>" min="1" max="10" /></td>
                    </tr>
                    <tr>
                        <th><label for="badge_status"><?php _e('Status', 'walkbyme'); ?></label></th>
                        <td>
                            <select id="badge_status" name="badge_status">
                                <option value="active" <?php selected($config['status'], 'active'); ?>><?php _e('Active', 'walkbyme'); ?></option>
                                <option value="inactive" <?php selected($config['status'], 'inactive'); ?>><?php _e('Inactive', 'walkbyme'); ?></option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <script>function toggleConditions(t){document.getElementById('condition_row').style.display='automatic'===t?'table-row':'none';}</script>
        <?php
    }
    
    /**
     * Preview Metabox
     */
    public function badge_preview_metabox($post) {
        $config = $this->get_badge_config($post->ID);
        ?>
        <div style="text-align: center; padding: 20px; background: #f0f0f1; border-radius: 4px;">
            <div id="badge-preview" style="display: inline-block; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: bold; background-color: <?php echo esc_attr($config['bg_color']); ?>; color: <?php echo esc_attr($config['text_color']); ?>;">
                <?php echo esc_html($config['text'] ?: 'Badge Preview'); ?>
            </div>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded',function(){
            const p=document.getElementById('badge-preview'),t=document.getElementById('badge_text'),b=document.getElementById('badge_bg_color'),c=document.getElementById('badge_text_color');
            function u(){if(p){p.textContent=t.value||'Preview';p.style.backgroundColor=b.value;p.style.color=c.value;}}
            [t,b,c].forEach(f=>{if(f)f.addEventListener('input',u);});
        });
        </script>
        <?php
    }
    
    /**
     * Product Edit Page Metabox
     */
    public function product_badges_metabox($post) {
        wp_nonce_field('save_product_badges', 'product_badges_nonce');
        
        $manual_badges = $this->get_cached_manual_badges_list();
        $selected_badges = get_post_meta($post->ID, '_selected_badges', true) ?: array();
        
        if (!empty($manual_badges)) {
            echo '<div class="badges-selection" style="max-height: 200px; overflow-y: auto;">';
            foreach ($manual_badges as $badge) {
                $config = $this->get_badge_config($badge->ID);
                $checked = in_array($badge->ID, $selected_badges) ? 'checked' : '';
                printf(
                    '<label style="display:block;margin-bottom:8px;"><input type="checkbox" name="selected_badges[]" value="%d" %s /> <span style="padding:2px 6px;font-size:11px;border-radius:2px;background:%s;color:%s;">%s</span></label>',
                    $badge->ID, $checked, esc_attr($config['bg_color']), esc_attr($config['text_color']), esc_html($config['text'])
                );
            }
            echo '</div>';
        } else {
            echo '<p>' . __('No active manual badges found.', 'walkbyme') . '</p>';
        }
        
        // Show auto badges preview
        $auto_badges = $this->get_product_auto_badges($post->ID);
        if (!empty($auto_badges)) {
            echo '<hr><p><strong>' . __('Active Auto Badges:', 'walkbyme') . '</strong></p>';
            foreach ($auto_badges as $badge) {
                printf('<span style="margin-right:5px;padding:2px 6px;font-size:11px;background:%s;color:%s;">%s</span>', esc_attr($badge['bg_color']), esc_attr($badge['text_color']), esc_html($badge['text']));
            }
        }
    }
    
    /**
     * Save Logic
     */
    public function save_badge_data($post_id) {
        // Save Badge Config
        if (isset($_POST['badge_config_nonce']) && wp_verify_nonce($_POST['badge_config_nonce'], 'save_badge_config')) {
            $fields = array('badge_text', 'badge_bg_color', 'badge_text_color', 'badge_type', 'badge_condition', 'badge_position', 'badge_priority', 'badge_status');
            foreach ($fields as $field) {
                if (isset($_POST[$field])) update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
        // Save Product Selection
        if (isset($_POST['product_badges_nonce']) && wp_verify_nonce($_POST['product_badges_nonce'], 'save_product_badges')) {
            $selected = isset($_POST['selected_badges']) ? array_map('intval', $_POST['selected_badges']) : array();
            update_post_meta($post_id, '_selected_badges', $selected);
        }
    }
    
    /**
     * Get Config
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
     * Performance: Get Cached List of Manual Badges
     */
    private function get_cached_manual_badges_list() {
        $badges = get_transient('walkbyme_manual_badges_list');
        if (false === $badges) {
            $badges = get_posts(array(
                'post_type' => 'product_badge',
                'post_status' => 'publish',
                'numberposts' => -1,
                'meta_query' => array(
                    array('key' => '_badge_type', 'value' => 'manual'),
                    array('key' => '_badge_status', 'value' => 'active')
                )
            ));
            set_transient('walkbyme_manual_badges_list', $badges, 12 * HOUR_IN_SECONDS);
        }
        return $badges;
    }

    /**
     * Performance: Get Cached List of Auto Badges Definitions
     */
    private function get_cached_auto_badges_definitions() {
        $badges = get_transient('walkbyme_auto_badges_list');
        if (false === $badges) {
            $badges = get_posts(array(
                'post_type' => 'product_badge',
                'post_status' => 'publish',
                'numberposts' => -1,
                'meta_query' => array(
                    array('key' => '_badge_type', 'value' => 'automatic'),
                    array('key' => '_badge_status', 'value' => 'active')
                )
            ));
            set_transient('walkbyme_auto_badges_list', $badges, 12 * HOUR_IN_SECONDS);
        }
        return $badges;
    }
    
    /**
     * Resolve Auto Badges for a Product
     */
    private function get_product_auto_badges($product_id) {
        $product = wc_get_product($product_id);
        if (!$product) return array();
        
        $definitions = $this->get_cached_auto_badges_definitions();
        $badges = array();
        
        foreach ($definitions as $badge_post) {
            $config = $this->get_badge_config($badge_post->ID);
            if ($this->check_condition($product, $config['condition'])) {
                $badges[] = $config;
            }
        }
        return $badges;
    }
    
    private function check_condition($product, $condition) {
        switch ($condition) {
            case 'new_product':
                $date = $product->get_date_created();
                if (!$date) return false;
                return (time() - $date->getTimestamp()) < (30 * DAY_IN_SECONDS);
            case 'on_sale': return $product->is_on_sale();
            case 'out_of_stock': return !$product->is_in_stock();
            case 'low_stock': 
                $stock = $product->get_stock_quantity();
                return $stock !== null && $stock <= 5 && $stock > 0;
            case 'featured': return $product->is_featured();
            default: return false;
        }
    }
    
    /**
     * Get ALL Badges for a product (Sorted)
     */
    public function get_product_badges($product_id) {
        $badges = array();
        
        // 1. Get Selected Manual Badges
        $selected = get_post_meta($product_id, '_selected_badges', true) ?: array();
        foreach ($selected as $badge_id) {
            $config = $this->get_badge_config($badge_id);
            if ($config['status'] === 'active') {
                $badges[] = $config;
            }
        }
        
        // 2. Get Auto Badges
        $auto_badges = $this->get_product_auto_badges($product_id);
        $badges = array_merge($badges, $auto_badges);
        
        // 3. Sort by priority
        usort($badges, function($a, $b) {
            return intval($a['priority']) - intval($b['priority']);
        });
        
        return $badges;
    }
    
    /**
     * FRONTEND: Render Logic
     */
    public function render_badges_html($product_id) {
        $badges = $this->get_product_badges($product_id);
        if (empty($badges)) return;
        
        echo '<div class="walkbyme-product-badges-container">';
        foreach ($badges as $badge) {
            printf(
                '<span class="walkbyme-badge badge-%s" style="background-color:%s; color:%s;">%s</span>',
                esc_attr($badge['position']),
                esc_attr($badge['bg_color']),
                esc_attr($badge['text_color']),
                esc_html($badge['text'])
            );
        }
        echo '</div>';
        
        // Inline CSS for positioning
        echo '<style>
            .walkbyme-product-badges-container { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 10; }
            .walkbyme-badge { position: absolute; padding: 4px 10px; font-size: 11px; font-weight: bold; border-radius: 3px; text-transform: uppercase; line-height: 1; z-index: 20; }
            .badge-top-left { top: 10px; left: 10px; }
            .badge-top-right { top: 10px; right: 10px; }
            .badge-bottom-left { bottom: 10px; left: 10px; }
            .badge-bottom-right { bottom: 10px; right: 10px; }
        </style>';
    }

    // Hook Callbacks
    public function render_badges_on_loop() {
        global $product;
        if ($product) $this->render_badges_html($product->get_id());
    }
    
    public function render_badges_on_single() {
        global $product;
        if ($product) $this->render_badges_html($product->get_id());
    }

    /**
     * Admin Columns
     */
    public function badge_columns($columns) {
        $new = array('cb' => $columns['cb'], 'title' => $columns['title'], 'badge_preview' => 'Preview', 'badge_type' => 'Type', 'badge_status' => 'Status');
        return $new;
    }
    
    public function badge_column_content($column, $post_id) {
        $config = $this->get_badge_config($post_id);
        switch ($column) {
            case 'badge_preview':
                printf('<span style="padding:3px 8px;border-radius:3px;font-size:10px;background:%s;color:%s;">%s</span>', $config['bg_color'], $config['text_color'], $config['text']);
                break;
            case 'badge_type': echo ucfirst($config['type']); break;
            case 'badge_status': echo $config['status'] === 'active' ? '<span style="color:green">Active</span>' : '<span style="color:red">Inactive</span>'; break;
        }
    }
}