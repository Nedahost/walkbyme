<?php

// Δημιουργήστε τον XML Sitemap
header("Content-Type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Λήψη όλων των προϊόντων του WooCommerce
$args = array(
    'post_type' => 'product',
    'posts_per_page' => -1,
);
$products = new WP_Query($args);

while ($products->have_posts()) : $products->the_post();
    $product_url = get_permalink();
    $product_title = get_the_title();
    $product_description = get_the_excerpt();

    echo '<url>';
    echo '<loc>' . esc_url($product_url) . '</loc>';
    echo '<changefreq>daily</changefreq>';
    echo '<priority>0.9</priority>';
    echo '<lastmod>' . date('Y-m-d') . '</lastmod>';
    echo '<![CDATA[' . esc_html($product_title) . ']]>';
    echo '<![CDATA[' . esc_html($product_description) . ']]>';
    echo '</url>';
endwhile;

// Λήψη όλων των κατηγοριών του WooCommerce
$categories = get_terms('product_cat', array('hide_empty' => false));

foreach ($categories as $category) {
    $category_url = get_term_link($category);
    $category_name = $category->name;

    echo '<url>';
    echo '<loc>' . esc_url($category_url) . '</loc>';
    echo '<changefreq>daily</changefreq>';
    echo '<priority>0.8</priority>';
    echo '<lastmod>' . date('Y-m-d') . '</lastmod>';
    echo '<![CDATA[' . esc_html($category_name) . ']]>';
    echo '</url>';
}


echo '</urlset>';
?>
