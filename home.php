<?php get_header(); ?>
<div class="wrapper"><!-- wrapper start -->
<div class="outerslider"><!-- outer slider start -->
        <div class="sliderimages"><!-- slider images start -->
            <figure><!-- figure start -->
                <a href="">
                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/slider-rings.png" />
                </a>
            </figure><!-- figure end -->
        </div><!-- slider images end -->
        

    </div><!-- outer slider end -->

    

    <section class="hm_categories"><!-- home categories start -->

    <div class="generic-titles"><!-- generic title start -->
        <p>
            <span class="font-size-24">ΑΓΟΡΕΣ ΑΝΑ ΚΑΤΗΓΟΡΙΑ</span>
        </p>
        <p>
            Λαμπρός σχεδιασμός και απαράμιλλη δεξιοτεχνία.
        </p>
    </div><!-- generic title end -->
    
        <ul>
            <li>
                <figure>
                    <a href="">
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/demo.jpg" />
                    </a>
                </figure>
                <h3>
                    <a href="">
                        Δαχτυλίδια
                    </a>
                </h3>
                <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
                </p>
                <span>
                    <a href="">
                        Ανακαλύψτε τα &raquo;
                    </a>
                </span>
            </li>
            <li>
                <figure>
                    <a href="">
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/demo.jpg" />
                    </a>
                </figure>
                <h3>
                    <a href="">
                        Κολιέ
                    </a>
                </h3>
                <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
                </p>
                <span>
                    <a href="">
                        Ανακαλύψτε τα &raquo;
                    </a>
                </span>
            </li>
            <li>
                <figure>
                    <a href="">
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/demo.jpg" />
                    </a>
                </figure>
                <h3>
                    <a href="">
                        Σκουλαρίκια
                    </a>
                </h3>
                <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
                </p>
                <span>
                    <a href="">
                        Ανακαλύψτε τα &raquo;
                    </a>
                </span>
            </li>
            <li>
                <figure>
                    <a href="">
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/demo.jpg" />
                    </a>
                </figure>
                <h3>
                    <a href="">
                        Βραχιόλια
                    </a>
                </h3>
                <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
                </p>
                <span>
                    <a href="">
                        Ανακαλύψτε τα &raquo;
                    </a>
                </span>
            </li>
        </ul>
    </section><!-- home categories end -->
</div> <!-- wrapper end -->
<?php /*
    <div class="homegifts"><!-- home gifts start -->
        <figure>
            <a href="">
            <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/4.jpg" />
            </a>
        </figure>
        <div class="giftscontent"><!-- gifts content start -->
            <h4>
                Special gifts for her
            </h4>
            <p>
                Κοσμήματα που θα κάνουν κάθε περίσταση ξεχωστή.
            </p>
            <span>
                <a href="" class="buttonstyle">
                    Αγόρασε τώρα &raquo;
                </a>
            </span>
        </div><!-- gifts content end -->
    </div><!-- home gifts end -->
    */
?>


<div id="outerparallax"><!-- outer parallax stat -->

</div><!-- outer parallax end -->


<div class="wrapper"><!-- wrapper start -->
    
    <div class="outercarousel"><!-- outer carousel start -->

    <div class="carouseltitle"> <!-- carousel title start -->
        Οι ΠΡΟΤΑΣΕΙΣ ΜΑΣ
    </div><!-- carousel title end -->

        
            <div class="carousel"><!-- carousel start -->
                <?php
                    $meta_query  = WC()->query->get_meta_query();
                    $tax_query   = WC()->query->get_tax_query();
                    $tax_query[] = array(
                        'taxonomy' => 'product_visibility',
                        'field'    => 'name',
                        'terms'    => 'featured',
                        'operator' => 'IN',
                    );

                    $args = array(
                        'post_type'           => 'product',
                        'post_status'         => 'publish',
                        'posts_per_page'      => 12,
                        'meta_query'          => $meta_query,
                        'tax_query'           => $tax_query,
                    );
                    $wc_query = new WP_Query( $args );
                    if ($wc_query->have_posts()) : ?>
          
                <?php while ($wc_query->have_posts()) : $wc_query->the_post();  ?>
                    <div>
                        <figure>
                            <a href="<?php the_permalink(); ?>">
                                <?php
                                $attr = array(
                                        // 'itemprop' => 'image'
                                );
                                $thumb = get_the_post_thumbnail($loop->ID, $attr);
                                echo $thumb;
                                ?>
                            </a> 
                        </figure>
                        <h3>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_title(); ?>
                            </a>
                        </h3>
                    </div>
                <?php endwhile;
                wp_reset_postdata(); ?>
                
                <?php endif; ?>
            </div><!-- carousel end -->
        
    </div><!-- outer carousel end -->

    </div><!-- wrapper end -->
<?php /*
<section class="smart_search"><!-- smart search start -->
  Βρες το ΙΔΑΝΙΚΟ ΚΟΣΜΗΜΑ για σένα

  <p>
  Για εσάς ή για ένα προσωπικό δώρο ανάμεσα σε μια μεγάλη συλλογή σε χρυσά και ασημένια κοσμήματα κατάλληλα για κάθε στιγμή και περίσταση 
  </p>
</section><!-- smart search end -->
    
<section class="our_suggestions"><!-- our suggestions start -->



<div class="outercarousel"><!-- outer carousel start -->


<h3>Οι Προτάσεις μας</h3>

        <div class="carousel"><!-- carousel start -->
            <?php
                $meta_query  = WC()->query->get_meta_query();
                $tax_query   = WC()->query->get_tax_query();
                $tax_query[] = array(
                    'taxonomy' => 'product_visibility',
                    'field'    => 'name',
                    'terms'    => 'featured',
                    'operator' => 'IN',
                );

                $args = array(
                    'post_type'           => 'product',
                    'post_status'         => 'publish',
                    'posts_per_page'      => 12,
                    'meta_query'          => $meta_query,
                    'tax_query'           => $tax_query,
                );
                $wc_query = new WP_Query( $args );
                if ($wc_query->have_posts()) : ?>
            <ul>
            <?php while ($wc_query->have_posts()) : $wc_query->the_post();  ?>
                <li>
                    <figure>
                        <a href="<?php the_permalink(); ?>">
                            <?php
                            $attr = array(
                                    // 'itemprop' => 'image'
                            );
                            $thumb = get_the_post_thumbnail($loop->ID, $attr);
                            echo $thumb;
                            ?>
                        </a> 
                    </figure>
                    <h3>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h3>
                    <div class="homeprice"><!-- home price start -->
                        <?php 
                         global $post, $product;
                        echo $product->get_regular_price() .' &euro;'; 
                        $product->get_sale_price();
                        $product->get_price();
                        ?>
                    </div><!-- home price end -->
                </li>
            <?php endwhile;
            wp_reset_postdata(); ?>
            </ul>
            <?php endif; ?>
        </div><!-- carousel end -->
    
</div><!-- outer carousel end -->

</section><!-- our suggestions end -->


*/
?>


<?php get_footer();