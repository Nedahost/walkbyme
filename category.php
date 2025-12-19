<?php get_header(); ?>

<div class="outerbreadcrumb"><div class="wrapper"><?php 
        // Ο έλεγχος !is_home() είναι περιττός εδώ γιατί το category.php δεν τρέχει ποτέ στην αρχική
        if ( function_exists('bcn_display') ) {
            bcn_display();
        }
        ?>
    </div></div><div class="wrapper"><main id="main-content" class="site-main">
        
        <header class="page-header archive-header">
            <h1 class="page-title"><?php single_cat_title(); ?></h1>
            <?php
            $category_description = category_description();
            if ( ! empty( $category_description ) ) {
                echo '<div class="archive-description">' . $category_description . '</div>';
            }
            ?>
        </header>

        <div class="outerarricles">
            <?php if ( have_posts() ) : ?>
                
                <?php while ( have_posts() ) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        
                        <?php if ( has_post_thumbnail() ) : ?>
                            <figure class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php 
                                    // Χρήση 'large' ή 'medium_large' για καλύτερη απόδοση αντί για full size
                                    the_post_thumbnail('large', array(
                                        'loading' => 'lazy',
                                        'alt'     => get_the_title() // Fallback alt text
                                    )); 
                                    ?>
                                </a>
                            </figure>
                        <?php endif; ?>
                        
                        <header class="entry-header">
                            <h2>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h2>
                        </header>

                        <div class="sortdescription"><?php the_excerpt(); ?>
                        </div></article>
                <?php endwhile; ?>

                <div class="pagination-wrapper">
                    <?php
                    the_posts_pagination( array(
                        'mid_size'  => 2,
                        'prev_text' => __( '&larr; Προηγούμενα', 'walkbyme' ),
                        'next_text' => __( 'Επόμενα &rarr;', 'walkbyme' ),
                    ) );
                    ?>
                </div>

            <?php else : ?>
                <p><?php esc_html_e( 'Δεν βρέθηκαν άρθρα σε αυτή την κατηγορία.', 'walkbyme' ); ?></p>
            <?php endif; ?>
        </div>
        
    </main>
</div><?php get_footer(); ?>