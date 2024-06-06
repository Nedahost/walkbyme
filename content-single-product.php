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
                    <div class="textsales">
                        <b><u><?php echo get_post_meta($post->ID, '_date_field', true); ?></u></b>
                    </div>
                </div>
                
                <div class="singleprice">
                    <?php echo $product->get_price_html(); ?>
                </div>

                <div class="productContent">
                    <?php the_content(); ?>
                </div>

                <?php do_action('woocommerce_product_additional_information', $product); ?>
                
                <?php echo woocommerce_template_single_add_to_cart(); ?>
            </section>
        </div>
    </div>
</div>

<?php echo woocommerce_output_related_products(); ?>