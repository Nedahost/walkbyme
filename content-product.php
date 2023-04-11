<li <?php wc_product_class(); ?>>
    <?php global $post, $product; ?>
    <?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
    
    <?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?>
    
    <?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
    
    <?php //the_excerpt(); ?>
    
    <?php 
    
    if ($product->is_type('simple')) {
       
        /*
        if (!empty($product->sale_price)) {
            $sales_simple = round( ( ( $product->regular_price - $product->sale_price ) / $product->regular_price ) * 100 );
    ?>
    <div class="product-promo">
        <?php echo sprintf( __(' Έκπτωση %s', 'woocommerce' ), $sales_simple . '%' ); ?>
    </div>
    <?php } 
    
    */
   
//    if ( ! $product->managing_stock() && ! $product->is_in_stock() ){
//    echo '<p>This product is out of stock. It can be purchased by custom made order.</p>';
//    }
    ?>
    
    
    <h3>
         <a href="<?php the_permalink(); ?>">
         <?php the_title(); ?>
         </a>
     </h3>
    
    
    <div class="listproductprice"><!-- list product price start -->
        <?php 
        $sales = $product->sale_price;
        if(!empty($sales)){ ?>
        <span class="product-standard-price">
            <?php echo $product->regular_price .'&euro;'; ?>
        </span>
        <span class="lowred">
            <?php echo $product->sale_price .'&euro;'; ?>
        </span>
        <?php } else{ ?>
        <span class="lowcblack">
            <?php echo $product->regular_price .'&euro;'; ?>
        </span>
        <?php }  ?>
    </div><!-- product price end -->
    
    <?php } elseif( $product->is_type( 'variable' ) ){
        
    #Step 1: Get product varations
    $available_variations = $product->get_available_variations();

    #Step 2: Get product variation id
    $variation_id=$available_variations[0]['variation_id']; // Getting the variable id of just the 1st product. You can loop $available_variations to get info about each variation.

    #Step 3: Create the variable product object
    $variable_product1= new WC_Product_Variation( $variation_id );
    
    $regular_price = $variable_product1 ->regular_price; 
    $sales_price = $variable_product1 ->sale_price;
   /*
    ?>
    <div class="product-promo">
        <?php 
        if(!empty($sales_price)){
        $variable_sales = round( ( ( $regular_price - $sales_price ) / $regular_price ) * 100 );
        echo sprintf( __(' Έκπτωση %s', 'woocommerce' ), $variable_sales . '%' ); 
        }
        ?>
    </div>
    */
    ?>
    <div class="product-name"><!-- product name start -->
       <h3>
            <a href="<?php the_permalink(); ?>">
            <?php the_title(); ?>
            </a>
        </h3>
    </div><!-- product name end --> 
    <?php if(!empty($sales_price)){ ?>
    
    <div class="product-price"><!-- product price start -->
        <span class="product-standard-price" style="text-decoration: line-through; opacity: .6; margin-right: .3em;">
            <?php echo $regular_price.'&euro;'; ?>
        </span>
        <span class="lowred">
            <?php echo $sales_price .'&euro;'; ?>
        </span>
    </div><!-- product price end -->
    
    <?php }else{ ?>
    <div class="product-price"><!-- product price start -->
        <span class="lowcblack">
            <?php echo $regular_price .' &euro;'; ?>
        </span>
    </div>
    <?php }
    }
    ?>
</li>
