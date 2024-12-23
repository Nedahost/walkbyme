<?php
// Product Sitemap Generation
function custom_product_sitemap() {
    if (isset($_GET['custom-sitemap']) && $_GET['custom-sitemap'] === 'generate') {
        // Αύξηση ορίου μνήμης και χρόνου εκτέλεσης
        ini_set('memory_limit', '256M');
        set_time_limit(300); // 5 λεπτά

        header('Content-Type: application/xml; charset=utf-8');

        $file_path = ABSPATH . 'custom-sitemap.xml';

        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once (ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }

        ob_start();
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
              xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
              http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

        // Προσθήκη αρχικής σελίδας
        echo '  <url>' . "\n";
        echo '    <loc>' . esc_url(home_url('/')) . '</loc>' . "\n";
        echo '    <changefreq>daily</changefreq>' . "\n";
        echo '    <priority>1.0</priority>' . "\n";
        echo '  </url>' . "\n";

        // Προσθήκη κατηγοριών προϊόντων
        $categories = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
        ));

        foreach ($categories as $category) {
            $category_url = get_term_link($category);
            if (!is_wp_error($category_url)) {
                echo '  <url>' . "\n";
                echo '    <loc>' . esc_url($category_url) . '</loc>' . "\n";
                echo '    <changefreq>weekly</changefreq>' . "\n";
                echo '    <priority>0.8</priority>' . "\n";
                echo '  </url>' . "\n";
            }
        }

        // Προσθήκη προϊόντων
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );
        $products = new WP_Query($args);

        if ($products->have_posts()) {
            while ($products->have_posts()) {
                $products->the_post();
                $product_url = get_permalink();
                $modified_time = get_the_modified_time('c');
                echo '  <url>' . "\n";
                echo '    <loc>' . esc_url($product_url) . '</loc>' . "\n";
                echo '    <lastmod>' . $modified_time . '</lastmod>' . "\n";
                echo '    <changefreq>weekly</changefreq>' . "\n";
                echo '    <priority>0.6</priority>' . "\n";
                echo '  </url>' . "\n";
            }
            wp_reset_postdata();
        }

        echo '</urlset>';

        $xml_content = ob_get_clean();
        
        if ($wp_filesystem->put_contents($file_path, $xml_content, FS_CHMOD_FILE)) {
            echo $xml_content;
        } else {
            wp_die('Αποτυχία εγγραφής του sitemap στο αρχείο.');
        }
        
        die();
    }
}
add_action('init', 'custom_product_sitemap');

// Similar structure for articles sitemap.
function custom_sitemap_articles() {
    $sitemap_action = isset($_GET['custom-sitemap-articles']) ? sanitize_text_field($_GET['custom-sitemap-articles']) : '';
    if ($sitemap_action === 'generate') {
        // Αύξηση του ορίου μνήμης και χρόνου εκτέλεσης
        ini_set('memory_limit', '256M');
        set_time_limit(300); // 5 λεπτά

        header('Content-Type: application/xml; charset=utf-8');

        $file_path = ABSPATH . 'custom-sitemap-articles.xml';

        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once (ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }

        ob_start();
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
              xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
              http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

        // Προσθήκη κατηγοριών
        $categories = get_categories(array('taxonomy' => 'category', 'hide_empty' => false));
        foreach ($categories as $category) {
            $category_url = get_term_link($category);
            if (!is_wp_error($category_url)) {
                echo '  <url>' . "\n";
                echo '    <loc>' . esc_url(trim($category_url)) . '</loc>' . "\n";
                echo '    <changefreq>weekly</changefreq>' . "\n";
                echo '    <priority>0.8</priority>' . "\n";
                echo '  </url>' . "\n";
            }
        }

        // Προσθήκη άρθρων
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );
        $articles = new WP_Query($args);

        if ($articles->have_posts()) {
            while ($articles->have_posts()) {
                $articles->the_post();
                $post_modified = get_the_modified_time('c');
                echo '  <url>' . "\n";
                echo '    <loc>' . esc_url(get_permalink()) . '</loc>' . "\n";
                echo '    <lastmod>' . $post_modified . '</lastmod>' . "\n";
                echo '    <changefreq>monthly</changefreq>' . "\n";
                echo '    <priority>0.6</priority>' . "\n";
                echo '  </url>' . "\n";
            }
            wp_reset_postdata();
        }

        echo '</urlset>';

        $xml_content = ob_get_clean();
        
        if ($wp_filesystem->put_contents($file_path, $xml_content, FS_CHMOD_FILE)) {
            echo $xml_content;
        } else {
            wp_die('Αποτυχία εγγραφής του sitemap στο αρχείο.');
        }
        
        die();
    }
}
add_action('init', 'custom_sitemap_articles');


// Update functions for sitemaps
function schedule_product_sitemap_update($post_id) {
    if (get_post_type($post_id) === 'product' && get_post_status($post_id) === 'publish') {
        wp_schedule_single_event(time() + 300, 'update_product_sitemap_event');
    }
}
add_action('save_post', 'schedule_product_sitemap_update');
add_action('edited_product_cat', 'schedule_product_sitemap_update');
add_action('delete_product_cat', 'schedule_product_sitemap_update');

// Update execution
function do_update_product_sitemap() {
    $sitemap_url = add_query_arg('custom-sitemap', 'generate', home_url());
    wp_remote_get($sitemap_url);
}
add_action('update_product_sitemap_event', 'do_update_product_sitemap');


// Admin menu buttons
function add_update_product_sitemap_button() {
    add_submenu_page(
        'edit.php?post_type=product',
        'Ενημέρωση Sitemap Προϊόντων',
        'Ενημέρωση Sitemap Προϊόντων',
        'manage_options',
        'update-product-sitemap',
        'update_product_sitemap_page'
    );
}
add_action('admin_menu', 'add_update_product_sitemap_button');

function update_product_sitemap_page() {
    echo '<div class="wrap">';
    echo '<h1>Ενημέρωση XML Sitemap Προϊόντων</h1>';
    echo '<p>Πατήστε το κουμπί για να ενημερώσετε χειροκίνητα το XML sitemap των προϊόντων.</p>';
    echo '<a href="' . esc_url(add_query_arg('custom-sitemap', 'generate', home_url())) . '" class="button button-primary">Ενημέρωση Sitemap Προϊόντων</a>';
    echo '</div>';
}

// Articles sitemap updates
function update_custom_sitemap_articles($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    $post_type = get_post_type($post_id);
    if ($post_type === 'post' && get_post_status($post_id) === 'publish') {
        wp_schedule_single_event(time() + 300, 'update_sitemap_event');
    }
}
add_action('save_post', 'update_custom_sitemap_articles');
add_action('edit_category', 'update_custom_sitemap_articles');
add_action('delete_category', 'update_custom_sitemap_articles');

// Articles sitemap update execution
function do_update_sitemap() {
    custom_sitemap_articles();
}
add_action('update_sitemap_event', 'do_update_sitemap');

// Articles sitemap admin menu
function add_update_sitemap_button() {
    add_management_page(
        'Ενημέρωση Sitemap',
        'Ενημέρωση Sitemap', 
        'manage_options', 
        'update-sitemap', 
        'update_sitemap_page'
    );
}
add_action('admin_menu', 'add_update_sitemap_button');

function update_sitemap_page() {
    echo '<div class="wrap">';
    echo '<h1>Ενημέρωση XML Sitemap</h1>';
    echo '<p>Πατήστε το κουμπί για να ενημερώσετε χειροκίνητα το XML sitemap.</p>';
    echo '<a href="' . esc_url(add_query_arg('custom-sitemap-articles', 'generate', home_url())) . '" class="button button-primary">Ενημέρωση Sitemap</a>';
    echo '</div>';
}