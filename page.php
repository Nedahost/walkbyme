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
    <div class="container"><!-- container start -->
        <?php if(have_posts()): ?>
        <?php get_template_part('template-parts/post/content', 'page'); ?>
        <?php else: ?>

        <?php endif; ?>
        <div class="clear_0"></div>
    </div><!-- container end -->
</div><!-- wrapper end -->
<?php get_footer(); 