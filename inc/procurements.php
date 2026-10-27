<?php
/**
 * Procurement query helpers.
 *
 * @package MCA
 */

namespace MCA\Procurements;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether a procurement is pinned to the top of lists.
 *
 * @param int $post_id Procurement post ID.
 */
function is_pinned( $post_id ) {
	return (bool) get_field( 'procurement_pinned', $post_id );
}

/**
 * Flag a WP_Query to sort pinned procurements first, then by date.
 *
 * @param array  $args       Query arguments.
 * @param string $date_order ASC or DESC for post_date after pinned sort.
 * @return array
 */
function with_pinned_first( array $args, $date_order = 'DESC' ) {
	$args['mca_procurement_pinned_first'] = true;
	$args['mca_pinned_date_order']        = strtoupper( (string) $date_order ) === 'ASC' ? 'ASC' : 'DESC';
	unset( $args['orderby'], $args['order'] );

	return $args;
}

/**
 * @param array     $clauses Query clauses.
 * @param \WP_Query $query   Current query.
 * @return array
 */
function posts_clauses( $clauses, $query ) {
	if ( ! $query->get( 'mca_procurement_pinned_first' ) ) {
		return $clauses;
	}

	global $wpdb;

	$date_order = $query->get( 'mca_pinned_date_order' ) === 'ASC' ? 'ASC' : 'DESC';

	$clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} AS mca_procurement_pin ON ({$wpdb->posts}.ID = mca_procurement_pin.post_id AND mca_procurement_pin.meta_key = 'procurement_pinned')";
	$clauses['orderby'] = "CAST(COALESCE(NULLIF(mca_procurement_pin.meta_value, ''), '0') AS UNSIGNED) DESC, {$wpdb->posts}.post_date {$date_order}, {$wpdb->posts}.ID {$date_order}";

	if ( empty( $clauses['groupby'] ) ) {
		$clauses['groupby'] = "{$wpdb->posts}.ID";
	} elseif ( false === strpos( $clauses['groupby'], "{$wpdb->posts}.ID" ) ) {
		$clauses['groupby'] .= ", {$wpdb->posts}.ID";
	}

	return $clauses;
}

/**
 * Remove duplicate posts (e.g. after a pinned meta JOIN).
 *
 * @param \WP_Post[] $posts Post objects.
 * @return \WP_Post[]
 */
function unique_posts( array $posts ) {
	$unique = [];
	$seen   = [];

	foreach ( $posts as $post ) {
		$id = $post->ID ?? 0;
		if ( ! $id || isset( $seen[ $id ] ) ) {
			continue;
		}
		$seen[ $id ] = true;
		$unique[]    = $post;
	}

	return $unique;
}
add_filter( 'posts_clauses', __NAMESPACE__ . '\\posts_clauses', 10, 2 );
