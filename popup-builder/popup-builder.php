<?php
/**
 * Advanced Popup Builder System for WooCommerce
 * Version 2.0 - Complete Rewrite
 */

// Αποτροπή άμεσης πρόσβασης
if (!defined('ABSPATH')) {
    exit;
}

// Έλεγχος αν το WooCommerce είναι ενεργό
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    return;
}

class WC_Advanced_Popup_Builder {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_action('wp_footer', array($this, 'display_active_popups'));
        add_action('wp_ajax_save_popup_campaign', array($this, 'save_popup_campaign'));
        add_action('wp_ajax_delete_popup_campaign', array($this, 'delete_popup_campaign'));
        add_action('wp_ajax_toggle_popup_status', array($this, 'toggle_popup_status'));
        add_action('wp_ajax_get_popup_analytics', array($this, 'get_popup_analytics'));
    }
    
    public function init() {
        $this->register_post_type();
        $this->create_database_tables();
    }
    
    /**
     * Εγγραφή Custom Post Type για Popup Campaigns
     */
    public function register_post_type() {
        $args = array(
            'label' => 'Popup Campaigns',
            'labels' => array(
                'name' => 'Popup Campaigns',
                'singular_name' => 'Popup Campaign',
                'add_new' => 'Add New Popup',
                'add_new_item' => 'Add New Popup Campaign',
                'edit_item' => 'Edit Popup Campaign',
                'new_item' => 'New Popup Campaign',
                'view_item' => 'View Popup',
                'search_items' => 'Search Popups',
                'not_found' => 'No popups found',
                'not_found_in_trash' => 'No popups found in trash'
            ),
            'public' => false,
            'show_ui' => false, // Θα χρησιμοποιήσουμε custom interface
            'show_in_menu' => false,
            'capability_type' => 'post',
            'hierarchical' => false,
            'supports' => array('title'),
            'has_archive' => false,
            'rewrite' => false,
            'query_var' => false
        );
        
        register_post_type('popup_campaign', $args);
    }
    
    /**
     * Δημιουργία πινάκων για analytics
     */
    public function create_database_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'popup_analytics';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            popup_id bigint(20) NOT NULL,
            event_type varchar(20) NOT NULL,
            user_ip varchar(45) DEFAULT '',
            user_agent text DEFAULT '',
            page_url text DEFAULT '',
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            session_id varchar(100) DEFAULT '',
            user_id bigint(20) DEFAULT 0,
            conversion_value decimal(10,2) DEFAULT 0,
            PRIMARY KEY (id),
            KEY popup_id (popup_id),
            KEY event_type (event_type),
            KEY timestamp (timestamp)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Προσθήκη Admin Menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            'Popup Builder',
            'Popup Builder',
            'manage_options',
            'popup-builder',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Enqueue Admin Scripts & Styles
     */
    public function admin_scripts($hook) {
        if ($hook !== 'woocommerce_page_popup-builder') return;
        
        wp_enqueue_media();
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');
        
        // Vue.js για reactive interface
        wp_enqueue_script('vue-js', 'https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.14/vue.min.js', array(), '2.6.14', true);
        
        // Custom Scripts
        wp_enqueue_script('popup-builder-admin', plugin_dir_url(__FILE__) . 'admin.js', array('jquery', 'vue-js'), '1.0', true);
        wp_enqueue_style('popup-builder-admin', plugin_dir_url(__FILE__) . 'admin.css', array(), '1.0');
        
        // Localize script
        wp_localize_script('popup-builder-admin', 'popupBuilder', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('popup_builder_nonce'),
            'mediaUploadTitle' => 'Select Image/Video',
            'mediaUploadButton' => 'Use This Media'
        ));
    }
    
    /**
     * Main Admin Page
     */
    public function admin_page() {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        $popup_id = isset($_GET['popup_id']) ? intval($_GET['popup_id']) : 0;
        
        switch ($action) {
            case 'edit':
            case 'new':
                $this->render_popup_editor($popup_id);
                break;
            case 'analytics':
                $this->render_analytics_page($popup_id);
                break;
            default:
                $this->render_popup_list();
                break;
        }
    }
    
    /**
     * Render Popup List Page
     */
    private function render_popup_list() {
        $popups = $this->get_all_popups();
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Popup Builder</h1>
            <a href="<?php echo admin_url('admin.php?page=popup-builder&action=new'); ?>" class="page-title-action">Add New Popup</a>
            
            <div class="popup-stats-overview">
                <?php $this->render_stats_overview(); ?>
            </div>
            
            <div class="popup-campaigns-table">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 40px;">Status</th>
                            <th>Campaign Name</th>
                            <th>Type</th>
                            <th>Display On</th>
                            <th style="width: 100px;">Views</th>
                            <th style="width: 100px;">Clicks</th>
                            <th style="width: 80px;">CVR</th>
                            <th>Schedule</th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($popups)): ?>
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 40px;">
                                    <div style="color: #666;">
                                        <h3>🎨 No popups created yet</h3>
                                        <p>Create your first popup campaign to start engaging your customers!</p>
                                        <a href="<?php echo admin_url('admin.php?page=popup-builder&action=new'); ?>" class="button button-primary button-large">Create First Popup</a>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($popups as $popup): 
                                $meta = $this->get_popup_meta($popup->ID);
                                $analytics = $this->get_popup_analytics_summary($popup->ID);
                                $status = get_post_meta($popup->ID, '_popup_status', true) ?: 'draft';
                                ?>
                                <tr data-popup-id="<?php echo $popup->ID; ?>">
                                    <td>
                                        <div class="popup-status-toggle">
                                            <label class="switch">
                                                <input type="checkbox" <?php checked($status, 'active'); ?> 
                                                       onchange="togglePopupStatus(<?php echo $popup->ID; ?>)">
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?php echo esc_html($popup->post_title); ?></strong>
                                        <div class="row-actions">
                                            <span class="edit">
                                                <a href="<?php echo admin_url('admin.php?page=popup-builder&action=edit&popup_id=' . $popup->ID); ?>">Edit</a> |
                                            </span>
                                            <span class="duplicate">
                                                <a href="#" onclick="duplicatePopup(<?php echo $popup->ID; ?>)">Duplicate</a> |
                                            </span>
                                            <span class="analytics">
                                                <a href="<?php echo admin_url('admin.php?page=popup-builder&action=analytics&popup_id=' . $popup->ID); ?>">Analytics</a> |
                                            </span>
                                            <span class="delete">
                                                <a href="#" onclick="deletePopup(<?php echo $popup->ID; ?>)" style="color: #a00;">Delete</a>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php 
                                        $type = $meta['popup_type'] ?? 'custom';
                                        $type_icons = array(
                                            'discount' => '💰',
                                            'video' => '🎬',
                                            'newsletter' => '📧',
                                            'announcement' => '📢',
                                            'exit_intent' => '🚪',
                                            'custom' => '⚙️'
                                        );
                                        echo $type_icons[$type] . ' ' . ucfirst(str_replace('_', ' ', $type));
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $pages = $meta['display_pages'] ?? array();
                                        if (in_array('all', $pages)) {
                                            echo '🌐 All Pages';
                                        } else {
                                            echo implode(', ', array_slice($pages, 0, 2));
                                            if (count($pages) > 2) echo ' +' . (count($pages) - 2);
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo number_format($analytics['views']); ?></td>
                                    <td><?php echo number_format($analytics['clicks']); ?></td>
                                    <td>
                                        <?php 
                                        $cvr = $analytics['views'] > 0 ? ($analytics['clicks'] / $analytics['views']) * 100 : 0;
                                        echo number_format($cvr, 1) . '%';
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $schedule = $meta['schedule'] ?? array();
                                        if (!empty($schedule['start_date'])) {
                                            echo date('M j', strtotime($schedule['start_date']));
                                            if (!empty($schedule['end_date'])) {
                                                echo ' - ' . date('M j', strtotime($schedule['end_date']));
                                            }
                                        } else {
                                            echo 'Always active';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo admin_url('admin.php?page=popup-builder&action=edit&popup_id=' . $popup->ID); ?>" class="button button-small">Edit</a>
                                        <a href="#" onclick="previewPopup(<?php echo $popup->ID; ?>)" class="button button-small">Preview</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <style>
        .popup-stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .stat-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 28px;
            font-weight: bold;
            color: #2271b1;
        }
        
        .stat-label {
            color: #666;
            margin-top: 5px;
        }
        
        .switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: #2271b1;
        }
        
        input:checked + .slider:before {
            transform: translateX(20px);
        }
        </style>
        
        <script>
        function togglePopupStatus(popupId) {
            jQuery.post(ajaxurl, {
                action: 'toggle_popup_status',
                popup_id: popupId,
                nonce: '<?php echo wp_create_nonce('popup_builder_nonce'); ?>'
            }, function(response) {
                if (response.success) {
                    console.log('Status updated');
                } else {
                    alert('Error updating status');
                }
            });
        }
        
        function deletePopup(popupId) {
            if (confirm('Are you sure you want to delete this popup?')) {
                jQuery.post(ajaxurl, {
                    action: 'delete_popup_campaign',
                    popup_id: popupId,
                    nonce: '<?php echo wp_create_nonce('popup_builder_nonce'); ?>'
                }, function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error deleting popup');
                    }
                });
            }
        }
        
        function previewPopup(popupId) {
            // Άνοιγμα σε νέο tab για preview
            window.open('<?php echo home_url('/?popup_preview='); ?>' + popupId, '_blank');
        }
        
        function duplicatePopup(popupId) {
            jQuery.post(ajaxurl, {
                action: 'duplicate_popup_campaign',
                popup_id: popupId,
                nonce: '<?php echo wp_create_nonce('popup_builder_nonce'); ?>'
            }, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error duplicating popup');
                }
            });
        }
        </script>
        <?php
    }
    
    /**
     * Render Stats Overview
     */
    private function render_stats_overview() {
        $total_popups = wp_count_posts('popup_campaign')->publish;
        $active_popups = $this->count_active_popups();
        $total_analytics = $this->get_total_analytics();
        
        ?>
        <div class="stat-card">
            <div class="stat-number"><?php echo $total_popups; ?></div>
            <div class="stat-label">Total Popups</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $active_popups; ?></div>
            <div class="stat-label">Active Popups</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo number_format($total_analytics['views']); ?></div>
            <div class="stat-label">Total Views (30d)</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo number_format($total_analytics['clicks']); ?></div>
            <div class="stat-label">Total Clicks (30d)</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">
                <?php 
                $cvr = $total_analytics['views'] > 0 ? ($total_analytics['clicks'] / $total_analytics['views']) * 100 : 0;
                echo number_format($cvr, 1) . '%';
                ?>
            </div>
            <div class="stat-label">Average CVR</div>
        </div>
        <?php
    }
    
    /**
     * Helper Functions
     */
    private function get_all_popups() {
        return get_posts(array(
            'post_type' => 'popup_campaign',
            'post_status' => array('publish', 'draft'),
            'numberposts' => -1,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
    }
    
    private function get_popup_meta($popup_id) {
        $meta = get_post_meta($popup_id, '_popup_settings', true);
        return is_array($meta) ? $meta : array();
    }
    
    private function count_active_popups() {
        global $wpdb;
        return $wpdb->get_var("
            SELECT COUNT(*) 
            FROM {$wpdb->posts} p 
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
            WHERE p.post_type = 'popup_campaign' 
            AND pm.meta_key = '_popup_status' 
            AND pm.meta_value = 'active'
        ");
    }
    
    private function get_total_analytics() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'popup_analytics';
        
        $results = $wpdb->get_results("
            SELECT 
                SUM(CASE WHEN event_type = 'view' THEN 1 ELSE 0 END) as views,
                SUM(CASE WHEN event_type = 'click' THEN 1 ELSE 0 END) as clicks
            FROM {$table_name} 
            WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        
        return array(
            'views' => $results[0]->views ?: 0,
            'clicks' => $results[0]->clicks ?: 0
        );
    }
    
    private function get_popup_analytics_summary($popup_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'popup_analytics';
        
        $results = $wpdb->get_results($wpdb->prepare("
            SELECT 
                SUM(CASE WHEN event_type = 'view' THEN 1 ELSE 0 END) as views,
                SUM(CASE WHEN event_type = 'click' THEN 1 ELSE 0 END) as clicks
            FROM {$table_name} 
            WHERE popup_id = %d 
            AND timestamp >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ", $popup_id));
        
        return array(
            'views' => $results[0]->views ?: 0,
            'clicks' => $results[0]->clicks ?: 0
        );
    }
    
    /**
     * AJAX Handlers
     */
    public function toggle_popup_status() {
        check_ajax_referer('popup_builder_nonce', 'nonce');
        
        $popup_id = intval($_POST['popup_id']);
        $current_status = get_post_meta($popup_id, '_popup_status', true);
        $new_status = ($current_status === 'active') ? 'inactive' : 'active';
        
        update_post_meta($popup_id, '_popup_status', $new_status);
        
        wp_send_json_success(array('new_status' => $new_status));
    }
    
    public function delete_popup_campaign() {
        check_ajax_referer('popup_builder_nonce', 'nonce');
        
        $popup_id = intval($_POST['popup_id']);
        
        // Διαγραφή analytics data
        global $wpdb;
        $table_name = $wpdb->prefix . 'popup_analytics';
        $wpdb->delete($table_name, array('popup_id' => $popup_id));
        
        // Διαγραφή post
        wp_delete_post($popup_id, true);
        
        wp_send_json_success();
    }
    
    /**
     * Display Active Popups στο Frontend
     */
    public function display_active_popups() {
        if (is_admin()) return;
        
        $active_popups = $this->get_active_popups_for_current_page();
        
        if (empty($active_popups)) return;
        
        foreach ($active_popups as $popup) {
            $this->render_frontend_popup($popup);
        }
        
        $this->enqueue_frontend_scripts();
    }
    
    private function get_active_popups_for_current_page() {
        // TODO: Θα υλοποιηθεί στο επόμενο κομμάτι
        return array();
    }
    
    private function render_frontend_popup($popup) {
        // TODO: Θα υλοποιηθεί στο επόμενο κομμάτι
    }
    
    private function enqueue_frontend_scripts() {
        // TODO: Θα υλοποιηθεί στο επόμενο κομμάτι
    }
}

// Initialize the plugin
function init_popup_builder() {
    return WC_Advanced_Popup_Builder::get_instance();
}

// Start the plugin - Αμέσως, όχι στο plugins_loaded
init_popup_builder();

?>