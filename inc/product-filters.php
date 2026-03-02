<?php
/**
 * WalkByMe Product Filters
 * Ενιαίο αρχείο φίλτρων προϊόντων
 * CSS + JS + Logic όλα μαζί
 * v1.2 — Προσθήκη φίλτρου τιμής με presets και custom range
 */

if (!defined('ABSPATH')) {
    exit;
}

class NH_Product_Filters {
    private static $instance = null;

    // Προκαθορισμένα price ranges
    private $price_ranges = array(
        array('label' => 'Έως 30€',    'min' => 0,   'max' => 30),
        array('label' => '30€ - 60€',  'min' => 30,  'max' => 60),
        array('label' => '60€ - 100€', 'min' => 60,  'max' => 100),
        array('label' => '100€+',       'min' => 100, 'max' => 999999),
    );

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init();
    }

    public function init() {
        add_action('wp_head',   array($this, 'output_styles'));
        add_action('wp_footer', array($this, 'output_scripts'), 999);
        add_action('woocommerce_before_shop_loop', array($this, 'display_filters'), 10);
        add_action('pre_get_posts', array($this, 'filter_products_by_attributes'));
        add_action('admin_menu', array($this, 'add_settings_page'));
    }

    /**
     * Inline CSS
     */
    public function output_styles() {
        if (!is_shop() && !is_product_category() && !is_product_tag() && !is_search()) return;
        ?>
        <style>
        /* ── Wrapper ── */
        .product-filters {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }
        .product-filters > span {
            font-weight: bold;
            margin-right: 5px;
        }

        /* ── Attribute dropdowns ── */
        .filter-group {
            display: flex;
            align-items: center;
            position: relative;
        }
        .filter-group select {
            padding: 8px 30px 8px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #fff;
            font-size: 14px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            outline: none;
            cursor: pointer;
            min-width: 150px;
        }
        .filter-group select:focus { border-color: #999; }
        .filter-group::after {
            content: "";
            width: 12px;
            height: 12px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%23333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>') no-repeat center;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }
        .filter-group.selected::after { display: none; }
        .filter-group .clear-filter {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 12px;
            height: 12px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%23333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>') no-repeat center;
            cursor: pointer;
            display: none;
        }
        .filter-group.selected .clear-filter { display: block; }

        /* ── Price filter ── */
        .price-filter-wrap {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            width: 100%;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .price-filter-wrap > span {
            font-weight: bold;
            margin-right: 5px;
        }
        .price-presets {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
        }
        .price-preset {
            padding: 6px 14px;
            border: 1px solid #ddd;
            border-radius: 20px;
            background: #fff;
            font-size: 13px;
            cursor: pointer;
            transition: all .2s;
        }
        .price-preset:hover { border-color: #333; }
        .price-preset.active {
            background: #333;
            color: #fff;
            border-color: #333;
        }
        .price-count {
            font-size: 11px;
            opacity: 0.7;
            margin-left: 2px;
        }
        .clear-price-filter {
            font-size: 12px;
            color: #999;
            text-decoration: none;
            margin-left: 4px;
        }
        .clear-price-filter:hover { color: #c00; }


        /* ── Mobile ── */
        @media (max-width: 767px) {
            .product-filters { flex-direction: column; align-items: stretch; }
            .filter-group { width: 100%; }
            .filter-group select { width: 100%; }
            .price-filter-wrap { flex-direction: column; align-items: flex-start; }
            .price-custom { margin-left: 0; flex-wrap: wrap; }
            .filter-toggle {
                display: block;
                width: 100%;
                padding: 10px;
                background: #f5f5f5;
                border: 1px solid #ddd;
                text-align: center;
                font-weight: bold;
                cursor: pointer;
                margin-bottom: 10px;
            }
        }
        </style>
        <?php
    }

    /**
     * Inline JS
     */
    public function output_scripts() {
        if (!is_shop() && !is_product_category() && !is_product_tag() && !is_search()) return;
        ?>
        <script>
        jQuery(function($) {

            // ── Βοηθητική: Ενημέρωση URL parameters ──
            function updateUrlParameters(params) {
                var url = new URL(window.location.href);

                // Καθαρισμός παλιών φίλτρων
                Array.from(url.searchParams.keys()).forEach(function(key) {
                    if (key.startsWith('filter_') || key === 'min_price' || key === 'max_price') {
                        url.searchParams.delete(key);
                    }
                });

                // Προσθήκη νέων
                for (var k in params) {
                    if (params[k] !== '' && params[k] !== null) {
                        url.searchParams.set(k, params[k]);
                    }
                }
                return url.toString();
            }

            // ── Attribute filters ──
            function updateFilters(clearTaxonomy) {
                clearTaxonomy = clearTaxonomy || null;
                var params = {};

                $('.filter-select').each(function() {
                    var taxonomy = $(this).data('taxonomy');
                    var term = $(this).val();
                    if (term !== '' && taxonomy !== clearTaxonomy) {
                        params['filter_' + taxonomy] = term;
                    }
                });

                // Κράτα τα price params αν υπάρχουν
                var minPrice = '<?php echo isset($_GET['min_price']) ? intval($_GET['min_price']) : ''; ?>';
                var maxPrice = '<?php echo isset($_GET['max_price']) ? intval($_GET['max_price']) : ''; ?>';
                if (minPrice !== '') params['min_price'] = minPrice;
                if (maxPrice !== '') params['max_price'] = maxPrice;

                window.location.href = updateUrlParameters(params);
            }

            $(document).on('change', '.filter-select', function() {
                updateFilters();
            });

            $(document).on('click', '.clear-filter', function(e) {
                e.preventDefault();
                var taxonomy = $(this).data('taxonomy');
                updateFilters(taxonomy);
            });

            // ── Price presets ──
            $(document).on('click', '.price-preset', function() {
                var min = $(this).data('min');
                var max = $(this).data('max');
                var params = getCurrentAttributeParams();
                params['min_price'] = min;
                params['max_price'] = max;
                window.location.href = updateUrlParameters(params);
            });

            // ── Clear price ──
            $(document).on('click', '.clear-price-filter', function(e) {
                e.preventDefault();
                var params = getCurrentAttributeParams();
                window.location.href = updateUrlParameters(params);
            });

// ── Βοηθητική: παίρνει τα τρέχοντα attribute params ──
            function getCurrentAttributeParams() {
                var params = {};
                $('.filter-select').each(function() {
                    var taxonomy = $(this).data('taxonomy');
                    var term = $(this).val();
                    if (term !== '') params['filter_' + taxonomy] = term;
                });
                return params;
            }

            // ── Mobile collapse ──
            function setupCollapsibleFilters() {
                if ($(window).width() < 768) {
                    if (!$('.filter-toggle').length) {
                        $('<button class="filter-toggle">Φίλτρα</button>').insertBefore('.product-filters');
                        $('.product-filters').hide();
                    }
                } else {
                    $('.filter-toggle').remove();
                    $('.product-filters').show();
                }
            }

            $(window).on('resize', setupCollapsibleFilters);
            setupCollapsibleFilters();

            $(document).on('click', '.filter-toggle', function() {
                $('.product-filters').slideToggle();
            });
        });
        </script>
        <?php
    }

    /**
     * Εμφάνιση φίλτρων
     */
    public function display_filters() {
        $attribute_taxonomies = wc_get_attribute_taxonomies();
        $enabled_filters      = get_option('nh_product_filter_attributes', array());
        $current_filters      = $this->get_current_filters();
        $filtered_products    = $this->get_filtered_products($current_filters);

        if (!empty($attribute_taxonomies) && !empty($enabled_filters) && !empty($filtered_products)) {
            echo '<div class="product-filters">';
            echo '<span>Φίλτρα:</span>';

            foreach ($attribute_taxonomies as $tax) {
                $taxonomy = wc_attribute_taxonomy_name($tax->attribute_name);
                if (!in_array($tax->attribute_name, $enabled_filters)) continue;

                $terms = $this->get_used_terms($taxonomy, $filtered_products);
                if (empty($terms)) continue;

                $selected_term = isset($current_filters[$taxonomy]) ? $current_filters[$taxonomy] : '';

                echo '<div class="filter-group' . ($selected_term ? ' selected' : '') . '">';
                echo '<select class="filter-select" data-taxonomy="' . esc_attr($taxonomy) . '">';
                echo '<option value="">' . esc_html($tax->attribute_label) . '</option>';
                foreach ($terms as $term) {
                    echo '<option value="' . esc_attr($term->slug) . '" ' . selected($selected_term, $term->slug, false) . '>' . esc_html($term->name) . '</option>';
                }
                echo '</select>';
                if ($selected_term) {
                    echo '<a class="clear-filter" href="#" data-taxonomy="' . esc_attr($taxonomy) . '"></a>';
                }
                echo '</div>';
            }

            echo '</div>';

            // Price filter
            $this->display_price_filter();
        }
    }

    /**
     * Μετράει τα προϊόντα για κάθε price range στην τρέχουσα κατηγορία
     */
    private function count_products_in_range($min, $max, $current_filters) {
        $args = array(
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => array(
                array(
                    'key'     => '_price',
                    'value'   => $max >= 999999 ? $min : array($min, $max),
                    'compare' => $max >= 999999 ? '>=' : 'BETWEEN',
                    'type'    => 'NUMERIC',
                ),
            ),
        );

        // Φίλτρο κατηγορίας
        $tax_query = array('relation' => 'AND');

        if (is_product_category()) {
            $tax_query[] = array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => get_queried_object()->slug,
            );
        }
        if (is_product_tag()) {
            $tax_query[] = array(
                'taxonomy' => 'product_tag',
                'field'    => 'slug',
                'terms'    => get_queried_object()->slug,
            );
        }

        // Εφαρμογή attribute φίλτρων
        foreach ($current_filters as $taxonomy => $term_slug) {
            $tax_query[] = array(
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => $term_slug,
            );
        }

        if (count($tax_query) > 1) {
            $args['tax_query'] = $tax_query;
        }

        if (is_search()) {
            $args['s'] = get_search_query();
        }

        $query = new WP_Query($args);
        return $query->found_posts;
    }

    /**
     * Εμφάνιση φίλτρου τιμής με dynamic ranges
     */
    private function display_price_filter() {
        $current_min     = isset($_GET['min_price']) ? intval($_GET['min_price']) : '';
        $current_max     = isset($_GET['max_price']) ? intval($_GET['max_price']) : '';
        $current_filters = $this->get_current_filters();

        // Βρες αν κάποιο preset είναι ενεργό
        $active_preset = '';
        foreach ($this->price_ranges as $range) {
            if ($current_min === $range['min'] && $current_max === $range['max']) {
                $active_preset = $range['min'] . '_' . $range['max'];
            }
        }

        // Μέτρησε προϊόντα για κάθε range
        $ranges_with_counts = array();
        foreach ($this->price_ranges as $range) {
            $count = $this->count_products_in_range($range['min'], $range['max'], $current_filters);
            if ($count > 0) {
                $range['count'] = $count;
                $ranges_with_counts[] = $range;
            }
        }

        // Αν δεν υπάρχει κανένα range με προϊόντα, μην εμφανίσεις το φίλτρο
        if (empty($ranges_with_counts) && $current_min === '' && $current_max === '') return;
        ?>
        <div class="price-filter-wrap">
            <span><?php _e('Τιμή:', 'walkbyme'); ?></span>
            <div class="price-presets">
                <?php foreach ($ranges_with_counts as $range) :
                    $key = $range['min'] . '_' . $range['max'];
                    ?>
                    <button type="button"
                            class="price-preset<?php echo ($active_preset === $key) ? ' active' : ''; ?>"
                            data-min="<?php echo esc_attr($range['min']); ?>"
                            data-max="<?php echo esc_attr($range['max']); ?>">
                        <?php echo esc_html($range['label']); ?>
                        <span class="price-count">(<?php echo intval($range['count']); ?>)</span>
                    </button>
                <?php endforeach; ?>
                <?php if ($current_min !== '' || $current_max !== '') : ?>
                    <a href="#" class="clear-price-filter">&times; Καθαρισμός</a>
                <?php endif; ?>
            </div>

        </div>
        <?php
    }

    /**
     * Φιλτράρισμα μέσω WP_Query
     */
    public function filter_products_by_attributes($query) {
        if (!is_admin() && $query->is_main_query() && (is_shop() || is_product_taxonomy() || is_search())) {
            $current_filters = $this->get_current_filters();
            $tax_query = $query->get('tax_query') ?: array();

            foreach ($current_filters as $taxonomy => $term) {
                $tax_query[] = array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'    => $term,
                );
            }
            if (!empty($tax_query)) $query->set('tax_query', $tax_query);

            // Price filter
            $min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : '';
            $max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : '';

            if ($min_price !== '' || $max_price !== '') {
                $meta_query = $query->get('meta_query') ?: array();
                $price_query = array('key' => '_price', 'type' => 'NUMERIC');
                if ($min_price !== '') $price_query['value'][0] = $min_price;
                if ($max_price !== '' && $max_price < 999999) $price_query['value'][1] = $max_price;

                if (isset($price_query['value'][0]) && isset($price_query['value'][1])) {
                    $price_query['compare'] = 'BETWEEN';
                } elseif (isset($price_query['value'][0])) {
                    $price_query['value']   = $price_query['value'][0];
                    $price_query['compare'] = '>=';
                } elseif (isset($price_query['value'][1])) {
                    $price_query['value']   = $price_query['value'][1];
                    $price_query['compare'] = '<=';
                }

                $meta_query[] = $price_query;
                $query->set('meta_query', $meta_query);
            }
        }
    }

    /**
     * Παίρνει τα ενεργά φίλτρα από το URL
     */
    private function get_current_filters() {
        $filters = array();
        foreach ($_GET as $key => $value) {
            if (strpos($key, 'filter_') === 0) {
                $taxonomy = str_replace('filter_', '', $key);
                $filters[$taxonomy] = sanitize_text_field($value);
            }
        }
        return $filters;
    }

    /**
     * Επιστρέφει προϊόντα με WC_Product_Query
     */
    private function get_filtered_products($current_filters) {
        $args = array(
            'status' => 'publish',
            'limit'  => -1,
            'return' => 'ids',
        );

        if (is_product_category()) {
            $args['category'] = array(get_queried_object()->slug);
        }
        if (is_product_tag()) {
            $args['tag'] = array(get_queried_object()->slug);
        }
        if (is_search()) {
            $args['s'] = get_search_query();
        }

        if (!empty($current_filters)) {
            $tax_query = array('relation' => 'AND');
            foreach ($current_filters as $taxonomy => $term_slug) {
                $tax_query[] = array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'    => $term_slug,
                );
            }
            $args['tax_query'] = $tax_query;
        }

        return wc_get_products($args);
    }

    /**
     * Επιστρέφει μοναδικά terms
     */
    private function get_used_terms($taxonomy, $product_ids) {
        $terms        = wp_get_object_terms($product_ids, $taxonomy, array('fields' => 'all'));
        $unique_terms = array();
        foreach ($terms as $term) {
            if (!isset($unique_terms[$term->term_id])) {
                $unique_terms[$term->term_id] = $term;
            }
        }
        return $unique_terms;
    }

    /**
     * Admin settings
     */
    public function add_settings_page() {
        add_submenu_page(
            'woocommerce',
            'Φίλτρα Προϊόντων',
            'Φίλτρα Προϊόντων',
            'manage_options',
            'nh-product-filters',
            array($this, 'render_settings_page')
        );
    }

    public function render_settings_page() {
        if (isset($_POST['submit_product_filters']) &&
            check_admin_referer('nh_filters_settings', 'nh_filters_nonce')) {
            $enabled_attributes = isset($_POST['enabled_attributes']) ? $_POST['enabled_attributes'] : array();
            update_option('nh_product_filter_attributes', $enabled_attributes);
            echo '<div class="updated"><p>Οι ρυθμίσεις αποθηκεύτηκαν.</p></div>';
        }

        $product_attributes = wc_get_attribute_taxonomies();
        $enabled_attributes = get_option('nh_product_filter_attributes', array());
        ?>
        <div class="wrap">
            <h2>Ρυθμίσεις Φίλτρων Προϊόντων</h2>
            <form method="post" action="">
                <?php wp_nonce_field('nh_filters_settings', 'nh_filters_nonce'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Ενεργοποίηση φίλτρων για:</th>
                        <td>
                            <?php if (!empty($product_attributes)) : ?>
                                <?php foreach ($product_attributes as $attribute) : ?>
                                    <label>
                                        <input type="checkbox"
                                               name="enabled_attributes[]"
                                               value="<?php echo esc_attr($attribute->attribute_name); ?>"
                                               <?php checked(in_array($attribute->attribute_name, $enabled_attributes)); ?>>
                                        <?php echo esc_html($attribute->attribute_label); ?>
                                    </label><br>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <p>Δεν βρέθηκαν χαρακτηριστικά προϊόντων.</p>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="submit_product_filters" class="button-primary" value="Αποθήκευση Αλλαγών">
                </p>
            </form>
        </div>
        <?php
    }
}

NH_Product_Filters::get_instance();