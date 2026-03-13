<?php
defined( 'ABSPATH' ) || exit;

global $product;

do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
    echo get_the_password_form();
    return;
}
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class(); ?>>
    <div class="bgproducts">
        <div class="productflex">
            
            <div class="product-gallery">
                <?php
                $main_image_id = $product->get_image_id();
                $gallery_ids = $product->get_gallery_image_ids();
                
                $all_images = array();
                if ($main_image_id) {
                    $all_images[] = $main_image_id;
                }
                if (!empty($gallery_ids)) {
                    $all_images = array_merge($all_images, $gallery_ids);
                }
                
                $total = count($all_images);
                
                if ($total > 0) :
                ?>
                    <!-- Mobile Carousel -->
                    <div class="product-gallery__track">
                        <?php foreach ($all_images as $image_id) : ?>
                            <div class="product-gallery__slide">
                                <?php echo wp_get_attachment_image($image_id, 'large'); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="product-gallery__counter">
                        <span class="product-gallery__current">1</span> | <?php echo esc_html($total); ?>
                    </div>

                    <!-- Desktop Grid -->
                    <div class="product-gallery__desktop">
                        <?php
                        $index = 0;
                        ?>
                        <div class="product-gallery__item product-gallery__item--full">
                            <?php echo wp_get_attachment_image($all_images[0], 'large'); ?>
                        </div>
                        <?php
                        $index = 1;
                        
                        while ($index < $total) {
                            $remaining = $total - $index;
                            
                            if ($remaining === 1) {
                                ?>
                                <div class="product-gallery__item product-gallery__item--full">
                                    <?php echo wp_get_attachment_image($all_images[$index], 'large'); ?>
                                </div>
                                <?php
                                $index++;
                            } elseif ($remaining >= 2) {
                                ?>
                                <div class="product-gallery__row">
                                    <div class="product-gallery__item">
                                        <?php echo wp_get_attachment_image($all_images[$index], 'medium_large'); ?>
                                    </div>
                                    <div class="product-gallery__item">
                                        <?php echo wp_get_attachment_image($all_images[$index + 1], 'medium_large'); ?>
                                    </div>
                                </div>
                                <?php
                                $index += 2;
                            }
                        }
                        ?>
                    </div>

                    <!-- Lightbox -->
                    <div class="product-lightbox" id="productLightbox">
                        <button class="product-lightbox__close">&times;</button>
                        <button class="product-lightbox__nav product-lightbox__nav--prev">&#8249;</button>
                        <button class="product-lightbox__nav product-lightbox__nav--next">&#8250;</button>
                        <img class="product-lightbox__image" src="" alt="">
                        <div class="product-lightbox__counter">
                            <span class="product-lightbox__current">1</span> / <?php echo esc_html($total); ?>
                        </div>
                    </div>

                <?php
                else :
                ?>
                    <div class="product-gallery__item product-gallery__item--full">
                        <img src="<?php echo esc_url(wc_placeholder_img_src()); ?>" alt="<?php esc_attr_e('Placeholder', 'walkbyme'); ?>">
                    </div>
                <?php
                endif;
                ?>
            </div>
    
            <section class="rightdetailsproduct">
                <h1><?php echo wp_kses_post($product->get_name()); ?></h1>
                
                <div class="product-identity">
                    <?php 
                        $slogan = get_post_meta($product->get_id(), '_text_field', true); 
                        if ( $slogan ) {
                            echo $slogan;
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
                                $availability_label = get_availability_label();
                                echo ' • <b>' . esc_html($availability_label) . ':</b> ' . esc_html($availability);
                            }
                        ?>
                    </div> 
                </div>

                <div class="singleprice">
                    <?php
                    $regular_price = $product->get_regular_price();
                    $sale_price    = $product->get_sale_price();
                    
                    if ( $product->is_type('variable') ) {
                        $min_price = $product->get_variation_price('min', true);
                        $max_price = $product->get_variation_price('max', true);
                        $regular_price = $product->get_variation_regular_price('min', true);
                        $sale_price    = $min_price;

                        if ( $min_price !== $max_price ) {
                            echo '<span class="lowcblack">' . wc_price($min_price) . ' - ' . wc_price($max_price) . '</span>';
                        } else {
                            goto render_price; 
                        }
                    } else {
                        render_price:
                        if ( $product->is_on_sale() && $regular_price ) {
                            echo '<span class="price-container">';
                            echo '<span class="product-standard-price"> Τιμή: ' . wc_price($regular_price) . '</span>';
                            echo '<span class="lowred">' . wc_price($sale_price) . '</span>';
                            echo '</span>';

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

                <div class="product-handmade-note">
                    <i><?php echo wp_kses_post(get_product_handmade_note()); ?></i>
                </div>

                <div class="productContent">
                    <?php the_content(); ?>
                </div>

                <?php
                $accordion_items = get_option('product_tabs_data', array());

                if (!empty($accordion_items)) {
                    echo '<div class="accordion product-accordion">';
                    
                    echo '<div class="accordion__item">';
                    echo '<div class="accordion__title">Χαρακτηριστικά <span class="accordion__icon"></span></div>';
                    echo '<div class="accordion__content"><div class="accordion__inner">';
                    do_action('woocommerce_product_additional_information', $product);
                    echo '</div></div></div>';
                    
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

<div class="faqproducts"></div>

<?php echo woocommerce_output_related_products(); ?>