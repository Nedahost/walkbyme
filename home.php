<?php get_header(); ?>

<div class="wrapper"><!-- wrapper start -->
<div class="outerslider"><!-- outer slider start -->
  
        <div class="slideshow">
    <?php
    $args = array(
        'post_type' => 'gallery',
        'posts_per_page' => -1,
        'meta_key' => '_slider_order',
        'orderby' => 'meta_value_num',
        'order' => 'ASC'
    );
    $slider_query = new WP_Query($args);
    if ($slider_query->have_posts()) :
        while ($slider_query->have_posts()) : $slider_query->the_post();
            $button_url = get_post_meta(get_the_ID(), '_slider_button_url', true);
            $button_text = get_post_meta(get_the_ID(), '_slider_button_text', true);
    ?>
            
                
                <div class="sliderimages">
                <figure><!-- figure start -->
                    <?php the_post_thumbnail('full', ['style' => 'width: 100%; height: auto;']); ?>
                    <figcaption class="slider-caption">
                        <h2><?php the_title(); ?></h2>
                        <p><?php the_content(); ?></p>
                        <?php if ($button_url && $button_text) : ?>
                            <a href="<?php echo esc_url($button_url); ?>" class="cta-button">
                                <?php echo esc_html($button_text); ?>
                            </a>
                        <?php endif; ?>
                    </figcaption>
                </figure><!-- figure end -->
                </div>
                
                
            
    <?php
        endwhile;
        wp_reset_postdata();
    endif;
    ?>
    </div>

</div><!-- outer slider end -->


    <div class="outercarousel"><!-- outer carousel start -->
        <div class="generic-titles"><!-- generic title start -->
            <h2>
                ΟΙ ΠΡΟΤΑΣΕΙΣ ΜΑΣ
            </h2>
        </div><!-- generic title end -->
        <div class="carousel"><!-- carousel start -->
            <?php display_featured_products(); ?>
        </div><!-- carousel end -->
    </div><!-- outer carousel end -->
</div><!-- wrapper end -->
<section>
    <div class="felxbox">
        <div class="outerimagelarge">
            <div class="imagewrapper">
            
            </div>
            <div class="imagecontent">
                <header class="sectionheader"><!-- section header start -->
                    <h1><u>Χειροποίητα Κοσμήματα</u></h1>
                    <h2 class="sectionheader_title">
                        Το πάθος μας για χειροποίητα κοσμήματα από ασήμι 925 και χρυσό!
                    </h2>
                    <div class="sectionheadaer_description">
                        <p>
                            Η κατασκευή <b><u>χειροποίητων ασημένιων και χρυσών</u></b> κοσμημάτων είναι το πάθος μας. Δίνουμε μεγάλη προσοχή σε κάθε βήμα της διαδικασίας.
                        </p>
                        <p>
                            <a href="https://www.walkbyme.gr/%ce%b7-%ce%b5%cf%84%ce%b1%ce%b9%cf%81%ce%b5%ce%af%ce%b1-%ce%bc%ce%b1%cf%82/">Δείτε ποιοί είμαστε</a>
                        </p>
                    </div>
                </header>
            </div>
        </div>
    </div>
 </section>

<div class="wrapper"><!-- wrapper start -->

    <section class="hm_categories"><!-- home categories start -->
        <div class="generic-titles"><!-- generic title start -->
            <h2>
                ΑΓΟΡΕΣ ΑΝΑ ΚΑΤΗΓΟΡΙΑ
            </h2>
            <p>
                Λαμπρός σχεδιασμός και απαράμιλλη δεξιοτεχνία.
            </p>
        </div><!-- generic title end -->
        <ul>
<?php
// Λάβετε όλες τις κατηγορίες του WooCommerce
$product_categories = get_terms('product_cat',  array(
    'orderby' => 'meta_value_num',
    'meta_key' => 'category_priority',
    'order' => 'ASC',
));

foreach ($product_categories as $category) {
    $show_on_homepage = get_term_meta($category->term_id, 'show_on_homepage', true);

    if ($show_on_homepage === 'yes') { 
        $category_url = get_term_link($category);
        ?>
        <li>
            <?php 
            $category_image = get_term_meta($category->term_id, 'category_image', true);
            if (!empty($category_image)) { 
            ?>
            <figure>
                <a href="<?php echo  esc_url($category_url); ?>">
                    <?php echo '<img src="' . esc_url($category_image) . '" alt="' . esc_attr($category->name) . '" style="width:100%; height:100%;" >'; ?>
                </a>
            </figure>
            <?php } ?>
            <h3>
                <a href="<?php echo  esc_url($category_url); ?>">
                    <?php echo esc_html($category->name); ?> 
                </a>
            </h3>
            <p>
                <?php echo  esc_html($category->description); ?>
            </p>
            <span class="more">
                <a href="<?php echo  esc_url($category_url); ?>">
                    Ανακαλύψτε τα &raquo;
                </a>
            </span>
        </li>
        <?php 
    }
}

?>
        </ul>
    </section><!-- home categories end -->
</div> <!-- wrapper end -->
<?php get_footer();