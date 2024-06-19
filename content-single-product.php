<?php
defined( 'ABSPATH' ) || exit;
global $product;
/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked wc_print_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class(); ?>>
    <div class="bgproducts">
        <div class="productflex">
            <div class="product-image">
                <?php echo woocommerce_show_product_images(); ?>
            </div>
    
            <section class="rightdetailsproduct">
                <h1><?php echo $product->get_name(); ?></h1>
                
                <?php 
                    $slogan = get_post_meta(get_the_ID(), '_text_field', true); 
                    $result = substr(strstr($slogan, " "), 1);
                    echo $result;
                ?>
                
                <div class="importantdetails">
                    <div class="skuproduct">
                        <?php
                            $sku = $product->get_sku();
                            if (!empty($sku)) {
                                echo '<b>Ref.:</b> ' . $sku;
                            }
                        ?>
                    </div>
                    <?php 
                    // Εμφάνιση διαθεσιμότητας
                    $availability = get_post_meta(get_the_ID(), '_select', true);
                    if (!empty($availability)) {
                        echo '<div class="availability"><b>Διαθεσιμότητα:</b> ' . esc_html($availability) . '</div>';
                    }
                    ?>
                    <div class="textsales">
                        <b><u><?php echo get_post_meta($post->ID, '_date_field', true); ?></u></b>
                    </div>
                </div>
                <div class="singleprice">
                    <?php
                    if ($product->is_type('variable')) {
                        // Μεταβλητό προϊόν
                        $variations = $product->get_available_variations();
                        $min_price = $product->get_variation_price('min', true);
                        $max_price = $product->get_variation_price('max', true);
                        $min_regular_price = $product->get_variation_regular_price('min', true);
                        $max_regular_price = $product->get_variation_regular_price('max', true);

                        if ($min_price !== $max_price) {
                            echo '<span class="lowcblack">' . wc_price($min_price) . ' - ' . wc_price($max_price) . '</span>';
                        } else {
                            if ($min_regular_price !== $min_price) {
                                echo '<span class="price-container">';
                                echo '<span class="product-standard-price">' . wc_price($min_regular_price) . '</span>';
                                echo '<span class="lowred">' . wc_price($min_price) . '</span>';
                                echo '</span>';

                                // Εμφάνιση ποσοστού έκπτωσης
                                $discount_percentage = calculate_discount_percentage($min_regular_price, $min_price);
                                if ($discount_percentage > 0) {
                                    echo '<span class="discount-percentage-badge">' . esc_html($discount_percentage) . '% OFF</span>';
                                }
                            } else {
                                echo '<span class="lowcblack">' . wc_price($min_price) . '</span>';
                            }
                        }
                    } else {
                        // Απλό προϊόν
                        $regular_price = $product->get_regular_price();
                        $sale_price = $product->get_sale_price();

                        if (!empty($sale_price) && !empty($regular_price) && $sale_price < $regular_price) {
                            echo '<span class="price-container">';
                            echo '<span class="product-standard-price">' . wc_price($regular_price) . '</span>';
                            echo '<span class="lowred">' . wc_price($sale_price) . '</span>';
                            echo '</span>';

                            // Εμφάνιση ποσοστού έκπτωσης
                            $discount_percentage = calculate_discount_percentage($regular_price, $sale_price);
                            if ($discount_percentage > 0) {
                                echo '<span class="discount-percentage-badge">' . esc_html($discount_percentage) . '% OFF</span>';
                            }
                        } else {
                            echo '<span class="lowcblack">' . wc_price($regular_price) . '</span>';
                        }
                    }
                    ?>
                </div>
                <div class="productContent">
                    <?php the_content(); ?>
                </div>
                <?php
// Έλεγχος αν θα εμφανιστεί το συγκεκριμένο accordion "Φροντίδα Κοσμημάτων"
$hide_jewelry_care_tab = get_post_meta($product->get_id(), '_hide_jewelry_care_tab', true);
$accordion_items = get_option('nedahost_tabs_items', array());

// Λήψη της κατηγορίας του τρέχοντος προϊόντος
$product_category = get_the_terms($product->get_id(), 'product_cat');

if (!empty($accordion_items)) {
    echo '<div class="product-accordions">';

    // Εμφάνιση χαρακτηριστικών προϊόντος στο πρώτο accordion
    echo '<div class="accordion-item">';
    echo '<h3 class="accordion-title">Χαρακτηριστικά <span class="accordion-icon"></span></h3>';
    echo '<div class="accordion-content">';
    do_action('woocommerce_product_additional_information', $product);
    echo '</div>';
    echo '</div>';

    // Εμφάνιση υπόλοιπων accordions
    foreach ($accordion_items as $item) {
        if (isset($item['question']) && isset($item['answer'])) {
            // Έλεγχος αν η κατηγορία του προϊόντος υπάρχει στις επιλεγμένες κατηγορίες του accordion
            $item_categories = explode(',', $item['category']);
            $show_accordion = false;

            foreach ($product_category as $category) {
                if (in_array($category->slug, $item_categories)) {
                    $show_accordion = true;
                    break;
                }
            }

            // Αν το custom field είναι τσεκαρισμένο, παράλειψε το accordion "Φροντίδα Κοσμημάτων"
            if ($hide_jewelry_care_tab === 'yes' && $item['question'] === 'Φροντίδα Κοσμημάτων') {
                $show_accordion = false;
            }

            if ($show_accordion) {
                echo '<div class="accordion-item">';
                echo '<h3 class="accordion-title">' . esc_html($item['question']) . '<span class="accordion-icon"></span></h3>';
                echo '<div class="accordion-content">' . wpautop($item['answer']) . '</div>';
                echo '</div>';
            }
        }
    }

    echo '</div>';
}
?>
                
                <?php echo woocommerce_template_single_add_to_cart(); ?>
            </section>
        </div>
    </div>
</div>

<?php echo woocommerce_output_related_products(); ?>