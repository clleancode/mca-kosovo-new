<?php
/**
 * Admin list table for form submissions.
 *
 * @package MCA
 */

namespace MCA\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Form_Submissions_List_Table extends \WP_List_Table {
	/**
	 * Define columns shown in the submissions list.
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'full_name' => __( 'Name Surname', 'MCA' ),
			'email'     => __( 'Email', 'MCA' ),
			'industry'  => __( 'Industry', 'MCA' ),
			'created_at' => __( 'Submitted', 'MCA' ),
		);
	}

	/**
	 * Load rows and pagination metadata.
	 */
	public function prepare_items() {
		global $wpdb;

		$per_page = 20;
		$current_page = max( 1, $this->get_pagenum() );
		$search = isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : '';
		$table = \MCA\FormSubmissions\table_name();
		$where = '';
		$params = array();

		if ( '' !== $search ) {
			$where = ' WHERE full_name LIKE %s';
			$params[] = '%' . $wpdb->esc_like( $search ) . '%';
		}

		$count_sql = "SELECT COUNT(*) FROM {$table}{$where}";
		$total_items = $params
			? (int) $wpdb->get_var( $wpdb->prepare( $count_sql, $params ) )
			: (int) $wpdb->get_var( $count_sql );

		$offset = ( $current_page - 1 ) * $per_page;
		$items_sql = "SELECT id, full_name, email, industry, created_at FROM {$table}{$where} ORDER BY created_at DESC LIMIT %d OFFSET %d";
		$item_params = array_merge( $params, array( $per_page, $offset ) );
		$this->items = $wpdb->get_results( $wpdb->prepare( $items_sql, $item_params ), ARRAY_A );

		$columns = $this->get_columns();
		$this->_column_headers = array( $columns, array(), array(), 'full_name' );
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => (int) ceil( $total_items / $per_page ),
			)
		);
	}

	/**
	 * Render a cell value.
	 *
	 * @param array  $item        Current submission.
	 * @param string $column_name Column key.
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		if ( ! isset( $item[ $column_name ] ) ) {
			return '';
		}

		if ( 'created_at' === $column_name ) {
			return esc_html( mysql2date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $item[ $column_name ] ) );
		}

		return esc_html( $item[ $column_name ] );
	}

	/**
	 * Message shown when no submissions match.
	 */
	public function no_items() {
		esc_html_e( 'No form submissions found.', 'MCA' );
	}
}
