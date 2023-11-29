<li <?php wc_product_class('<li', true); ?>>
    <?php
    global $post, $product;
    do_action('woocommerce_before_shop_loop_item');
    do_action('woocommerce_before_shop_loop_item_title');
    do_action('woocommerce_after_shop_loop_item');
    ?>

    <?php if ($product->is_type('simple')) : ?>
        <h3>
            <a href="<?php echo esc_url(get_permalink()); ?>">
                <?php
                $titlecategories = get_post_meta(get_the_ID(), '_text_field', true);
                if (!empty($titlecategories)) {
                    echo $titlecategories;
                } else {
                    the_title();
                }
                ?>
            </a>
        </h3>

        <div class="listproductprice"><!-- list product price start -->
            <?php
            $sales = $product->sale_price;
            if (!empty($sales)) :
            ?>
                <span class="product-standard-price">
                    <?php printf('%s&euro;', $product->regular_price); ?>
                </span>
                <span class="lowred">
                    <?php printf('%s&euro;', $sales); ?>
                </span>
            <?php else : ?>
                <span class="lowcblack">
                    <?php printf('%s&euro;', $product->regular_price); ?>
                </span>
            <?php endif; ?>
            
            <?php
            // Εμφανίστε το ποσοστό έκπτωσης
            $dynamic_discount_percentage = calculate_dynamic_discount_percentage($product->get_regular_price(), $sales);
            if ($dynamic_discount_percentage != 0 && $dynamic_discount_percentage != '') {
                echo '<div class="discount-percentage-badge">' . $dynamic_discount_percentage . '%</div>';
            }
            ?>
        </div><!-- product price end -->

    <?php elseif ($product->is_type('variable')) :
        $available_variations = $product->get_available_variations();
        $variation_id = $available_variations[0]['variation_id'];
        $variable_product1 = new WC_Product_Variation($variation_id);
        $regular_price = $variable_product1->regular_price;
        $sales_price = $variable_product1->sale_price;
    ?>
        <div class="product-name"><!-- product name start -->
            <h3>
                <a href="<?php echo esc_url(get_permalink()); ?>">
                    <?php
                    $titlecategories = get_post_meta(get_the_ID(), '_text_field', true);
                    if (!empty($titlecategories)) {
                        echo $titlecategories;
                    } else {
                        the_title();
                    }
                    ?>
                </a>
            </h3>
        </div><!-- product name end -->

        <?php if (!empty($sales_price)) : ?>
            <div class="product-price"><!-- product price start -->
                <span class="product-standard-price" style="text-decoration: line-through; opacity: .6; margin-right: .3em;">
                    <?php printf('%s&euro;', $regular_price); ?>
                </span>
                <span class="lowred">
                    <?php printf('%s&euro;', $sales_price); ?>
                </span>
                
                <?php
                // Εμφανίστε το ποσοστό έκπτωσης
                $dynamic_discount_percentage = calculate_dynamic_discount_percentage($regular_price, $sales_price);
                echo '<div class="discount-percentage-badge">' . $dynamic_discount_percentage . '% OFF</div>';
                ?>
            </div><!-- product price end -->
        <?php else : ?>
            <div class="product-price"><!-- product price start -->
                <span class="lowcblack">
                    <?php printf('%s&euro;', $regular_price); ?>
                </span>
            </div><!-- product price end -->
        <?php endif; ?>
    <?php endif; ?>
</li>