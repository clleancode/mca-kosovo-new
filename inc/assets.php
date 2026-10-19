<?php
namespace MCA\Assets;

function mca_asset_ver(string $rel_path) {
    $abs = get_stylesheet_directory() . $rel_path;
    return file_exists($abs) ? filemtime($abs) : (defined('THEME_VERSION') ? THEME_VERSION : null);
}

function register_assets() {
    $theme_uri  = get_stylesheet_directory_uri();

    wp_register_style(
        'mca-style',
        get_stylesheet_uri(),
        [],
        mca_asset_ver('/style.css')
    );

    wp_register_style(
        'mca-main-style',
        $theme_uri . '/assets/css/style.min.css',
        [],
        mca_asset_ver('/assets/css/style.min.css')
    );

    wp_register_script('mca-fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0.31/dist/fancybox.umd.js', [], '4.0.31', true);

    wp_register_script(
        'mca-main-script',
        $theme_uri . '/assets/js/script.min.js',
        ['mca-fancybox'],
        mca_asset_ver('/assets/js/script.min.js'),
        true
    );

    if ( defined( 'GOOGLE_MAPS_API_KEY' ) && GOOGLE_MAPS_API_KEY ) {
        wp_register_script(
            'google-maps-api',
            'https://maps.googleapis.com/maps/api/js?key=' . urlencode( GOOGLE_MAPS_API_KEY ),
            [],
            null,
            true
        );
    }
}

function mca_needs_maps(): bool {
    if (is_admin()) return false;
    if (is_page('contact') || is_page_template('templates/contact.php')) return true;
    return false;
}

function enqueue_assets() {
    wp_enqueue_style('mca-style');
    wp_enqueue_style('mca-main-style');

    wp_enqueue_script('mca-main-script');

    if (mca_needs_maps()) {
        wp_enqueue_script('google-maps-api');
    }
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\register_assets');
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_assets', 20);

add_filter('script_loader_tag', function($tag, $handle) {
    if ($handle === 'mca-main-script') {
        $tag = preg_replace('/\s+type=([\'"])text\/javascript\1/', '', $tag);
        return str_replace('<script ', '<script type="module" ', $tag);
    }
    $defer = ['google-maps-api'];
    if (in_array($handle, $defer, true)) {
        return str_replace(' src', ' defer src', $tag);
    }
    return $tag;
}, 10, 2);

add_action('wp_head', function () {
    ?>
    <script>
window.dataLayer = window.dataLayer || [];
    function gtag() { window.dataLayer.push(arguments); }

    gtag('consent', 'default', {
      'analytics_storage': 'denied',
      'ad_storage': 'denied',
      'ad_user_data': 'denied',
      'ad_personalization': 'denied',
      'functionality_storage': 'denied',
      'personalization_storage': 'denied',
      'security_storage': 'granted'
    });

    try {
      var storedConsent = localStorage.getItem('cookie_consent');
      var preferences = null;
      if (storedConsent === 'accepted' || storedConsent === 'rejected') {
        preferences = { statistics: storedConsent === 'accepted', marketing: storedConsent === 'accepted', preferences: storedConsent === 'accepted' };
      } else if (storedConsent) {
        var parsedConsent = JSON.parse(storedConsent);
        if (parsedConsent.version === 1 && ['preferences', 'statistics', 'marketing'].every(function (key) { return typeof parsedConsent[key] === 'boolean'; })) {
          preferences = parsedConsent;
        }
      }
      if (preferences) {
        gtag('consent', 'update', {
          'analytics_storage': preferences.statistics ? 'granted' : 'denied',
          'ad_storage': preferences.marketing ? 'granted' : 'denied',
          'ad_user_data': preferences.marketing ? 'granted' : 'denied',
          'ad_personalization': preferences.marketing ? 'granted' : 'denied',
          'functionality_storage': preferences.preferences ? 'granted' : 'denied',
          'personalization_storage': preferences.preferences ? 'granted' : 'denied'
        });
      }
    } catch (e) {
    }

gtag('js', new Date());
    gtag('config', 'G-QM4TSQ9CVV');
    </script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-QM4TSQ9CVV"></script>
    <?php
}, 5);
