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

    wp_register_script(
        'mca-main-script',
        $theme_uri . '/assets/js/script.min.js',
        [],
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
    $defer = ['google-maps-api'];
    if (in_array($handle, $defer, true)) {
        return str_replace(' src', ' defer src', $tag);
    }
    return $tag;
}, 10, 2);


add_filter('the_content', function($html){

    return preg_replace('/<img(?![^>]+loading=)/i', '<img loading="lazy"', $html);
}, 11);


add_action('wp_head', function () {
  if (is_front_page()) {
    echo '<link rel="preload" as="image" fetchpriority="high" href="https://www.mcakosovo.org/wp-content/uploads/2025/08/hero-image.webp" imagesizes="100vw">';
  }
}, 2);

add_action('wp_head', function () {
    ?>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}

    gtag('consent', 'default', {
      'analytics_storage': 'denied',
      'ad_storage': 'denied',
      'ad_user_data': 'denied',
      'ad_personalization': 'denied'
    });

    try {
      var storedConsent = localStorage.getItem('cookie_consent');
      if (storedConsent === 'accepted') {
        gtag('consent', 'update', {
          'analytics_storage': 'granted',
          'ad_storage': 'granted',
          'ad_user_data': 'granted',
          'ad_personalization': 'granted'
        });
      } else if (storedConsent === 'rejected') {
        gtag('consent', 'update', {
          'analytics_storage': 'denied',
          'ad_storage': 'denied',
          'ad_user_data': 'denied',
          'ad_personalization': 'denied'
        });
      }
    } catch (e) {
    }
    </script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-QM4TSQ9CVV"></script>
    <script>
    gtag('js', new Date());
    gtag('config', 'G-QM4TSQ9CVV');
    </script>
    <?php
}, 5);
