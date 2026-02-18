<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <meta name="format-detection" content="telephone=no">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@100..900&display=swap" rel="stylesheet">
    
    <?php wp_head(); ?>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleSwitch = document.querySelector('#theme-checkbox');
        const body = document.body;

        if (!toggleSwitch) return;

        function applyTheme(isDark) {
            if (isDark) {
                body.classList.add('dark-mode');
                toggleSwitch.checked = true;
            } else {
                body.classList.remove('dark-mode');
                toggleSwitch.checked = false;
            }
        }

        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        if (savedTheme) {
            applyTheme(savedTheme === 'dark');
        } else {
            applyTheme(prefersDark);
        }

        // Toggle Event
        toggleSwitch.addEventListener('change', (e) => {
            const isDark = e.target.checked;
            applyTheme(isDark);
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
    });
    </script>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="masthead" class="site-header">
    
    <div class="topeader"> <div class="wrapper">
            <?php esc_html_e('ΔΩΡΕΑΝ ΜΕΤΑΦΟΡΙΚΑ ΜΕ ΑΓΟΡΕΣ ΑΝΩ ΤΩΝ 50€ | ΔΩΡΕΑΝ ΑΝΤΙΚΑΤΑΒΟΛΗ ΣΕ ΠΑΡΑΓΓΕΛΙΕΣ ΑΝΩ ΤΩΝ 40€', 'walkbyme'); ?>
        </div>
    </div>
    
    <div class="wrapper">
        <div id="outerheader"> 
            
            <div id="logo">
                <?php
                if ( has_custom_logo() ) {
                    the_custom_logo();
                } else {
                    ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                        <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/walkbyme_logo_site300pxls.svg' ); ?>" 
                             alt="<?php echo esc_attr( get_bloginfo('name') ); ?>" 
                             width="300" height="55">
                    </a>
                    <?php
                } 
                ?>
            </div>

            <nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e('Primary Navigation', 'walkbyme'); ?>">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class'     => 'dropdown', 
                    'container'      => false,
                ));
                ?>
            </nav>

            <div class="shopdetails">
                <ul>
                    
                    <li class="headeraccount">
                         <?php 
                        $account_link = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('myaccount')) : wp_login_url();
                        ?>
                        <a href="<?php echo esc_url($account_link); ?>" title="<?php esc_attr_e('My Account', 'walkbyme'); ?>">
                             </a>
                    </li>
                    
                    <?php if (is_user_logged_in()) : ?>
                        <li class="headerlogout">
                            <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" title="<?php esc_attr_e('Logout', 'walkbyme'); ?>"></a>
                        </li>
                    <?php endif; ?>

                    <li>
                        <button class="search-trigger" aria-label="<?php esc_attr_e('Search', 'walkbyme'); ?>">
                            <i class="fas fa-search"></i>
                        </button>
                        <?php get_template_part('template-parts/header/search-overlay'); ?> 
                    </li>
                    
                    <li class="headercart">
                        <?php 
                        $cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : '#';
                        $cart_count = 0;
                        if (function_exists('WC') && WC()->cart) {
                            $cart_count = WC()->cart->get_cart_contents_count();
                        }
                        ?>
                        
                        <a href="<?php echo esc_url($cart_url); ?>" 
                           id="walkbyme-cart-trigger" 
                           class="cart-customlocation"
                           title="<?php esc_attr_e('View your shopping cart', 'walkbyme'); ?>">
                            
                            <span class="cart-icon-wrapper">
                                <i class="fas fa-shopping-bag" style="font-size: 20px;"></i>
                                <?php if ($cart_count > 0) : ?>
                                    <span class="cart-dot"></span>
                                <?php endif; ?>
                            </span>
                        </a>
                    </li>

                    <li class="header-theme-toggle">
                        <label class="theme-switch" for="theme-checkbox">
                            <input type="checkbox" id="theme-checkbox" />
                            <div class="theme-slider"></div>
                        </label>
                    </li>

                </ul>
            </div> </div> </div> </header>
            <?php if ( is_front_page() ) : ?>
            <div class="seo-tagline-container">
                <h1>Ελληνικά χειροποίητα κοσμήματα από το Walk By Me</h1>
            </div>
            <?php endif; ?>
<main id="primary" class="site-main">