<!DOCTYPE html>
    <html <?php language_attributes(); ?> class="no-js no-svg">
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <meta name="format-detection" content="telephone=no">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
        <meta name="facebook-domain-verification" content="hywzavqq5x79jm858z7t71fq8gur2m" />
        <title><?php wp_title( '|', true, 'right' ); ?></title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed&family=Roboto+Flex:wght@200&display=swap" rel="stylesheet">
        <?php wp_head(); ?> 
    </head>
    <body>
        <header>
            <div class="wrapper"><!-- wrapper start -->
                <div class="shopdetails"><!-- shop details start -->
                    <ul>
                            <?php if (is_user_logged_in() ) { ?>
                            <li class="headeraccount">
                                <a href="<?php echo get_permalink( wc_get_page_id( 'myaccount' ) ); ?> " title="My account" />
                                <span> Ο Λογαριασμός μου </span>
                                </a>
                            </li>
                            <li class="headerlogout">
                                <a href="<?php echo wp_logout_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?> " title="Logout">
                                   <span>  Αποσύνδεση </span>
                                </a>
                            </li>
                        <?php }elseif (!is_user_logged_in() ) { ?>
                            <li class="headeraccount"><a href="<?php echo get_permalink( wc_get_page_id( 'myaccount' ) ); ?> " title="είσοδος">
                                    <span>  Είσοδος / Εγγραφή </span>
                                </a>
                            </li> 
                        <?php } ?>
                            <li class="headercart">
                                <a class="cart-customlocation" href="<?php echo wc_get_cart_url(); ?>" title="<?php _e( 'View your shopping cart' ); ?>">

                               <?php echo WC()->cart->get_cart_total() . ' (' . WC()->cart->get_cart_contents_count() .')' ; ?></a>
                        </li>
                        </ul>
                </div><!-- shop details end -->
                <div id="logo"><!-- logo start -->
                    <?php
                    $header_image = get_header_image();
                    if ( ! empty( $header_image ) ) { ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" rel="home">
                        <img src="<?php header_image(); ?>" height="<?php echo get_custom_header()->height; ?>" width="<?php echo get_custom_header()->width; ?>" alt="<?php echo get_bloginfo( 'name' ); ?>" />
                    </a>
                    <?php } ?>
                </div><!-- logo end -->
                <nav>
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'primary',
                            'menu_class' => 'dropdown'
                        )
                    );
                    ?>
                </nav>
            </div><!-- wrapper end -->
        </header>
    <main>