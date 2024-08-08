<?php get_header(); ?>

<?php if (!is_home()) { ?>
    <div class="outerbreadcrumb">
        <div class="wrapper">
            <?php
            if (function_exists('bcn_display')) {
                bcn_display();
            }
            ?>
        </div>
    </div>
<?php } ?>

<div class="wrapper">
    <div class="container">
        <div class="row">
            <!-- Main content area (70%) -->
            <div class="col-main">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <div class="article-wrapper"><!-- article wrapper start -->
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="post-thumbnail">
                                    <?php the_post_thumbnail('large'); ?>
                                </div>
                            <?php endif; ?>

                            <div class="article-content"><!-- article content start -->
                                <h1 class="article-title"><?php the_title(); ?></h1>
                                <?php the_content(); ?>
                            </div><!-- article content end -->
                        </div><!-- article wrapper end -->
                    </article>

                

                <?php endwhile; ?>
            </div>

            <!-- Sidebar (30%) -->
            <div class="col-sidebar">
                <?php get_sidebar('jewelry'); ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>