<?php
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

<?php while (have_posts()) : the_post();
    ?>
    <article <?php post_class(); ?>>
        <h1><?php the_title(); ?></h1>
        <div class="entry-meta">
            <span class="date"><?php the_date(); ?></span>
            <span class="author">by <?php the_author(); ?></span>
        </div>
        <div class="entry-content">
            <?php the_content(); ?>
        </div>
    </article>
    <?php
endwhile; ?>
</div><!-- wrapper end -->
<?php
get_footer();
?>