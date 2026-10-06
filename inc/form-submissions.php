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

const DB_VERSION = '1.0.0';
const DB_VERSION_OPTION = 'mca_form_submissions_db_version';

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
	update_option( DB_VERSION_OPTION, DB_VERSION );
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
		'edit_posts',
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
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die( esc_html__( 'You do not have permission to view submissions.', 'MCA' ) );
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
			$table->search_box( __( 'Search by name / last name', 'MCA' ), 'mca-form-submission' );
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
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die( esc_html__( 'You do not have permission to export submissions.', 'MCA' ) );
	}

	check_admin_referer( 'mca_export_form_submissions' );

	global $wpdb;

	$table  = table_name();
	$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
	$sql    = "SELECT full_name, email, industry, created_at FROM {$table}";
	$params = array();

	if ( '' !== $search ) {
		$like     = '%' . $wpdb->esc_like( $search ) . '%';
		$sql     .= ' WHERE full_name LIKE %s';
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
		__( 'Name Surname', 'MCA' ),
		__( 'Email', 'MCA' ),
		__( 'Industry', 'MCA' ),
		__( 'Submitted', 'MCA' ),
	) );

	if ( $rows ) {
		foreach ( $rows as $row ) {
			fputcsv( $output, array(
				$row['full_name'],
				$row['email'],
				$row['industry'],
				$row['created_at'],
			) );
		}
	}

	fclose( $output );
	exit;
}
add_action( 'admin_post_mca_export_form_submissions', __NAMESPACE__ . '\\export_csv' );
