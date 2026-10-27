<?php
/**
 * Form submissions custom database table + admin UI.
 *
 * @package MCA
 */

namespace MCA\FormSubmissions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const DB_VERSION = '1.1.0';
const DB_VERSION_OPTION = 'mca_form_submissions_db_version';
const MANAGE_CAPABILITY = 'mca_manage_form_submissions';
const CAPABILITY_VERSION_OPTION = 'mca_form_submissions_capability_version';

function maybe_install_capability() {
	if ( '1' === get_option( CAPABILITY_VERSION_OPTION ) ) {
		return;
	}

	$administrator = get_role( 'administrator' );
	if ( ! $administrator ) {
		return;
	}

	$administrator->add_cap( MANAGE_CAPABILITY );
	update_option( CAPABILITY_VERSION_OPTION, '1' );
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\\maybe_install_capability' );

/**
 * Prefixed table name.
 *
 * @return string
 */
function table_name() {
	global $wpdb;

	return $wpdb->prefix . 'mca_form_submissions';
}

/**
 * Create / update the submissions table with dbDelta.
 */
function install_table() {
	global $wpdb;

	$table           = table_name();
	$charset_collate = $wpdb->get_charset_collate();

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$sql = "CREATE TABLE {$table} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		full_name varchar(255) NOT NULL DEFAULT '',
		email varchar(255) NOT NULL DEFAULT '',
		industry varchar(255) NOT NULL DEFAULT '',
		created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		PRIMARY KEY  (id),
		KEY email (email),
		KEY created_at (created_at)
	) {$charset_collate};";

	dbDelta( $sql );

	$rate_table = $wpdb->prefix . 'mca_submission_limits';
	dbDelta( "CREATE TABLE {$rate_table} (
		bucket_key varchar(64) NOT NULL,
		hits int unsigned NOT NULL DEFAULT 0,
		expires_at bigint(20) unsigned NOT NULL,
		PRIMARY KEY  (bucket_key),
		KEY expires_at (expires_at)
	) {$charset_collate};" );
	update_option( DB_VERSION_OPTION, DB_VERSION );
}

function consume_submission_attempt( $scope, $identity, $limit ) {
	global $wpdb;
	$table = $wpdb->prefix . 'mca_submission_limits';
	$now   = time();
	$key   = hash_hmac( 'sha256', $scope . ':' . $identity, wp_salt( 'nonce' ) );

	$wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE expires_at <= %d", $now ) );
	$result = $wpdb->query( $wpdb->prepare(
		"INSERT INTO {$table} (bucket_key, hits, expires_at) VALUES (%s, 1, %d)
		 ON DUPLICATE KEY UPDATE hits = LEAST(hits + 1, %d)",
		$key, $now + 15 * MINUTE_IN_SECONDS, $limit + 1
	) );
	if ( false === $result ) {
		return false;
	}
	$hits = $wpdb->get_var( $wpdb->prepare( "SELECT hits FROM {$table} WHERE bucket_key = %s", $key ) );
	return null !== $hits && (int) $hits <= $limit;
}

/** Neutralize spreadsheet formulas at export only; retain original stored text. */
function csv_text( $value ) {
	$value = (string) $value;
	if ( preg_match( '/^[\s\p{Z}\p{Cc}\x{FEFF}]*[=+@\-＝＋＠－]/u', $value )
		|| preg_match( '/^[\t\r\n]/', $value ) ) {
		// A tab also makes fputcsv quote the field; Excel imports it as text.
		return "\t" . $value;
	}
	return $value;
}

/**
 * Ensure the table exists (theme load / version bump).
 */
function maybe_install_table() {
	$installed = get_option( DB_VERSION_OPTION );

	if ( DB_VERSION !== $installed ) {
		install_table();
	}
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\\maybe_install_table' );
add_action( 'after_switch_theme', __NAMESPACE__ . '\\install_table' );

/**
 * Insert a submission row.
 *
 * @param string $full_name Full name.
 * @param string $email     Email.
 * @param string $industry  Industry.
 * @return int|\WP_Error Insert ID or error.
 */
function insert_submission( $full_name, $email, $industry ) {
	global $wpdb;

	$table = table_name();

	// Safety: ensure table exists before insert.
	maybe_install_table();

	$result = $wpdb->insert(
		$table,
		array(
			'full_name'  => $full_name,
			'email'      => $email,
			'industry'   => $industry,
			'created_at' => current_time( 'mysql' ),
		),
		array( '%s', '%s', '%s', '%s' )
	);

	if ( false === $result ) {
		return new \WP_Error(
			'mca_form_submission_insert_failed',
			__( 'Something went wrong while saving your submission. Please try again.', 'MCA' ),
			array( 'db_error' => $wpdb->last_error )
		);
	}

	return (int) $wpdb->insert_id;
}

/**
 * Register admin menu page.
 */
function register_admin_menu() {
	add_menu_page(
		__( 'Form Submissions', 'MCA' ),
		__( 'Form Submissions', 'MCA' ),
		MANAGE_CAPABILITY,
		'mca-form-submissions',
		__NAMESPACE__ . '\\render_admin_page',
		'dashicons-email-alt',
		25
	);
}
add_action( 'admin_menu', __NAMESPACE__ . '\\register_admin_menu' );

/**
 * Render admin list page.
 */
function render_admin_page() {
	if ( ! current_user_can( MANAGE_CAPABILITY ) ) {
		wp_die( esc_html__( 'You do not have permission to view submissions.', 'MCA' ), '', array( 'response' => 403 ) );
	}

	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
	require_once get_template_directory() . '/inc/admin/class-form-submissions-list-table.php';

	$table = new \MCA\Admin\Form_Submissions_List_Table();
	$table->prepare_items();

	$search = isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : '';
	$export_url = wp_nonce_url(
		add_query_arg(
			array(
				'action' => 'mca_export_form_submissions',
				's'      => $search,
			),
			admin_url( 'admin-post.php' )
		),
		'mca_export_form_submissions'
	);
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Form Submissions', 'MCA' ); ?></h1>
		<a href="<?php echo esc_url( $export_url ); ?>" class="page-title-action">
			<?php esc_html_e( 'Export CSV / Excel', 'MCA' ); ?>
		</a>
		<hr class="wp-header-end">

		<form method="get">
			<input type="hidden" name="page" value="mca-form-submissions" />
			<?php
			$table->search_box( __( 'Search by email', 'MCA' ), 'mca-form-submission' );
			$table->display();
			?>
		</form>
	</div>
	<?php
}

/**
 * Export submissions as CSV (Excel-compatible).
 */
function export_csv() {
	if ( ! current_user_can( MANAGE_CAPABILITY ) ) {
		wp_die( esc_html__( 'You do not have permission to export submissions.', 'MCA' ), '', array( 'response' => 403 ) );
	}

	check_admin_referer( 'mca_export_form_submissions' );

	global $wpdb;

	$table  = table_name();
	$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
	$sql    = "SELECT email, created_at FROM {$table}";
	$params = array();

	if ( '' !== $search ) {
		$like     = '%' . $wpdb->esc_like( $search ) . '%';
		$sql     .= ' WHERE email LIKE %s';
		$params[] = $like;
	}

	$sql .= ' ORDER BY created_at DESC';

	$rows = $params
		? $wpdb->get_results( $wpdb->prepare( $sql, $params ), ARRAY_A )
		: $wpdb->get_results( $sql, ARRAY_A );

	$filename = 'form-submissions-' . gmdate( 'Y-m-d-His' ) . '.csv';

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=' . $filename );

	$output = fopen( 'php://output', 'w' );
	fwrite( $output, "\xEF\xBB\xBF" );

	fputcsv( $output, array(
		__( 'Email', 'MCA' ),
		__( 'Submitted', 'MCA' ),
	), ',', '"', '' );

	if ( $rows ) {
		foreach ( $rows as $row ) {
			fputcsv( $output, array(
				csv_text( $row['email'] ),
				$row['created_at'],
			), ',', '"', '' );
		}
	}

	fclose( $output );
	exit;
}
add_action( 'admin_post_mca_export_form_submissions', __NAMESPACE__ . '\\export_csv' );

/**
 * Store validated CF7 newsletter signups before its configured mail notification.
 * Newsletter forms opt in with an acceptance field named newsletter-consent.
 * CF7 runs this hook only after validation, acceptance and spam checks pass.
 */
function save_cf7_newsletter( $form, &$abort, $submission ) {
	if ( $abort ) {
		return;
	}
	$email_field = '';
	$is_newsletter = false;
	foreach ( $form->scan_form_tags() as $tag ) {
		if ( 'acceptance' === $tag->basetype && 'newsletter-consent' === $tag->name ) {
			$is_newsletter = true;
		}
		if ( 'email' === $tag->basetype && '' === $email_field ) {
			$email_field = $tag->name;
		}
	}
	if ( ! $is_newsletter || ! $email_field ) {
		return;
	}
	$value = $submission->get_posted_data( $email_field );
	$consent = $submission->get_posted_data( 'newsletter-consent' );
	$email = is_string( $value ) ? sanitize_email( $value ) : '';
	$error = '';
	if ( ! is_string( $value ) || strlen( $value ) > 254 || ! is_email( $email ) || empty( $consent ) ) {
		$error = __( 'Please enter a valid email and accept the privacy notice.', 'MCA' );
	} else {
		$peer = $_SERVER['REMOTE_ADDR'] ?? '';
		$peer = filter_var( $peer, FILTER_VALIDATE_IP ) ? inet_ntop( inet_pton( $peer ) ) : 'unknown';
		if ( ! consume_submission_attempt( 'ip', $peer, 10 )
			|| ! consume_submission_attempt( 'email', strtolower( $email ), 3 ) ) {
			$error = __( 'Too many attempts. Please wait 15 minutes and try again.', 'MCA' );
		} elseif ( is_wp_error( insert_submission( '', $email, '' ) ) ) {
			$error = __( 'Your subscription could not be saved. Please try again.', 'MCA' );
		}
	}
	if ( $error ) {
		$abort = true;
		$submission->set_response( $error );
	}
}
add_action( 'wpcf7_before_send_mail', __NAMESPACE__ . '\\save_cf7_newsletter', 20, 3 );
