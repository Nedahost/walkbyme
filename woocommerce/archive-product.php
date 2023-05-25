
    
    
    <div class="contentproducts"><!-- content products start -->
    

    <?php if (have_posts()) : ?>

        
        <?php  do_action('woocommerce_before_shop_loop'); ?>

       
        <?php woocommerce_product_loop_start(); ?>

        
        <?php woocommerce_product_subcategories(); ?>

        
        
        <?php while (have_posts()) : the_post(); ?>

            <?php wc_get_template_part('content', 'product'); ?>

        <?php endwhile; // end of the loop.  ?>

        <?php woocommerce_product_loop_end(); ?>

        <?php // do_action('woocommerce_after_shop_loop'); 
        
        custom_pagination();

        ?>

    <?php elseif (!woocommerce_product_subcategories(array('before' => woocommerce_product_loop_start(false), 'after' => woocommerce_product_loop_end(false)))) : ?>

        <?php do_action('woocommerce_no_products_found'); ?>


    <?php endif; ?>
        <div class="clear_0"></div>
    </div><!-- content products end -->
     <div class="clear_0"></div>


