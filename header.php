<!DOCTYPE html>
    <html <?php language_attributes(); ?> class="no-js no-svg">
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <meta name="format-detection" content="telephone=no">
        <script src="https://analytics.ahrefs.com/analytics.js" data-key="q5e3pMcT7fo/ZnKPMuJFNA" defer="true"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
        <meta name="p:domain_verify" content="47d2a4d6f4ed2663e7cbd09e076d1c6e">
        <title><?php wp_title( '|', true, 'right' ); ?></title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed&family=Roboto+Flex:wght@200&display=swap" rel="stylesheet">
        <script type="text/javascript" async="" src="https://static.klaviyo.com/onsite/js/klaviyo.js?company_id=R2nrLY"></script>
        <?php wp_head(); ?> 
         <!-- Google Tag Manager -->
         <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-5QBSL8G');</script>
        

    
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-16633311272"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-16633311272');
</script>



<script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "mif7q4dhr5");
</script>

    </head>
    <body>
    
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5QBSL8G"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        
        <!-- End Google Tag Manager (noscript) -->
        <noscript><img height="1" width="1" style="display:none" alt="facebook" src="https://www.facebook.com/tr?id=891327725921929&ev=PageView&noscript=1"/></noscript>
        <header>
            <div class="wrapper"><!-- wrapper start -->
                <div class="shopdetails"><!-- shop details start -->
                    <ul>
                            <?php if (is_user_logged_in() ) { ?>
                            <li class="headeraccount">
                                <a href="<?php echo get_permalink( wc_get_page_id( 'myaccount' ) ); ?> " title="My account" >
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