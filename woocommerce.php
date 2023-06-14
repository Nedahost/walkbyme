<?php get_header();  ?>
<div class="wrapper">
<?php if ( is_product_category() ){
    global $wp_query;
    // get the query object
    $cat = $wp_query->get_queried_object();
    // get the thumbnail id using the queried category term_id
    $thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true ); 
    // get the image URL
    $image = wp_get_attachment_url( $thumbnail_id ); 
    // print the IMG HTML
   // echo "<img src='{$image}' alt='' width='100%' height='auto' />";
?>  
    <div class="outercategories" style="background-image:url(<?php echo $image; ?>); background-color: #f7f7f7;">
        <div class="row"><!-- row start -->
            <div class="outerpageinfo">
                <div class="pageinfo"><!-- page info start -->
                <h1 class="page-title">
                <i> <?php woocommerce_page_title(); ?></i>
                <?php /*
                 global $wp_query;
// get the query object
$cat_obj = $wp_query->get_queried_object();


if($cat_obj)    {
    $category_name = $cat_obj->name;
    $category_desc = $cat_obj->description;
    $category_ID  = $cat_obj->term_id;
}


$term = get_term( $category_ID, 'product_cat' ); 
echo '('. $term->count . ')'; 
*/ ?>
            </h1>
            
                <?php do_action('woocommerce_archive_description'); ?>
                </div><!-- page info end -->
            </div>
        </div><!-- row end -->
            
       
    </div>
<?php
}

 /* */?>




<?php if(!is_home()){ ?>
    <div class="outerbreadcrumb"><!-- outer breadcrumb start -->
        
            <?php if(function_exists('bcn_display'))
            {
                bcn_display();
            }?>
        
    </div><!-- outer breadcrumb end -->
<?php } ?>
<div class="woocommerce"><!-- woocommerce start -->
    
    <?php
    if (have_posts()) : if (is_singular('product')) {
            woocommerce_content();
        } else {

            woocommerce_get_template('archive-product.php');
        }
    endif;
    ?>
   
</div><!-- woocommerce end -->
</div>
<?php get_footer();