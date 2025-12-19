<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <meta name="format-detection" content="telephone=no">
    <meta name="p:domain_verify" content="47d2a4d6f4ed2663e7cbd09e076d1c6e">
    
    <?php if ( ! function_exists( '_wp_render_title_tag' ) ) : ?>
        <title><?php wp_title('|', true, 'right'); ?></title>
    <?php endif; ?>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@100..900&display=swap" rel="stylesheet">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="masthead" class="site-header">
    <div class="topeader"><div class="wrapper"><?php esc_html_e('ΔΩΡΕΑΝ ΜΕΤΑΦΟΡΙΚΑ ΜΕ ΑΓΟΡΕΣ ΑΝΩ ΤΩΝ 50€ | ΔΩΡΕΑΝ ΑΝΤΙΚΑΤΑΒΟΛΗ ΣΕ ΠΑΡΑΓΓΕΛΙΕΣ ΑΝΩ ΤΩΝ 40€', 'walkbyme'); ?>
        </div></div><div class="wrapper"><div id="outerheader">
            <div id="logo"><?php
                if ( has_custom_logo() ) {
                    the_custom_logo();
                } elseif ( get_header_image() ) { ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr(get_bloginfo('name')); ?>" rel="home">
                        <img src="<?php header_image(); ?>" 
                             height="<?php echo esc_attr(get_custom_header()->height); ?>" 
                             width="<?php echo esc_attr(get_custom_header()->width); ?>" 
                             alt="<?php echo esc_attr(get_bloginfo('name')); ?>" />
                    </a>
                <?php } else { ?>
                    <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
                <?php } ?>
            </div><nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e('Primary Navigation', 'walkbyme'); ?>">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class'     => 'dropdown',
                    'container'      => false,
                    'fallback_cb'    => false,
                    'depth'          => 4,
                ));
                ?>
            </nav>

            <div class="shopdetails"><ul>
                    <?php 
                    $account_link = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('myaccount')) : '#';
                    $is_logged_in = is_user_logged_in();
                    ?>
                    
                    <li class="headeraccount">
                        <a href="<?php echo esc_url($is_logged_in ? $account_link : wp_login_url()); ?>" 
                           title="<?php echo esc_attr($is_logged_in ? __('My Account', 'walkbyme') : __('Login', 'walkbyme')); ?>"
                           aria-label="<?php echo esc_attr($is_logged_in ? __('My Account', 'walkbyme') : __('Login', 'walkbyme')); ?>">
                        </a>
                    </li>

                    <?php if ($is_logged_in) : ?>
                        <li class="headerlogout">
                            <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" 
                               title="<?php esc_attr_e('Logout', 'walkbyme'); ?>"
                               aria-label="<?php esc_attr_e('Logout', 'walkbyme'); ?>">
                            </a>
                        </li>
                    <?php endif; ?>

                    <li>
                        <div class="search-container">
                            <button class="search-trigger" aria-label="<?php esc_attr_e('Open Search', 'walkbyme'); ?>">
                                <i class="fas fa-search" aria-hidden="true"></i>
                            </button>
                            <div class="search-overlay">
                                <div class="search-overlay-content">
                                    <div class="close-search" role="button" tabindex="0">×</div>
                                    <form role="search" method="get" class="woocommerce-product-search" action="<?php echo esc_url(home_url('/')); ?>">
                                        <label class="screen-reader-text" for="woocommerce-product-search-field-<?php echo isset($unique_id) ? $unique_id : 0; ?>">
                                            <?php esc_html_e('Αναζήτηση για:', 'walkbyme'); ?>
                                        </label>
                                        <input type="search" 
                                            id="woocommerce-product-search-field-<?php echo isset($unique_id) ? $unique_id : 0; ?>" 
                                            class="search-field" 
                                            placeholder="<?php esc_attr_e('Αναζήτηση προϊόντων...', 'walkbyme'); ?>" 
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
                        <?php 
                        $cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : '#';
                        $cart_has_items = function_exists('WC') && WC()->cart && WC()->cart->get_cart_contents_count() > 0;
                        ?>
                        <a id="walkbyme-cart-trigger" 
                           class="cart-customlocation" 
                           href="<?php echo esc_url($cart_url); ?>" 
                           title="<?php esc_attr_e('View your shopping cart', 'walkbyme'); ?>"
                           aria-label="<?php esc_attr_e('Shopping Cart', 'walkbyme'); ?>">
                            
                            <?php if (function_exists('WC') && WC()->cart) {
                                $cart_count = WC()->cart->get_cart_contents_count();
                                $cart_total = WC()->cart->get_cart_total();
                                echo wp_kses_post($cart_total . ' (' . $cart_count . ')');
                                
                                if ($cart_count > 0) {
                                    echo '<span class="cart-badge">' . esc_html($cart_count) . '</span>';
                                }
                            } else {
                                esc_html_e('Cart', 'walkbyme');
                            } ?>
                        </a>
                    </li>
                </ul>
            </div></div>
    </div></header>

<main id="primary" class="site-main">