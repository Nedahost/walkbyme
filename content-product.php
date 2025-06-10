<li <?php wc_product_class(); ?>>
    <?php
    global $post, $product;
    do_action('woocommerce_before_shop_loop_item');
    do_action('woocommerce_before_shop_loop_item_title');
    do_action('woocommerce_after_shop_loop_item');
    // Εμφάνιση ονόματος προϊόντος (τίτλος ή custom field)
    $product_title = get_post_meta(get_the_ID(), '_text_field', true);
    $title = get_the_title();
    ?>
    
    <!-- Product Badges Container -->
    <div class="product-badges">
        <?php
        // Get badges από το δικό μας σύστημα
        if (function_exists('get_wc_product_badges')) {
            $badges = get_wc_product_badges(get_the_ID());
            
            if (!empty($badges)) {
                foreach ($badges as $badge) {
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
        <a href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html($title); ?></a>
    </h3>
    
    <?php if ($product->is_type('simple')) : ?>
        <div class="listproductprice"><!-- list product price start -->
            <?php
            $regular_price = $product->get_regular_price();
            $sale_price = $product->get_sale_price();
            if ($sale_price) :
            ?>
                <span class="product-standard-price"><?php echo wc_price($regular_price); ?></span>
                <span class="lowred"><?php echo wc_price($sale_price); ?></span>
            <?php else : ?>
                <span class="lowcblack"><?php echo wc_price($regular_price); ?></span>
            <?php endif; ?>
            <?php
            // Εμφάνιση ποσοστού έκπτωσης
            $discount_percentage = calculate_discount_percentage($regular_price, $sale_price);
            if ($discount_percentage) {
                echo '<div class="discount-percentage-badge">' . esc_html($discount_percentage) . '% OFF</div>';
            }
            ?>
        </div><!-- product price end -->
    <?php elseif ($product->is_type('variable')) :
        $available_variations = $product->get_available_variations();
        if ($available_variations) :
            $variation_id = $available_variations[0]['variation_id'];
            $variable_product = new WC_Product_Variation($variation_id);
            $regular_price = $variable_product->get_regular_price();
            $sale_price = $variable_product->get_sale_price();
        ?>
            <div class="product-price"><!-- product price start -->
                <?php if ($sale_price) : ?>
                    <span class="product-standard-price"><?php echo wc_price($regular_price); ?></span>
                    <span class="lowred"><?php echo wc_price($sale_price); ?></span>
                    <?php
                    // Εμφάνιση ποσοστού έκπτωσης
                    $discount_percentage = calculate_discount_percentage($regular_price, $sale_price);
                    echo '<div class="discount-percentage-badge">' . esc_html($discount_percentage) . '% OFF</div>';
                    ?>
                <?php else : ?>
                    <span class="lowcblack"><?php echo wc_price($regular_price); ?></span>
                <?php endif; ?>
            </div><!-- product price end -->
        <?php endif; ?>
    <?php endif; ?>
</li>