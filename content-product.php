<?php
defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}
?>

<li <?php wc_product_class(); ?>>
    <?php
    do_action('woocommerce_before_shop_loop_item');
    do_action('woocommerce_before_shop_loop_item_title');
    
    // Τίτλος
    $title = get_the_title();
    ?>
    
    <div class="product-badges">
        <?php
        if ( function_exists('get_wc_product_badges') ) {
            $badges = get_wc_product_badges( $product->get_id() );
            
            if ( ! empty($badges) ) {
                foreach ( $badges as $badge ) {
                    printf(
                        '<span class="product-badge badge-%s" style="background-color: %s; color: %s;">%s</span>',
                        esc_attr($badge['position']),
                        esc_attr($badge['bg_color']),
                        esc_attr($badge['text_color']),
                        esc_html($badge['text'])
                    );
                }
            }
        }
        ?>
    </div>
    
    <h3>
        <a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html($title); ?></a>
    </h3>
    
    <div class="listproductprice">
        <?php
        // Unified Price Logic (Works for Simple & Variable without heavy loading)
        $regular_price = $product->get_regular_price();
        $sale_price    = $product->get_sale_price();

        // Ειδικός χειρισμός για Variable Products για να δείχνουν "Από..." ή την φθηνότερη τιμή
        if ( $product->is_type('variable') ) {
            $regular_price = $product->get_variation_regular_price('min', true);
            $sale_price    = $product->get_variation_sale_price('min', true);
        }

        if ( $product->is_on_sale() && $sale_price ) : ?>
            
            <span class="product-standard-price"><?php echo wc_price($regular_price); ?></span>
            <span class="lowred"><?php echo wc_price($sale_price); ?></span>
            
            <?php
            // Υπολογισμός έκπτωσης
            if ( function_exists('calculate_discount_percentage') ) {
                $discount = calculate_discount_percentage($regular_price, $sale_price);
                if ( $discount > 0 ) {
                    echo '<div class="discount-percentage-badge">' . esc_html($discount) . '% OFF</div>';
                }
            }
            ?>
            
        <?php else : ?>
            <span class="lowcblack"><?php echo wc_price($regular_price); ?></span>
        <?php endif; ?>
    </div>

    <?php 
    do_action('woocommerce_after_shop_loop_item'); 
    ?>
</li>