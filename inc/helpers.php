<?php
/**
 * Helper functions for the theme.
 *
 * @package MCA
 */

namespace MCA\Helpers;
use WP_Query;

function mca_get_album_photos($album_id, array $visited = []) {
    $album_id = absint($album_id);
    if (!$album_id || in_array($album_id, $visited, true)) return [];
    $visited[] = $album_id;
    $album = get_post($album_id);
    if (!$album) return [];

    $normalize = static function ($images) {
        return array_values(array_filter(array_map(static function ($image) {
            return absint(is_array($image) ? ($image['ID'] ?? $image['id'] ?? 0) : $image);
        }, (array) $images)));
    };
    $legacy = static function ($source) use ($normalize) {
        $images = [];
        foreach ((array) get_field('images', $source) as $row) {
            if (is_array($row) && !empty($row['image'])) $images[] = $row['image'];
        }
        return $normalize($images);
    };
    $has_gallery = false;
    $collect = static function ($blocks) use (&$collect, &$has_gallery, $normalize, $legacy, $album_id, $visited) {
        $photos = [];
        foreach ($blocks as $gallery_block) {
            if (($gallery_block['blockName'] ?? '') === 'acf/gallery') {
                $has_gallery = true;
                $data = $gallery_block['attrs']['data'] ?? [];
                $images = $normalize($data['gallery_photos'] ?? []);
                if (!$images) {
                    $source = absint($data['gallery_source'] ?? 0) ?: $album_id;
                    $images = $source === $album_id
                        ? $legacy($source)
                        : mca_get_album_photos($source, $visited);
                }
                $photos = array_merge($photos, $images);
            }
            if (!empty($gallery_block['innerBlocks'])) {
                $photos = array_merge($photos, $collect($gallery_block['innerBlocks']));
            }
        }
        return $photos;
    };
    $photos = $collect(parse_blocks($album->post_content));
    return array_values(array_unique($has_gallery ? $photos : $legacy($album_id)));
}

function get_document_search_args() {
    return [
        'post_type' => ['publication', 'attachment'],
        'post_status' => ['publish', 'inherit'],
        'mca_document_search' => true,
    ];
}

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
// function mca_get_child_count($post_id) {
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
/**
 * Published FAQ hierarchy: a direct leaf counts as one; a group counts its
 * published direct children. Cap sidebar output at 200 groups / 1000 links;
 * aggregate counts remain exact and query count does not grow per item.
 */
function mca_faq_category_sidebar($parent = 0, $current_term = '') {
    global $wpdb;
    if (!post_type_exists('faq')) return;

    $cats = get_posts([
        'post_type' => 'faq', 'post_status' => 'publish',
        'post_parent' => absint($parent), 'posts_per_page' => 200,
        'orderby' => ['title' => 'ASC', 'ID' => 'ASC'],
        'update_post_meta_cache' => false, 'update_post_term_cache' => false,
        'suppress_filters' => false,
    ]);
    if (!$cats) return;
    $ids = wp_list_pluck($cats, 'ID');
    $placeholders = implode(',', array_fill(0, count($ids), '%d'));
    $rows = $wpdb->get_results($wpdb->prepare(
        "SELECT child.post_parent AS category_id, SUM(COALESCE(grand.total, 1)) AS total
         FROM {$wpdb->posts} child
         LEFT JOIN (
             SELECT post_parent, COUNT(*) AS total FROM {$wpdb->posts}
             WHERE post_type = 'faq' AND post_status = 'publish' GROUP BY post_parent
         ) grand ON grand.post_parent = child.ID
         WHERE child.post_type = 'faq' AND child.post_status = 'publish'
         AND child.post_parent IN ($placeholders)
         GROUP BY child.post_parent", $ids
    ));
    $counts = [];
    foreach ((array) $rows as $row) $counts[(int) $row->category_id] = (int) $row->total;
    $children = [];
    if ($parent) {
        $items = get_posts([
            'post_type' => 'faq', 'post_status' => 'publish',
            'post_parent__in' => $ids, 'posts_per_page' => 1000,
            'orderby' => ['title' => 'ASC', 'ID' => 'ASC'],
            'update_post_meta_cache' => false, 'update_post_term_cache' => false,
            'suppress_filters' => false,
        ]);
        foreach ($items as $item) $children[$item->post_parent][] = $item;
    }
    ?>
    <nav class="m-menu m-menu--categories">
        <p class="a-text a-text--m a-categories"><?php esc_html_e('Category', 'mca'); ?> <i class="icon-arrow-down a-icon a-icon--xs a-icon--nospace h-white-700" aria-hidden="true"></i></p>
        <ul>
            <?php foreach ($cats as $cat): ?>
                <li class="<?php echo absint($current_term) === (int) $cat->ID ? 'faq-active open' : ''; ?>">
                    <a href="<?php echo esc_url(get_permalink($cat)); ?>" class="a-text">
                        <?php echo esc_html(ucwords(get_the_title($cat))); ?>
                        <span>(<?php echo (int) ($counts[$cat->ID] ?? 0); ?>)</span>
                    </a>
                    <i class="icon-arrow-down a-icon a-icon--s a-icon--nospace h-white" aria-hidden="true"></i>
                    <?php if (!empty($children[$cat->ID])): ?>
                        <ul>
                            <?php foreach ($children[$cat->ID] as $item): ?>
                                <li><a href="<?php echo esc_url(get_permalink($item)); ?>"><?php echo esc_html(get_the_title($item)); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <?php
}


/**
 * FAQ Category Topbar
 */
function mca_faq_category_topbar($current = 0) {
	$cats = mca_get_saved_cat_ids(true);
	$ids = array();

	if ($cats) {
		echo '<div class="m-faq m-faq--menu">';
	    echo    '<div class="m-faq__header">';
	    echo        '<h5 class="a-subheading a-subheading--m h-white">' . esc_html(get_the_title($current)) . '</h5>';
	    echo        '<i class="icon-arrow-down a-icon a-icon--nospace a-icon--l"></i>';
	    echo    '</div>';
	    echo    '<div class="m-faq__body">';
		echo    	'<ul>';
					foreach ($cats as $group_id => $post_id) {
						$title = get_the_title($post_id);
						$url = get_the_permalink($post_id);
						echo sprintf('<li class="a-list-item-s"><a href="%s"><span>%s</span></a></li>', esc_url($url), esc_html(ucwords($title)));
					}
		echo    	'</ul>';
		echo    '</div>';
		echo '</div>';
	}
}

/**
 * FAQ Category Boxes
 */
function mca_faq_category_boxes($current = 0) {
	$cats = mca_get_saved_cat_ids(true);
	$ids = array();
	if ($cats) {
		echo '<ul class="faq_category_boxes">';
		foreach ($cats as $group_id => $post_id) {
			$title = get_the_title($post_id);
			$active = $current == $post_id ? 'active' : '';
			$url = get_the_permalink($post_id);
			if($post_id != 2699 && $post_id != 2700 && $post_id != 10732) {
				echo sprintf('<li class="%s" data-id="%d"><a href="%s"><span>%s</span></a></li>', esc_attr($active), absint($post_id), esc_url($url), esc_html(ucwords($title)));
			}
		}
		echo ' </ul>';
	}
}

/**
 * Function to get saved categories from database
 */
function mca_get_saved_cat_ids($associative = false, $parent = 0, $all = false) {
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
 * Get MCA Locations based on posts
 */


/**
 * Format recipe text
 */
function mca_format_recipe_text($text, $textColorClass = 'h-dune', $textTypeClass = 'a-text--m', $textLineHeightClass = 'h-text-l h--l') {
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
function mca_format_text_with_breaks($text, $class = 'a-text a-text--m') {
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
function mca_format_inline_text($text, $class = '') {
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

function mca_blog_content($content, $class = 'a-text a-text--m') {
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
function mca_get_current_request_url($query_args = []) {
	$request_path = '';

	if (!empty($GLOBALS['wp']) && isset($GLOBALS['wp']->request)) {
		$request_path = trim((string) $GLOBALS['wp']->request, '/');
	}

	$base_url = $request_path ? home_url(user_trailingslashit($request_path)) : home_url('/');

	return !empty($query_args) ? add_query_arg($query_args, $base_url) : $base_url;
}

/**
 * Viewport visibility classes for blocks, independent of the User-Agent.
 * 
 * @param string $desktop_field ACF field name for desktop visibility (default: 'desktop')
 * @param string $tablet_field ACF field name for tablet visibility (default: 'tablet') 
 * @param string $mobile_field ACF field name for mobile visibility (default: 'mobile')
 * @return array 'show' skips blocks disabled for every viewport; CSS handles the rest.
 */
function mca_get_device_visibility($desktop_field = 'desktop', $tablet_field = 'tablet', $mobile_field = 'mobile') {
	$desktop = get_field($desktop_field);
	$tablet = get_field($tablet_field);
	$mobile = get_field($mobile_field);

	$desktop = $desktop === null ? true : $desktop;
	$tablet  = $tablet  === null ? true : $tablet;
	$mobile  = $mobile  === null ? true : $mobile;

	// Build visibility classes for responsive handling
	$visibility_classes = [];
	if (!$desktop) $visibility_classes[] = 'h-hide-desktop';
	if (!$tablet) $visibility_classes[] = 'h-hide-tablet';
	if (!$mobile) $visibility_classes[] = 'h-hide-mobile';
	$visibility_class_string = implode(' ', $visibility_classes);

	return [
		'show' => (bool) ($desktop || $tablet || $mobile),
		'visibility_classes' => $visibility_class_string,
	];
}

/**
 * Get notification visibility
 */
function mca_get_notification_visibility() {
    $result = [
        'show' => false,
        'visibility_classes' => '',
        'notification_text' => '',
        'book_link' => '',
    ];
    if (!function_exists('get_field')) return $result;

    $path = wp_parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $current_slug = trim(is_string($path) ? $path : '', '/');
    $locations = get_field('notification_locations', 'option');
    if (!is_array($locations)) return $result;

    foreach ($locations as $location) {
        if (!is_array($location)) continue;
        $slug = $location['notification_location_slug'] ?? '';
        if (!is_string($slug) || trim($slug, '/') === ''
            || strpos($current_slug, trim($slug, '/')) === false) continue;

        // Notifications have two device options; tablets follow the mobile option.
        $desktop = $location['notification_desktop_active'] ?? true;
        $mobile = $location['notification_mobile_active'] ?? true;
        $result['show'] = (bool) ($desktop || $mobile);
        $result['visibility_classes'] = implode(' ', array_filter([
            !$desktop ? 'h-hide-desktop' : '',
            !$mobile ? 'h-hide-mobile h-hide-tablet' : '',
        ]));
        $text = $location['notification_text'] ?? '';
        $result['notification_text'] = is_string($text) ? wp_kses_post($text) : '';
        $booking_url = get_field('booking_url', 'option');
        $booking_locations = get_field('booking_locations', 'option');
        if (is_string($booking_url) && is_array($booking_locations)) {
            foreach ($booking_locations as $booking) {
                if (!is_array($booking)) continue;
                $booking_slug = $booking['location_slug'] ?? '';
                $product = $booking['bookinglayer_slug'] ?? '';
                if (is_string($booking_slug) && trim($booking_slug, '/') !== ''
                    && is_string($product) && $product !== ''
                    && strpos($current_slug, trim($booking_slug, '/')) !== false) {
                    $base = esc_url_raw($booking_url, ['http', 'https']);
                    if ($base && wp_parse_url($base, PHP_URL_HOST)) {
                        $result['book_link'] = esc_url_raw(trailingslashit($base) . 'product/' . rawurlencode($product));
                    }
                }
            }
        }
        return $result;
    }
    return $result;
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
