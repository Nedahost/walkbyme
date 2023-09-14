<?php
// Ορίστε τον τύπο περιεχομένου ως XML
header("Content-Type: application/xml; charset=utf-8");

// Ξεκινήστε το XML αρχείο
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Προσθέστε τα URL των σελίδων του καταστήματός σας δυναμικά -->

    <!-- Προϊόντα του WooCommerce -->
    <?php
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
    );

    $products = new WP_Query($args);

    if ($products->have_posts()) :
        while ($products->have_posts()) : $products->the_post();
            $product_url = get_permalink();
    ?>
            <url>
                <loc><?php echo esc_url($product_url); ?></loc>
                <changefreq>weekly</changefreq>
                <priority>0.8</priority>
            </url>
    <?php
        endwhile;
    endif;
    wp_reset_postdata();
    ?>

    <!-- Κατηγορίες του WooCommerce -->
    <?php
    $product_categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
    ));

    foreach ($product_categories as $category) {
        $category_url = get_term_link($category);
    ?>
        <url>
            <loc><?php echo esc_url($category_url); ?></loc>
            <changefreq>weekly</changefreq>
            <priority>0.6</priority>
        </url>
    <?php
    }
    ?>

    <!-- Σελίδες του καταστήματος -->
    <?php
    $pages = get_pages(array(
        'post_type' => 'page',
        'posts_per_page' => -1,
    ));

    foreach ($pages as $page) {
        $page_url = get_permalink($page);
    ?>
        <url>
            <loc><?php echo esc_url($page_url); ?></loc>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
    <?php
    }
    ?>
</urlset>
