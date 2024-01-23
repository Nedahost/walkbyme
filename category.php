<?php get_header(); ?>
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
<?php while (have_posts()) : the_post(); ?>
    <article <?php post_class(); ?>>
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <div class="entry-content">
            <?php the_excerpt(); ?>
        </div>
    </article>
    <?php endwhile; ?>
</div><!-- wrapper end -->
<?php  get_footer();