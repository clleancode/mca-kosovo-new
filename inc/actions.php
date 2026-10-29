<?php
/**
 * Remove default WordPress actions to optimize performance and security.
 *
 * @package MCA
 */

namespace MCA\Actions;
use function MCA\Helpers\getMCALocations;

/**
 * Clean up <head> output and remove unnecessary actions.
 */
function clean_up_head() {
    // Remove WordPress Meta Generator
    remove_action('wp_head', 'wp_generator');

    // Remove RSS feed links if not needed.
    remove_action( 'wp_head', 'feed_links_extra', 3 ); // Category and tag feeds
    remove_action( 'wp_head', 'feed_links', 2 ); // Default post and comment feeds

    // Remove meta tags and unnecessary links.
    remove_action( 'wp_head', 'rsd_link' ); // RSD link
    remove_action( 'wp_head', 'wlwmanifest_link' ); // Windows Live Writer

    // Remove shortlinks and emoji scripts.
    remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 ); // Shortlink
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 ); // Emoji script
    remove_action( 'wp_print_styles', 'print_emoji_styles' ); // Emoji styles
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' ); // Admin emoji script
    remove_action( 'admin_print_styles', 'print_emoji_styles' ); // Admin emoji styles

    // Remove REST API links
    remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
}
add_action( 'init', __NAMESPACE__ . '\\clean_up_head' );

/**
 * Register Widgets
 */
function wpb_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Footer', 'angel vjosa' ),
		'id' => 'footer',
		'description' => __( 'The main sidebar appears on the right on each page except the front page template', 'angel vjosa' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s col-sm-3">',
		'after_widget' => '</aside>',
		'before_title' => '<h5 class="widget-title">',
		'after_title' => '</h5>',
	) );

	register_sidebar( array(
		'name' =>__( 'Category Sidebar', 'angel vjosa'),
		'id' => 'category_sidebar',
		'description' => __( 'This sidebar is for Journal Post Category Listing', 'angel vjosa' ),
		'before_widget' => '<aside class="m-menu m-menu--categories %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<p class="a-text a-text--m a-categories">',
		'after_title' => '<i class="icon-arrow-down a-icon a-icon--xs a-icon--nospace h-white-700"></i></p>',
	) );
}
add_action( 'widgets_init', __NAMESPACE__ . '\\wpb_widgets_init' );

/**
 * Change surfcamp query
 */
function change_surfcamp_query($query){
	if(($query->is_main_query()) && (is_post_type_archive('surfcamp')) && !is_admin()):
		$query->set( 'post_parent', 0 );
		$query->set( 'posts_per_page', -1);
	endif;

	if(($query->is_main_query()) && ($query->is_tax('faq-location') || is_post_type_archive( 'faq' )) && !is_admin()):
		$query->set( 'posts_per_page', -1);
	endif;

	// Ensure global search includes all public post types we care about
	if (($query->is_main_query()) && $query->is_search() && !is_admin()) :
		$query->set('post_type', [ 'post', 'page', 'project', 'procurement', 'job', 'gallery' ]);
	endif;
};
add_action( 'pre_get_posts', __NAMESPACE__ . '\\change_surfcamp_query');

function configure_shared_archive_query( $query ) {
    if ( is_admin() || ! $query->is_main_query() || ! $query->is_archive()
        || $query->is_category() || $query->is_feed() ) {
        return;
    }

    $query->set( 'posts_per_page', 12 );
    $query->set( 'posts_per_archive_page', 12 );
    $query->set( 'nopaging', false );
    $query->set( 'no_found_rows', false );
    $sort = isset( $_GET['sorting'] ) && is_string( $_GET['sorting'] )
        ? sanitize_key( wp_unslash( $_GET['sorting'] ) ) : 'most_recent';
    $orders = [
        'most_recent' => [ 'date' => 'DESC', 'ID' => 'DESC' ],
        'less_recent' => [ 'date' => 'ASC', 'ID' => 'ASC' ],
        'alphabetical_order' => [ 'title' => 'ASC', 'ID' => 'ASC' ],
    ];
    $query->set( 'orderby', $orders[ $sort ] ?? $orders['most_recent'] );
}
add_action( 'pre_get_posts', __NAMESPACE__ . '\\configure_shared_archive_query', 20 );

/**
 * Get AJAX Search Suggestions
 */
function ajax_search_suggestions() {
	if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) {
		wp_send_json_error( [ 'message' => __( 'Please use POST for search suggestions.', 'mca' ) ], 405 );
	}
	if ( ! isset( $_POST['security'], $_REQUEST['security'] )
		|| ! is_string( $_POST['security'] ) || ! is_string( $_REQUEST['security'] )
		|| ! check_ajax_referer( 'mca-search-form', 'security', false ) ) {
		wp_send_json_error( [ 'message' => __( 'Security check failed. Please refresh the page and try again.', 'mca' ) ], 403 );
	}

	$peer = $_SERVER['REMOTE_ADDR'] ?? '';
	$peer = filter_var( $peer, FILTER_VALIDATE_IP ) ? inet_ntop( inet_pton( $peer ) ) : 'unknown';
	if ( ! \MCA\FormSubmissions\consume_submission_attempt( 'search_suggestions_ip', $peer, 120 ) ) {
		header( 'Retry-After: 900' );
		wp_send_json_error( [ 'message' => __( 'Too many searches. Please wait 15 minutes and try again.', 'mca' ) ], 429 );
	}

	if ( ( isset( $_POST['cat_id'] ) && ! is_string( $_POST['cat_id'] ) )
		|| ( isset( $_POST['keyword'] ) && ! is_string( $_POST['keyword'] ) ) ) {
		wp_send_json_error( [ 'message' => __( 'Invalid search request.', 'mca' ) ], 400 );
	}
	if ( isset( $_POST['keyword'] ) && strlen( $_POST['keyword'] ) > 800 ) {
		wp_send_json_error( [ 'message' => __( 'Please use a search term of 200 characters or fewer.', 'mca' ) ], 400 );
	}
	$parentCategory = isset( $_POST['cat_id'] ) ? absint( wp_unslash( $_POST['cat_id'] ) ) : 0;
	$keyword = isset( $_POST['keyword'] ) ? sanitize_text_field( wp_unslash( $_POST['keyword'] ) ) : '';
	if ( title_strlen( $keyword ) > 200 ) {
		wp_send_json_error( [ 'message' => __( 'Please use a search term of 200 characters or fewer.', 'mca' ) ], 400 );
	}

	$args = [
		'post_type' => 'faq',
		'post_status' => 'publish',
		'posts_per_page' => 10,
		'no_found_rows' => true,
		's' => $keyword,
	];
	if ( $parentCategory ) {
		$args['meta_query'] = [
			[
				'key' => 'parent_category',
				'value' => $parentCategory,
				'compare' => '=',
				'type' => 'NUMERIC',
			],
		];
	}

	$items = '';
	try {
		$faqs = new \WP_Query( $args );
		while ( $faqs->have_posts() ) {
			$faqs->the_post();
			$id = get_the_ID();
			$parentId = wp_get_post_parent_id( $id );
			$grandParentId = $parentId ? wp_get_post_parent_id( $parentId ) : 0;
			$text = '';
			if ( $parentId ) {
				$text = ' - <span>' . esc_html( get_the_title( $parentId ) );
				if ( $grandParentId ) {
					$text .= ' (' . esc_html( get_the_title( $grandParentId ) ) . ')';
				}
				$text .= '</span>';
			}
			$items .= '<li><a href="' . esc_url( get_the_permalink( $id ) ) . '">'
				. esc_html( get_the_title( $id ) ) . $text . '</a></li>';
		}
	} catch ( \Throwable $e ) {
		wp_reset_postdata();
		error_log( 'MCA search suggestions failed: ' . $e->getMessage() );
		wp_send_json_error( [ 'message' => __( 'Search suggestions are temporarily unavailable. Please try again.', 'mca' ) ], 500 );
	}
	wp_reset_postdata();

	echo $items ?: '<li>' . esc_html__( 'No suggested articles found.', 'mca' ) . '</li>';
	wp_die();
}

add_action('wp_ajax_ajax_search_suggestions', __NAMESPACE__ . '\\ajax_search_suggestions');
add_action('wp_ajax_nopriv_ajax_search_suggestions', __NAMESPACE__ . '\\ajax_search_suggestions');

/**
 * Surfcamp menu register
 */
// function surfcamp_menu_register() {
// 	foreach(getMCALocations() as $id=>$camp) {
// 		$post_language_information = apply_filters( 'wpml_post_language_details', null, $id);

// 		if ($post_language_information["language_code"]=="en") {
// 			register_nav_menu("${id}-surfcamp-header", $camp." Surfcamp Header");
// 		}
// 	}
// }
// add_action('after_setup_theme', __NAMESPACE__ . '\\surfcamp_menu_register');

/**
 * Related posts next and prev
 * SEO Functionality to add prev and next links to the head
 */
function rel_next_prev() {
	global $paged;

	if ( get_previous_posts_link() ) {
		echo '<link rel="prev" href="'.get_pagenum_link( $paged - 1 ).'" />';
	}

	if ( get_next_posts_link(null, 9) ) {
		echo '<link rel="next" href="'.get_pagenum_link( $paged + 1 ).'" />';
	}
}

add_action( 'wp_head', __NAMESPACE__ . '\\rel_next_prev' );

/**
 * Fix common legacy 404 URLs by redirecting them to valid destinations.
 */
function mca_handle_legacy_404_redirects() {
    // Only run on frontend before rendering the template.
    if ( is_admin() ) {
        return;
    }

    $request_uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = wp_parse_url( $request_uri, PHP_URL_PATH );
    $path = is_string( $path ) ? '/' . trim( $path, '/' ) : '';
    $base = rtrim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
    if ( $base && 0 === strpos( $path, $base . '/' ) ) {
        $path = substr( $path, strlen( $base ) );
    }
    $path = rtrim( $path, '/' );

    // 1) Redirect legacy /news/ URL to the news category archive.
    if ( $path === '/news' ) {
        $news_category = get_category_by_slug( 'news' );
        if ( $news_category ) {
            $target = get_category_link( $news_category->term_id );
        } else {
            // Fallback to home if category is missing.
            $target = home_url( '/' );
        }

        wp_safe_redirect( $target, 301 );
        exit;
    }

    // 2) Avoid 404s for over-paginated or invalid category URLs such as /category/.../page/3/.
    if ( is_category() ) {
        global $wp_query;

        $paged         = get_query_var( 'paged' ) ? (int) get_query_var( 'paged' ) : 1;
        $max_num_pages = isset( $wp_query->max_num_pages ) ? (int) $wp_query->max_num_pages : 0;

        if ( $paged > 1 && $paged > max( 1, $max_num_pages ) ) {
            $category_link = get_category_link( get_queried_object_id() );

            wp_safe_redirect( $category_link, 301 );
            exit;
        }
    }

    // 3) If WordPress already treats the request as 404, try to rescue common legacy patterns.
    if ( is_404() && ! empty( $path ) ) {
        // Category pagination that points to a non-existing term or page, e.g. /category/all/page/2/.
        if ( preg_match( '#^/category/([^/]+)/page/(\d+)$#', $path, $matches ) ) {
            $slug = sanitize_title( $matches[1] );
            $page = (int) $matches[2];

            // Try to find a real category first.
            $term = get_category_by_slug( $slug );
            if ( $term ) {
                $target = get_category_link( $term->term_id );
            } else {
                // Fallback: send any non-existent "all" or similar pseudo-categories to the main posts page or home.
                $posts_page_id = (int) get_option( 'page_for_posts' );
                $target        = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/' );
            }

            wp_safe_redirect( $target, 301 );
            exit;
        }
    }
}
// Run before WordPress's canonical guessing can send /news/ to /newsletter/.
add_action( 'template_redirect', __NAMESPACE__ . '\\mca_handle_legacy_404_redirects', 9 );


/**
 * Remove Gutenberg styles from the frontend
 * but keep them in the editor.
 */
function remove_gutenberg_frontend_styles() {

    // Dequeue core block library CSS
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('global-styles'); // removes theme.json inline styles
    wp_dequeue_style('classic-theme-styles'); // for classic themes

    // Optional: Also deregister to prevent re-adding by other plugins
    wp_deregister_style('wp-block-library');
    wp_deregister_style('wp-block-library-theme');
    wp_deregister_style('global-styles');
    wp_deregister_style('classic-theme-styles');
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\remove_gutenberg_frontend_styles', 100);

/**
 * Remove jQuery & jQuery Migrate from frontend
 */
function remove_jquery() {
    if (is_admin()) {
        return;
    }

    wp_deregister_script('jquery');
    wp_deregister_script('jquery-migrate');
    wp_deregister_script('jquery-core');
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\remove_jquery', 20);

function content_needs_cf7_assets( $content, $seen = [] ) {
    if ( shortcode_exists( 'contact-form-7' )
        && preg_match_all( '/' . get_shortcode_regex( [ 'contact-form-7' ] ) . '/s', $content, $matches, PREG_SET_ORDER ) ) {
        foreach ( $matches as $match ) {
            if ( '[' !== $match[1] || ']' !== $match[6] ) {
                return true;
            }
        }
    }

    $blocks = parse_blocks( $content );
    while ( $blocks ) {
        $block = array_pop( $blocks );
        if ( ! empty( $block['innerBlocks'] ) ) {
            $blocks = array_merge( $blocks, $block['innerBlocks'] );
        }

        if ( 'core/block' !== $block['blockName'] || empty( $block['attrs']['ref'] ) ) {
            continue;
        }

        $ref = absint( $block['attrs']['ref'] );
        if ( isset( $seen[ $ref ] ) ) {
            continue;
        }
        $seen[ $ref ] = true;
        $reusable = get_post( $ref );
        if ( $reusable && 'wp_block' === $reusable->post_type
            && content_needs_cf7_assets( $reusable->post_content, $seen ) ) {
            return true;
        }
    }

    return false;
}

function load_cf7_scripts_and_styles() {
    if ( is_admin() || ! is_singular()
        || ! function_exists( 'wpcf7_enqueue_scripts' )
        || ! function_exists( 'wpcf7_enqueue_styles' ) ) {
        return;
    }

    $post = get_queried_object();
    if ( $post instanceof \WP_Post && content_needs_cf7_assets( $post->post_content ) ) {
        wpcf7_enqueue_scripts();
        wpcf7_enqueue_styles();
    }
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\load_cf7_scripts_and_styles', 20 );

/** Route this Local site's test emails to Mailpit instead of the PHP FastCGI port. */
function configure_local_application_mail( $mailer ) {
    if ( wp_get_environment_type() !== 'local'
        || wp_parse_url( home_url(), PHP_URL_HOST ) !== 'mca-new.local' ) {
        return;
    }

    $mailer->isSMTP();
    $mailer->Host = '127.0.0.1';
    $mailer->Port = defined( 'MCA_LOCAL_SMTP_PORT' ) ? (int) MCA_LOCAL_SMTP_PORT : 10286;
    $mailer->SMTPAuth = false;
    $mailer->SMTPSecure = '';
    $mailer->SMTPAutoTLS = false;
    $mailer->Timeout = 10;
}
add_action( 'phpmailer_init', __NAMESPACE__ . '\\configure_local_application_mail', 100 );


function mca_submit_contact_form() {
	if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) {
		wp_send_json_error( [ 'message' => __( 'Please submit the form using POST.', 'MCA' ) ], 405 );
	}
	if ( ! isset( $_POST['security'] ) || ! is_string( $_POST['security'] )
		|| ! check_ajax_referer( 'mca_contact_form', 'security', false ) ) {
		wp_send_json_error(
			[
				'message' => __( 'Security check failed. Please refresh the page and try again.', 'MCA' ),
			],
			403
		);
	}

	$peer = $_SERVER['REMOTE_ADDR'] ?? '';
	$peer = filter_var( $peer, FILTER_VALIDATE_IP ) ? inet_ntop( inet_pton( $peer ) ) : 'unknown';
	if ( ! \MCA\FormSubmissions\consume_submission_attempt( 'ip', $peer, 10 ) ) {
		header( 'Retry-After: 900' );
		wp_send_json_error( [ 'message' => __( 'Too many attempts. Please wait 15 minutes and try again.', 'MCA' ) ], 429 );
	}

	$errors = [];
	foreach ( [ 'full_name' => 255, 'email' => 254, 'industry' => 255, 'pdf_url' => 2048, 'privacy' => 20 ] as $field => $max_length ) {
		if ( isset( $_POST[ $field ] ) && ( ! is_string( $_POST[ $field ] )
			|| strlen( $_POST[ $field ] ) > $max_length * 4
			|| title_strlen( wp_unslash( $_POST[ $field ] ) ) > $max_length ) ) {
			$errors[ $field ] = sprintf( __( 'Please enter text no longer than %d characters.', 'MCA' ), $max_length );
		}
	}
	if ( $errors ) {
		wp_send_json_error( [ 'message' => __( 'Please correct the highlighted fields.', 'MCA' ), 'errors' => $errors ], 422 );
	}

	$full_name = isset( $_POST['full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['full_name'] ) ) : '';
	$email     = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$industry  = isset( $_POST['industry'] ) ? sanitize_text_field( wp_unslash( $_POST['industry'] ) ) : '';
	$privacy   = ! empty( $_POST['privacy'] );

	$errors = [];

	if ( '' === $full_name ) {
		$errors['full_name'] = __( 'Name Surname is required.', 'MCA' );
	}

	if ( '' === $email || ! is_email( $email ) ) {
		$errors['email'] = __( 'A valid email address is required.', 'MCA' );
	}

	if ( '' === $industry ) {
		$errors['industry'] = __( 'Industry is required.', 'MCA' );
	}

	if ( ! $privacy ) {
		$errors['privacy'] = __( 'You must accept the Privacy Notice.', 'MCA' );
	}

	if ( ! empty( $errors ) ) {
		wp_send_json_error(
			[
				'message' => __( 'Please correct the highlighted fields.', 'MCA' ),
				'errors'  => $errors,
			],
			422
		);
	}

	if ( wp_check_comment_disallowed_list( $full_name, $email, '', $industry, $peer, '' ) ) {
		wp_send_json_error( [ 'message' => __( 'Your submission could not be accepted. Please contact us for assistance.', 'MCA' ) ], 422 );
	}
	if ( ! \MCA\FormSubmissions\consume_submission_attempt( 'email', strtolower( $email ), 3 ) ) {
		header( 'Retry-After: 900' );
		wp_send_json_error( [ 'message' => __( 'Too many attempts for this email address. Please wait 15 minutes and try again.', 'MCA' ) ], 429 );
	}

	$inserted = \MCA\FormSubmissions\insert_submission( $full_name, $email, $industry );

	if ( is_wp_error( $inserted ) ) {
		wp_send_json_error(
			[
				'message' => __( 'Something went wrong while saving your submission. Please try again.', 'MCA' ),
			],
			500
		);
	}

	$pdf_url = isset( $_POST['pdf_url'] ) ? esc_url_raw( wp_unslash( $_POST['pdf_url'] ) ) : '';
	if ( empty( $pdf_url ) && defined( 'MCA_FORM_PDF_URL' ) ) {
		$pdf_url = esc_url_raw( MCA_FORM_PDF_URL );
	}

	wp_send_json_success(
		[
			'message' => __( 'Thank you! Your submission has been received.', 'MCA' ),
			'pdf_url' => $pdf_url,
		]
	);
}
add_action( 'wp_ajax_mca_submit_contact_form', __NAMESPACE__ . '\\mca_submit_contact_form' );
add_action( 'wp_ajax_nopriv_mca_submit_contact_form', __NAMESPACE__ . '\\mca_submit_contact_form' );

/**
 * Multibyte-safe strlen wrapper.
 *
 * @param string $value
 * @return int
 */
function title_strlen( $value ) {
    return function_exists( 'mb_strlen' ) ? mb_strlen( $value ) : strlen( $value );
}


/**
 * Multibyte-safe substr wrapper.
 *
 * @param string $value
 * @param int    $start
 * @param int    $length
 * @return string
 */
function title_substr( $value, $start, $length = null ) {
    if ( function_exists( 'mb_substr' ) ) {
        return null === $length ? mb_substr( $value, $start ) : mb_substr( $value, $start, $length );
    }

    return null === $length ? substr( $value, $start ) : substr( $value, $start, $length );
}

/**
 * Multibyte-safe strrpos wrapper.
 *
 * @param string $haystack
 * @param string $needle
 * @return int|false
 */
function title_strrpos( $haystack, $needle ) {
    return function_exists( 'mb_strrpos' ) ? mb_strrpos( $haystack, $needle ) : strrpos( $haystack, $needle );
}

/**
 * Normalize verbose titles before enforcing a shorter SEO title.
 *
 * @param string $title
 * @return string
 */
function normalize_document_title( $title ) {
    $title = wp_strip_all_tags( html_entity_decode( (string) $title, ENT_QUOTES, get_bloginfo( 'charset' ) ) );

    $replacements = [
        '/\bDesign and Build of Utility Scale Battery Energy Storage Systems \(BESS\) and Transmission Connection Infrastructure\b/ui' => 'Utility-Scale BESS & Grid Connection',
        '/\bDesign & Build of Large Scale Battery Energy Storage Systems and Transmission Connection Infrastructure\b/ui' => 'Large-Scale BESS & Grid Connection',
        '/\bAdvancing the Participation of Women Entrepreneurs in Kosovo[\'’]s Clean Energy Transition\b/ui' => 'Women Entrepreneurs in Clean Energy',
        '/\bSupport to the Establishment and Operationalization of the\b/ui' => 'Support for',
        '/\bMinutes of Opening of Technical Offers\b/ui' => 'Technical Offers Opening',
        '/\bPre-Bid conference meeting minutes\b/ui' => 'Pre-Bid Minutes',
        '/\bMinutes of Pre-Bid Conference\b/ui' => 'Pre-Bid Minutes',
        '/\bMinutes of Pre-Bid\b/ui' => 'Pre-Bid Minutes',
        '/\bMinutes of Opening\b/ui' => 'Opening Minutes',
        '/\bMillennium Challenge Account-?Kosovo\b/ui' => 'MCA Kosovo',
        '/\bBattery Energy Storage Systems?\b/ui' => 'BESS',
        '/\bEnergy Storage Corporation\b/ui' => 'ESCorp',
        '/\bSpecific Procurement Notice(?:\s*\(SPN\))?\b/ui' => 'SPN',
        '/\bGeneral Procurement Notice\s*\(GPN\)\b/ui' => 'GPN',
        '/\bGENERAL PROCUREMENT NOTICE\s*\(GPN\)\b/u' => 'GPN',
        '/\bPre-qualification\b/ui' => 'Prequal.',
        '/\bPrequalification\b/ui' => 'Prequal.',
        '/\bClarification No\.?\s*/ui' => 'Clarification ',
        '/\bAddendum No\.?\s*/ui' => 'Addendum ',
        '/\bReference No\.?\s*/ui' => 'Ref. ',
        '/\bUniversity of Prishtina\b/ui' => 'UP',
        '/\s+and\s+/ui' => ' & ',
        '/\s*[–—]+\s*/u' => ' - ',
        '/\s+/' => ' ',
    ];

    foreach ( $replacements as $pattern => $replacement ) {
        $title = preg_replace( $pattern, $replacement, $title );
    }

    return trim( $title, " \t\n\r\0\x0B-:|" );
}

/**
 * Trim a document title at a word boundary.
 *
 * @param string $title
 * @param int    $max_length
 * @return string
 */
function trim_document_title( $title, $max_length ) {
    if ( title_strlen( $title ) <= $max_length ) {
        return $title;
    }

    $trimmed = title_substr( $title, 0, max( 1, $max_length - 1 ) );
    $last_space = title_strrpos( $trimmed, ' ' );

    if ( false !== $last_space && $last_space > (int) floor( $max_length * 0.6 ) ) {
        $trimmed = title_substr( $trimmed, 0, $last_space );
    }

    return rtrim( $trimmed, " \t\n\r\0\x0B,;:-" ) . '…';
}

/**
 * Keep single-entry document titles concise for search results.
 *
 * @param string $title
 * @return string
 */
function build_short_document_title( $title ) {
    $site_name = get_bloginfo( 'name' );
    $separator = ' | ';
    $max_total_length = 60;

    $normalized_title = normalize_document_title( $title );

    if ( title_strlen( $normalized_title . $separator . $site_name ) <= $max_total_length ) {
        return $normalized_title . $separator . $site_name;
    }

    if ( title_strlen( $normalized_title ) <= $max_total_length ) {
        return $normalized_title;
    }

    return trim_document_title( $normalized_title, $max_total_length );
}

/**
 * Shorten overly long document titles without changing the visible heading.
 *
 * @param string $title
 * @return string
 */
function filter_document_title( $title ) {
	// Preserve Yoast's configured titles and social metadata when it owns SEO.
	if ( defined( 'WPSEO_VERSION' ) ) {
		return $title;
	}
    if ( is_admin() || ! is_singular( [ 'post', 'procurement', 'job', 'publication' ] ) ) {
        return $title;
    }

    $post_id = get_queried_object_id();
    if ( ! $post_id ) {
        return $title;
    }

    $post_title = get_the_title( $post_id );
    if ( empty( $post_title ) ) {
        return $title;
    }

    return build_short_document_title( $post_title );
}
add_filter( 'pre_get_document_title', __NAMESPACE__ . '\\filter_document_title' );

/** Require both application documents even when the request omits file fields. */
add_filter('wpcf7_validate', function ($result, $tags) {
    $form = \WPCF7_ContactForm::get_current();
    if (!$form) return $result;
    foreach ($form->scan_form_tags() as $tag) {
        if ($tag->type !== 'file*' || !in_array($tag->name, ['application-resume', 'application-form'], true)) continue;
        $file = $_FILES[$tag->name] ?? null;
        if (!$file || empty($file['name']) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            $result->invalidate($tag, __('Please upload the required document.', 'MCA'));
        }
    }
    return $result;
}, 20, 2);
