<?php
defined( 'ABSPATH' ) || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
    echo get_the_password_form();
    return;
}
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class(); ?>>
    <div class="bgproducts">
        <div class="productflex">
            
            <div class="product-image">
                <?php 
                // Εμφάνιση των badges πάνω στην εικόνα (αν έχουν οριστεί στο badges.php hook)
                // Αν θες να εμφανίζονται εδώ χειροκίνητα, πες μου να προσθέσουμε τον κώδικα.
                echo woocommerce_show_product_images(); 
                ?>
            </div>
    
            <section class="rightdetailsproduct">
                <h1><?php echo wp_kses_post($product->get_name()); ?></h1>
                
                <div class="product-identity">
                    <?php 
                        $slogan = get_post_meta($product->get_id(), '_text_field', true); 
                        if ( $slogan ) {
                            // Ασφαλέστερη μέθοδος αφαίρεσης της πρώτης λέξης αν υπάρχει κενό
                            $parts = explode(" ", $slogan, 2);
                            echo isset($parts[1]) ? esc_html($parts[1]) : esc_html($slogan);
                        }
                    ?>
                </div>

                <div class="importantdetails">
                    <div class="skuproduct">
                        <?php
                            $sku = $product->get_sku();
                            $availability = get_post_meta($product->get_id(), '_select', true);
                            
                            if ( $sku ) {
                                echo '<b>Ref.:</b> ' . esc_html($sku);
                            }
                            if ( $availability ) {
                                echo ' • <b>Διαθεσιμότητα:</b> ' . esc_html($availability);
                            }
                        ?>
                    </div> 
                </div>

                <div class="singleprice">
                    <?php
                    // Λογική Τιμής
                    $regular_price = $product->get_regular_price();
                    $sale_price    = $product->get_sale_price();
                    
                    // Αν είναι variable, παίρνουμε τις min τιμές
                    if ( $product->is_type('variable') ) {
                        $min_price = $product->get_variation_price('min', true);
                        $max_price = $product->get_variation_price('max', true);
                        $regular_price = $product->get_variation_regular_price('min', true);
                        $sale_price    = $min_price;

                        // Αν υπάρχει εύρος τιμών
                        if ( $min_price !== $max_price ) {
                            echo '<span class="lowcblack">' . wc_price($min_price) . ' - ' . wc_price($max_price) . '</span>';
                        } else {
                            // Αν όλες οι παραλλαγές έχουν ίδια τιμή, συνεχίζουμε στην κανονική λογική εμφάνισης
                            goto render_price; 
                        }
                    } else {
                        render_price:
                        if ( $product->is_on_sale() && $regular_price ) {
                            echo '<span class="price-container">';
                            echo '<span class="product-standard-price"> Τιμή: ' . wc_price($regular_price) . '</span>';
                            echo '<span class="lowred">' . wc_price($sale_price) . '</span>';
                            echo '</span>';

                            // Υπολογισμός Έκπτωσης
                            if ( function_exists('calculate_discount_percentage') ) {
                                $discount = calculate_discount_percentage($regular_price, $sale_price);
                                if ( $discount > 0 ) {
                                    echo '<span class="discount-percentage-badge">' . esc_html($discount) . '% OFF</span>';
                                }
                            }
                        } else {
                            echo '<span class="lowcblack">' . wc_price($regular_price) . '</span>';
                        }
                    }
                    ?>
                </div>

                <?php echo woocommerce_template_single_add_to_cart(); ?>

                <div class="productContent">
                    <?php the_content(); ?>
                </div>

                <?php
$accordion_items = get_option('product_tabs_data', array());

if (!empty($accordion_items)) {
    echo '<div class="accordion product-accordion">';
    
    // 1. Static Info Tab
    echo '<div class="accordion__item">';
    echo '<div class="accordion__title">Χαρακτηριστικά <span class="accordion__icon"></span></div>';
    echo '<div class="accordion__content"><div class="accordion__inner">';
    do_action('woocommerce_product_additional_information', $product);
    echo '</div></div></div>';
    
    // 2. Dynamic Tabs
    foreach ($accordion_items as $item) {
        $title = isset($item['title']) ? $item['title'] : (isset($item['question']) ? $item['question'] : '');
        $content = isset($item['content']) ? $item['content'] : (isset($item['answer']) ? $item['answer'] : '');
        $cats = isset($item['categories']) ? $item['categories'] : (isset($item['category']) ? explode(',', $item['category']) : array());
        
        if (!empty($title) && !empty($content)) {
            $target_cats = array_map('trim', (array)$cats);
            
            if (empty($target_cats) || has_term($target_cats, 'product_cat', $product->get_id())) {
                echo '<div class="accordion__item">';
                echo '<div class="accordion__title">' . esc_html($title) . '<span class="accordion__icon"></span></div>';
                echo '<div class="accordion__content"><div class="accordion__inner">' . wpautop(wp_kses_post($content)) . '</div></div>';
                echo '</div>';
            }
        }
    }
    
    echo '</div>';
}
?>
            </section>
        </div>
    </div>
</div>

<div class="faqproducts">
    </div>

<?php echo woocommerce_output_related_products(); ?>