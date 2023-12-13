<?php
/**
 * Template Name: Thank You
 */

get_header();

while (have_posts()) : the_post();
    the_content();
endwhile;

get_footer();