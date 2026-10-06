
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_template_directory_uri(); ?>/assets/favicons/favicon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo get_template_directory_uri(); ?>/assets/favicons/favicon.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo get_template_directory_uri(); ?>/assets/favicons/favicon.png">
	<link rel="mask-icon" href="<?php echo get_template_directory_uri(); ?>/assets/favicons/favicon.png" color="#5bbad5">
	<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/assets/favicons/favicon.png">
	<meta name="theme-color" content="#000000">
	<?php wp_head(); ?>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&family=Red+Hat+Display:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
</head>
	<?php
$current_post_id = get_the_ID();

$icon_color  = 'dark-blue';
$button_color = 'pink';

if (get_post_type($current_post_id) === 'project') {
    $color_from_acf = get_field('project_color', $current_post_id);
    if (!empty($color_from_acf)) {
        $icon_color   = $color_from_acf;
        $button_color = $color_from_acf;
    }
}
?>
<body class="h-<?php echo is_singular('project') ? esc_attr($button_color) : ''; ?>">
	<header class="o-header">
	    <div class="m-header__wrapper">
	        <div class="m-header__inner">
	            <a href="<?php echo esc_url(home_url('/')); ?>" class="a-logo" rel="home" aria-label="MCA Kosovo - Home">
	                <img src="<?php echo get_template_directory_uri() ?>/assets/img/pics/logo.svg" alt="MCA Kosovo" loading="lazy" decoding="async" class="a-img">
	            </a>
	            <nav class="m-menu">
	                <?php
						wp_nav_menu([
							'theme_location' => 'primary_navigation',
							'menu_class'     => '',      
							'container'      => false,                  
							'fallback_cb'    => false,      
							'walker'         => new \MCA\Menus\Custom_Main_Menu_Walker(), 
						]);
	                ?>
	            </nav>

				<div class="m-header__actions">
					<button type="button" class="icon-search a-icon a-icon--s a-icon--search" aria-label="Open Search"></button>

					<button type="button" class="m-header__burger" aria-label="Open Mobile Navigation" aria-expanded="false" aria-controls="mobile-menu">
						<span class="m-header__line"></span>
						<span class="m-header__line"></span>
						<span class="m-header__line"></span>
					</button>
				</div>

				<form class="m-search--form" role="search"  method="get" action="<?php echo esc_url(home_url('/')); ?>">
					<label class="sr-only" for="site-search"><?php echo esc_attr_x('Search…', 'placeholder', 'mca-kosovo'); ?></label>
					<input id="site-search" type="search" class="a-input" placeholder="<?php echo esc_attr_x('Search…', 'placeholder', 'mca-kosovo'); ?>" value="<?php echo get_search_query(); ?>" name="s" required aria-required="true">
					<button type="submit" class="icon-search a-icon a-icon--xs" aria-label="<?php echo esc_attr_x('Search', 'submit button', 'mca-kosovo'); ?>"></button>
					<button type="button" class="icon-close a-icon a-icon--xs a-icon--close" aria-label="Close Search"></button>
				</form>
	        </div>
	    </div>
	</header>


	<nav class="m-header__menu-mobile" id="mobile-menu" aria-label="Primary Mobile Navigation">
		<img src="<?php echo get_template_directory_uri() ?>/assets/img/pics/lines.png" alt="Lines" role="presentation" loading="lazy" decoding="async" class="a-img">

		<?php wp_nav_menu([
				'theme_location' => 'primary_navigation',
				'menu_class'     => 'm-header__menu-list',      
				'container'      => false,                  
				'fallback_cb'    => false,      
				'walker'         => new \MCA\Menus\Custom_Main_Menu_Mobile_Walker(),           
			]);?>
	</nav>

<a href="#"
   class="icon-message a-icon a-icon--circle a-icon--bg-blue a-icon--fixed"
   style="background-color: var(--<?php echo esc_attr($icon_color); ?>)"
   aria-label="Open Chat"></a>
<a href="mailto:mcchotline@usaid.gov"
   class="a-btn a-btn--fixed-left"
   style="background-color: var(--<?php echo esc_attr($button_color); ?>); border-color: var(--<?php echo esc_attr($button_color); ?>)">
    <span class="h-<?php echo esc_attr($button_color); ?>">Report Fraud and Corruption
        <i class="a-icon a-icon--xs" style="border-color: var(--<?php echo esc_attr($button_color); ?>); ">
            <svg enable-background="new 0 0 48 48" height="25px" id="Layer_1" version="1.1" viewBox="0 0 48 48" width="25px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="Layer_3"><g><circle cx="24" cy="43.489" style="fill:var(--<?php echo esc_attr($button_color); ?>);" fill="var(--dark-green)" r="4.324"/><rect style="fill:var(--<?php echo esc_attr($button_color); ?>);" fill="var(--dark-green)" height="32.494" width="8.639" x="19.679" y="0.188"/></g></g></svg>
        </i>
    </span>
</a>


<div id="a-countdown"></div>

<!-- <div
    id="cookie-banner"
    class="cookie-banner"
    aria-live="polite"
    role="dialog"
    aria-label="Cookie preferences"
    hidden
>
    <div class="cookie-banner__inner">
        <h2 class="cookie-banner__title a-heading a-heading--h3 h-semibold">
            Cookie Preferences
        </h2>
        <p class="cookie-banner__text a-text a-text--m">
            We use cookies to ensure the proper functioning of our website, enhance your browsing
            experience, and analyze site usage. You can accept all cookies, reject non-essential
            cookies, or manage your preferences at any time.
        </p>
        <div class="cookie-banner__actions">
            <button id="cookie-accept" class="a-btn a-btn--white">
                <span>Accept All Cookies <i class="icon-arrow-right-up a-icon a-icon--xs" aria-hidden="true"></i></span>
            </button>
            <button id="cookie-reject" class="a-btn a-btn--border-white">
                <span>Reject Cookies <i class="icon-arrow-right-up a-icon a-icon--xs" aria-hidden="true"></i></span>
            </button>
            <button id="cookie-more" class="a-btn a-btn--border-white">
                <span>Privacy Policy <i class="icon-arrow-right-up a-icon a-icon--xs" aria-hidden="true"></i></span>
            </button>
        </div>
    </div>
</div> -->

<script>
  window.openCookieBanner = function () {
    var banner = document.getElementById('cookie-banner');
    if (!banner) return;

    banner.hidden = false;
    banner.style.opacity = '1';
    banner.style.transform = 'translateY(0)';
    banner.style.transition = '';
    banner.classList.add('active');
  };

  document.addEventListener('DOMContentLoaded', function () {
    var banner      = document.getElementById('cookie-banner');
    var acceptBtn   = document.getElementById('cookie-accept');
    var rejectBtn   = document.getElementById('cookie-reject');
    var moreBtn     = document.getElementById('cookie-more');

    var consentChoice = null;
    try {
      consentChoice = localStorage.getItem('cookie_consent');
    } catch (e) {
      consentChoice = null;
    }

    if (!consentChoice && banner) {
      window.openCookieBanner();
    }

    if (acceptBtn) {
      acceptBtn.addEventListener('click', function () {
        if (typeof gtag === 'function') {
          gtag('consent', 'update', {
            'analytics_storage': 'granted',
            'ad_storage': 'granted',
            'ad_user_data': 'granted',
            'ad_personalization': 'granted'
          });
        }

        try {
          localStorage.setItem('cookie_consent', 'accepted');
        } catch (e) {}

        if (banner) {
          banner.hidden = true;
          banner.classList.remove('active');
        }
      });
    }

    if (rejectBtn) {
      rejectBtn.addEventListener('click', function () {
        if (typeof gtag === 'function') {
          gtag('consent', 'update', {
            'analytics_storage': 'denied',
            'ad_storage': 'denied',
            'ad_user_data': 'denied',
            'ad_personalization': 'denied'
          });
        }

        try {
          localStorage.setItem('cookie_consent', 'rejected');
        } catch (e) {}

        if (banner) {
          banner.hidden = true;
          banner.classList.remove('active');
        }
      });
    }

    if (moreBtn) {
      moreBtn.addEventListener('click', function () {
        if (banner) {
          banner.classList.toggle('cookie-banner--expanded');
        }
      });
    }
  });
</script>
