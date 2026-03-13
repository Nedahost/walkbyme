<?php get_header(); ?>

<div class="wrapper">
    <?php if ( is_product_category() ) : ?>
        <div class="outercategories">
            <div class="row"><div class="outerpageinfo">
                    <div class="pageinfo"><h1 class="page-title">
                            <?php woocommerce_page_title(); ?>
                        </h1>

                        <?php 
                        // Χρήση της term_description που είναι πιο συμβατή με WooCommerce
                        $desc = term_description();
                        if ( ! empty( $desc ) ) {
                            echo '<div class="term-description">' . $desc . '</div>';
                        }
                        ?>
                    </div></div>
            </div></div>
    <?php endif; ?>

    <?php if ( ! is_home() && ! is_front_page() ) : ?>
        <div class="outerbreadcrumb"><?php 
            if ( function_exists('bcn_display') ) {
                bcn_display();
            } elseif ( function_exists('woocommerce_breadcrumb') ) {
                // Fallback στα native breadcrumbs του Woo αν λείπει το plugin
                woocommerce_breadcrumb();
            }
            ?>
        </div><?php endif; ?>

    <div class="woocommerce"><?php
        if ( have_posts() ) : 
            if ( is_singular('product') ) {
                woocommerce_content();
            } else {
                // Φόρτωση του archive template
                woocommerce_get_template('archive-product.php');
            }
        endif;
        ?>
    </div></div>

<?php get_footer(); ?>