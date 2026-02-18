<?php get_header(); ?>

<div class="wrapper"><div class="outerslider"><div class="slideshow">
            <?php
            $args = array(
                'post_type'      => 'gallery',
                'posts_per_page' => -1,
                'meta_key'       => '_slider_order',
                'orderby'        => 'meta_value_num',
                'order'          => 'ASC',
                'post_status'    => 'publish',
                'no_found_rows'  => true // Optimization: Don't count total rows since we show all
            );
            
            $slider_query = new WP_Query($args);
            
            if ($slider_query->have_posts()) :
                while ($slider_query->have_posts()) : $slider_query->the_post();
                    $button_url  = get_post_meta(get_the_ID(), '_slider_button_url', true);
                    $button_text = get_post_meta(get_the_ID(), '_slider_button_text', true);
            ?>
                <div class="sliderimages">
                    <figure><?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('full', array(
                                'loading' => 'lazy', // SEO: Lazy loading
                                'alt'     => get_the_title(),
                                'style'   => 'width: 100%; height: auto;'
                            )); ?>
                        <?php endif; ?>
                        
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
                                   rel="<?php echo (strpos($button_url, home_url()) === false) ? 'nofollow noopener' : ''; ?>">
                                    <?php echo esc_html($button_text); ?>
                                </a>
                            <?php endif; ?>
                        </figcaption>
                    </figure></div>
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
    </div>
    <div class="outercarousel"><div class="generic-titles">
        <?php esc_html_e('Επιλογές που Ξεχωρίζουν', 'walkbyme'); ?>
        </div><div class="carousel"><?php 
            if (function_exists('display_featured_products')) {
                display_featured_products(); 
            } else {
                echo '<p>' . esc_html__('Δεν υπάρχουν διαθέσιμα προϊόντα προς το παρόν.', 'walkbyme') . '</p>';
            }
            ?>
        </div></div></div>
        
        <section class="home-intro-section">
    <div class="felxbox">
        <div class="outerimagelarge">
            <div class="imagewrapper"></div> <div class="imagecontent">
                <header class="sectionheader">
                    <h2><?php esc_html_e('Χειροποίητα Κοσμήματα', 'walkbyme'); ?></h2>
                    
                    <h3 class="sectionheader_title">
                        <?php esc_html_e('Ασήμι 925 και Χρυσό σε μοναδικά σχέδια', 'walkbyme'); ?>
                    </h3>
                    
                    <div class="sectionheadaer_description">
                        <p>
                            <?php 
                            /* COPYWRITING UPDATE: 
                                Αφαιρέσαμε το "κατασκευάζουμε" και το "πάθος".
                                Μιλάμε για "Ελληνική Υπογραφή" και "Λεπτομέρεια".
                                Αυτό ταιριάζει και σε Curator και σε Maker.
                            */
                            esc_html_e('Η αυθεντική ομορφιά του χειροποίητου σε κάθε λεπτομέρεια. Κοσμήματα με ταυτότητα, που ξεχωρίζουν για το φινίρισμα και την αντοχή τους.', 'walkbyme'); 
                            ?>
                        </p>
                        
                        <p>
                            <?php 
                            $about_url = home_url('/about/'); 
                            $about_page = get_page_by_path('η-εταιρεία-μας');
                            if ($about_page) {
                                $about_url = get_permalink($about_page->ID);
                            }
                            ?>
                            <a href="<?php echo esc_url($about_url); ?>" class="read-more-link">
                                <?php esc_html_e('Η Φιλοσοφία μας', 'walkbyme'); ?> &rarr;
                            </a>
                        </p>
                    </div>
                </header>
            </div>
        </div>
    </div>
</section>

<div class="wrapper"><section class="hm_categories" aria-label="<?php esc_attr_e('Product Categories', 'walkbyme'); ?>">
<div class="generic-titles">
    <?php esc_html_e('Κοσμήματα ανά Κατηγορία', 'walkbyme'); ?>
            <p><?php esc_html_e('Μοναδικά σχέδια από Ασήμι 925 & Χρυσό σε Δαχτυλίδια, Κολιέ και Βραχιόλια.', 'walkbyme'); ?></p>
        </div><ul>
            <?php
            $cache_key = 'homepage_categories_v2';
            $product_categories = wp_cache_get($cache_key, 'walkbyme_categories');
            
            if (false === $product_categories) {
                $product_categories = get_terms('product_cat', array(
                    'orderby'    => 'meta_value_num',
                    'meta_key'   => 'category_priority',
                    'order'      => 'ASC',
                    'hide_empty' => true,
                    'meta_query' => array(
                        array(
                            'key'     => 'show_on_homepage',
                            'value'   => 'yes',
                            'compare' => '='
                        )
                    )
                ));
                wp_cache_set($cache_key, $product_categories, 'walkbyme_categories', 3600); // 1 hour cache
            }

            if (!empty($product_categories) && !is_wp_error($product_categories)) :
                foreach ($product_categories as $category) :
                    $category_url = get_term_link($category);
                    if (is_wp_error($category_url)) continue;
                    
                    $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                    // Fallback to custom meta if standard thumbnail is empty
                    $category_image = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : get_term_meta($category->term_id, 'category_image', true);
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
                        
                        <h2>
                            <a href="<?php echo esc_url($category_url); ?>">
                                <?php echo esc_html($category->name); ?>
                            </a>
                        </h2>
                        
                        <?php if (!empty($category->description)) : ?>
                            <p><?php echo wp_trim_words(esc_html($category->description), 15); ?></p>
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
                <li class="no-cats">
                    <p><?php esc_html_e('Δεν υπάρχουν διαθέσιμες κατηγορίες προς το παρόν.', 'walkbyme'); ?></p>
                </li>
            <?php endif; ?>
        </ul>
    </section></div><?php get_footer(); ?>