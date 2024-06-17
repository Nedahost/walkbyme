<?php get_header(); ?>

<div class="wrapper"><!-- wrapper start -->
<div class="outerslider"><!-- outer slider start -->
        <div class="sliderimages"><!-- slider images start -->
            <figure><!-- figure start -->
                <a href="#" >
                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/banner1.jpg"  style="width: 100%; height: auto;"  alt="sliderimage" />
                </a>
            </figure><!-- figure end -->
        </div><!-- slider images end -->
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