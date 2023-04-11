

<?php 

/**
 * Template name: Contact page
 */


get_header(); ?>  
<?php if(!is_home()){ ?>
    <div class="outerbreadcrumb"><!-- outer breadcrumb start -->
        <div class="wrapper"><!-- wrapper start -->
            <?php if(function_exists('bcn_display'))
            {
                bcn_display();
            }?>
        </div><!-- wrapper end -->
    </div><!-- outer breadcrumb end -->
<?php } ?>

    <div class="wrapper"><!-- wrapper start -->
    <?php  while ( have_posts() ) :
            the_post();

            get_template_part( 'template-parts/page/content', 'contact' );

            // If comments are open or we have at least one comment, load up the comment template.
            if ( comments_open() || get_comments_number() ) :
                    comments_template();
            endif;

    endwhile; // End of the loop.
    ?>
</div><!-- wrapper end -->
<?php get_footer();