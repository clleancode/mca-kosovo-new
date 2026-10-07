<?php
/**
 * Helper functions for the theme.
 *
 * @package MCA
 */

namespace MCA\Helpers;
use WP_Query;
use Detection\MobileDetect;

/** Return an ACF choice only when it is one of the supported values. */
function get_field_choice($name, array $allowed, $default) {
    $value = get_field($name);
    return in_array($value, $allowed, true) ? $value : $default;
}

/** Shared heading and text settings; blocks may override their defaults. */
function get_block_typography(array $defaults = []) {
    $colors = ['h-white', 'h-dark-blue', 'h-blue', 'h-purple', 'h-dark-green'];
    $headings = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
    $choices = [
        'header_tag' => array_merge($headings, ['p']),
        'header_size' => $headings,
        'header_color' => $colors,
        'header_line_height' => ['3xs', '2xs', 'xs', 's', 'm', 'ls', 'l', 'xl'],
        'text_type' => ['xs', 's', 'm', 'l', 'xl', 'xxl'],
        'text_color' => $colors,
        'text_line_height' => ['3xs', '2xs', 'xs', 's', 'm', 'l', 'xl'],
    ];
    $defaults = array_replace([
        'header_tag' => 'h2', 'header_size' => 'h4', 'header_color' => 'h-white',
        'header_line_height' => 's', 'text_type' => 'm', 'text_color' => 'h-white',
        'text_line_height' => 'l',
    ], $defaults);
    $settings = [];
    foreach ($choices as $field => $allowed) {
        $settings[$field] = get_field_choice($field, $allowed, $defaults[$field]);
    }
    return $settings;
}

/**
 * Get child count for FAQ post
 */
// function rapture_get_child_count($post_id) {
// 	$args = array(
// 		'post_type' => 'faq',
// 		'posts_per_page' => -1,
// 		'post_parent' => $post_id,
// 	);
// 	$posts = new WP_Query($args);
// 	return $posts->found_posts;
// }

/**
 * FAQ Category sidebar listing
 */
function rapture_faq_category_sidebar($parent = 0, $current_term = '') {
	global $wpdb;

	$cats = rapture_get_saved_cat_ids(true, $parent);

	if (!empty($cats)) {
?>
		<nav class="m-menu m-menu--categories">
			<p class="a-text a-text--m a-categories">
				<?php _e('Category', 'rapture'); ?> <i class="icon-arrow-down a-icon a-icon--xs a-icon--nospace h-white-700"></i>
			</p>
			<ul>
				<?php
					foreach ($cats as $group_id => $post_id) {
						$class = $current_term == $post_id ? 'faq-active open' : '';
						$url = esc_url(get_the_permalink($post_id));
						$name = ucwords(get_the_title($post_id));
						$count = rapture_get_child_count($post_id);
						$get_count_query = "SELECT ID from {$wpdb->prefix}posts where post_parent='{$post_id}'";
						$get_count_query_result = $wpdb->get_results($get_count_query,OBJECT);
						$category_count = 0;

						foreach($get_count_query_result as $key=>$value){
							$get_query = "select ID from {$wpdb->prefix}posts where post_parent='{$value->ID}'";
							$get_query_result = $wpdb->get_results($get_query,OBJECT);
							
							if(count($get_query_result)>0){
								$category_count+= count($get_query_result);
							} else {
								$category_count = $count;
							}
						}
				  
					?>
					<li>
						<a href="<?php echo $url; ?>" class="a-text">
							<?php echo $name; ?>
							<span class="">(<?php echo $category_count; ?>)</span>	
						</a>
						<i class="icon-arrow-down a-icon a-icon--s a-icon--nospace h-white"></i>
						<?php
						if ($parent) {
							$args = array(
								'post_type' => 'faq',
								'posts_per_page' => -1,
								'post_status' => 'publish',
								'post_parent' => $post_id,
								'orderby' => 'title',
								'order' => 'asc'
							);
							$q = new WP_Query($args);
							if ($q->have_posts()) {
								echo '<ul>';
								while ($q->have_posts()) {
									$q->the_post();
									$title = get_the_title();
									$link = esc_url(get_the_permalink());
									echo sprintf('<li><a href="%s">%s</a></li>', $link, $title);
								}
								echo '</ul>';
								wp_reset_postdata();
							}
						}
						?>
					</li>
				<?php }
				?>
			</ul>
		</nav>
		<?php
	}
}


/**
 * FAQ Category Topbar
 */
function rapture_faq_category_topbar($current = 0) {
	$cats = rapture_get_saved_cat_ids(true);
	$ids = array();

	if ($cats) {
		echo '<div class="m-faq m-faq--menu">';
	    echo    '<div class="m-faq__header">';
	    echo        '<h5 class="a-subheading a-subheading--m h-white">' . get_the_title($current) . '</h5>';
	    echo        '<i class="icon-arrow-down a-icon a-icon--nospace a-icon--l"></i>';
	    echo    '</div>';
	    echo    '<div class="m-faq__body">';
		echo    	'<ul>';
					foreach ($cats as $group_id => $post_id) {
						$title = get_the_title($post_id);
						$url = get_the_permalink($post_id);
						echo sprintf('<li class="a-list-item-s"><a href="%s"><span>%s</span></a></li>', esc_url($url), ucwords($title));
					}
		echo    	'</ul>';
		echo    '</div>';
		echo '</div>';
	}
}

/**
 * FAQ Category Boxes
 */
function rapture_faq_category_boxes($current = 0) {
	$cats = rapture_get_saved_cat_ids(true);
	$ids = array();
	if ($cats) {
		echo '<ul class="faq_category_boxes">';
		foreach ($cats as $group_id => $post_id) {
			$title = get_the_title($post_id);
			$active = $current == $post_id ? 'active' : '';
			$url = get_the_permalink($post_id);
			if($post_id != 2699 && $post_id != 2700 && $post_id != 10732) {
				echo sprintf('<li class="%s" data-id="%s"><a href="%s"><span>%s</span></a></li>', $active, $post_id, esc_url($url), ucwords($title));
			}
		}
		echo ' </ul>';
	}
}

/**
 * Function to get saved categories from database
 */
function rapture_get_saved_cat_ids($associative = false, $parent = 0, $all = false) {
    $args = array(
        'post_type' => 'faq',
        'posts_per_page' => -1,
        'order' => 'ASC',
        'post_parent' => $parent,
    );

    if ($all) {
        unset($args['post_parent']);
    }

    $cats = new WP_Query($args);

    $ids = array();

    if ($cats->have_posts()) {
        while ($cats->have_posts()) {
            $cats->the_post();
            $id = get_the_ID();
            $ids[] = $id;
        }
        wp_reset_postdata();
    }

    return $ids;
}

/**************************************************
 NEED TO CHECK
**************************************************/


/**
 * Get Rapture Locations based on posts
 */


/**
 * Format recipe text
 */
function rapture_format_recipe_text($text, $textColorClass = 'h-dune', $textTypeClass = 'a-text--m', $textLineHeightClass = 'h-text-l h--l') {
	if (strpos($text, '<ul') !== false) {
		$dom = new \DOMDocument();
		@$dom->loadHTML('<?xml encoding="UTF-8"><div>' . $text . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		
		$xpath = new \DOMXPath($dom);
		$topLevelUls = $xpath->query('//div/ul');
		
		foreach ($topLevelUls as $ul) {
			$nav = $dom->createElement('nav');
			$nav->setAttribute('class', 'm-content__list ' . $textTypeClass . ' ' . $textColorClass . ' ' . $textLineHeightClass);

			$ul->parentNode->insertBefore($nav, $ul);
			$nav->appendChild($ul->cloneNode(true));
			$ul->parentNode->removeChild($ul);
		}

		$text = $dom->saveHTML($dom->documentElement);
		$text = preg_replace('/^<div>(.*)<\/div>$/s', '$1', $text);
	}

	// Add classes to <p> and <li>
	$classAttr     = trim("a-text $textTypeClass $textColorClass $textLineHeightClass");
	$classListAttr = trim("a-list-item $textTypeClass $textColorClass");

	$text = preg_replace('/<p([^>]*)>/', '<p$1 class="' . esc_attr($classAttr) . '">', $text);
	$text = preg_replace('/<li([^>]*)>/', '<li$1 class="' . esc_attr($classListAttr) . '">', $text);

	// Allowed tags
	$allowed_tags = [
		'p'        => ['class' => []],
		'nav'      => ['class' => []],
		'ul'       => [],
		'li'       => ['class' => []],
		'br'       => [],
		'a'        => ['href' => [], 'title' => [], 'class' => [], 'target' => []],
		'strong'   => [],
		'em'       => [],
		'sup'      => [],
		'span'     => ['class' => []]
	];

	return wp_kses($text, $allowed_tags);
}

/**
 * Apply classes to all <p> tags in a WYSIWYG HTML string, preserving existing classes.
 *
 * @param string $html The HTML content (from ACF WYSIWYG).
 * @param string $classAttr Classes to add to every <p> (space-separated).
 * @return string Modified HTML.
 */
function apply_paragraph_classes($html, $classAttr) {
    if (empty($html)) {
        return $html;
    }
    $classAttr = trim($classAttr);

    $html = preg_replace_callback('/<p(\s[^>]*)?>/i', function($matches) use ($classAttr) {
        $attrs = isset($matches[1]) ? $matches[1] : '';
        if (preg_match('/class=(\"|\')(.*?)(\"|\')/i', $attrs, $classMatch)) {
            $newClasses = trim($classMatch[2] . ' ' . $classAttr);
            $attrs = preg_replace('/class=(\"|\').*?(\"|\')/i', 'class="' . $newClasses . '"', $attrs);
        } else {
            $attrs .= ' class="' . $classAttr . '"';
        }
        return '<p' . $attrs . '>';
    }, $html);

    return $html;
}

/**
 * Format text with preserved line breaks
 */
function rapture_format_text_with_breaks($text, $class = 'a-text a-text--m') {
	if (empty($text)) return $text;

	// Convert line breaks to <br> tags
	$text = nl2br($text);

	// Add classes to paragraphs
	$text = preg_replace('/<p([^>]*)>/', '<p$1 class="' . esc_attr($class) . '">', $text);

	// Allowed tags
	$allowed_tags = [
		'p'        => ['class' => []],
		'br'       => [],
		'a'        => ['href' => [], 'title' => [], 'class' => [], 'target' => []],
		'strong'   => [],
		'em'       => [],
		'span'     => ['class' => []]
	];

	return wp_kses($text, $allowed_tags);
}

/**
 * Format inline text with preserved line breaks (no paragraph wrapping)
 */
function rapture_format_inline_text($text, $class = '') {
	if (empty($text)) return $text;

	// Convert line breaks to <br> tags
	$text = nl2br($text);

	// Allowed tags for inline text
	$allowed_tags = [
		//'p'        => [],
		'br'       => [],
		'a'        => ['href' => [], 'title' => [], 'class' => [], 'target' => []],
		'strong'   => [],
		'em'       => [],
		'span'     => ['class' => []]
	];

	return wp_kses($text, $allowed_tags);
}

/**
 * 
 */

function rapture_blog_content($content, $class = 'a-text a-text--m') {
	if (empty($content)) return $content;

	$dom = new \DOMDocument();
	libxml_use_internal_errors(true);
	$dom->loadHTML('<?xml encoding="UTF-8"><div>' . $content . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
	libxml_clear_errors();

	$xpath = new \DOMXPath($dom);

	// Wrap ul elements with nav
	$uls = $xpath->query('//div/ul');
	foreach ($uls as $ul) {
		$nav = $dom->createElement('nav');
		$nav->setAttribute('class', 'm-content__list ' . $class);
		$ul->parentNode->insertBefore($nav, $ul);
		$nav->appendChild($ul->cloneNode(true));
		$ul->parentNode->removeChild($ul);
	}

	// Add class to all paragraphs, including those in Gutenberg blocks
	//$paragraphs = $xpath->query('//div//p');
	$paragraphs = $xpath->query('//div//p[not(ancestor::*[contains(@class, "m-article__content")])]');

	foreach ($paragraphs as $p) {
		$existingClass = $p->getAttribute('class');
		$p->setAttribute('class', trim("$existingClass $class a-text"));
	}

	// Add class to all headings, regardless of block type
	//$headings = $xpath->query('//div//h1 | //div//h2 | //div//h3 | //div//h4 | //div//h5 | //div//h6');
	$headings = $xpath->query('//div//*[self::h1 or self::h2 or self::h3 or self::h4 or self::h5 or self::h6][not(ancestor::*[contains(@class, "m-article__content")])]');

	foreach ($headings as $heading) {
		$existingClass = $heading->getAttribute('class');
		$heading->setAttribute('class', trim("$existingClass a-heading a-heading--xs"));
	}

	// Add class to all list items
	$listItems = $xpath->query('//div//li');

	foreach ($listItems as $li) {
		$existingClass = $li->getAttribute('class');
		$li->setAttribute('class', trim("$existingClass a-list-item $class"));
	}

	// Preserve more HTML tags
	$allowed_tags = [
		'div'      => ['class' => [], 'id' => []],
		'p'        => ['class' => []],
		'h1'       => ['class' => []],
		'h2'       => ['class' => []],
		'h3'       => ['class' => []],
		'h4'       => ['class' => []],
		'h5'       => ['class' => []],
		'h6'       => ['class' => []],
		'br'       => [],
		'a'        => ['href' => [], 'title' => [], 'class' => [], 'target' => []],
		'strong'   => [],
		'em'       => [],
		'sup'      => [],
		'span'     => ['class' => []],
		'nav'      => ['class' => []],
		'ul'       => ['class' => []],
		'ol'       => ['class' => []],
		'li'       => ['class' => []],
		'img'      => ['src' => [], 'alt' => [], 'class' => [], 'width' => [], 'height' => []],
		'figure'   => ['class' => []],
		'figcaption' => ['class' => []],
		'blockquote' => ['class' => []],
		'pre'      => ['class' => []],
		'code'     => ['class' => []]
	];

	$html = $dom->saveHTML($dom->documentElement);
	$html = preg_replace('/^<div>(.*)<\/div>$/s', '$1', $html);
	
	return wp_kses($html, $allowed_tags);
}

/**
 * Build the current request URL with the site's trailing-slash setting.
 */
function rapture_get_current_request_url($query_args = []) {
	$request_path = '';

	if (!empty($GLOBALS['wp']) && isset($GLOBALS['wp']->request)) {
		$request_path = trim((string) $GLOBALS['wp']->request, '/');
	}

	$base_url = $request_path ? home_url(user_trailingslashit($request_path)) : home_url('/');

	return !empty($query_args) ? add_query_arg($query_args, $base_url) : $base_url;
}

/**
 * Device detection and visibility helper for blocks
 * 
 * @param string $desktop_field ACF field name for desktop visibility (default: 'desktop')
 * @param string $tablet_field ACF field name for tablet visibility (default: 'tablet') 
 * @param string $mobile_field ACF field name for mobile visibility (default: 'mobile')
 * @return array Returns array with 'show' boolean and 'visibility_classes' string
 */
function rapture_get_device_visibility($desktop_field = 'desktop', $tablet_field = 'tablet', $mobile_field = 'mobile') {
	$desktop = get_field($desktop_field);
	$tablet = get_field($tablet_field);
	$mobile = get_field($mobile_field);

	$desktop = $desktop === null ? true : $desktop;
	$tablet  = $tablet  === null ? true : $tablet;
	$mobile  = $mobile  === null ? true : $mobile;

	$detect = new MobileDetect();
	$is_mobile = $detect->isMobile();
	$is_tablet = $detect->isTablet();
	$is_desktop = !$is_mobile && !$is_tablet;

	$show = false;

	if ($desktop && $is_desktop) $show = true;
	if ($tablet && $is_tablet) $show = true;
	if ($mobile && $is_mobile && !$is_tablet) $show = true;

	// Build visibility classes for responsive handling
	$visibility_classes = [];
	if (!$desktop) $visibility_classes[] = 'h-hide-desktop';
	if (!$tablet) $visibility_classes[] = 'h-hide-tablet';
	if (!$mobile) $visibility_classes[] = 'h-hide-mobile';
	$visibility_class_string = implode(' ', $visibility_classes);

	return [
		'show' => $show,
		'visibility_classes' => $visibility_class_string,
		'device_states' => [
			'is_desktop' => $is_desktop,
			'is_tablet' => $is_tablet,
			'is_mobile' => $is_mobile
		]
	];
}

/**
 * Get notification visibility
 */
function rapture_get_notification_visibility() {
	$current_slug = trim($_SERVER['REQUEST_URI'], '/');
    $notification_locations = get_field('notification_locations', 'option');

	if($notification_locations):
		foreach ($notification_locations as $i => $location):
			if (strpos($current_slug, $location['notification_location_slug']) !== false):
				$notification_text = $location['notification_text'];
				$notification_desktop_active = $location['notification_desktop_active'];
				$notification_mobile_active = $location['notification_mobile_active'];
				$booking_url = get_field('booking_url','option');
				$booking_locations = get_field('booking_locations', 'option');
				$selected_location_id = '';

				foreach ($booking_locations as $i => $location) {
					if (strpos($current_slug, $location['location_slug']) !== false) {
						$selected_location_id    = $location['bookinglayer_slug'];
					}
				}

				$desktop = $notification_desktop_active === null ? true : $notification_desktop_active;
                $mobile  = $notification_mobile_active  === null ? true : $notification_mobile_active;

                $detect = new MobileDetect();
                $is_mobile = $detect->isMobile();
                $is_desktop = !$is_mobile && !$is_tablet;

                $show = false;

                if ($desktop && $is_desktop) $show = true;
                if ($mobile && $is_mobile) $show = true;

                $visibility_classes = [];
                if (!$desktop) $visibility_classes[] = 'h-hide-desktop';
                if (!$mobile) $visibility_classes[] = 'h-hide-mobile';

				$visibility_class_string = implode(' ', $visibility_classes);
				$book_link = $booking_url . '/product/' . $selected_location_id;

				return [
					'show' => $show,
					'visibility_classes' => $visibility_class_string,
					'notification_text' => $notification_text,
					'book_link' => $book_link,
					'device_states' => [
						'is_desktop' => $is_desktop,
						'is_tablet' => $is_tablet,
						'is_mobile' => $is_mobile
					]
				];

				return $visibility_class_string;
			endif;
		endforeach;
	endif;
}

//contact form to not insert <p>
add_filter('wpcf7_autop_or_not', '__return_false');


// // Disable emojis
// \remove_action('wp_head', 'print_emoji_detection_script', 7);
// \remove_action('wp_print_styles', 'print_emoji_styles');

// // Disable embeds
// function disable_embeds_code_init() {
//     \remove_action('rest_api_init', 'wp_oembed_register_route');
//     \remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
//     \remove_action('wp_head', 'wp_oembed_add_discovery_links');
//     \remove_action('wp_head', 'wp_oembed_add_host_js');
// }
// \add_action('init', __NAMESPACE__ . '\\disable_embeds_code_init', 9999);

// // Disable WP Heartbeat (optional)
// \add_filter('heartbeat_send', '__return_false');
