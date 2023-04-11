<ul>
        <?php while (have_posts()): the_post(); ?>
        <li><!-- prepei na valw prothema -->
            <figure>
                <?php the_post_thumbnail() ?>
            </figure>
            <div class="content hmcontent"><!-- conent start -->
                <div class="mdcontent"><!-- middle content start -->
                    <?php $the_content = apply_filters('the_content', get_the_content());
                    if ( !empty($the_content) ) { ?>
                    
                      <?php echo $the_content; ?>
                    
                    <?php } ?>
                </div><!-- middle content end -->
            </div><!-- conent end -->
        </li>
        <?php 
        $textarea1 = get_post_meta( get_the_ID(), 'opsonexperiences_textarea1', true ); 
        $textarea2 = get_post_meta( get_the_ID(), 'opsonexperiences_textarea2', true );
        $textarea3 = get_post_meta( get_the_ID(), 'opsonexperiences_textarea3', true );
        if(!empty($textarea1)){ ?>
        <li>
            <figure>
                <?php 
                $post_thumbnail_id = MultiPostThumbnails::get_post_thumbnail_id('page', 'second-option', get_the_ID());
                $thumb_url = wp_get_attachment_image_src($post_thumbnail_id,  true);
                $images = $thumb_url[0];
                ?>
                <img src="<?php echo $images;  ?>" alt="" />
            </figure>
            <div class="content"><!-- conent start -->
                <div class="mdcontent"><!-- middle content start -->
                    <div style="margin: 0px;font-size: 0.838rem; 
                                        padding: 0px 29px;
                                        line-height: 22px;">
                        <?php echo $textarea1; ?>
                    </div>
                </div><!-- middle content end -->
            </div><!-- conent end -->
            
        </li>
        <?php }if(!empty ($textarea2)){ ?>
        <li>
            <figure>
                <?php 
                $post_thumbnail_id = MultiPostThumbnails::get_post_thumbnail_id('page', 'third-option', get_the_ID());
                $thumb_url = wp_get_attachment_image_src($post_thumbnail_id,  true);
                $images = $thumb_url[0];
                ?>
                <img src="<?php echo $images;  ?>" alt="" />
            </figure>
        
            <div class="content hmcontent"><!-- conent start -->
                <div class="mdcontent"><!-- middle content start -->
                    <?php echo $textarea2; ?>
                </div><!-- middle content end -->
            </div><!-- conent end -->
        </li>
        <?php } endwhile; ?> 
    </ul>