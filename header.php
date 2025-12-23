<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <meta name="format-detection" content="telephone=no">
    <meta name="p:domain_verify" content="47d2a4d6f4ed2663e7cbd09e076d1c6e">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@100..900&display=swap" rel="stylesheet">
    
    <?php wp_head(); ?>

   <script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleSwitch = document.querySelector('#theme-checkbox');
    const body = document.body;

    if (!toggleSwitch) {
        console.error('Toggle switch not found!');
        return;
    }

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

    // ✅ Polling fallback (για περίπτωση που το event δεν πυροδοτείται)
    const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    let lastPreference = darkModeMediaQuery.matches;
    
    // Check every 2 seconds
    setInterval(() => {
        const currentPreference = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        if (!localStorage.getItem('theme') && currentPreference !== lastPreference) {
            console.log('System theme detected change via polling:', currentPreference ? 'dark' : 'light');
            applyTheme(currentPreference);
            lastPreference = currentPreference;
        }
    }, 2000);

    // Event listener (primary method)
    darkModeMediaQuery.addEventListener('change', (e) => {
        if (!localStorage.getItem('theme')) {
            console.log('System theme changed via event:', e.matches ? 'dark' : 'light');
            applyTheme(e.matches);
            lastPreference = e.matches;
        }
    });

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
    <div class="topeader">
        <div class="wrapper">
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
                    // Fallback: Αν δεν έχει οριστεί λογότυπο, εμφανίζουμε τον τίτλο του site
                    // Ή αν θέλεις να φορτώνει πάντα το SVG από το assets φάκελο ως έσχατη λύση:
                    ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" title="<?php echo esc_attr(get_bloginfo('name')); ?>">
                        <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/walkbyme_logo_site300pxls.svg' ); ?>" 
                             alt="<?php echo esc_attr( get_bloginfo('name') ); ?>" 
                             width="300" height="55">
                    </a>
                    <?php
                } 
                ?>
            </div>
            <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e('Primary Navigation', 'walkbyme'); ?>">
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

            <div class="shopdetails">
                <ul>
                    <?php 
                    // Βρίσκουμε το URL της σελίδας "My Account"
                    $account_link = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('myaccount')) : wp_login_url();
                    $is_logged_in = is_user_logged_in();
                    ?>
                    
                    <li class="headeraccount">
                        <a href="<?php echo esc_url($account_link); ?>" 
                           title="<?php echo esc_attr($is_logged_in ? __('My Account', 'walkbyme') : __('Login / Register', 'walkbyme')); ?>"
                           aria-label="<?php echo esc_attr($is_logged_in ? __('My Account', 'walkbyme') : __('Login / Register', 'walkbyme')); ?>">
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
                        $cart_count = 0;
                        if (function_exists('WC') && WC()->cart) {
                            $cart_count = WC()->cart->get_cart_contents_count();
                        }
                        ?>
                        <a id="walkbyme-cart-trigger" 
                        class="cart-customlocation" 
                        href="<?php echo esc_url($cart_url); ?>" 
                        title="<?php esc_attr_e('View your shopping cart', 'walkbyme'); ?>"
                        aria-label="<?php esc_attr_e('Shopping Cart', 'walkbyme'); ?>">
                            
                            <span class="cart-icon-wrapper">
                                <i class="fas fa-shopping-bag" aria-hidden="true" style="font-size: 20px;"></i>
                                
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
            </div>
        </div>
    </div>
</header>

<main id="primary" class="site-main">