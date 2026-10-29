
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
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
</head>
	<?php
$current_post_id = get_the_ID();

$icon_color  = 'dark-blue';
$button_color = 'pink';
$newsletter_header = is_singular() && has_block('acf/newsletter-subscribe', get_queried_object_id());

if (get_post_type($current_post_id) === 'project') {
    $color_from_acf = get_field('project_color', $current_post_id);
    if (!empty($color_from_acf)) {
        $icon_color   = $color_from_acf;
        $button_color = $color_from_acf;
    }
}
?>
<body class="h-<?php echo is_singular('project') ? esc_attr($button_color) : ''; ?>">
	<a class="skip-link" href="#main-content"><?php esc_html_e( 'Skip to content', 'mca' ); ?></a>
	<?php wp_body_open(); ?>
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

    <?php if ($newsletter_header) : ?>
        <div class="o-newsletter-header-background" aria-hidden="true"></div>
    <?php endif; ?>


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
<a href="<?php echo esc_url(home_url('/report-fraud-corruption/')); ?>"
   class="a-btn a-btn--fixed-left"
   style="background-color: var(--<?php echo esc_attr($button_color); ?>); border-color: var(--<?php echo esc_attr($button_color); ?>)">
    <i class="a-icon" aria-hidden="true"><svg width="16" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 3 4 6v6c0 5 8 9 8 9s8-4 8-9V6Z"/><path d="M12 8v5m0 3v1"/></svg></i>
    <span>Report fraud</span>
</a>


<div id="a-countdown"></div>

<?php get_template_part('template-parts/cookie-banner'); ?>
<main id="main-content" tabindex="-1">
