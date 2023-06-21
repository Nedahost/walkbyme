<article id="pagecontent"><!-- page content start -->
    
    <div class="outercontentpage"><!-- content page start -->
    <?php if ( has_post_thumbnail() ){ ?>
        <div class="imagepage">
            <?php the_post_thumbnail(); ?>  
        </div>
        <div class="contentpage">
            <div class="titlepage">
                <h1>
                    <?php the_title(); ?>
                </h1>
            </div>
            <div class="textpage">
                <?php the_content(); ?>
                <?php if(is_page(19)){ ?>
                <ul>
                    <li class="productpages">
                        <a href="#">
                            <figure>
                                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/meltedstone.jpg" />
                            </figure>
                        </a>
                    </li>
                    <li class="productpages">
                        <a href="#">
                            <figure>
                                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/lion_ring.jpg" />
                            </figure>
                        </a>
                    </li>
                    <li class="productpages">
                        <a href="#">
                            <figure>
                                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/mountain.jpg" />
                            </figure>
                        </a>
                    </li>
                </ul>
                <?php } ?>
            </div>
        </div>
        <?php } else{ ?>
            <div class="page">
                <div class="titlepage">
                    <h1>
                        <?php the_title(); ?>
                    </h1>
                </div>
                <div class="detailspages">
                <?php the_content(); ?>               
                </div>
            </div>
            <?php } ?>
    </div><!-- content page end -->
        
    
</article><!-- page content end -->