<!DOCTYPE html>
    <html <?php language_attributes(); ?> class="no-js no-svg">
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <meta name="format-detection" content="telephone=no">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
        <title><?php wp_title( '|', true, 'right' ); ?></title>
        <?php wp_head(); ?>
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-5QBSL8G');</script>
        <!-- End Google Tag Manager -->
    </head>
    <body>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5QBSL8G"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
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