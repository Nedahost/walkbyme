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
                'order' => 'ASC',
                'post_status' => 'publish'
            );
            
            $slider_query = new WP_Query($args);
            
            if ($slider_query->have_posts()) :
                while ($slider_query->have_posts()) : $slider_query->the_post();
                    $button_url = get_post_meta(get_the_ID(), '_slider_button_url', true);
                    $button_text = get_post_meta(get_the_ID(), '_slider_button_text', true);
            ?>
                <div class="sliderimages">
                    <figure><!-- figure start -->
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('full', array(
                                'loading' => 'lazy',
                                'alt' => get_the_title(),
                                'style' => 'width: 100%; height: auto;'
                            )); ?>
                        <?php endif; ?>
                        
                        <?php if (get_the_title() || get_the_content() || ($button_url && $button_text)) : ?>
                            <figcaption class="slider-caption">
                                <?php if (get_the_title()) : ?>
                                    <h2><?php the_title(); ?></h2>
                                <?php endif; ?>
                                
                                <?php if (get_the_content()) : ?>
                                    <div class="slider-content">
                                        <?php the_content(); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($button_url && $button_text) : ?>
                                    <a href="<?php echo esc_url($button_url); ?>" 
                                       class="cta-button"
                                       rel="<?php echo (strpos($button_url, home_url()) === false) ? 'noopener' : ''; ?>">
                                        <?php echo esc_html($button_text); ?>
                                    </a>
                                <?php endif; ?>
                            </figcaption>
                        <?php endif; ?>
                    </figure><!-- figure end -->
                </div>
            <?php
                endwhile;
                wp_reset_postdata();
            else :
            ?>
                <div class="sliderimages">
                    <figure>
                        <div class="no-slider-content">
                            <h2><?php esc_html_e('Καλώς ήρθατε', 'walkbyme'); ?></h2>
                            <p><?php esc_html_e('Ανακαλύψτε τα προϊόντα μας', 'walkbyme'); ?></p>
                        </div>
                    </figure>
                </div>
            <?php endif; ?>
        </div>
    </div><!-- outer slider end -->

    <div class="outercarousel"><!-- outer carousel start -->
        <div class="generic-titles"><!-- generic title start -->
            <h2>ΟΙ ΠΡΟΤΑΣΕΙΣ ΜΑΣ</h2>
        </div><!-- generic title end -->
        
        <div class="carousel"><!-- carousel start -->
            <?php 
            if (function_exists('display_featured_products')) {
                display_featured_products(); 
            } else {
                echo '<p>' . esc_html__('Δεν υπάρχουν διαθέσιμα προϊόντα προς το παρόν.', 'walkbyme') . '</p>';
            }
            ?>
        </div><!-- carousel end -->
    </div><!-- outer carousel end -->
</div><!-- wrapper end -->

<section>
    <div class="felxbox">
        <div class="outerimagelarge">
            <div class="imagewrapper">
                <!-- Background image will be handled via CSS -->
            </div>
            <div class="imagecontent">
                <header class="sectionheader"><!-- section header start -->
                    <h3><u>Χειροποίητα Κοσμήματα</u></h3>
                    <h4 class="sectionheader_title">
                        Το πάθος μας για χειροποίητα κοσμήματα από ασήμι 925 και χρυσό!
                    </h4>
                    <div class="sectionheadaer_description">
                        <p>
                            Η κατασκευή <strong><u>χειροποίητων ασημένιων και χρυσών</u></strong> κοσμημάτων είναι το πάθος μας. Δίνουμε μεγάλη προσοχή σε κάθε βήμα της διαδικασίας.
                        </p>
                        <p>
                            <?php 
                            $about_page = get_page_by_path('η-εταιρεία-μας');
                            if ($about_page) {
                                $about_url = get_permalink($about_page->ID);
                            } else {
                                // Fallback to search for about page
                                $about_pages = get_pages(array(
                                    'meta_key' => '_wp_page_template',
                                    'meta_value' => 'page-about.php'
                                ));
                                $about_url = !empty($about_pages) ? get_permalink($about_pages[0]->ID) : home_url('/about/');
                            }
                            ?>
                            <a href="<?php echo esc_url($about_url); ?>">
                                <?php esc_html_e('Δείτε ποιοί είμαστε', 'walkbyme'); ?>
                            </a>
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
            <h2>ΑΓΟΡΕΣ ΑΝΑ ΚΑΤΗΓΟΡΙΑ</h2>
            <p>Λαμπρός σχεδιασμός και απαράμιλλη δεξιοτεχνία.</p>
        </div><!-- generic title end -->
        
        <ul>
            <?php
            // Enhanced categories query with caching
            $cache_key = 'homepage_categories';
            $product_categories = wp_cache_get($cache_key, 'walkbyme_categories');
            
            if (false === $product_categories) {
                $product_categories = get_terms('product_cat', array(
                    'orderby' => 'meta_value_num',
                    'meta_key' => 'category_priority',
                    'order' => 'ASC',
                    'hide_empty' => true,
                    'meta_query' => array(
                        array(
                            'key' => 'show_on_homepage',
                            'value' => 'yes',
                            'compare' => '='
                        )
                    )
                ));
                
                // Cache for 30 minutes
                wp_cache_set($cache_key, $product_categories, 'walkbyme_categories', 1800);
            }

            if (!empty($product_categories) && !is_wp_error($product_categories)) :
                foreach ($product_categories as $category) :
                    $category_url = get_term_link($category);
                    if (is_wp_error($category_url)) continue;
                    
                    $category_image = get_term_meta($category->term_id, 'category_image', true);
            ?>
                    <li>
                        <?php if (!empty($category_image)) : ?>
                            <figure>
                                <a href="<?php echo esc_url($category_url); ?>" 
                                   title="<?php echo esc_attr(sprintf(__('Προβολή %s', 'walkbyme'), $category->name)); ?>">
                                    <img src="<?php echo esc_url($category_image); ?>" 
                                         alt="<?php echo esc_attr($category->name); ?>" 
                                         loading="lazy"
                                         width="300" 
                                         height="200" />
                                </a>
                            </figure>
                        <?php endif; ?>
                        
                        <h3>
                            <a href="<?php echo esc_url($category_url); ?>">
                                <?php echo esc_html($category->name); ?>
                            </a>
                        </h3>
                        
                        <?php if (!empty($category->description)) : ?>
                            <p><?php echo esc_html($category->description); ?></p>
                        <?php endif; ?>
                        
                        <span class="more">
                            <a href="<?php echo esc_url($category_url); ?>">
                                <?php esc_html_e('Ανακαλύψτε τα', 'walkbyme'); ?> &raquo;
                            </a>
                        </span>
                    </li>
            <?php 
                endforeach;
            else :
            ?>
                <li>
                    <p><?php esc_html_e('Δεν υπάρχουν διαθέσιμες κατηγορίες προς το παρόν.', 'walkbyme'); ?></p>
                </li>
            <?php endif; ?>
        </ul>
    </section><!-- home categories end -->
</div><!-- wrapper end -->

<?php get_footer(); ?>