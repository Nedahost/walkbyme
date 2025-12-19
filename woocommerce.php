<?php get_header(); ?>

<?php 
// Έλεγχος για συγκεκριμένη κατηγορία (ID: 135)
// Tip: Αν μπορείς, χρησιμοποίησε slug αντί για ID (π.χ. is_product_category('prosfores')) για σταθερότητα.
if ( is_product_category(135) ) { ?>
    <div class="category-header-banner">
        <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/headerjpg.jpg' ); ?>" 
             alt="<?php echo esc_attr( single_term_title('', false) ); ?>" 
             width="100%" 
             height="auto" 
             loading="lazy" />
    </div>
<?php } ?>

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