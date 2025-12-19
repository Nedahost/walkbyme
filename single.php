<?php 
/**
 * The template for displaying all single posts
 */
get_header(); 
?>

<div class="outerbreadcrumb">
    <div class="wrapper">
        <?php
        if ( function_exists('bcn_display') ) {
            bcn_display();
        }
        ?>
    </div>
</div>

<div class="wrapper">
    <div class="container">
        <div class="row">
            <main class="col-main">
                <?php 
                while ( have_posts() ) : 
                    the_post(); 
                ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <div class="main-article"><header class="entry-header">
                                <h1 class="article-title"><?php the_title(); ?></h1>
                            </header>

                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="article-image"><figure>
                                        <?php 
                                        the_post_thumbnail('large', array(
                                            'loading' => 'lazy',
                                            'class'   => 'img-responsive'
                                        )); 
                                        ?>
                                    </figure>
                                </div><?php endif; ?>

                            <div class="article-content"><?php 
                                the_content(); 
                                
                                // Υποστήριξη για άρθρα χωρισμένα σε σελίδες ()
                                wp_link_pages( array(
                                    'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'walkbyme' ),
                                    'after'  => '</div>',
                                ) );
                                ?>
                            </div><footer class="entry-footer">
                                <?php
                                // Εδώ μπορείς μελλοντικά να προσθέσεις tags ή categories
                                // edit_post_link( __('Edit', 'walkbyme'), '<span class="edit-link">', '</span>' );
                                ?>
                            </footer>
                        </div><?php
                        // Εμφάνιση σχολίων αν είναι ενεργά
                        if ( comments_open() || get_comments_number() ) :
                            comments_template();
                        endif;
                        ?>

                    </article>

                <?php endwhile; ?>
            </main>

            <aside class="col-sidebar">
                <?php get_sidebar('jewelry'); ?>
            </aside>
        </div>
    </div>
</div>

<?php get_footer(); ?>