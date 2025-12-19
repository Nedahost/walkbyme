<?php
/**
 * Analytics & Tracking Scripts
 * Centralized tracking management.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. HEAD SCRIPTS
 */
function walkbyme_analytics_head() {
    // A. Environment Check
    // Don't track on local or staging environments unless necessary
    if ( function_exists('wp_get_environment_type') && wp_get_environment_type() !== 'production' ) {
        // Uncomment the next line to strictly disable tracking on dev sites
        // return; 
    }

    // B. Admin Check (Optional)
    // Uncomment to stop tracking yourself (admins) to keep data clean
    /*
    if ( current_user_can( 'manage_options' ) ) {
        return;
    }
    */
    
    ?>
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-5QBSL8G');
    </script>
    
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-16633311272"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'AW-16633311272');
    </script>

    <script type="text/javascript" async src="https://static.klaviyo.com/onsite/js/klaviyo.js?company_id=R2nrLY"></script>
    
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "mif7q4dhr5");
    </script>

    <script src="https://analytics.ahrefs.com/analytics.js" data-key="q5e3pMcT7fo/ZnKPMuJFNA" defer></script>
    <?php
}
// Priority 20 ensures it loads after essential meta tags but high enough in head
add_action('wp_head', 'walkbyme_analytics_head', 20);


/**
 * 2. BODY SCRIPTS (Noscript fallbacks)
 */
function walkbyme_analytics_body() {
    if ( function_exists('wp_get_environment_type') && wp_get_environment_type() !== 'production' ) {
        // return;
    }
    
    ?>
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5QBSL8G"
                height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    
    <noscript>
        <img height="1" width="1" style="display:none" alt="facebook" 
             src="https://www.facebook.com/tr?id=891327725921929&ev=PageView&noscript=1"/>
    </noscript>
    <?php
}
add_action('wp_body_open', 'walkbyme_analytics_body');


/**
 * 3. JS CONFIGURATION
 * Passes ID data to your main JS file (myjs.js) if needed via localization.
 */
function walkbyme_analytics_config($localized_data) {
    // Ensure array exists before adding to it
    if (!is_array($localized_data)) {
        $localized_data = array();
    }

    $localized_data['analytics'] = array(
        'gtm_id'      => 'GTM-5QBSL8G',
        'ga_id'       => 'AW-16633311272',
        'fb_pixel_id' => '891327725921929',
        'clarity_id'  => 'mif7q4dhr5',
        'klaviyo_id'  => 'R2nrLY',
        'ahrefs_key'  => 'q5e3pMcT7fo/ZnKPMuJFNA'
    );
    
    return $localized_data;
}
add_filter('walkbyme_localized_data', 'walkbyme_analytics_config');


/**
 * 4. PERFORMANCE HINTS (DNS Prefetch & Preconnect)
 * Speeds up the connection to external tracking domains.
 */
function walkbyme_analytics_resource_hints($urls, $relation_type) {
    if ($relation_type === 'dns-prefetch') {
        $urls[] = '//www.googletagmanager.com';
        $urls[] = '//www.google-analytics.com';
        $urls[] = '//connect.facebook.net';
        $urls[] = '//www.clarity.ms';
        $urls[] = '//static.klaviyo.com';
        $urls[] = '//analytics.ahrefs.com';
    }
    
    // Add Preconnect for the most critical ones (GTM & GA)
    if ($relation_type === 'preconnect') {
        $urls[] = 'https://www.googletagmanager.com';
        $urls[] = 'https://www.google-analytics.com';
    }
    
    return $urls;
}
add_filter('wp_resource_hints', 'walkbyme_analytics_resource_hints', 10, 2);


/**
 * 5. ADMIN NOTICE
 * Shows a warning if we are in dev/staging to remind you analytics might be off.
 */
function walkbyme_analytics_admin_notice() {
    if (!current_user_can('manage_options')) return;
    
    // Using WP native function
    if (function_exists('wp_get_environment_type') && wp_get_environment_type() !== 'production') {
        // Only show if you actually uncommented the 'return' in the head function
        // leaving strictly informational for now
        /*
        echo '<div class="notice notice-info is-dismissible">
                <p><strong>Environment:</strong> ' . ucfirst(wp_get_environment_type()) . ' mode.</p>
              </div>';
        */
    }
}
add_action('admin_notices', 'walkbyme_analytics_admin_notice');