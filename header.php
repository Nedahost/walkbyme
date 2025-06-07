<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <meta name="format-detection" content="telephone=no">
    <meta name="p:domain_verify" content="47d2a4d6f4ed2663e7cbd09e076d1c6e">
    
    <!-- Title Tag -->
    <title><?php wp_title('|', true, 'right'); ?></title>
    
    <!-- Google Fonts Preload for Performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@100..900&display=swap" rel="stylesheet">
    

    

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header>
    <div class="topeader"><!-- top header start -->
        <div class="wrapper"><!-- wrapper start -->
            ΔΩΡΕΑΝ ΜΕΤΑΦΟΡΙΚΑ ΣΕ ΟΛΗ ΤΗΝ ΕΛΛΑΔΑ | ΔΩΡΕΑΝ ΑΝΤΙΚΑΤΑΒΟΛΗ ΣΕ ΠΑΡΑΓΓΕΛΙΕΣ ΑΝΩ ΤΩΝ 40€
        </div><!-- wrapper end -->
    </div><!-- top header end -->
    
    <div class="wrapper"><!-- wrapper start -->
        <div id="outerheader">
            <div id="logo"><!-- logo start -->
                <?php
                $header_image = get_header_image();
                if (!empty($header_image)) { ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" 
                       title="<?php echo esc_attr(get_bloginfo('name')); ?>" 
                       rel="home">
                        <img src="<?php header_image(); ?>" 
                             height="<?php echo get_custom_header()->height; ?>" 
                             width="<?php echo get_custom_header()->width; ?>" 
                             alt="<?php echo esc_attr(get_bloginfo('name')); ?>" />
                    </a>
                <?php } ?>
            </div><!-- logo end -->
            
            <nav role="navigation" aria-label="<?php esc_attr_e('Primary Navigation', 'walkbyme'); ?>">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class' => 'dropdown',
                    'container' => false,
                    'fallback_cb' => false
                ));
                ?>
            </nav>

            <div class="shopdetails"><!-- shop details start -->
                <ul>
                    <?php if (function_exists('is_user_logged_in') && is_user_logged_in()) { ?>
                        <li class="headeraccount">
                            <a href="<?php echo function_exists('wc_get_page_id') ? esc_url(get_permalink(wc_get_page_id('myaccount'))) : '#'; ?>" 
                               title="<?php esc_attr_e('My Account', 'walkbyme'); ?>"
                               aria-label="<?php esc_attr_e('My Account', 'walkbyme'); ?>"></a>
                        </li>
                        <li class="headerlogout">
                            <a href="<?php echo function_exists('wc_get_page_id') ? esc_url(wp_logout_url(get_permalink(wc_get_page_id('myaccount')))) : wp_logout_url(home_url()); ?>" 
                               title="<?php esc_attr_e('Logout', 'walkbyme'); ?>"
                               aria-label="<?php esc_attr_e('Logout', 'walkbyme'); ?>"></a>
                        </li>
                    <?php } else { ?>
                        <li class="headeraccount">
                            <a href="<?php echo function_exists('wc_get_page_id') ? esc_url(get_permalink(wc_get_page_id('myaccount'))) : wp_login_url(); ?>" 
                               title="<?php esc_attr_e('Login', 'walkbyme'); ?>"
                               aria-label="<?php esc_attr_e('Login', 'walkbyme'); ?>"></a>
                        </li>
                    <?php } ?>
                    <li>
                    <div class="search-container">
                        <div class="search-trigger">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="search-overlay">
                            <div class="search-overlay-content">
                                <div class="close-search">×</div>
                                <form role="search" method="get" class="woocommerce-product-search" action="<?php echo esc_url(home_url('/')); ?>">
                                    <input type="search" 
                                        class="search-field" 
                                        placeholder="Αναζήτηση προϊόντων..." 
                                        value="<?php echo get_search_query(); ?>" 
                                        name="s" 
                                        autocomplete="off" />
                                    <input type="hidden" name="post_type" value="product" />
                                </form>
                            </div>
                        </div>
                    </div>
                    </li>
                    <li class="headercart">
                        <?php if (function_exists('WC') && WC()->cart) { ?>
                            <a class="cart-customlocation" 
                               href="<?php echo function_exists('wc_get_cart_url') ? esc_url(wc_get_cart_url()) : '#'; ?>" 
                               title="<?php esc_attr_e('View your shopping cart', 'walkbyme'); ?>"
                               aria-label="<?php esc_attr_e('Shopping Cart', 'walkbyme'); ?>">
                                <?php
                                $cart_count = WC()->cart->get_cart_contents_count();
                                $cart_total = WC()->cart->get_cart_total();
                                echo wp_kses_post($cart_total . ' (' . $cart_count . ')');
                                
                                // Προσθήκη του κόκκινου σημαδιού
                                if ($cart_count > 0) {
                                    echo '<span class="cart-badge">' . $cart_count . '</span>';
                                }
                                ?>
                            </a>
                        <?php } else { ?>
                            <a class="cart-customlocation" href="#" 
                               title="<?php esc_attr_e('Shopping Cart', 'walkbyme'); ?>">
                               <?php esc_html_e('Cart', 'walkbyme'); ?>
                            </a>
                        <?php } ?>
                    </li>
                </ul>
            </div><!-- shop details end -->
        </div>
    </div><!-- wrapper end -->
</header>

<main>