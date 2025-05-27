<?php
/**
 * Analytics & Tracking Scripts
 * Centralized tracking management for better organization
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Load analytics scripts in head
 */
function walkbyme_analytics_head() {
    // Only load on production/live site - uncomment when ready
    // if (!is_production_site()) return;
    
    ?>
    <!-- Ahrefs Analytics -->
    <script src="https://analytics.ahrefs.com/analytics.js" data-key="q5e3pMcT7fo/ZnKPMuJFNA" defer="true"></script>
    
    <!-- Klaviyo - Back to head for immediate loading -->
    <script type="text/javascript" async src="https://static.klaviyo.com/onsite/js/klaviyo.js?company_id=R2nrLY"></script>
    
    <!-- Google Tag Manager -->
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-5QBSL8G');
    </script>
    
    <!-- Google Ads (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-16633311272"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'AW-16633311272');
    </script>
    
    <!-- Microsoft Clarity -->
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "mif7q4dhr5");
    </script>
    <?php
}
add_action('wp_head', 'walkbyme_analytics_head', 20);



/**
 * Load analytics scripts after body open
 */
function walkbyme_analytics_body() {
    // Only load on production/live site - uncomment when ready
    // if (!is_production_site()) return;
    
    ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5QBSL8G"
                height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    
    <!-- Facebook Pixel (noscript) -->
    <noscript>
        <img height="1" width="1" style="display:none" alt="facebook" 
             src="https://www.facebook.com/tr?id=891327725921929&ev=PageView&noscript=1"/>
    </noscript>
    <?php
}
add_action('wp_body_open', 'walkbyme_analytics_body');

/**
 * Helper function to check if we're on production site
 * Uncomment and modify when ready to use
 */
/*
function is_production_site() {
    $production_domains = array(
        'yourdomain.com',
        'www.yourdomain.com'
    );
    
    return in_array($_SERVER['HTTP_HOST'], $production_domains);
}
*/

/**
 * Add analytics configuration to localized script
 */
function walkbyme_analytics_config($localized_data) {
    $localized_data['analytics'] = array(
        'gtm_id' => 'GTM-5QBSL8G',
        'ga_id' => 'AW-16633311272',
        'fb_pixel_id' => '891327725921929',
        'clarity_id' => 'mif7q4dhr5',
        'klaviyo_id' => 'R2nrLY',
        'ahrefs_key' => 'q5e3pMcT7fo/ZnKPMuJFNA'
    );
    
    return $localized_data;
}
add_filter('walkbyme_localized_data', 'walkbyme_analytics_config');

/**
 * Add analytics DNS prefetch hints for performance
 */
function walkbyme_analytics_dns_prefetch($urls, $relation_type) {
    if ($relation_type === 'dns-prefetch') {
        $urls[] = '//www.googletagmanager.com';
        $urls[] = '//www.google-analytics.com';
        $urls[] = '//connect.facebook.net';
        $urls[] = '//www.clarity.ms';
        $urls[] = '//static.klaviyo.com';
        $urls[] = '//analytics.ahrefs.com';
    }
    
    return $urls;
}
add_filter('wp_resource_hints', 'walkbyme_analytics_dns_prefetch', 10, 2);

/**
 * Admin notice for analytics status
 */
function walkbyme_analytics_admin_notice() {
    if (!current_user_can('manage_options')) return;
    
    // Check if analytics are disabled
    if (function_exists('is_production_site') && !is_production_site()) {
        echo '<div class="notice notice-info is-dismissible">
                <p><strong>Analytics Notice:</strong> Tracking scripts are disabled on development/staging environment.</p>
              </div>';
    }
}
add_action('admin_notices', 'walkbyme_analytics_admin_notice');