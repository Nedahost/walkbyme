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
    <div class="outerarricles">
        <?php while (have_posts()) : the_post(); ?>
            <article <?php post_class(); ?>>
                <?php if (has_post_thumbnail()) : ?>
                    <figure>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail(); ?>
                        </a>
                    </figure>
                <?php endif; ?>
                <h2>
                    <a href="<?php the_permalink(); ?>">
                        <?php the_title(); ?>
                    </a>
                </h2>
                <div class="sortdescription"><!-- sort description start -->
                    <?php the_excerpt(); ?>
                </div><!-- sort description end -->
            </article>
            <?php endwhile; ?>
    </div>
</div><!-- wrapper end -->
<?php  get_footer();