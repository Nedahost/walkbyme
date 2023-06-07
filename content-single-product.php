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
    <div class="bgproducts"><!-- background products start -->
        
            <div class="productflex">
            
        <div class="product-image"><!-- product image start -->
            <?php echo woocommerce_show_product_images(); ?>
        </div><!-- product image end -->
    
    <section  class="rightdetailsproduct"><!-- right details product start -->
        <h1>
            <?php echo $product->get_name(); ?>
        </h1>
        
        <?php 
        
        $slogan =  get_post_meta( get_the_ID(), '_text_field', true); 

        $result = substr(strstr($slogan," "), 1);

        echo $result;

        ?>
        <div class="importantdetails"><!-- important details start -->
            <div class="skuproduct"><!-- sky start -->
                <?php
                $sku = $product->get_sku();
                if(!empty($sku)){
                    echo '<b>Ref.:</b> '. $sku ;
                } ?>
            </div><!-- sky end -->
            <div class="stockproduct"><!-- stock start -->
                <b>Διαθεσιμότητα:</b>
                <?php 
                if('instock' == $product->get_stock_status()){ ?>
                Σε απόθεμα
                <?php 
                    } elseif ('outofstock' == $product->get_stock_status()) { ?>
                Εξαντλημένο
                <?php 
                    } else { ?>
                Διαθέσιμο κατόπιν παραγγελίας
                <?php                  
                    }
                ?>
            </div><!-- stock end -->
            <div class="textsales" style="
    font-size: 18px;
    color: #e02929;
        padding: 10px 0;
"><!-- text sales start -->
                <b><u><?php echo get_post_meta($post->ID, '_date_field', true); ?></u></b>
            </div><!-- text sales end -->
        </div><!-- important details end -->
        <div class="singleprice"><!-- single price start -->
            <?php
            if('simple' == $product->product_type){ 
            $single_regular_price = $product->regular_price;
            $single_sales_price = $product->sale_price;
            ?>
            <div class="single_salesprice"><!-- sales price start -->
                <ul>
                    <?php if(!empty($single_sales_price)){ ?>
                    <li class="first-value">
                        <?php echo $single_regular_price .' &euro;'; ?>
                    </li>
                    <li>
                        <b>
                            <?php echo $single_sales_price .' &euro;'; ?>
                        </b>
                    </li>
                    <?php } else { ?>
                    <li>
                        <?php echo $single_regular_price .' &euro;'; ?>
                    </li>
                    <?php } ?>
                </ul>
            </div><!-- sales price end -->
            <?php 
            }elseif('variable' == $product->product_type){
                #Step 1: Get product varations
                $available_variations = $product->get_available_variations();

                #Step 2: Get product variation id
                $variation_id=$available_variations[0]['variation_id']; // Getting the variable id of just the 1st product. You can loop $available_variations to get info about each variation.

                #Step 3: Create the variable product object
                $variable_product1= new WC_Product_Variation( $variation_id );

                $regular_price = $variable_product1 ->regular_price; 
                $sales_price = $variable_product1 ->sale_price;
                if(!empty($sales_price)){ ?>
            <div class="variablesalesprice"><!-- variable sales price start -->
                <span style="text-decoration: line-through; opacity: .6; margin-right: .3em;">
                    <?php echo $regular_price .' &euro;'; ?>
                </span>
                <span style="font-size: 14pt;">
                    <b>
                        <?php echo $sales_price .' &euro;'; ?>
                    </b>
                </span>
            </div><!-- variable sales price end -->
            <?php    } else { ?>
            <div class="singlevariableprice"><!-- single variable price start -->
                <span style="font-size: 1.125rem;">
                    <?php echo $regular_price .' &euro;'; ?>
                </span>
            </div><!-- single variable price end -->
            <?php }
            }
            ?>
        </div><!-- single price end -->
        <div class="productContent"><!-- product content start -->
            <?php the_content(); ?>
        </div><!-- product content end -->
        
        <?php if(has_term( array(44,43), 'product_cat' )){ ?>
 
        <?php } ?>

        <?php do_action( 'woocommerce_product_additional_information', $product ); ?>
        
        <?php echo woocommerce_template_single_add_to_cart(); ?>
        

        
       
        </section><!-- right details product end -->
    
        </div>
    
    
    <div class="clear_0"></div>
</div>
</div>
<div class="clear_0"></div>

<?php  echo woocommerce_output_related_products(); ?>

<?php do_action( 'woocommerce_after_single_product' ); 

/*
$brands = $product->get_attribute( 'brands' );
if(!empty($brands)) { ?>
<div class="product-brands"><!-- product brands start -->
    <?php echo $brands; ?>
</div><!-- product brands end -->
<?php } */
